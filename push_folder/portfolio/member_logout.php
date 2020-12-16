<?php

session_start();
$_SESSION = array(); //セッション変数を空
if(isset($_COOKIE[session_name()]) == true)
{
    setcookie(session_name(),'',time()-42000,'/'); //セッションIDをクッキーから削除
}
session_destroy(); //セッション破棄
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ポートフォリオ</title>
<link rel="stylesheet" href="../stylesheet/index.css">
</head>
<body>
<div id="background_color">

<div id="content">
<div id="textarea">
ログアウトしました。<br/>
<br/>
<a href="portfolio.php">portfolioページへ移動</a>
</div>
</div>

</div>
</body>
</html>