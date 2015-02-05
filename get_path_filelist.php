<?php
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    $path = ($_POST['fm_path']) ? $_POST['fm_path'] : "";
    $path = str_replace("\\", '/', $path);
    if ($path)
    {
        $result = get_path_filelist($path);
        $result = implode("\n", $result);
    }

    function get_path_filelist($path)
    {
        $result = array();
        if (is_dir($path))
        {
            $dir = opendir($path);
            while (FALSE !== ($item = readdir($dir)))
            {
                if ($item == '.' || $item == '..') continue;
                $result = array_merge($result, get_path_filelist("$path/$item"));
            }
            return $result;
        }
        else if (file_exists($path))
        {
            return [$path];
        }
    }
?>
<html>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<head>
<title>取得目錄下的所有檔案清單</title>
</head>
<body>
<pre>
==  取得目錄下的所有檔案清單  ==
</pre>
<form method='post'>
    目錄:
    <input type=text id='fm_path' name='fm_path' value="<?= $path ?>" style='width:80%'>
    <input type=submit value='送出'>
</form>
<textarea id='fm_list' name='fm_list' style='width:100%; height:600px'><?= $result ?></textarea>
</body>
</html>