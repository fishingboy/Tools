<?
    ob_start();
    include_once ('D:/TMS/www_tools/FirePHPCore/fb.php');
    $console = FirePHP::getInstance(true);

    if (strpos($PHP_SELF, 'firephp') !== false)
    {
        // Defaults:
        $options = array('maxObjectDepth' => 5,
                         'maxArrayDepth' => 5,
                         'maxDepth' => 10,
                         'useNativeJsonEncode' => true,
                         'includeLineNumbers' => true);
        $console->getOptions();
        $console->setOptions($options);
        FB::setOptions($options);
        $console->setObjectFilter('ClassName',
                                   array('MemberName'));
        
        // try & catch
        $console->registerErrorHandler($throwErrorExceptions=false);
        $console->registerExceptionHandler();
        $console->registerAssertionHandler($convertAssertionErrorsToExceptions=true,$throwAssertionExceptions=false);
         
        try 
        {
          $a = 1 / 0;
          throw new Exception('Test Exception');
        } 
        catch(Exception $e) 
        {
          $console->error($e);  // or FB::
        }
                                   
                                   
        // group
        $console->group('Test Group');
        $console->log('Hello World');
        $console->groupEnd();
         
        $console->group('Collapsed and Colored Group',
                        array('Collapsed' => true,
                              'Color' => '#FF00FF'));
        
        // log
        $console->log($_SERVER, '$_SERVER');
        $console->log('原來只是這樣而以啊？', 'aaa');
        $console->info($_SESSION, '$_SESSION');
        $console->warn($_SERVER, '$_SERVER');
        $console->error($_SERVER, '$_SERVER');

        // table
        $table   = array();
        $table[] = array('Col 1 Heading','Col 2 Heading');
        $table[] = array('Row 1 Col 1','Row 1 Col 2');
        $table[] = array('Row 2 Col 1','Row 2 Col 2');
        $table[] = array('Row 3 Col 1','Row 3 Col 2');
        $console->table('Table Label', $table);  // or FB::
        fb($table, 'Table Label', FirePHP::TABLE);
        
        //trace
        $a = 1;
        $console->trace('Trace Label', "aa");  // or FB::
        fb('Trace Label', FirePHP::TRACE);
    }
?>