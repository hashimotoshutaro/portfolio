<?php

try
{
    
    require_once('../common/common.php');
    
    $post = sanitize($_POST);
    
    $dat_member_code = $post['dat_member_code'];
    $running_course_id = $post['running_course_id'];    
    $dsn='mysql:dbname=portfolio;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    $sql = 'SELECT * FROM location,running_course WHERE running_course_id=? AND code=?';
    
    $stmt = $dbh->prepare($sql);
    
    $data[] = $running_course_id;
    $data[] = $running_course_id;
    $stmt->execute($data);

    
    $dbh = null;
    
    
    while(true){
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        $lat_array[] = $rec['latitude'];
        $lon_array[] = $rec['longitude'];
        $kcal[] = $rec['kcal'];
        $member[] = $rec['dat_member_code']; 
        $dis[] = $rec['distance'];
        if($rec == false){
            array_pop($lat_array);
            array_pop($lon_array);
            array_pop($kcal);
            array_pop($member);
            array_pop($dis);
            if($member[0] == $dat_member_code){
                $location_array = array($lat_array,$lon_array,$kcal,$dis);
                $location_array = json_encode($location_array);
            
                echo $location_array;
            }      
            break;
        }
    }
}
catch(Exception $e){
    exit();
}

?>