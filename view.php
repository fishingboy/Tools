<html>
<head>
<script type="text/javascript" src="http://alexgorbatchev.com/pub/sh/2.0.296/scripts/shCore.js"></script>
<script type="text/javascript" src="http://alexgorbatchev.com/pub/sh/2.0.296/scripts/shBrushPhp.js"></script>
<script type="text/javascript">
    SyntaxHighlighter.config.clipboardSwf = 'http://alexgorbatchev.com/pub/sh/2.0.296/scripts/clipboard.swf';
    SyntaxHighlighter.all();
</script> 
<link type="text/css" rel="stylesheet" href="http://alexgorbatchev.com/pub/sh/2.0.296/styles/shCore.css"/>
<link type="text/css" rel="stylesheet" href="http://alexgorbatchev.com/pub/sh/2.0.296/styles/shThemeDefault.css" id="shTheme"/>
</head>
<body>
<input type=button value='OPEN IN Notepad++'>
<?
$filename = $_GET['filename'];
if(!$filename) exit;

$content = file_get_contents($filename);
echo "<pre class='brush:php'>" . htmlspecialchars($content, ENT_QUOTES) . "</pre>";
?>
</body>
</html>