<?php
/**
 * 因為 register_globals 已在 php 5.4 版移除，所以先寫個簡單的轉換機制
 */
$global_values = [];
if (isset($_COOKIE)) $global_values[] = $_COOKIE;
if (isset($_SESSION)) $global_values[] = $_SESSION;
if (isset($_GET)) $global_values[] = $_GET;
if (isset($_POST)) $global_values[] = $_POST;

// $global_values = array(, $_SESSION, $_GET, $_POST);

foreach ($global_values as $values)
{
    foreach ($values as $key => $value)
    {
        $$key = $value;
    }
}
