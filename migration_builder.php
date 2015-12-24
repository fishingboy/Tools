<?php
header("content-type: text/html; charset=utf-8");

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
    protected $TABLE_STRUCT = [
        'pk'      => '',
    ];

    /**
     * 資料欄位預設結構
     * @var array
     */
    protected $FIELD_STRUCT = [
        'type'       => 'varchar',
        'constraint' => '100',
        'null'       => TRUE,
    ];

    /**
     * 從 xmind 輸入的原始格式
     * @var string
     */
    protected $_text;

    /**
     * 哪個 framework
     * @var string
     */
    protected $_framework;

    /**
     * 資料庫 (mysql, mssql, oracle)
     * @var string
     */
    protected $_db;

    /**
     * 解析後的資料格式
     * @var array
     */
    protected $_schema = [];

    /**
     * 建構子
     * @param string $text 從 xmind 來的原始 schema
     */
    public function __construct($text, $framework = 'CI', $db = 'mssql')
    {
        $this->_text      = $text;
        $this->_framework = $framework;
        $this->_db        = $db;
        $this->_parse();
    }

    /**
     * 解析原始格式資料
     */
    protected function _parse()
    {
        // 將 tab 轉成 space
        $this->_text = str_replace("\t", "    ", $this->_text);

        // 逐行解析
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
                    $field            = $this->_parse_field($line);
                    $field_name       = $field['name'];
                    $field_comment    = $field['comment'];
                    $field_type       = $field['type'];
                    $field_constraint = $field['constraint'];
                    $field_key        = $field['key'];
                    $field_fk         = $field['fk'];

                    // 建立結構
                    $this->_schema[$curr_table]['fields'][$field_name]               = $this->FIELD_STRUCT;
                    $this->_schema[$curr_table]['fields'][$field_name]['type']       = $field_type;
                    $this->_schema[$curr_table]['fields'][$field_name]['constraint'] = $field_constraint;
                    if ($field_comment)
                    {
                        $this->_schema[$curr_table]['fields'][$field_name]['comment']    = $field_comment;
                    }

                    // 第一個欄位就是主 key
                    if ($field_count == 1)
                    {
                        $this->_schema[$curr_table]['pk'] = $field_name;
                        $this->_schema[$curr_table]['fields'][$field_name]['type'] = 'INT';
                        $this->_schema[$curr_table]['fields'][$field_name]['auto_increment'] = TRUE;
                        unset($this->_schema[$curr_table]['fields'][$field_name]['constraint']);
                        // 避免主 key 重覆建立索引
                        unset($field_key);
                    }

                    // 外來鍵
                    if ($field_fk)
                    {
                        $this->_schema[$curr_table]['fk'][$field_name] = $field_fk;
                    }

                    // 索引
                    if (isset($field_key) && $field_key)
                    {
                        $this->_schema[$curr_table]['index'][] = $field_name;
                    }

                    break;

                // 註解
                case 12:
                default:
                    if ( ! isset($this->_schema[$curr_table]['fields'][$field_name]['comment']))
                    {
                        echo "$curr_table -> $field_name -> comment not defined!! <br>";
                    }
                    $this->_schema[$curr_table]['fields'][$field_name]['comment'] .= ', ' . trim($line);
                    break;
            }
        }
    }

    /**
     * 取得 CodeIgniter 的 Migration 語法
     * @return string [description]
     */
    public function get_migration()
    {
        // echo "<pre>this->_schema = " . print_r($this->_schema, TRUE). "</pre>";

        $output_up = $output_down = $output_fk_up = $output_fk_down = [];
        foreach ($this->_schema as $table_name => $table)
        {
            // 取得語法
            $output_up[]   = $this->_up_template($table_name, $table);
            $output_down[] = $this->_down_template($table_name);

            // 外來鍵
            $fk = $this->_fk_template($table_name, $table);
            if ($fk)
            {
                $output_fk_up[]   = $fk['up'];
                $output_fk_down[] = $fk['down'];
            }
        }

        return [
            'up'      => implode("\n\n", $output_up),
            'down'    => implode("\n", $output_down),
            'fk_up'   => implode("\n", $output_fk_up),
            'fk_down' => implode("\n", $output_fk_down),
        ];
    }

    /**
     * 解析欄位資料
     * @param  string $str 原始的欄位資料
     * @return array       解析完的欄位資料
     */
    protected function _parse_field($str)
    {
        $fk = $key = $comment = $constraint = "";

        // - 切開
        $tmp = explode('-', $str);

        // 欄位名稱
        $field_str = trim($tmp[0]);
        $name = explode(" ", $field_str)[0];

        // 索引
        $key = (preg_match("/\[key\]/", $field_str));

        // 外來鍵
        if (preg_match("/\[fk:(.*)\]/", $field_str, $matches))
        {
            $referer = $matches[1];
            $tmp = explode('.', $referer);
            $fk = [
                'referer_table' => $tmp[0],
                'referer_field' => $tmp[1],
            ];

            // 一併建立索引
            $key = TRUE;
        }

        // 註解
        $comment = isset($tmp[1]) ? trim($tmp[1]) : '';

        // TYPE (取得括號內的字串)
        if (preg_match('/\((.*)\)/i', $field_str, $matches))
        {
            // 拆解 type
            $type_str = $matches[1];
            $tmp2 = explode(',', $type_str);
            $type = $tmp2[0];
            $constraint = (isset($tmp2[1])) ? trim($tmp2[1]) : '';
        }
        else if (preg_match('/_time$|_at$/', $field_str))
        {
            $type = 'datetime';
        }
        else if (preg_match('/ID$|id$/', $field_str))
        {
            $type = 'int';
            $key = 'index';
        }
        else
        {
            $type = 'varchar';
        }

        return [
            'name'       => $name,
            'type'       => $type,
            'constraint' => $constraint,
            'comment'    => $comment,
            'key'        => $key,
            'fk'         => $fk
        ];
    }

    /**
     * 加上縮排
     * @param  string  $output 語法
     * @param  integer $length 縮排長度
     * @return string          縮排後語法
     */
    public static function append_space($output, $length = 8)
    {
        $tmp = explode("\n", $output);
        $output_arr = [];
        foreach ($tmp as $line)
        {
            $output_arr[] = str_pad(' ', $length) . $line;
        }
        return implode("\n", $output_arr);
    }
}

