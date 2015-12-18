<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$file = "clipboard.txt";

if ( ! file_exists($file))
{
    touch($file);
}


if (isset($_POST['fm_submit']))
{
    $content = $_POST['fm_content'];
    $fp = fopen($file, "w");
    fwrite($fp, $content);
    fclose($fp);
}


$content = file_get_contents($file);
?>
<form method='post'>
<textarea name='fm_content' style='width:100%; height:400px;'><?= $content ?></textarea>
<input name='fm_submit' type='submit' value='更新'>
</fomr>
