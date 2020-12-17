<?php
session_start();
session_regenerate_id(true);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ポートフォリオ</title>
<link rel="stylesheet" href="../stylesheet/index.css">
<link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
</head>
<body>
<div id="background_color">

<div id="header">
<div id="title">
    Shutaro's Portfolio Website
</div>

<div id="menu">
    <a href="./index.php">Top</a>
    <a href="./portfolio.php">Portfolio</a>
</div>
</div> <!--header-->

<div id="content">
    <div id="coffee-cup-img">
        <div id="steam1"></div>
        <div id="steam2"></div>
        <img src="../img/indeximg/40902white.png">
    </div>
    <div id="textarea">
        <div id="namearea">Shutaro</div>
        <div id="photograph"><img src="../img/indeximg/photograph.jpg"></div>
        <p>初めまして!<span>橋本周太郎</span>です。<br>私のポートフォリオサイトをご覧いただきありがとうございます。<br>私は現在プログラマーになることを目標に日々勉強に励んでいます。</p>
    </div>
</div>
</div>
</body>
</html>