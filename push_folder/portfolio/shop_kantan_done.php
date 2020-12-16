<?php

session_start();
session_regenerate_id(true);
if(isset($_SESSION['member_login']) == false)
{
    print 'ログインされていません。';
    print '<a href="shop_list.php">商品一覧へ<a/>';
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ポートフォリオ</title>
</head>
<body>

<?php

try
{

require_once('../common/common.php');

$post = sanitize($_POST);

$onamae  = $post['onamae'];
$email   = $post['email'];
$postal1 = $post['postal1'];
$postal2 = $post['postal2'];
$address = $post['address'];
$tel     = $post['tel'];

print $onamae.'様<br/>';
print '<br/>';
print 'ご注文ありがとうございました。<br/>';
print $email.'にメールを送信しましたのでご確認ください。<br/>';
print '商品は以下の住所に発送させて頂きます。<br/>';
print '〒 '.$postal1.'-'.$postal2.'<br/>';
print $address.'<br/>';
print '℡ '.$tel.'<br/>';
print '<br/>';

$honbun = '';  //ここだけ空の$honbunを作成して初期化してやる。
$honbun .= $onamae."様\n\n この度はご注文ありがとうございました。\n";
$honbun .= "ご注文商品\n";
$honbun .= "--------------------------------------------------------\n";

$cart = $_SESSION['cart'];
$kazu = $_SESSION['kazu'];
$max  = count($cart);

$dsn='mysql:dbname=shop;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

for($i=0;$i<$max;$i++)
{
    $sql='SELECT name,price FROM mst_product WHERE code=?';
    $stmt=$dbh->prepare($sql);
    $data[0]=$cart[$i];
    $stmt->execute($data);

    $rec =$stmt->fetch(PDO::FETCH_ASSOC);

    $name   = $rec['name'];
    $price  = $rec['price'];
    $kakaku[] = $price;
    $suryo  = $kazu[$i];
    $shokei = $price * $suryo;

    $honbun .= $name.' ';
    $honbun .= $price.'円 X';
    $honbun .= $suryo.'個 =';
    $honbun .= $shokei."円\n";
}

//テーブルをロック
$sql = 'LOCK TABLES dat_sales WRITE, dat_sales_product WRITE, dat_member WRITE';
$stmt = $dbh->prepare($sql);
$stmt->execute();

//注文データの追加

$lastmembercode = $_SESSION['member_code']; //$lastmembercodeを定義しておくことでif内の$lastmembercode = $rec['LAST_INSERT_ID()']の内容がdat_salesにも反映される

$sql = 'INSERT INTO dat_sales(code_member,name,email,postal1,postal2,address,tel)VALUES(?,?,?,?,?,?,?)';
$stmt = $dbh->prepare($sql);
$data = array();
$data[] = $lastmembercode;
$data[] = $onamae;
$data[] = $email;
$data[] = $postal1;
$data[] = $postal2;
$data[] = $address;
$data[] = $tel;
$stmt->execute($data);

//今追加された注文コードの取得
$sql = 'SELECT LAST_INSERT_ID()';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$rec = $stmt->fetch(PDO::FETCH_ASSOC);
$lastcode = $rec['LAST_INSERT_ID()']; 

//注文明細データの追加
for($i=0;$i<$max;$i++)
{
    $sql = 'INSERT INTO dat_sales_product(code_sales,code_product,price,quantity)VALUES(?,?,?,?)';
    $stmt = $dbh->prepare($sql);
    $data = array();
    $data[] = $lastcode;
    $data[] = $cart[$i];
    $data[] = $kakaku[$i];
    $data[] = $kazu[$i];
    $stmt->execute($data);
}

//ロック解除
$sql = 'UNLOCK TABLES';
$stmt = $dbh->prepare($sql);
$stmt->execute();

$dbh=null;

$honbun .="送料は無料です。\n";
$honbun .="--------------------------------------------------------\n";
$honbun .="\n";
$honbun .="代金は以下の口座にお振込みください。\n";
$honbun .="ＯＯ銀行 ＯＯ支店 普通口座 1234567\n";
$honbun .="入金の確認が取れ次第、梱包、発送させて頂きます。\n";
$honbun .="\n";

$honbun .="□□□□□□□□□□□□□□□□□□□□□□□□□\n";
$honbun .="~安心野菜のろくまる農園~\n";
$honbun .="\n";
$honbun .="ＯＯ県ＯＯ市ＯＯ町123-4\n";
$honbun .="℡ 123-123-1234\n";
$honbun .="メール yahoo@gmail.com\n";
$honbun .="□□□□□□□□□□□□□□□□□□□□□□□□□\n";
// print '<br/>';
// print nl2br($honbun);

//====================メール送信部分=======================

/*
$title = 'ご注文ありがとうございます。';
$header = 'From: info@rokumaru.co.jp';
$honbun = html_entity_decode($honbun,ENT_QUOTES,'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
//mb_send_mail(宛先,件名,本文,ヘッダ);
mb_send_mail($email,$title,$honbun,$header);

$title = 'お客様からご注文がありました。';
$header = 'From: '.$email;
$honbun = html_entity_decode($honbun,ENT_QUOTES,'UTF-8');
mb_language('Japanese');
mb_internal_encoding('UTF-8');
//mb_send_mail(宛先,件名,本文,ヘッダ);
mb_send_mail('info@rokumaru.co.jp',$title,$honbun,$header);
*/

//====================メール送信部分=======================

}
catch(Exception $e)
{
    print 'ただいま障害により大変ご迷惑をお掛けしております。';
    exit();
}

?>

<br/>
<a href="shop_list.php">商品画面へ</a>
</body>
</html>