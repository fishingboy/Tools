<?
include ("../../lib/common.php");

$dbName = "lms";
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
.hint {color:#aaa}
.tableChecked {border:1px solid #f00; background:#fee}
</style>
<script src='/sys/lib/js/dom.js'></script>
<script src='/sys/lib/js/jquery.js'></script>
<script>
    function tableChecked(table)
    {
        $j('#check_' + table).toggleClass('tableChecked','');
    }
</script>
</head>
<body>
<div style='font-weight:bold'>請選擇你要匯出的資料表</div>
<form action="/module/schema/xls.php" method=post>
<input type=hidden name=dbName value='$dbName'>
<?
$objs = mysql_db_query("db_schema", "SELECT * FROM tbl WHERE db='$dbName' ORDER BY _table");
$i = 0;
while ($obj = mysql_fetch_object($objs))
{
    $i++;
    $table = $obj->_table;
    $note = $obj->note;    
    echo "<div id='check_{$table}' style='margin:2px;'>
              <input type=checkbox id=table_{$table} name=table[] value='$table' onclick='tableChecked(this.value)'><label for=table_{$table}>$i. $table</label>
          </div>";
}
?>
<hr>
<input type=submit value='確定'>
<input type=button value='取消' onclick='self.close()'>
<input type=checkbox name=fmNotNull value='1'>忽略未填寫欄位
</form>
</body>
</html>