<?php

session_start();
session_regenerate_id(true);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ポートフォリオ</title>
<link rel="stylesheet" href="../stylesheet/shop_form.css">

</head>
<body>
<div id="content">
<div id="input_area">

<?php

try
{

require_once('../common/common.php');

$post = sanitize($_POST);

$onamae  = $post['onamae'];
$email   = $post['email'];
$pass    = $post['pass'];

print $onamae.'様<br/>';
print '<br/>';
print '登録ありがとうございました。<br/>下記の内容で登録させていただきます。<br/><br/>';
print 'お名前:　　　　　';
print $onamae;
print '<br/>';
print 'メールアドレス:　';
print $email;
print '<br/>';
print 'パスワード:　　　';
print $pass;
print '<br/>';


$dsn='mysql:dbname=portfolio;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

$sql = 'INSERT INTO dat_member(password,name,email)VALUES(?,?,?)';
$stmt = $dbh->prepare($sql);
$data[] = md5($pass);
$data[] = $onamae;
$data[] = $email;
$stmt->execute($data);

$dbh = null;

}
catch(Exception $e)
{
    print 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}

?>

<br/>
<a href="portfolio.php">portfolioページへ移動</a>
</div>
</div>

</body>
</html>