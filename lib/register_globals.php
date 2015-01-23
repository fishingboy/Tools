<?php
/**
 * 因為 register_globals 已在 php 5.4 版移除，所以先寫個簡單的轉換機制
 */
$global_values = array($_COOKIE, $_SESSION, $_GET, $_POST);
foreach ($global_values as $$values)
{
    foreach ($values as $key => $value)
    {
        $$key = $value;
    }
}
