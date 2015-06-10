<?php
    // 防呆處理
    if ( ! file_exists('const.php'))
    {
        // TODO:跳到設定介面
        echo "TODO:跳到設定介面";
        exit;
    }

    include_once('const.php');

    $program = ($_GET['program']) ? $_GET['program'] : 'text_editor';
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>工具箱</title>
    <link rel="icon" type="image/ico" href="site.ico"></link>
    <link rel="shortcut icon" href="site.ico"></link>
</head>
<frameset cols="15%,85%">
    <frame name=menu src='<?= BASE_DIR ?>/index_menu.php' noresize frameborder=0></frame>
    <frame name=main src='<?= BASE_DIR ?>/<?= $program ?>.php' noresize frameborder=0></frame>
</frameset>
</html>