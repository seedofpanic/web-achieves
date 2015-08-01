<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="achievesApp">
<head>
	<meta charset="utf-8">
	<title>Achievments - Administration</title>
    <link rel="stylesheet" type="text/css" href="/css/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular.min.js"></script>
    <script type="text/javascript" src="/js/app/app.js"></script>
    <script type="text/javascript" src="/js/semantic.min.js"></script>
</head>
<body>
<div class="<?if (!$logged_in) {
    echo 'login-page';
 }else{
    echo 'account-page';
 }?>"></div>


</body>
</html>