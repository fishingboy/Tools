<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);

register_shutdown_function("code_finder::init");
class code_finder
{
    public static $_instance;
    public $files;

    public function init()
    {
        if ( ! isset(self::$_instance))
        {
            self::$_instance = new self();
        }
        $self = self::$_instance;
        $self->action();
    }

    public function action()
    {
        $this->get_files();
        $this->output_file_list();

        $keyword = $_GET['__keyword'];
        if ($keyword)
        {
            $this->find_keyword($keyword);
        }
    }

    public function get_files()
    {
        $this->files = get_included_files();
    }

    public function output_file_list()
    {
        echo "<pre>file_list = " . print_r($this->files, TRUE). "</pre>";
    }

    public function find_keyword($keyword)
    {
        $this->find_file_list($keyword);
        $this->find_files_content($keyword);
    }

    public function find_file_list($keyword)
    {
        foreach ($this->files as $file)
        {
            // echo "strpos({$file}, {$keyword}) <br>";
            if (strpos($file, $keyword) !== FALSE)
            {
                $html .= "$file<br>";
            }
        }
        echo $html;
    }

    public function find_files_content($keyword)
    {

    }
}
