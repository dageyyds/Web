<?php 
require './WeChat.class.php';
define('APPID', '');
define('APPSECRET', '');
$wechat=new WeChat(APPID,APPSECRET);
$access_token=$wechat->getAccessToken();
var_dump($access_token);