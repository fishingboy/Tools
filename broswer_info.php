<?php
    // 設定要顯示的資訊
    $display_array = array
    (
        'REMOTE_ADDR',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_USER_AGENT',
        'HTTP_HOST',
        'HTTP_ACCEPT',
        'HTTP_ACCEPT_LANGUAGE',
        'HTTP_ACCEPT_ENCODING',
        'HTTP_REFERER',
        'HTTP_X_INSIGHT',
        'HTTP_CONNECTION',
        'HTTP_CACHE_CONTROL',
    );
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>取得瀏灠器資訊</title>
<style type="text/css">
th, td {text-align: left; padding: 5px;}
th     {font-weight: normal; background: #EEF;}
</style>
</head>
<body>
<div style='border:1px solid #ccc;'>
<table style='width:100%'>
 <?php
    foreach ($display_array as $key) 
    {
        $value = ($_SERVER[$key]) ? $_SERVER[$key] : "無";
        echo <<<HTML
        <tr><th>{$key}</th></tr>
        <tr><td>{$value}</td></tr>
HTML;
    }
?>
</table>
</div>
</body>
</html>