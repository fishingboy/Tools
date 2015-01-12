<?php
ini_set('display_errors', 'ON');
error_reporting(E_ALL & ~E_NOTICE);

include_once("phpunit/phpunit_tester.php");

// 1. 兩種類型：檔案 / 群組
// 2. 都是用複選方式呈現
// 3. 用 AJAX 呼叫，並顯示進度 BAR
?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<html>
<head>
<title>PHP 單元測試工具</title>
<style>
#subject 
{
    border:1px solid #ccc; 
    padding:5px; 
    margin: 5px 0px;
    border-radius: 5px; 
    background: #Efe; 
    text-align: center; 
    font-weight: bold;
}
</style>
</head>
<body>
<div id='subject'>PHP 單元測試工具</div>
<!-- <form action='phpunit_tester.php' method='post'> -->
<!-- </form> -->
<?php
    $fd = opendir(TEST_CASE_PATH);
    while (($f = readdir($fd)) !== false) 
    {
        $file = TEST_CASE_PATH . '\\' . $f;
        if (is_file($file) && strpos($file, 'Test.php'))
        {
            $result = phpunit_tester::exec($file, $output, $ret);
            phpunit_tester::result_render($file, $result);
        }
    }
    closedir($fd);

?>
</body>
</html>