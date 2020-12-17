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

require_once('../common/common.php');
require_once('../common/regex.php');

$post = sanitize($_POST);

$onamae  = $post['onamae'];
$email   = $post['email'];
$pass    = $post['pass'];
$pass2   = $post['pass2'];

$okflg = true;

if($onamae == '')
{
    print 'お名前が入力されていません。<br/><br/>';
    $okflg = false;
}
else
{
    print 'お名前<br/>';
    print $onamae;
    print '<br/><br/>';
}

if(preg_match($email_checker,$email) == 0)
{
    print 'メールアドレスを正確に入力して下さい。<br/><br/>';
    $okflg = false;
}
else
{
    print 'メールアドレス<br/>';
    print $email;
    print '<br/><br/>';
}


if($pass == '')
{
    print 'パスワードが入力されていません。<br/><br/>';
    $okflg = false;
}

if($pass != $pass2)
{
    print 'パスワードが一致しません。<br/><br/>';
    $okflg = false;
}

print '<br/><br/>';

if($okflg == true)
{
    print '<form method="post" action="create_acount_done.php">';
    print '<input type="hidden" name="onamae" value="'.$onamae.'">';
    print '<input type="hidden" name="email" value="'.$email.'">';
    print '<input type="hidden" name="pass" value="'.$pass.'">';
    print '<input type="button" onclick="history.back()" value="戻る">';
    print '<input type="submit" value="ＯＫ"><br/>';
    print '</form>';
}
else
{
    print '<form>';
    print '<input type="button" onclick="history.back()" value="戻る">';
    print '</form>';
}

?>
</div>
</div>

</body>
</html>