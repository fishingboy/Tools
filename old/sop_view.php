<?php
    function line_format($line)
    {
        if (ereg('^[ ]*[#]{1}', $line))
        {
            // echo ereg_replace('(^[ ]*)([.]+)', "\\1<span class=cmd>\\2</span>", $line);
            $line = str_replace('#', '', $line);
            if ((ereg('^[ ]*[vi]{1}', $line)))
            {
                $line = str_replace('vi', 'vim', $line);
            }
            echo "<span class=cmd>$line</span>";
        }
        else
            echo htmlspecialchars($line, ENT_QUOTES);

    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>SOP 檢視器</title>
<style>
    .cmd {color:#f00; background:url(/sys/res/icon/sharp.png) no-repeat 0px 2px; padding-left:14px;}
</style>
<script src='dom.js'></script>
<script src='/sys/lib/js/jquery.js'></script>
</head>
<body style='align:left'>
<pre>
<?php
    $file = "D:/document/ilms_install.txt";
    if (!is_file($file)) exit;
    
    $fp = fopen($file, "r");
    while ($line = fgets($fp))
    {
        echo line_format($line);
    }
?>
</pre>
</body>
</html>