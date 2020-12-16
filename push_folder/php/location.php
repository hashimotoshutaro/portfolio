<?php

try
{
    
    require_once('../common/common.php');
    
    $post = sanitize($_POST);
    
    $dat_member_code = $post['dat_member_code'];
    $running_course_id = $post['running_course_id'];
    $click_count = $post['click_count'];
    $array = $_POST['array'];  
    $array = json_decode($array);//連想配列に変換 返り値：array
    
    $latitude = array_column($array,'latitude');//返り値：array
    $longitude = array_column($array,'longitude');
    
    $dsn='mysql:dbname=portfolio;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    for($i=0; $i<$click_count; $i++){
        $sql = 'INSERT INTO location(running_course_id,order_num,latitude,longitude)VALUES(?,?,?,?)';
        $stmt = $dbh->prepare($sql);
        $data = array();
        $data[] = $running_course_id;
        $data[] = $i;
        $data[] = $latitude[$i];
        $data[] = $longitude[$i];
        $stmt->execute($data);
    }
    $dbh = null;
    
    $latitude = json_encode($latitude);
    $longitude = json_encode($longitude);
 
    echo $latitude;
}
catch(Exception $e){
    
    $latitude = json_encode($latitude);
        echo $latitude;

    exit();
}

?>