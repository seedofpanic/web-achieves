<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="achievesApp">
<head>
	<meta charset="utf-8">
	<title>Achievments - Administration</title>
    <link rel="stylesheet" type="text/css" href="/css/semantic.min.css">
    <link rel="stylesheet" type="text/css" href="/css/ng-ckeditor.css">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular.min.js"></script>
    <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/js/ng-ckeditor.min.js"></script>
    <script type="text/javascript" src="/js/app/app<?=($enviroment == 'production') ? '.min' : ''?>.js"></script>
    <script type="text/javascript" src="/js/semantic.min.js"></script>
    <!-- BEGIN JIVOSITE CODE {literal} -->
    <script type='text/javascript'>
        (function(){ var widget_id = 'az0ymhSuVz';
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
    <!-- {/literal} END JIVOSITE CODE -->
</head>
<body>
    <div class="login-page" ng-hide="user.id>0"></div>
    <div class="account-page" ng-show="user.id>0"></div>
</div>

</body>
</html>