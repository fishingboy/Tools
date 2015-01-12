<?php
function fb_log($msg)
{
    static $init;
    static $firephp;
    
    if (!$init)
    {
        ob_start();
        $SELF_PATH = dirname(__FILE__);
        include_once ("$SELF_PATH/../FirePHPCore/fb.php");
        $firephp = FirePHP::getInstance(true);
        $init = true;
    }

    $firephp->log($msg);
}