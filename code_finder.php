<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);

register_shutdown_function("code_finder::init");
class code_finder
{
    public static $_instance;
    public $files;

    public $keyword;
    public $upper_case;

    public $find_list;
    public $find_content;

    // 初始化
    public function init()
    {
        if ( ! isset(self::$_instance))
        {
            self::$_instance = new self();
        }
        $self = self::$_instance;
        $self->action();
    }

    // 開始
    public function action()
    {
        $this->get_files();
        $this->get_keyword();
        if ($this->keyword)
        {
            $this->get_upper_case();
            $this->find_keyword($this->keyword);
        }
        $this->output();
    }

    // 取得關鍵字
    public function get_keyword()
    {
        $keyword = (isset($_GET['__keyword'])) ? $_GET['__keyword'] : "";
        $keyword = (isset($_POST['__keyword'])) ? $_POST['__keyword'] : $keyword;
        $this->keyword = $keyword;
    }

    // 取得是否區分大小寫
    public function get_upper_case()
    {
        $this->upper_case = $_POST['__upper_case'];
    }

    // 取得 include 檔案
    public function get_files()
    {
        $this->files = get_included_files();
    }

    // 關鍵字搜尋
    public function find_keyword($keyword)
    {
        $this->find_list    = $this->find_file_list($keyword);
        $this->find_content = $this->find_files_content($keyword);
    }

    public function keyword_cmp($haystack, $needle)
    {
        if ($this->upper_case)
            return preg_match("/$needle/i", $haystack);
        else
            return strpos($haystack, $needle);
    }

    // 搜尋檔案名稱
    public function find_file_list($keyword)
    {
        $result = array();
        foreach ($this->files as $file)
        {
            if ($this->keyword_cmp($file, $keyword))
            {
                $result[] = $file;
            }
        }
        return $result;
    }

    // 搜尋檔案內容
    public function find_files_content($keyword)
    {
        $result = array();
        foreach ($this->files as $file)
        {
            $fp = fopen($file, "r");
            $line = 0;
            while ($content = fgets($fp))
            {
                $line++;
                if ($this->keyword_cmp($content, $keyword))
                {
                    $result[$file][$line] = $content;
                }
            }
        }
        return $result;
    }

    /***************************************************************/

    // 畫面輸出
    public function output($value='')
    {
        echo "<hr>";
        echo $this->output_form();
        if ($this->keyword)
        {
            echo $this->output_find_list();
        }
        else
        {
            echo $this->output_file_list();
        }
        echo $this->output_find_content();
    }

    // 輸出檔案清單
    public function output_file_list()
    {
        return "<pre>file_list = " . print_r($this->files, TRUE). "</pre>";
    }

    // 輸出符合關鍵字的檔案清單
    public function output_find_list()
    {
        return "<pre>list = " . print_r($this->find_list, TRUE). "</pre>";
    }

    // 輸出符合關鍵字的檔案內容
    public function output_find_content()
    {
        return "<pre>content = " . print_r($this->find_content, TRUE). "</pre>";
    }

    // 建立送出表單
    public function output_form()
    {
        // 找出 get 網址
        $get_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        // 找出 post 欄位
        $hidden_black_list = array('__keyword', '__upper_case');
        $post_hidden = "";
        if (isset($_POST))
        {
            foreach ($_POST as $key => $value)
            {
                if (in_array($key, $hidden_black_list)) continue;

                $value = htmlspecialchars($value, ENT_QUOTES);
                $post_hidden .= "<input type='hidden' name='$key' value='$value'>";
            }
        }

        $keyword = htmlspecialchars($this->keyword, ENT_QUOTES);
        $checked = ($this->upper_case) ? "checked" : "";
        $html = <<<HTML
        <div style='background:#EFE; padding:5px;'>
            <form action='{$get_url}' method='post'>
                $post_hidden
                搜尋: <input type='text' name='__keyword' value='{$keyword}'>
                <input type='checkbox' name='__upper_case' value='1' {$checked}> 不分大小寫
                <input type='submit' value='查詢'>
            </form>
        </div>
HTML;
        return $html;
    }
}
