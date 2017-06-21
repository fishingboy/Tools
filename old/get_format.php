<?php
    // function
    $type_list = array("'int'", 
                       "'float'", 
                       "'string'", 
                       "'string_no_trim'", 
                       "'account'", 
                       "'white_list'", 
                       "'time'", 
                       "'editor'", 
                       "'editor_adm'", 
                       "'editor_user'", 
                       "'int_list'", 
                       "'int_list2'", 
                       "'precedence'", 
                       "'nothing'");
    
    function quote_match($str)
    {
        if (substr_count($str, "'") % 2 != 0) return "'";
        if (substr_count($str, '"') % 2 != 0) return '"';
        if (substr_count($str, '(') != substr_count($str, ')')) return '()';
        return "";        
    }
    
    function line_format($str)
    {
        global $type_list;
        
        if (strpos($str, "XMS") !== false)
        {
            return "<span style='color:green'>" . $str . "</span>";
        }
        
        $arr1 = explode(":", $str);
        $arr2 = explode("=", $arr1[1]);
        $var       = trim($arr2[0]);
        $func_call = trim($arr2[1]);
        if (!ereg("^[$]{1}[a-zA-Z_]+[0-9_]*$", $var)) 
        {
            return $str . "......<span style='color:red; font-size:14px'>var is error format!</span>";
        }
        if (strpos($func_call, 'getParam') !== 0)
        {
            return $str . "......<span style='color:red; font-size:14px'>special call</span>";
        }

        // ¿À¨d ; ∏π
        if (substr(trim($str), -1) != ";")
        {
            return $str . "......<span style='color:red; font-size:14px'>parse error</span>";
        }
        
        $quote_not_match = quote_match($str);
        if ($quote_not_match)
        {
            return $str . "......<span style='color:red; font-size:14px'>$quote_not_match not match</span>";
        }
        $func_call = substr($func_call, 9);
        $func_call = substr($func_call, 0, strpos($func_call, ')'));
        $args = explode(",", $func_call);
        
        $args[0] = str_replace ("'", "", $args[0]);
        $keyname     = trim($args[0]);
        $filter_type = trim($args[1]);

        // check key = key
        $var = str_replace ("$", "", $var);
        if ($var != $keyname && strtoupper("fm$var") != strtoupper($keyname))
        {
            return $str . "......<span style='color:red; font-size:14px'>key error</span>";
        }        
        
        // check type
        if (!in_array($filter_type, $type_list))
        {
            return $str . "......<span style='color:red; font-size:14px'>type error</span>";
        }

        // return $str. " <span style='color:#999'>.........OK!</span>";
    }

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>==  getParam format check...  ==</title>
<script src='dom.js'></script>
<script src='/sys/lib/js/jquery.js'></script>
</head>
<body style='align:left'>
<pre>
</pre>
<div style='border:1px solid #ccc; padding:10px; height:600px; width=100%; overflow-y:auto'>
<?php
    $file = "get_param.txt";
    if (!is_file($file)) exit;
    
    $fp = fopen($file, "r");
    $cache_arr = array();
    while ($line = fgets($fp))
    {
        $line = line_format($line);
        if ($line) echo "$line <br>";
    }
?>
</div>
</body>
</html>