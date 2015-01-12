<?php 
    class Sanitizer
    {
        // Private fields
        var $_allowedTags;
        var $_allowJavascriptEvents;
        var $_allowJavascriptInUrls;
        var $_allowObjects;
        var $_allowScript;
        var $_allowStyle;
        var $_additionalTags;
        
       
        function Sanitizer()
        {
            $this->resetAll();
        }
        
        
        function resetAll()
        {
            $this->_allowDOMEvents = false;
            $this->_allowJavascriptInUrls = false;
            $this->_allowStyle = false;
            $this->_allowScript = false;
            $this->_allowObjects = false;
            $this->_allowStyle = false;

            $this->_allowedTags = '<a><br><b><h1><h2><h3><h4><h5><h6>'
                . '<img><li><ol><p><strong><font><table><tr><td><th><u><ul><thead>'
                . '<tbody><tfoot><em><dd><dt><dl><span><div><del><add><i><hr>'
                . '<pre><br><blockquote><address><code><caption><abbr><acronym>'
                . '<cite><dfn><q><ins><sup><sub><kbd><samp><var><tt><small><big>'
                ;
                
            $this->_additionalTags = '';
        }
        
       
        function addAdditionalTags( $tags )
        {
            $this->_additionalTags .= $tags;
        }

       
        function allowObjects()
        {
            $this->_allowObjects = true;
        }
        
       
        function allowDOMEvents()
        {
            $this->_allowDOMEvents = true;
        }
        
      
        function allowScript()
        {
            $this->_allowScript = true;
        }
        
      
        function allowJavascriptInUrls()
        {
            $this->_allowJavascriptInUrls = true;
        }
        
      
        function allowStyle()
        {
            $this->_allowStyle = true;
        }
       
        function allowAllJavascript()
        {
            $this->allowDOMEvents();
            $this->allowScript();
            $this->allowJavascriptInUrls();
        }
        
       
        function allowAll()
        {
            $this->allowAllJavascript();
            $this->allowObjects();
            $this->allowStyle();
        }
        
        
        function filterHTTPResponseSplitting( $url )
        {
            $dangerousCharactersPattern = '~(\r\n|\r|\n|%0a|%0d|%0D|%0A)~';
            return preg_replace( $dangerousCharactersPattern, '', $url );
        }
        
        /**
         * Remove potential javascript in urls
         * @access  public
         * @param   string url
         * @return  string filtered url
         */
        function removeJavascriptURL( $str )
        {
            $HTML_Sanitizer_stripJavascriptURL = 'javascript:[^"]+';

            $str = preg_replace("/$HTML_Sanitizer_stripJavascriptURL/i"
                , ''
                , $str );

            return $str;
        }
        
        /**
         * Remove potential flaws in urls
         * @access  private
         * @param   string url
         * @return  string filtered url
         */
        function sanitizeURL( $url )
        {
            if ( ! $this->_allowJavascriptInUrls )
            {
                $url = $this->removeJavascriptURL( $url );
            }
            
            $url = $this->filterHTTPResponseSplitting( $url );

            return $url;
        }        
        
        function _sanitizeURLCallback( $matches )
        {
            return 'href="'.$this->sanitizeURL( $matches[1] ).'"';
        }
        
       
        function sanitizeHref( $str )
        {
            $HTML_Sanitizer_URL = 'href="([^"]+)"';

            return preg_replace_callback("/$HTML_Sanitizer_URL/i"
                , array( &$this, '_sanitizeURLCallback' )
                , $str );
        }
        
        
        function _sanitizeSrcCallback( $matches )
        {
            return 'src="'.$this->sanitizeURL( $matches[1] ).'"';
        }
        
        /**
         * Remove potential flaws in href attributes
         * @access  private
         * @param   string html tag
         * @return  string filtered html tag
         */
        function sanitizeSrc( $str )
        {
            $HTML_Sanitizer_URL = 'src="([^"]+)"';

            return preg_replace_callback("/$HTML_Sanitizer_URL/i"
                , array( &$this, '_sanitizeSrcCallback' )
                , $str );
        }
        
        /**
         * Remove dangerous attributes from html tags
         * @access  private
         * @param   string html tag
         * @return  string filtered html tag
         */
        function removeEvilAttributes( $str )
        {
            if ( ! $this->_allowDOMEvents )
            {
                $str = preg_replace_callback('/<(.*?)>/i'
                    , array( &$this, '_removeDOMEventsCallback' )
                    , $str );
            }
            
            if ( ! $this->_allowStyle )
            {
                $str = preg_replace_callback('/<(.*?)>/i'
                    , array( &$this, '_removeStyleCallback' )
                    , $str );
            }
                
            return $str;
        }
        
        /**
         * Remove DOM events attributes from html tags
         * @access  private
         * @param   string html tag
         * @return  string filtered html tag
         */
        function removeDOMEvents( $str )
        {
            $str = preg_replace ( '/\s*=\s*/', '=', $str );

            $HTML_Sanitizer_stripAttrib = '(onclick|ondblclick|onmousedown|'
                . 'onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|'
                . 'onkeyup|onfocus|onblur|onabort|onerror|onload)'
                ;

            // $str = stripslashes( preg_replace("/$HTML_Sanitizer_stripAttrib/i"
                // , 'forbidden'
                // , $str ) );
            $str = preg_replace("/$HTML_Sanitizer_stripAttrib/i"
                , 'forbidden'
                , $str );

            return $str;
        }
        
        /**
         * Callback for PCRE
         * @access private
         * @param matches array
         * @return string
         * @see removeDOMEvents
         */
        function _removeDOMEventsCallback( $matches )
        {
            return '<' . $this->removeDOMEvents( $matches[1] ) . '>';
        }
        
        /**
         * Remove style attributes from html tags
         * @access  private
         * @param   string html tag
         * @return  string filtered html tag
         */
        function removeStyle( $str )
        {
            $str = preg_replace ( '/\s*=\s*/', '=', $str );

            $HTML_Sanitizer_stripAttrib = '(style)'
                ;

            // $str = stripslashes( preg_replace("/$HTML_Sanitizer_stripAttrib/i"
                // , 'forbidden'
                // , $str ) );
            $str =  preg_replace("/$HTML_Sanitizer_stripAttrib/i"
                , 'forbidden'
                , $str );

            return $str;
        }
        
        /**
         * Callback for PCRE
         * @access private
         * @param matches array
         * @return string
         * @see removeStyle
         */
        function _removeStyleCallback( $matches )
        {
            return '<' . $this->removeStyle( $matches[1] ) . '>';
        }
        
        /**
         * Remove dangerous HTML tags
         * @access  private
         * @param   string html code
         * @return  string filtered url
         */
        function removeEvilTags( $str )
        {
            $allowedTags = $this->_allowedTags;
            
            if ( $this->_allowScript )
            {
                $allowedTags .= '<script>';
            }
            
            if ( $this->_allowStyle )
            {
                $allowedTags .= '<style>';
            }
            
            if ( $this->_allowObjects )
            {
                $allowedTags .= '<object><embed><applet><param>';
            }
            
            $allowedTags .= $this->_additionalTags;
            
            $str = strip_tags($str, $allowedTags );
            
            // if ( $this->_allowObjects )
            // {
                // $str = removeEmbedAttr($str, ''); 
            // }

            return $str;
        }
        
       
        function sanitize( $html )
        {
            $html = $this->removeEvilTags( $html );
            
            $html = $this->removeEvilAttributes( $html );
            
            $html = $this->sanitizeHref( $html );
            
            $html = $this->sanitizeSrc( $html );
            
            return $html;
        }
        
        /**
         remove  
             allowscriptaccess ÄÝ©Ê³]¬° never¡B
             allownetworking none
        */
        function removeEmbedAttr($str, $attr)
        {
            $pos = 0;
            while (1)
            {
                $lstr = strtolower($str);
                $pos = strpos($str, "<object", $pos);
                if ($pos !== false)
                {
                    $pos2 = strpos($str, "</object>", $pos+7);
                }
                $str2 = substr($str, $pos, $pos2 + 9);
            }
            return $str;
        }
    }
?>