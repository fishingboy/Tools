<?php 
require_once 'PHPUnit/Autoload.php'; 

// ci 環境變數
define('ENVIRONMENT' , 'development');
define('SELF' , 'index.php');
define('EXT' , '.php');
define('BASEPATH' , 'D:/www/www_ci/system/');
define('FCPATH' , 'D:\www\www_ci\\');
define('SYSDIR' , 'system');
define('APPPATH' , 'D:/www/www_ci/application/');
// define('CI_VERSION' , '2.1.3');
// define('CI_CORE' , '');
// define('FILE_READ_MODE' , '420');
// define('FILE_WRITE_MODE' , '438');
// define('DIR_READ_MODE' , '493');
// define('DIR_WRITE_MODE' , '511');
// define('FOPEN_READ' , 'rb');
// define('FOPEN_READ_WRITE' , 'r+b');
// define('FOPEN_WRITE_CREATE_DESTRUCTIVE' , 'wb');
// define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE' , 'w+b');
// define('FOPEN_WRITE_CREATE' , 'ab');
// define('FOPEN_READ_WRITE_CREATE' , 'a+b');
// define('FOPEN_WRITE_CREATE_STRICT' , 'xb');
// define('FOPEN_READ_WRITE_CREATE_STRICT' , 'x+b');
// define('WWW_ROOT' , 'http://ci/');
// define('UTF8_ENABLED' , '1');
// define('MB_ENABLED' , '1');
// require_once 'D:\www\www_ci\system\core\Common.php'; 
// require_once 'D:\www\www_ci\system\core\Model.php'; 
// require_once 'D:\www\www_ci\system\core\Controller.php'; 

// require_once 'D:\www\www_ci\application\controllers\cal.php';  
require_once 'D:\www\www_ci\system\core\CodeIgniter.php';  


class cal_controllerTest extends PHPUnit_Framework_TestCase 
{
    public function testCase1() 
    {
        $cal = new cal(); 
        $this->assertEquals(3, $cal->pow(1, 2)); 
    }

    public function testCase2() 
    {
        $cal = new cal(); 
        $this->assertEquals(5, $cal->pow(3, 2)); 
    }

    public function testCase3() 
    {
        $cal = new cal(); 
        $this->assertEquals(0, $cal->pow(3, 2)); 
    }
} 
?>