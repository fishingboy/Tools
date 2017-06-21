<?php
ob_flush();

function ob_msg($msg)
{
    echo $msg;
    print str_repeat(" ", 4096);
    flush();
}
?>