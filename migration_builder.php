<?php
include_once "const.php";

/**
 * Migration Builder
 * @author Leo <leo@zuvio.com>
 */
class Migration_builder
{
    /**
     * 資料表預設結構
     * @var array
     */
    private $TABLE_STRUCT = [
        'pk'      => '',
    ];

    /**
     * 資料欄位預設結構
     * @var array
     */
    private $FIELD_STRUCT = [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'null'       => TRUE,
    ];

    /**
     * 從 xmind 輸入的原始格式
     * @var string
     */
    private $_text;

    /**
     * 解析後的資料格式
     * @var array
     */
    private $_schema = [];

    /**
     * 建構子
     * @param string $text 從 xmind 來的原始 schema
     */
    public function __construct($text)
    {
        $this->_text =  $text;
        $this->_type =  $type;
        $this->_parse();
    }

    /**
     * 解析原始格式資料
     */
    private function _parse()
    {
        $lines = explode("\n", $this->_text);
        $curr_table = '';
        foreach ($lines as $line)
        {
            preg_match('/^[ ]+/', $line, $matches);
            $space = (isset($matches[0])) ? $matches[0]: '';
            $length = strlen($space);
            switch ($length)
            {
                // 根節點(不做事)
                case 0:
                    break;

                // 資料表
                case 4:
                    // 拆解字串
                    $tmp = explode('-', $line);
                    $table_name    = trim($tmp[0]);
                    $table_comment = isset($tmp[1]) ? trim($tmp[1]) : '';

                    // 建立格式
                    $this->_schema[$table_name] = $this->TABLE_STRUCT;
                    $this->_schema[$table_name]['comment'] = $table_comment;

                    // 指定目前 table
                    $curr_table = $table_name;

                    // 指定目前的欄位總數
                    $field_count = 0;

                    break;

                // 資料欄位
                case 8:
                    // 目前是第幾欄位
                    $field_count++;

                    // 資料拆解
                    $field         = $this->_parse_field($line);
                    $field_name    = $field['name'];
                    $field_comment = $field['comment'];

                    // 建立結構
                    $this->_schema[$curr_table]['fields'][$field_name] = $this->FIELD_STRUCT;
                    $this->_schema[$curr_table]['fields'][$field_name]['comment'] = $field_comment;

                    // 第一個欄位就是主 key
                    if ($field_count == 1)
                    {
                        $this->_schema[$curr_table]['pk'] = $field_name;
                        $this->_schema[$curr_table]['fields'][$field_name]['type'] = 'INT';
                        $this->_schema[$curr_table]['fields'][$field_name]['auto_increment'] = TRUE;
                        unset($this->_schema[$curr_table]['fields'][$field_name]['constraint']);
                    }

                    break;

                // 註解
                case 12:
                default:
                    $this->_schema[$curr_table]['fields'][$field_name]['comment'] .= ', ' . trim($line);
                    break;
            }
        }
    }

    /**
     * 取得 migration 語法
     * @return string 語法
     */
    public function get_migration($type = 'CI')
    {
        $method = "get_{$type}_migration";
        return $this->$method();
    }

    /**
     * 取得 CodeIgniter 的 Migration 語法
     * @return string [description]
     */
    public function get_CI_migration()
    {
        $output = [];
        foreach ($this->_schema as $table_name => $table)
        {
            // 拿掉欄位註解(CI 不支援)
            foreach ($table['fields'] as $field_name => $field_param)
            {
                unset($table['fields'][$field_name]['comment']);
            }

            // 取得語法
            $output[] = $this->_ci_template($table_name, $table);
        }

        return implode("\n\n", $output);
    }

    /**
     * 取得 Laravel Migration 語法
     * @return string         語法
     */
    public function get_Laravel_migration()
    {
        return print_r($this->_schema, TRUE);
    }

    /**
     * 解析欄位資料
     * @param  string $str 原始的欄位資料
     * @return array       解析完的欄位資料
     */
    private function _parse_field($str)
    {
        // - 切開
        $tmp = explode('-', $str);

        // 欄位名稱
        $name = trim($tmp[0]);

        // 註解
        $comment = isset($tmp[1]) ? trim($tmp[1]) : '';

        // TYPE (取得括號內的字串)
        if (preg_match('/([a-z\_]+) \((.*)\)/i', $name, $matches))
        {
            $name = $matches[1];

            // 拆解 type
            $type_str = $matches[2];
            $tmp2 = explode(',', $type_str);
            $type = $tmp2[0];
            $constraint = (isset($tmp2[1])) ? $tmp2[1] : '';
        }
        else
        {
            $type = 'VARCHAR';
        }

        return [
            'name'       => $name,
            'type'       => $type,
            'constraint' => $constraint,
            'comment'    => $comment
        ];
    }

    /**
     * CI Migration 語法的樣版
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法
     */
    public function _ci_template($table_name, $table)
    {
        // 取得 fields 的 php 語法
        $fields = var_export($table['fields'], TRUE);

        $html = <<<HTML
\$this->dbforge->add_field({$fields});
\$this->dbforge->add_key('{$table['pk']}', TRUE);
\$this->dbforge->create_table('{$table_name}');
HTML;
        return $this->_append_space($html);
    }

    /**
     * 加上縮排
     * @param  string  $output 語法
     * @param  integer $length 縮排長度
     * @return string          縮排後語法
     */
    private function _append_space($output, $length = 8)
    {
        $tmp = explode("\n", $output);
        $output = '';
        foreach ($tmp as $line)
        {
            $output .= str_pad(' ', $length) . $line . "\n";
        }
        return $output;
    }
}

if ($_POST['fm_action'] == 'build')
{
    $schema  = $_POST['fm_schema'];
    $builder = new Migration_builder($schema);
    // $output  = $builder->get_migration();
    $output  = $builder->get_CI_migration();
}
?>
<html>
<head>
<base href="<?php echo BASE_URL ?>/">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Migration Builder</title>
<script type="text/javascript" src='sys/lib/js/jquery.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery-ui.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery.tmpl.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery.tmpl.html.js'></script>
<style type="text/css">
textarea {font-size:14px; font-family: "Yahei Consolas Hybrid"; width:100%; height:300px;}
input[type='button'] {background: #EFE;}
</style>
</head>
<body>
<pre>
==  Migration Builder (CI版) ==
</pre>
<form id='form_editor' method='post'>
    DB Schema (從 xmind 複製):
    <textarea id='fm_schema' name='fm_schema' onfocus='this.select()'><?= $schema ?></textarea>
    FrameWork:
    <input type='radio' name='type' value='CI'> Codeigniter
    <input type='radio' name='type' value='Laravel'> Laravel
    <input type='submit' value='產生 Migration 語法'>
    <textarea id='fm_output' name='fm_output' onfocus='this.select()'><?= $output ?></textarea>
    <input type='hidden' name='fm_action' value='build'>
</form>
</body>
</html>