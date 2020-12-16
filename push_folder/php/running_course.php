<?php

try
{
    
    require_once('../common/common.php');
    
    $post = sanitize($_POST);
    
    $code = $post['dat_member_code'];
    $kcal = $post['kcal'];
    $distance = $post['distance'];
    
    $dsn='mysql:dbname=portfolio;host=localhost;charset=utf8';
    $user='root';
    $password='';
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql = 'INSERT INTO running_course(dat_member_code,kcal,distance)VALUES(?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data[] = $code;
    $data[] = $kcal;
    $data[] = $distance;
    $stmt->execute($data);
    
    $sql = 'SELECT LAST_INSERT_ID()';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $rec = $stmt -> fetch(PDO::FETCH_ASSOC);
    $lastcode = $rec['LAST_INSERT_ID()'];
    
    $dbh = null;
    
    
    $array = [$code,$lastcode];
    echo json_encode($array);
}
catch(Exception $e){
    print 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}

?>