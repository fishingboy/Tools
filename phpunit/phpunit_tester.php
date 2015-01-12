<?php
include_once("phpunit/phpunit_conf.php");

class phpunit_tester
{
    /**
     * 執行 phpunit 命令
     */
    public static function exec($file, &$output="", &$ret="")
    {
        $cmd = PHPUNIT_PATH . ' ' . $file;

        unset($output);
        exec($cmd, $output, $ret);

        return self::parse_cmd_output($output);
    }

    /**
     * 分析 phpunit 的結果
     */
    public static function parse_cmd_output($output)
    {
        $status = 0;
        $msg = 'FAIL!';
        $length = count($output);
        for ($i=0; $i < $length; $i++) 
        { 
            if (strpos($output[$i], "OK") !== FALSE)
            {
                $status = 1;
                $msg    = 'OK!';
            }
        }

        return array
        (
            'status' => $status,
            'msg'    => $msg,
            'detail' => implode("\n", $output)
        );
    }

    public static function result_render($file, $result)
    {
        $style = ($result['status'] == 1) ? "font-weight:bold;" : "color:#F00; font-weight:bold;";

        $html = "<div>$file ..............<span style='{$style}'>{$result['msg']}</span></div> ";
        
        if ($result['status'] == 0)
        {        
            $html .= "<div style='border:1px solid #ccc; margin-left:20px; padding:10px; font-size:12px; '>" .  nl2br($result['detail']) . "</div>";
        }

        echo $html;
    }
}    
