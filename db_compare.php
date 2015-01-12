<?
    include ("lib/common.php");
    $dbName1 = ($dbName1) ? $dbName1 : "lms";
    $dbName2 = ($dbName2) ? $dbName2 : "lms2";

    $db_list = mysql_list_dbs();
    $i = 0; $db_opt = $db_opt2 = "";
    $cnt = mysql_num_rows($db_list);
    while ($i < $cnt) 
    {
        $db_name = mysql_db_name($db_list, $i);
        $selected = ($db_name == $dbName1) ? "selected" : "";
        $db_opt .=  "<option value='$db_name' $selected>$db_name</option>\n";
        $selected = ($db_name == $dbName2) ? "selected" : "";
        $db_opt2 .=  "<option value='$db_name' $selected>$db_name</option>\n";
        $i++;
    }
    
    function get_db_info($dbName)
    {
        $ret = array();
        $tables = mysql_list_tables($dbName);
        while ($row = mysql_fetch_array($tables))
        {
            $tableName = $row[0];
            if (ereg("poll_result_[0-9]+", $tableName) ||
                ereg("quiz_score_[0-9]+", $tableName)  || 
                ereg("evaluate_result_[0-9]+", $tableName) ||
                ereg("event_result_[0-9]+", $tableName) || 
                ereg("vote_signup_result_[0-9]+", $tableName) 
                ) 
                    continue;
            
            $ret[$tableName] = array();

            $fields = mysql_list_fields($dbName, $tableName);
            while ($field = mysql_fetch_field($fields))
            {   
                $fieldName = $field->name;
                $ret[$tableName][$fieldName] = (array) $field;
            }
        }

        return $ret;
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>資料庫比對(bata)</title>
<style>
    body {font-size:16px; color:#666}
    .tbl   {color: red}
    .field {color: blue}
    .hint  {color: green}
</style>
<script src='/sys/lib/js/jquery.js'></script>
</head>
<body>
    <form action='<?= $PHP_SELF ?>'>
        資料庫1: <select name=dbName1><?= $db_opt ?></select>
        資料庫2: <select name=dbName2><?= $db_opt2 ?></select>
        <input type=hidden name=fmSubmit value='yes'>
        <input type=submit value='比對'>
    </form>
    <?
    if ($fmSubmit)
    {
        echo "<div style='padding:5px; border:1px solid #ccc'>";
        $arr1  = get_db_info($dbName1);
        $arr2 = get_db_info($dbName2);

        $left_only_tables = $right_only_fields = array();
        foreach ($arr1 as $table => $fields)
        {
            if (!isSet($arr2[$table]))
            {
                $left_only_tables[] = $table;
            }
            else
            {
                foreach ($fields as $field => $attr)
                {
                    if (!isSet($arr2[$table][$field]))
                        $left_only_fields[] = "$table.$field";
                }
            }
        }

        $right_only_tables = $right_only_fields = array();
        foreach ($arr2 as $table => $fields)
        {
            if (!isSet($arr1[$table]))
            {
                $right_only_tables[] = $table;
            }
            else
            {
                foreach ($fields as $field => $attr)
                {
                    if (!isSet($arr1[$table][$field]))
                        $right_only_fields[] = "$table.$field";
                }
            }
        }
        
        echo "<b>左邊獨有表格:</b><br>";
        for($i=0; $i<count($left_only_tables); $i++)
            echo ($i+1) . ". {$left_only_tables[$i]} <br>";
        echo "<p><b>左邊獨有欄位:</b><br>";
        for($i=0; $i<count($left_only_fields); $i++)
            echo ($i+1) . ". {$left_only_fields[$i]} <br>";
        echo "<p><b>右邊獨有表格:</b><br>";
        for($i=0; $i<count($right_only_tables); $i++)
            echo ($i+1) . ". {$right_only_tables[$i]} <br>";
        echo "<p><b>右邊獨有欄位:</b><br>";
        for($i=0; $i<count($right_only_fields); $i++)
            echo ($i+1) . ". {$right_only_fields[$i]} <br>";
            
        echo "</div>";
    }
    ?>
</body>
</html>