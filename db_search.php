<?
    include ("lib/common.php");
    
    $dbName = ($dbName) ? $dbName : "lms";
    $DB = $dbName;
    
    if ($insertSql) $checkInsert = "checked";
    if ($key == "")    $allField = "1";
    if ($checkVal)
    {
        $checked = "checked";
    }
    else
    {
        $disabled = "disabled";
    }
    if ($allMatch)  $checkedMatch = "checked";
    if ($onlyCheckStr) $checkedStr    = "checked";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>資料庫搜尋</title>
<style>
    body {font-size:16px; color:#666}
    .tbl   {color: red}
    .field {color: blue}
    .hint  {color: green}    
</style>
<script>
    function changeDisabled(v)
    {
        document.getElementById("V").disabled = v;
    }
</script>
</head>

<body>
    <form action='<?= $PHP_SELF ?>'>
        資料庫名稱<input type=text name=dbName value='<?= $dbName ?>'>
        欄位名稱<input type=text name=key value='<?= $key ?>' style='width:300px;'> <span class=hint>(,號隔開篩選多個欄位)</span><br>
        <input type=checkbox name=insertSql <?= $checkInsert ?> onclick='changeDisabled(!this.checked)'>顯示新增語法
        
        <input type=checkbox name=checkVal <?= $checked ?> onclick='changeDisabled(!this.checked)'>篩選欄位值
        <input type=checkbox name=allMatch <?= $checkedMatch ?>>完整比對
        <input type=checkbox name=onlyCheckStr <?= $checkedStr ?>>只對字串進行比對
        <input type=text id=V name=V value='<?= $V ?>' <?= $disabled ?>>
        <input type=submit value='搜尋'>
    </form>
    <div style='border:1px solid #ccc'></div>
<?
    /* 
            搜尋資料庫內所有資料表、資料欄位
    */
          
    $key = explode(",", $key);          
    for($j=0; $j<count($key); $j++) $key[$j] = trim($key[$j]);
    
    //search table        
    $result = mysql_list_tables($dbName);
    
    $totalCount = $tblCount = $fieldCount = $VCount = 0;
    $tblResult  = $fieldResult = $VResult = "";
    while ($row = mysql_fetch_array($result))
    {
        $tblName = $row[0];
        if (!$allField)
        {
            for($j=0; $j<count($key); $j++) if (stristr($tblName, $key[$j])) break;
        }
        
        if ($j < count($key) || $allField)
        {
            $tblCount++; $totalCount++;
            $tblResult .= "$tblCount : <span class=tbl>$tblName</span> <br>";
        }

        //search field    
        $fields = mysql_list_fields($dbName, $tblName);
        $i = 0; $insertTag = "";
        while($field = @mysql_field_name($fields, $i++))
        {
            if (!$allField)
            {
                for($j=0; $j<count($key); $j++)
                {
                    // if (stristr($field, $key[$j])) break;
                    if ($field == $key[$j]) break;
                }
            }
            
            if ($field != "id")
            {
                $insertTag .= ($insertTag) ? ", $field=''" : "$field=''";
            }
            
            $tmpResult = ""; $find = 0;
            if ($j<count($key) || $allField)
            {
                $totalCount++;
                $field_type = @mysql_field_type($fields, $i-1);
                $field_type = ($field_type) ? "($field_type)" : "";
                $tmpResult .= "<span class=tbl>$tblName</span>.<span class=field>$field</span> $field_type<br>";
                
                //search value
                if ($checkVal)
                {
                    $type = mysql_field_type($fields, $i-1);
                    if ($onlyCheckStr && $type != "string") continue;
                    if (!$allMatch && $type == "string")
                    {
                        $sql = "SELECT $field FROM $tblName WHERE $field like '%$V%' LIMIT 1";
                    }
                    else
                    {
                        $sql = "SELECT $field FROM $tblName WHERE $field='$V' LIMIT 1";
                    }

                    $objs = db_query($sql);
                    $f = 0;
                    while ($obj = mysql_fetch_array($objs))
                    {
                        $f = 1;
                        $val =  htmlentities($obj[$field]);
                        if (!$allMatch)
                            $tmpResult .= "<span style='padding-left:100px' class=tbl>$tblName</span>.<span class=field>$field</span> = '$val' <br>";
                        $find = 1;
                        break;
                    }
                }
            }
            if ($tmpResult && (!$checkVal || $find == 1))
            {
                $fieldCount++;                
                $fieldResult .= "$fieldCount. $tmpResult";
            }
        }
        if ($insertSql) $fieldResult .= "INSERT INTO $tblName SET $insertTag;<br>";
        // $fields = "";
        //if ($f) $fieldResult .= "<br>";
    }
    
    echo "
            資料表：<br>
            $tblResult
            <span class=hint>共 $tblCount 筆</span><br><br>
            
            資料欄位：<br>
            $fieldResult
            <span class=hint>共 $fieldCount 筆<span><br><br> 
            
            <span class=hint>一共找到 $totalCount 筆資料</span><br>
    ";
?>
</body>
</html>