class CI_Migration_builder extends Migration_builder
{

    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _up_template($table_name, $table)
    {
        $code_arr = [];

        // 欄位 schema
        $fields = var_export($table['fields'], TRUE);
        $code_arr[] = "\$this->dbforge->add_field({$fields});";

        // 主 key
        $code_arr[] = "\$this->dbforge->add_key('{$table['pk']}', TRUE);";

        // 索引
        foreach ( (array) $table['index'] as $field_name)
        {
            $code_arr[] = "\$this->dbforge->add_key('{$field_name}', FALSE);";
        }

        // 建立資料表
        $code_arr[] = "\$this->dbforge->create_table('{$table_name}');";

        $code = implode("\n", $code_arr);
        return self::append_space($code);
    }

    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _fk_template($table_name, $table)
    {
        $code_arr_up = $code_arr_down = [];

        // 區隔符號
        $symbol_before = ($this->_db == 'mssql') ? '[' : '`';
        $symbol_after  = ($this->_db == 'mssql') ? ']' : '`';

        // 外來鍵(必須在建立資料表之後執行)
        $table['fk'] = (isset($table['fk'])) ? $table['fk'] : [];
        foreach ( (array) $table['fk'] as $field_name => $referer)
        {
            // up
            $code_arr_up[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                    ADD CONSTRAINT {$symbol_before}fk_{$table_name}_{$field_name}{$symbol_after}
                    FOREIGN KEY ({$field_name})
                    REFERENCES {$symbol_before}{$referer['referer_table']}{$symbol_after} ({$referer['referer_field']})');";

            // down
            if ($this->_db == 'mssql')
            {
                $code_arr_down[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                        DROP fk_{$table_name}_{$field_name}');";
            }
            else
            {
                $code_arr_down[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                        DROP FOREIGN KEY fk_{$table_name}_{$field_name}');";
            }
        }

        if (count($code_arr_up))
        {
            $code_up   = implode("\n", $code_arr_up);
            $code_down = implode("\n", $code_arr_down);
            return [
                'up'   => self::append_space($code_up),
                'down' => self::append_space($code_down)
            ];
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _fk_down_template($table_name, $table)
    {
        $code_arr = [];

        // 外來鍵(必須在建立資料表之後執行)
        foreach ( (array) $table['fk'] as $field_name => $referer)
        {
        }

        if (count($code_arr))
        {
            $code = implode("\n", $code_arr);
            return self::append_space($code);
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * CI Migration 語法的樣版 - down
     * @param  string $table_name 資料表名稱
     * @return string             CodeIgniter Migration 語法 - down
     */
    public function _down_template($table_name)
    {
        return self::append_space("\$this->dbforge->drop_table('{$table_name}', TRUE);");
    }
}

/**
 * Migration Builder - Laravel 版本
 */
class Laravel_Migration_builder extends Migration_builder
{
    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table       資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _up_template($table_name, $table)
    {
        $code_arr = [];

        if ( ! isset($table['fields']))
        {
            return FALSE;
        }

        // laravel timestamps
        $timestamps = '';
        if (isset($table['fields']['created_at']) && isset($table['fields']['updated_at']))
        {
            $timestamps = self::append_space('$table->timestamps();', 12);
            unset($table['fields']['created_at']);
            unset($table['fields']['updated_at']);
        }

        // 欄位 schema
        foreach ($table['fields'] as $key => $attrs)
        {
            $type = 'string';
            $constraint = "";
            switch ($attrs['type'])
            {
                case 'int':
                    $type = 'integer';
                    break;

                case 'float':
                    $type = 'integer';
                    break;

                case 'text':
                    $type = 'text';
                    break;

                case 'dateTime':
                    $type = 'dateTime';
                    break;

                case 'enum':
                    $type = 'enum';
                    // echo "<pre>attrs = " . print_r($attrs, TRUE). "</pre>";
                    $tmp = explode('/', $attrs['constraint']);
                    $constraint = ', ' . var_export($tmp, TRUE);
                    break;

                case 'varchar':
                default:
                    $type = 'string';
                    break;
            }

            if ($key == 'id' || preg_match('/ID$/', $key))
            {
                $type = 'increments';
            }

            $comment = (isset($attrs['comment'])) ? "->comment('{$attrs['comment']}')" : '';
            $code_arr[] = "\$table->{$type}('{$key}'{$constraint}){$comment};";
        }

        // 主 key
        // $table->increments('id'); 就已經設定好主 key 了
        // $index_arr[] = "\$table->primary('{$table['pk']}');";

        // 索引
        $index_arr = [];
        if (isset($table['index']))
        {
            foreach ( (array) $table['index'] as $field_name)
            {
                $index_arr[] = "\$table->index('{$field_name}');";
            }
        }
        $index = self::append_space(implode("\n", $index_arr), 12);

        $fields_code = self::append_space(implode("\n", $code_arr), 12);
        $code = <<<HTML
        Schema::create('{$table_name}', function (Blueprint \$table)
        {
$fields_code
$timestamps
$index
        });
HTML;
        return $code;
    }

    /**
     * CI Migration 語法的樣版 - down
     * @param  string $table_name 資料表名稱
     * @return string             CodeIgniter Migration 語法 - down
     */
    public function _down_template($table_name)
    {
        return self::append_space("Schema::drop('{$table_name}');", 12);
    }

    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _fk_template($table_name, $table)
    {
        $code_arr_up = $code_arr_down = [];

        // 區隔符號
        $symbol_before = ($this->_db == 'mssql') ? '[' : '`';
        $symbol_after  = ($this->_db == 'mssql') ? ']' : '`';

        // 外來鍵(必須在建立資料表之後執行)
        $table['fk'] = (isset($table['fk'])) ? $table['fk'] : [];
        foreach ( (array) $table['fk'] as $field_name => $referer)
        {
            // up
            $code_arr_up[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                    ADD CONSTRAINT {$symbol_before}fk_{$table_name}_{$field_name}{$symbol_after}
                    FOREIGN KEY ({$field_name})
                    REFERENCES {$symbol_before}{$referer['referer_table']}{$symbol_after} ({$referer['referer_field']})');";

            // down
            if ($this->_db == 'mssql')
            {
                $code_arr_down[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                        DROP fk_{$table_name}_{$field_name}');";
            }
            else
            {
                $code_arr_down[] = "\$this->db->query('ALTER TABLE {$symbol_before}{$table_name}{$symbol_after}
                        DROP FOREIGN KEY fk_{$table_name}_{$field_name}');";
            }
        }

        if (count($code_arr_up))
        {
            $code_up   = implode("\n", $code_arr_up);
            $code_down = implode("\n", $code_arr_down);
            return [
                'up'   => self::append_space($code_up),
                'down' => self::append_space($code_down)
            ];
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * CI Migration 語法的樣版 - up
     * @param  string $table_name 資料表名稱
     * @param  array $table      資料表格式
     * @return string             CodeIgniter Migration 語法 - up
     */
    public function _fk_down_template($table_name, $table)
    {
        $code_arr = [];

        // 外來鍵(必須在建立資料表之後執行)
        foreach ( (array) $table['fk'] as $field_name => $referer)
        {
        }

        if (count($code_arr))
        {
            $code = implode("\n", $code_arr);
            return self::append_space($code);
        }
        else
        {
            return FALSE;
        }
    }
}


$schema = "";
$framework = "Laravel";
$output = [
    'up' => '',
    'down' => '',
    'fk_up' => '',
    'fk_down' => '',
];
if (isset($_POST['fm_action']))
{
    $schema    = $_POST['fm_schema'];
    $framework = $_POST['fm_framework'];
    $db        = $_POST['fm_db'];

    switch ($_POST['fm_action'])
    {
        case 'shift':
            $schema = Migration_builder::append_space($schema, 4);
            break;

        case 'build':
        default:
            $builder_name =  $framework . '_Migration_builder';
            $builder = new $builder_name($schema, $framework, $db);
            $output  = $builder->get_migration();
            break;
    }
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
textarea {font-size:14px; font-family: "Yahei Consolas Hybrid"; width:100%; height:200px;}
input[type='button'] {background: #EFE;}
.box {border: 1px solid #ccc; border-radius: 10px; background: #EFE;}
.box .title {padding: 5px;}
</style>
<script>
function go(action)
{
    $('#fm_action').val(action);
    $('#form_migration').submit();
}
</script>
</head>
<body>
<pre>
==  Migration Builder  ==
</pre>
<form id='form_migration' method='post'>
    <input type='hidden' id='fm_action' name='fm_action' value=''>
    <div class='box'>
        <div class='title'>DB Schema (從 xmind 複製):</div>
        <textarea id='fm_schema' name='fm_schema' onfocus='this.select()'><?= $schema ?></textarea>
    </div>
    <div>
        FrameWork:
        <input type='radio' name='fm_framework' value='CI'      <?= ($framework == 'CI') ? 'checked' : '' ?> > Codeigniter
        <input type='radio' name='fm_framework' value='Laravel' <?= ($framework == 'Laravel') ? 'checked' : '' ?>> Laravel,
    </div>
    <div>
        資料庫(只影響 Foreign Key):
        <input type='radio' name='fm_db' value='mysql'> MySQL
        <input type='radio' name='fm_db' value='mssql' checked> SQL SERVER
        <input type='radio' name='fm_db' value='oracle' disabled> Oracle
    </div>
    <input type='button' id='build' value='產生語法' onclick='go(this.id)'>
    <input type='button' id='shift' value='->'      onclick='go(this.id)'>
    <div class='box'>
        <div class='title'>up():</div>
        <textarea id='fm_output_up' name='fm_output_up' onfocus='this.select()'><?= $output['up'] ?></textarea>
        <div class='title'>down():</div>
        <textarea id='fm_output_down' name='fm_output_down' onfocus='this.select()'><?= $output['down'] ?></textarea>
        <?php if (isset($output['fk_up'])): ?>
            <div class='title'>Foreign Key - up():</div>
            <textarea id='fm_output_fk' name='fm_output_fk' onfocus='this.select()'><?= $output['fk_up'] ?></textarea>
            <div class='title'>Foreign Key - down():</div>
            <textarea id='fm_output_fk' name='fm_output_fk' onfocus='this.select()'><?= $output['fk_down'] ?></textarea>
        <?php endif; ?>
    </div>

<pre>
** 資料可以直接從 xmind 複制過來

範例：
資料表
    user - 帳號
        userID
        account (varchar, 100) - 帳號
        password (varchar, 200) - 密碼
        roleType (int) [key] - 角色類別
        status (int) [key] - 狀態
            0 停用
            1 啟用
        name (varchar, 100) - 姓名
        adminPriv (int)- 模組管理權限
            以 bit 表示各模組權限
            系統管理者為固定帳號 admin
            系統管理者權限不可修改
        userAdminPriv (int) - 使用者管理權限
            管理使用者權限
            看要不要綁定特定帳號然後不可調整
        create_time
        update_time

說明：
* 第一個欄位會自動變成主 key
* 以縮排區分資料類型(因為 xmind 會自動縮排)
   4 個空白: 資料表名稱
   8 個空白: 資料表欄位名稱
   8 個空白以上，都當成註解
* 每一欄的格式為
    {field_name} ({type}, [{長度限制}]) [key] [fk:referer_table.referer.field] - {description}
    例如:
    account (varchar, 100) - 帳號
    roleType (int) [key] - 角色類別
    adminPriv (int)- 模組管理權限
* fk 為外來鍵
* 欄位名稱結尾為 _time ，會自動變成 datetime 格式
* 欄位名稱結尾為 ID ，會自動變成 int 格式，並設為 index
</pre>
</form>
</body>
</html>