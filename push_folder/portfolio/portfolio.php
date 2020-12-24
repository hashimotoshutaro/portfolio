<?php
session_start();
session_regenerate_id(true);
if(isset($_SESSION['member_login']) == false)
{
    $okflag = 0;
}
else
{
    $okflag = 1;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ポートフォリオ</title>
<link rel="stylesheet" href="../stylesheet/portfolio.css">
<link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
<!--Leaflet-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" />
</head>
<body onload="init()">
<div id="background_color">

<div id="header">
<div id="title">
    Shutaro's Portfolio Website
</div>

<div id="menu">
    <a href="./portfolio/index.php"><img src=""></a>
    <a href="./index.php">Top</a>
    <a href="./portfolio.php">Portfolio</a>
</div>

</div> <!--header-->
<div id="content">

<?php 
    if($okflag == 0){
        print '<div id="welcome">';
        print 'ようこそゲスト様';
        print '<br/>';
        print '<a href="member_login.html">ログイン画面へ<a/><br/>';
        print '<br/>';
        print '</div>';
    }else{
        print '<div id="welcome">';
        print 'ようこそ';
        print $_SESSION['member_name'];
        print '様';
        print '<br/>';
        print '<a href="member_logout.php">ログアウト</a><br/>';
        print '<br/>';
        print '</div>';
    }
    
?>

<div id="map_textarea">
   
    <?php
    if($okflag == 0){
        print '<p>アカウントを作成するとデータベースを利用して過去に登録したランニングコースを呼び出すことができます。</p>';
        print '<p>アカウント作成は<a href="create_acount.html">コチラ</a></p>';
    }
    
    ?>
    
    <p>体重とランニングコースを選択して計算するボタンをクリックしてください。</p>
    <p>ランニングコースをリセットしたい場合は青線をクリックしてください。</p>
</div>
<div id="weight_textarea">
    <p>体重を半角数字で入力して下さい。</p>
    <form name="form1">
    <textarea name="weight"></textarea>kg
    </form>
    <br/>
    <button><a href="portfolio.php">リセット</a></button>
</div>
<div id="mapcontainer" style="width: 100%;height: 500px;"></div>
<div id="next_button">
<input type="button" value="計算する" onclick="total_mets_func()">
<input type="button" value="ランニングコースを登録" onclick="recode_func()">

<?php

if($okflag == 1){
    $code = $_SESSION['member_code'];
    $param = array(
	   "code" => $code,
    );
    $param_json = json_encode( $param );  //JSONエンコード
    
    $dsn='mysql:dbname=portfolio;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT code FROM running_course WHERE dat_member_code=?';
    $stmt = $dbh->prepare($sql);
    $data[] = $code;
    $stmt->execute($data);
        
    $dbh = null;
        
    print '<div id=call_course>';
    print '<p>呼び出せるランニングコース番号一覧</p>';
        
    while(true){
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        if($rec == false){
            break;
        }
        print $rec['code'];
        print ' / ';
        
    }
    print '</div>';
}
?>

<div id="input_course_textarea">
    <p>呼び出したいコース番号を半角数字で入力して下さい。</p>
    <form name="form2">
    <textarea name="course"></textarea>
    </form>
</div>
<input type="button" value="過去に登録したランニングコースを呼び出す" onclick="call_course_func()"><br/><br/>
</div>
<div id="kcal"></div>
<div id="distance"></div>
<div id="textarea">
    
</div>
</div>
</div>

<!--Leaflet-->
<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"></script>
<!--jquery Ver3.5.1-->
<script src="../javascript/jquery-3.5.1.min.js"></script>

<script>
    'use strict'
    
    //グローバル変数にmapとplineを設定
    let map;
    let pline;
    let lat;
    let lng;
    let mapclickcounter = 0;
    let mk = null;
    
    //グローバル変数地獄
    let new_latitude;  //1点の位置　(10,20,30)
    let new_longitude;
    let old_latitude;  //1点の位置　(10,20,30)
    let old_longitude;
    let latlng_array =[];
    let elevation;
    let new_elevation;
    let old_elevation;
    let elevation_array = [];
    let distansce;
    let distance_array = [];
    let click_counter = 0;
    let kcal_array = [];
    const running_speed = 5;
    let mets;
    let total_kcal;
    let total_distance =0;
    let array;
    let sub_count = 0;
    
    
    function init() {
        map = L.map('mapcontainer', { zoomControl: false });
        var mpoint = [34.66112248216162, 133.91780376499813];
        map.setView(mpoint, 15);
        L.tileLayer('https://cyberjapandata.gsi.go.jp/xyz/std/{z}/{x}/{y}.png', {
          attribution: "<a href='https://maps.gsi.go.jp/development/ichiran.html' target='_blank'>地理院タイル</a>"
        }).addTo(map);
        
        //地図のclickイベントでonMapClick関数を呼び出す
        map.on('click', onMapClick);
        map.on('click',conlatlng);
          
        //plineをpolylineオブジェクトとし、空の座標を入れて地図に追加
        //bubblingMouseEvents属性をfalseに設定しておき、イベントがmapオブジェクトに連鎖するのを防ぐ
        pline = L.polyline([], { color: 'blue', weight: 5, bubblingMouseEvents: false }).addTo(map)
        
        //plineのclickイベントでonLineClick関数を呼び出す
        pline.on('click', onLineClick);
    }
    
    
    function onMapClick(e) {
        //地図のclickイベントで呼び出される
        //plineにクリック地点の座標を追加する
        pline.addLatLng(e.latlng); //青線の追加

        //地図のclickイベント呼び出される
        //クリック地点の座標にマーカーを追加、マーカーのclickイベントでonMarkerClick関数を呼び出し
        if(mapclickcounter == 0){
            //mk = L.marker(e.latlng).on('click', onMarkerClick).addTo(map);
            mk = L.marker(e.latlng).addTo(map);
            mapclickcounter = 1;
        }
    }
    
    function conlatlng(e){
        if(click_counter >= 1){
            old_latitude = new_latitude;
            old_longitude = new_longitude;
            new_latitude = e.latlng.lat;
            new_longitude = e.latlng.lng;       
            latlng_array.push(make_obj(new_latitude,new_longitude));
            console.log("new2_lat: " + new_latitude + ", new2_lng: " + new_longitude);
            elevrequest(new_latitude,new_longitude);
        }
        if(click_counter == 0){
            new_latitude = e.latlng.lat;
            new_longitude = e.latlng.lng;
            latlng_array.push(make_obj(new_latitude,new_longitude));
            console.log("new_lat: " + new_latitude + ", new_lng: " + new_longitude);
            console.log(latlng_array);
            elevrequest(new_latitude,new_longitude);
        }
    }
    
    
    //国土地理院へリクエスト
    function elevrequest(new_latitude,new_longitude){
        let url = `https://cyberjapandata2.gsi.go.jp/general/dem/scripts/getelevation.php?lon=${new_longitude}&lat=${new_latitude}&outtype=JSON`;
        fetch(url).then(function(response) {
            return response.json();
        }).then(function(data) {
            if(click_counter >= 1){
                console.log('=====elevrequest_secondtime_START=====')
                
                new_latitude = new_latitude;
                new_longitude = new_longitude;
                old_elevation = new_elevation;
                new_elevation = data.elevation;
                elevation = new_elevation - old_elevation
                elevation_array.push(old_elevation - new_elevation);
                distansce = calculateDistance(old_latitude,old_longitude,new_latitude,new_longitude);
                distance_array.push(calculateDistance(old_latitude,old_longitude,new_latitude,new_longitude));
                sub_count += 1;
            }
            if(click_counter == 0){
            console.log('=====elevrequest_START=====');
                
            new_elevation = data.elevation;
            click_counter += 1;
            sub_count += 1;
            console.log(click_counter);
            }
            
            let slope = (elevation * 100) / distansce ;

            //勾配をMETsに振り分ける
            if(slope >= 1 && slope <= 5){
                mets = 5.3;
            }else if(slope > 5){
                mets = 8;
            }else if(slope < 1){
                mets = 3.3;
            }
            
            mets_func();                
        });
    }
    
    //ラジアンに変換
    function deg2rad(deg) {
        return deg * Math.PI / 180.0;
    }
    
    //距離の計算
    function calculateDistance(new_lat, new_lon, old_lat, old_lon) {
        let rad_lat1 = deg2rad(new_lat);
        let rad_lon1 = deg2rad(new_lon);
        let rad_lat2 = deg2rad(old_lat);
        let rad_lon2 = deg2rad(old_lon);
    
        let dp = rad_lon1 - rad_lon2;       // 2点の緯度差
        let dr = rad_lat1 - rad_lat2;       // 2点の経度差
        let p = (rad_lon1 + rad_lon2) / 2.0;// 2点の平均緯度
        
        // 先に計算しておいた定数
        const e2 = 0.00669437999019758;   // WGS84における「離心率e」の2乗
        const Rx = 6378137.0;             // WGS84における「赤道半径Rx」
        const m_numer = 6335439.32729246; // WGS84における「子午線曲率半径M」の分子(Rx(1-e^2))

        let w = Math.sqrt(1.0 - e2 * Math.pow(Math.sin(p), 2));
        let m = m_numer / Math.pow(w, 3);   // 子午線曲率半径
        let n = Rx / w;                     // 卯酉(ぼうゆう)線曲率半径

        // 2点間の距離(単位m)
        let d = Math.sqrt(Math.pow((m * dp), 2) + Math.pow((n * Math.cos(p) * dr), 2));
        return d;
    }
    
    //線ｸﾘｯｸでリセット
    function onLineClick(e) {
        //plineのclickイベントで呼び出される
        //plineに空の座標を入れて非表示にする
        pline.setLatLngs([]);
        
        new_latitude = 0;  
        new_longitude = 0;
        old_latitude = 0;  
        old_longitude = 0;
    
        elevation = 0;
        new_elevation = 0;
        old_elevation = 0;
    
        distansce = 0;
        distance_array = [];
        elevation_array = [];
        click_counter = 0;
        total_distance = [];
        
        kcal_array = [];
        mets = 0;
        
        mapclickcounter = 0;
        //map.removeLayer(mk);
    }
    
    
    function mets_func(){
        const weight = document.form1.weight.value;
        console.log(typeof(weight));
        let flag = 0;
        if(document.form1.weight.value.match(/^([1-9]\d*|0)$/)){
		    flag = 1;
	    }
        if(document.form1.weight.value == ""){
            flag = 2;
        }
	    if(flag == 0){
		    window.alert('異常な入力を受け付けません');
		    return 0;
	    }else if(flag == 2){
		    window.alert('数値の入力を確認できません');
		    return 0;
	    }else{
		    let kcal = 1.05 * weight * mets * ((distansce / 1000) / running_speed);
            kcal_array.push(kcal);
	    }
    }
    
    function total_mets_func(){
        total_kcal = 0;
        kcal_array.shift();
        //kcal_array.pop();
        for(let i=0; i <= kcal_array.length-1; i++){
            total_kcal += kcal_array[i];
            console.log(kcal_array[i]);
        }
        
        for(let i=0; i <= distance_array.length-1; i++){
            total_distance += distance_array[i];
            console.log(distance_array[i]);
        }
        
        total_kcal = Math.floor(total_kcal);
        document.getElementById('kcal').textContent = total_kcal + "kcalの消費です。";
        total_distance = Math.floor(total_distance);
        document.getElementById('distance').textContent = "総距離" +total_distance + "mです。";
    }
    
    function make_obj(new_latitude,new_longitude){
        let latlngobj = {}; //連想配列作成latlngobj = new Object();
        latlngobj.latitude = new_latitude;
        latlngobj.longitude = new_longitude;
        
        return latlngobj;
    }
    
    function running_course_recode_func(){
        console.log('==running_course_recode_func START==');
        let dfd = $.Deferred();
        let param = JSON.parse('<?php if($okflag == 1){echo $param_json;} ?>');  //JSONデコード 
        let sendkcal = Math.floor(total_kcal);
        let senddistance = Math.floor(total_distance);
        
        $.ajax({
            type: "POST",
            url: "../php/running_course.php",
            data: {
                'dat_member_code': param.code,
                'kcal': sendkcal,
                'distance': senddistance
            }
        }).done(function(data){
                array = JSON.parse(data);
            console.log('success running_course_recode_func');
                dfd.resolve();
        }).fail( function(XMLHttpRequest, textStatus, errorThrown) {
                alert('登録失敗');
            dfd.reject();
        });
        
        return dfd.promise();      
    }
 
    function location_recode_func(array){
        console.log('==location_recode_func START==');
        let dfd = $.Deferred();
        let senddat_member_code = array[0];
        let sendrunning_course_id = array[1];
        let location_array = JSON.stringify(latlng_array);
        
        console.log(senddat_member_code);
        console.log(sendrunning_course_id);
        for(let o=0;o<location_array.length;o++){
            //console.log(location_array[o]);
        }
        
        
        $.ajax({
            type: "POST",
            url: "../php/location.php",
            data: {
                'dat_member_code': senddat_member_code,
                'running_course_id': sendrunning_course_id,
                'click_count': sub_count,
                'array': location_array
            }
        }).done(function(data){
                array = JSON.parse(data);
                console.log('success location_recode_func');
                dfd.resolve();
        }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert('登録失敗');
                dfd.reject();
        })
            
        return dfd.promise();
    }
    
    function recode_func(){
        console.log('ajax_START!!!!!');
        running_course_recode_func()
            .then(function(){
                return location_recode_func(array);
        });
    }
    
    function call_course_func(){
        console.log('==call_course_func START==');
        let course = document.form2.course.value;
        let mem_code = JSON.parse('<?php if($okflag == 1){echo $code;} ?>');
        if(course == ""){ 
            alert("呼び出したいランニングコース番号を半角数字で入力して下さい。");
        }else{
            $.ajax({
            type: "POST",
            url: "../php/call_course.php",
            data: {
                'dat_member_code': mem_code,
                'running_course_id': course
            }
        }).done(function(data){
                array = JSON.parse(data);
                console.log('==call_course_func DATACATCH==');
                
                let location_array2 = [];
                for(let i=0; i<array[0].length; i++){
                    let location_array = [];
                    location_array.push(array[0][i],array[1][i]);
                    location_array2.push(location_array);
                }                
                document.getElementById('kcal').textContent = array[2][0] + "kcalの消費です。";
                document.getElementById('distance').textContent = "総距離" + array[3][0] + "mです。";
                
                pline = L.polyline(location_array2, { color: 'red', weight: 5, bubblingMouseEvents: false }).addTo(map);
                console.log('success location_recode_func');
        }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
                alert('登録失敗');
        })
        }
    }
    
    
</script>


</body>
</html>