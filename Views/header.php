<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet/less" href="/Resource/style.less">
	<script src="/Resource/less.js"></script>
</head>
<body>
	<header>
		<div class="wrapper">
			<img src="/Resource/gfx/logo.png">
			<span>Twitter Clone</span>
			<?php if($User !== false){ ?>
                <nav>
                    <a href="/buddies">Your Buddies</a>
                    <a href="/public">Public Ribbits</a>
                    <a href="/profiles">Profiles</a>
                </nav>
                <form action="/logout" method="get">
                    <input type="submit" id="btnLogOut" value="Log Out">
                </form>
            <?php }else{ ?>
                <form method="post" action="/login">
                    <input name="username" type="text" placeholder="username">
                    <input name="password" type="password" placeholder="password">
                    <input type="submit" id="btnLogIn" value="Log In">
                </form>
            <?php } ?>
		</div>
	</header>
    <div id="content">
		<div class="wrapper">
