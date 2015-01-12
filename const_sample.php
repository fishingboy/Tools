<?php
/* 系統 */
define('ROOT_PATH',       'D:/www/www_tools');
define('HTTP_HOST',       'tools');

// 計算網址的根目錄
$tmp = explode("/", $_SERVER['PHP_SELF']);
$base_dir = array();
foreach ($tmp as $dir)
{
    if (strpos($dir, '.') !== FALSE) break;
    $base_dir[] = $dir;
}
$base_dir = implode("/", $base_dir);
define('BASE_DIR', $base_dir);
define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].$base_dir);
