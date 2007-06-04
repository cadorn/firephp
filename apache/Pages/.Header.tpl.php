<html>
<head>
  <title>FirePHP - Test Site</title>
  <link rel="stylesheet" href="/Style.css"></link>
  <script src="/prototype.js"></script>
  <style>
    HTML, BODY {
      padding-right: 10px;
      background-color: #FFFFFF;
    }
  </style>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <base href="<?php print substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'/',1)+1); ?>"/>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
