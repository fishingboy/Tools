<?
    include ("lib/common.php");

    /* 操作 */
    $dbName = ($_GET['db']) ? $_GET['db'] : "zuvio";
    $update = ($_GET['update']) ? $_GET['update'] : "zuvio";
    if ($update == 1)
    {
        $schema = get_db_info($dbName);
        // echo "<pre>" . print_r($schema, true). "</pre>";
        $log = update_schema($dbName, $schema);
        header("Location: db_schema.php?db=$dbName");
        exit;
    }

    /* 忽略的資料表 */
    class ignore
    {
        public static $list = array
        (
            "poll_result_[0-9]+",
            "quiz_score_[0-9]+",
            "evaluate_result_[0-9]+",
            "event_result_[0-9]+",
            "settlement_score_[0-9]+",
            "settlement_score_detail_[0-9]+",
            "settlement_score_weight_[0-9]+",
            "user_ep_[0-9]+",
            "vote_signup_result_[0-9]+"
        );

        public static function test($tableName)
        {
            foreach (self::$list as $ignore_rule)
            {
                if (preg_match("/{$ignore_rule}/", $tableName)) return true;
            }
            return false;
        }
    }


    /* 函式區 */
    function get_db_info($dbName)
    {
        $ret = array();
        $tables = mysql_list_tables($dbName);
        while ($row = mysql_fetch_array($tables))
        {
            $tableName = $row[0];
            if (ignore::test($tableName)) continue;

            $ret[$tableName] = array();

            $fields = mysql_db_query($dbName, "SHOW COLUMNS FROM `$tableName`");
            while ($field = mysql_fetch_object($fields))
            {
                $fieldName = $field->Field;
                $ret[$tableName][$fieldName] = (array) $field;
            }
        }

        return $ret;
    }

    function get_schema_info($dbName)
    {
        $ret = array();
        $tables = mysql_list_tables($dbName);
        while ($row = mysql_fetch_array($tables))
        {
            $tableName = $row[0];
            if (ignore::test($tableName)) continue;

            $ret[$tableName] = array();

            $fields = mysql_db_query($dbName, "SHOW COLUMNS FROM `$tableName`");
            while ($field = mysql_fetch_object($fields))
            {
                $fieldName = $field->Field;
                $ret[$tableName][$fieldName] = (array) $field;
            }
        }

        return $ret;
    }

    function update_schema($dbName, $schema)
    {
        mysql_db_query("db_schema", "UPDATE `schema` SET status='0' WHERE db='$dbName'");
        mysql_db_query("db_schema", "UPDATE tbl SET status='0' WHERE db='$dbName'");

        $tbl_object = array();
        $objs = mysql_db_query("db_schema", "SELECT * FROM tbl WHERE db='$dbName' ORDER BY _table ASC");
        while ($obj = mysql_fetch_object($objs))
        {
            $table = $obj->_table;
            $tbl_object[$table] = $obj;
        }

        $field_object = array();
        $objs = mysql_db_query("db_schema", "SELECT * FROM `schema` WHERE db='$dbName' ORDER BY _table ASC, sn ASC");
        while ($obj = mysql_fetch_object($objs))
        {
            $table = $obj->_table;
            $field = $obj->_field;
            $field_object[$table][$field] = $obj;
        }

        foreach ($schema as $table => $fields)
        {
            // echo "$table<br>";
            if (!isset($tbl_object[$table]))
            {
                    $sql = "INSERT INTO tbl SET db='$dbName',
                                               _table='$table',
                                               createTime=now()";
                    mysql_db_query("db_schema", $sql);

                    $log = "New Table: $table";
                    $log_msg .= ($log) ? "<div><a href='#table_{$table}'>$log</a></div>" : "";
            }
            else
            {
                mysql_db_query("db_schema", "UPDATE tbl SET status='1' WHERE db='$dbName' AND _table='$table'");
            }

            $sn = 0;
            foreach ($fields as $field => $attrs)
            {
                // echo "$table, $field<br>";

                $sn++;
                $type    = $attrs['Type'];
                $default = $attrs['Default'];
                $null    = $attrs['Null'];

                $sql = $update_sql = $log = "";
                if (isset($field_object[$table][$field]))
                {
                    $field_obj = $field_object[$table][$field];
                    if ($type != $field_obj->_type)
                    {
                        $update_sql .= ", _type='$type'";
                        $log_detail .= "type($field_obj->_type => $type), ";
                    }
                    if ($null != $field_obj->_null)
                    {
                        $update_sql .= ", _null='$null'";
                        $log_detail .= "null($field_obj->_null => $null), ";
                    }
                    if ($default != $field_obj->_default)
                    {
                        $update_sql .= ", _default='$default'";
                        $log_detail .= "default($field_obj->_default => $default), ";
                    }
                    if ($sn != $field_obj->sn)
                    {
                        $update_sql .= ", sn='$sn'";
                        $log_detail .= "sn($field_obj->sn => $sn), ";
                    }

                    if ($update_sql)
                    {
                        $sql = "UPDATE `schema` SET updateTime=now(),
                                                  status='1'
                                                  $update_sql
                                WHERE db='$dbName'
                                      AND _table='$table'
                                      AND _field='$field'";
                        mysql_db_query("db_schema", $sql);

                        $log = "modify Field: $table.$field : $log";
                        $sql = "INSERT INTO log SET db='$dbName',
                                                    _table='$table',
                                                    _field='$field',
                                                    note='$log',
                                                    createTime=now()";
                        mysql_db_query("db_schema", $sql);
                    }
                    else
                    {
                        $sql = "UPDATE `schema` SET status='1'
                                WHERE db='$dbName'
                                      AND _table='$table'
                                      AND _field='$field'";
                        mysql_db_query("db_schema", $sql);
                    }
                }
                else
                {
                    $sql = "INSERT INTO `schema` SET db='$dbName',
                                                   _table='$table',
                                                   _field='$field',
                                                   _type='$type',
                                                   _null='$null',
                                                   _default='$default',
                                                   createTime=now(),
                                                   updateTime=now(),
                                                   status='1',
                                                   sn='$sn'";
                    mysql_db_query("db_schema", $sql);

                    $log = "New Field: $table.$field";
                    $sql = "INSERT INTO log SET db='$dbName',
                                                _table='$table',
                                                _field='$field',
                                                note='New Field: $table.$field',
                                                createTime=now()";
                    mysql_db_query("db_schema", $sql);
                }
                $log_msg .= ($log) ? "<div><a href='#field_{$table}_{$field}'>$log</a></div>" : "";
            }
        }
        return $log_msg;
    }

    function view_schema($dbName)
    {
        // $objs = mysql_db_query("db_schema", "SELECT DISTINCT _table FROM schema WHERE db='$dbName'");
        $objs = mysql_db_query("db_schema", "SELECT * FROM tbl WHERE db='$dbName' AND status='1' ORDER BY _table");
        echo "<div style='border:1px solid #ccc; margin:10px 0px;'>
                <div style='padding:5px; cursor:pointer;' onclick='change_display_tableList()'>資料表:</div>
                <div id='tableList' style='display:block'>";
        $table_finish_count = $table_count = 0;
        while ($obj = mysql_fetch_object($objs))
        {
            if ($obj->note) $table_finish_count++;
            $table_count++;
            echo "<div class=table_link><a href='#table_{$obj->_table}'>$obj->_table</a></div>";
        }
        echo "<div style='clear:both'></div>";
        echo "</div>";
        echo "</div>";
        mysql_data_seek($objs, 0);

        $field_finish_count = $field_count = 0;
        while ($obj = mysql_fetch_object($objs))
        {
            $table = $obj->_table;
            $note = ($obj->note) ? $obj->note : "<span class=hint>請輸入說明...</span>";

            echo "<div id=table_{$table} style='margin-bottom:20px;'>";
            echo "  <div style='font-size:14px; font-weight:bold;'>
                        $table :
                        <span id=table_note_{$obj->id} class=table_note onclick='table_edit($obj->id)'>$note</span>
                        <img id=table_display_status_{$table} border=0 style='cursor:pointer;' src='/sys/res/icon/ctree_hide.gif' onclick='change_display_table(\"$table\")'>
                    </div>";
            echo "  <div id=table_detail_{$table}>";

            $tobjs = mysql_db_query("db_schema", "SELECT * FROM `schema` WHERE db='$dbName' AND _table='$table' AND status='1' ORDER BY _table ASC, sn ASC");
            echo "<table width=100% class=print>
                    <tr><th width=150>欄位</th>
                        <th width=150>型態</th>
                        <th width=50>Null</th>
                        <th width=180>預設值</th>
                        <th>註解</th>
                    </tr>";
            $sn = 0;
            while ($tobj = mysql_fetch_object($tobjs))
            {
                if ($tobj->note) $field_finish_count++;
                $field_count++;

                $sn++;
                $tr_class = ($sn % 2) ? "odd" : "even";
                $note = htmlspecialchars($tobj->note, ENT_QUOTES);
                $note = str_replace(" ", "&nbsp;", $note);
                $note = nl2br($note);
                $note = str_replace("\n", "", $note);
                echo "<tr id=field_{$table}_{$tobj->_field} class=$tr_class>
                        <td>$tobj->_field</td>
                        <td>$tobj->_type</td>
                        <td align=center>$tobj->_null</td>
                        <td align=center>$tobj->_default</td>
                        <td onclick='note_edit($tobj->id)'><div id=note_{$tobj->id} class=note>$note</div></td>
                      </tr>";
            }
            echo "</table>";
            echo "  </div>";
            echo "</div>";
        }
        $table_precent = sprintf("%.2f", $table_finish_count / $table_count * 100);
        $field_precent = sprintf("%.2f", $field_finish_count / $field_count * 100);
        echo "完成度: <span style='font-weight:bold'>$table_precent%($table_finish_count/$table_count), $field_precent%($field_finish_count/$field_count)</span>";
    }

    function create_db_select_html($dbName, $inputName='fmDatabase')
    {
        $db_list = mysql_list_dbs();
        $i = 0; $db_opt = $db_opt2 = "";
        $cnt = mysql_num_rows($db_list);
        while ($i < $cnt)
        {
            $name = mysql_db_name($db_list, $i);
            $selected = ($name == $dbName) ? "selected" : "";
            $db_opt .=  "<option value='$name' $selected>$name</option>\n";
            $i++;
        }
        return "<select id=$inputName name=$inputName>$db_opt</select>";
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>資料字典(db schema)</title>
<style>
body       { font-size:12px; color:#000; }
a          {color:#44f; text-decoration:none;}
a:link     {color:#44f; text-decoration:none;}
a:visited  {color:#44f; text-decoration:none;}
a:hover    {text-decoration:underline; color:#f00;}

.pageTitle { color: #009900; font-size:16px; font-weight:bold; text-align:center}
table caption, table th, table td
{
    margin: 1px;
    padding: 2px 1px;
    vertical-align: top;
}
th
{
    background: none repeat scroll 0 0 #D3DCE3;
    color: #000000;
    font-weight: bold;
}
table tr.odd th, .odd   { background: none repeat scroll 0 0 #E5E5E5; }
table tr.even th, .even { background: none repeat scroll 0 0 #D5D5D5; }
.note {	font-family:"Courier New"; cursor:pointer; width:100%}
.table_link {float:left; margin:5px 10px; width:200px; }
.table_note {color:#333;}
input {font-size:12px}
.hint {color:#aaa};
</style>
<script src='/sys/lib/js/dom.js'></script>
<script src='/sys/lib/js/jquery.js'></script>
<script>
    $j(function ()
    {
        $j('#fmDatabase').bind('change', change_database);
    });
    function schemaReload()
    {
        var db = $j('#fmDatabase').val();
        window.location.href = "/db_schema.php?update=1&db=" + db;
    }
    function change_database()
    {
        var db = $j('#fmDatabase').val();
        window.location.href = "/db_schema.php?db=" + db;
    }
    function change_display_table(table)
    {
        var ctrl = $j('#table_detail_' + table);
        var iconExpand = "/sys/res/icon/ctree_expand.gif";
        var iconHide   = "/sys/res/icon/ctree_hide.gif";

        if (ctrl.css('display') == 'block')
        {
            // ctrl.css('display', 'none');
            ctrl.fadeOut("fast");
            $j('#table_display_status_' + table).attr('src', iconExpand);
        }
        else
        {
            // ctrl.css('display', 'block');
            ctrl.fadeIn("fast");
            $j('#table_display_status_' + table).attr('src', iconHide);
        }
    }

    function change_display_tableList()
    {
        var ctrl = $j('#tableList');
        if (ctrl.css('display') == 'block')
        {
            ctrl.fadeOut("fast");
        }
        else
        {
            ctrl.fadeIn("fast");
        }
    }

    var note = [];
    var note_status = [];
    function note_edit(id)
    {
        if (!note_status[id])
        {
            note_status[id] = true;
            var ctrl = $j('#note_' + id);
            note[id] = $j('#note_' + id).html();
            var html  = "<textarea id=fmNote_" + id + " style='width:100%'  onkeypress='fieldGetKey(event, this)' placeholder='[Ctrl + Enter] 儲存, [ESC] 取消'>" + $br2nl(note[id]) + "</textarea>";
                html += "<input type=button id=fmBtnOK value='儲存' onclick='note_save(" + id + ")'> ";
                html += "<input type=button id=fmBtnOK value='取消' onclick='note_cancel(" + id + "); event.stopPropagation();'> ";
            ctrl.html(html);
            $("fmNote_" + id).focus();
        }
    }
    function note_save(id)
    {
        var val = $j('#fmNote_' + id).val();
        $j.post('/module/schema/http_field_insert.php', {id:id, note:val}, function (data)
        {
            var ctrl = $j('#note_' + id);
            ctrl.html($nl2br(val).replace(/\s/g, '&nbsp;'));
            note_status[id] = false;
        });
    }
    function note_cancel(id)
    {
        var ctrl = $j('#note_' + id);
        ctrl.html(note[id]);
        note_status[id] = false;
    }

    function fieldGetKey(e, ctrl)
    {
        var e = (!e) ? window.event : e;
    	var key = e.keyCode;
        var id = ctrl.id.split("_")[1];
    	switch(key)
		{
			// ctrl + enter
			case 13:
                if (e.ctrlKey)
                {
					note_save(id);
                    e.returnValue = false;
                    e.cancelBubble = true;
				    return true;
                }
                break;
            // ESC
			case 27:
                note_cancel(id);
                e.returnValue = false;
                e.cancelBubble = true;
                return true;
                break;
			default:
				return true;
		}
    }

    var table = [];
    var table_status = [];
    function table_edit(id)
    {
        if (!table_status[id])
        {
            table_status[id] = true;
            var ctrl = $j('#table_note_' + id);
            table[id] = $j('#table_note_' + id).html();
            var html  = "<input type=text id=fmTableNote_" + id + " style='width:300px'  onkeypress='tableGetKey(event, this)' value='" + table[id] + "' placeholder='[Enter] 儲存, [ESC] 取消'>";
                html += "<input type=button id=fmBtnOK value='儲存' onclick='table_save(" + id + ")'>";
                html += "<input type=button id=fmBtnOK value='取消' onclick='table_cancel(" + id + "); event.stopPropagation();'> ";
            ctrl.html(html);

            $("fmTableNote_" + id).focus();
            if (table[id].indexOf('span>') > 0)
            {
                $("fmTableNote_" + id).value = "";
            }
            else
            {
                $("fmTableNote_" + id).select();
            }
        }
    }

    function table_save(id)
    {
        var val = $j('#fmTableNote_' + id).val();
        $j.post('/module/schema/http_table_insert.php', {id:id, note:val}, function (data)
        {
            var ctrl = $j('#table_note_' + id);
            if (val == "") val = "<span class=hint>請輸入說明...</span>";
            ctrl.html($nl2br(val).replace(/\s/g, '&nbsp;'));
            table_status[id] = false;
        });
    }
    function table_cancel(id)
    {
        var ctrl = $j('#table_note_' + id);
        ctrl.html(table[id]);
        table_status[id] = false;
    }

    function tableGetKey(e, ctrl)
    {
        var e = (!e) ? window.event : e;
    	var key = e.keyCode;
        var id = ctrl.id.split("_")[1];
    	switch(key)
		{
			// enter
			case 13:
                table_save(id);
                e.returnValue = false;
                e.cancelBubble = true;
                return true;
                break;
            // ESC
			case 27:
                table_cancel(id);
                e.returnValue = false;
                e.cancelBubble = true;
                return true;
                break;
			default:
				return true;
		}
    }
    function extendAll()
    {
        $j('[id^=table_detail_]').each(function(i)
        {
            var ctrl = $j(this);
            var table = this.id.split("_")[2];
            var iconExpand = "/sys/res/icon/ctree_expand.gif";
            var iconHide   = "/sys/res/icon/ctree_hide.gif";
            ctrl.css('display', 'block');
            $j('#table_display_status_' + table).attr('src', iconHide);
        });
    }
    function hideAll()
    {
        $j('[id^=table_detail_]').each(function(i)
        {
            var ctrl = $j(this);
            var table = this.id.split("_")[2];
            var iconExpand = "/sys/res/icon/ctree_expand.gif";
            var iconHide   = "/sys/res/icon/ctree_hide.gif";
            ctrl.css('display', 'none');
            $j('#table_display_status_' + table).attr('src', iconExpand);
        });
    }
</script>
</head>
<body>
<?
    echo "<div class=pageTitle>==  資料字典  ==</div>";
    echo "資料庫： " . create_db_select_html($dbName);
    echo " &nbsp;
           <a href='javascript:schemaReload()' style='color:#f00'>資料庫異動檢查</a> |
           <a href='javascript:extendAll()'>全部展開</a> |
           <a href='javascript:hideAll()'>全部收攏</a> |
           <a href='/module/schema/xls.php' target=_blank>匯出 excel</a> |
           <a href='/module/schema/xls_opt.php' target=_blank>部份匯出 excel</a>
           ";
    if ($log) echo "<div style='border:1px solid #ccc; margin:5px 0px; padding:5px;'>異動:<br>$log</div>";
    echo view_schema($dbName);
?>
</body>
</html>