<?
class XMLParser 
{
    var $version;
    var $parser;
    var $xml;
    var $document;
    var $stack;

    function XMLParser($xml='', $version='1.0')
    {       
        $this->version = $version; 
        $this->xml = $xml;
        $this->stack = array();
    }

    /**
     * Initiates and runs PHP's XML parser
     */
    function Parse()
    {
        //Create the parser resource
        $this->parser = xml_parser_create("UTF-8");
        
        //Set the handlers
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'StartElement', 'EndElement');
        xml_set_character_data_handler($this->parser, 'CharacterData');

        //Error handling
        if (!xml_parse($this->parser, $this->xml))
            $this->HandleError(xml_get_error_code($this->parser), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser));

        //Free the parser
        xml_parser_free($this->parser);
    }
    
   
    function HandleError($code, $line, $col)
    {
        trigger_error('XML Parsing Error at '.$line.':'.$col.'. Error '.$code.': '.xml_error_string($code));
    }
   
    function GenerateXML()
    {
        return "<?xml version=\"$this->version\"?>" . $this->document->GetXML();
    }

   
    function GetStackLocation()
    {
        $return = '';

        foreach($this->stack as $stack)
            $return .= $stack.'->';
        
        return rtrim($return, '->');
    }

    
    function StartElement($parser, $name, $attrs = array())
    {
        //Make the name of the tag lower case
        $name = strtolower($name);       
        
        //Check to see if tag is root-level
        if (count($this->stack) == 0) 
        {
            //If so, set the document as the current tag
            $this->document = new XMLTag($name, $attrs);

            //And start out the stack with the document tag
            $this->stack = array('document');
        }
        //If it isn't root level, use the stack to find the parent
        else
        {
            //Get the name which points to the current direct parent, relative to $this
            $parent = $this->GetStackLocation();
			
			$name = str_replace("-", "_", $name);
			$name = str_replace("_>", "->", $name);
			
            //Add the child
            eval('$this->'.$parent.'->AddChild($name, $attrs, '.count($this->stack).');');

            //Update the stack
            eval('$this->stack[] = $name.\'[\'.(count($this->'.$parent.'->'.$name.') - 1).\']\';');
        }
    }

    
    function EndElement($parser, $name)
    {
        //Update stack by removing the end value from it as the parent
        array_pop($this->stack);
    }

   
    function CharacterData($parser, $data)
    {
        //Get the reference to the current parent object
        $tag = $this->GetStackLocation();
		$tag = str_replace("-", "_", $tag);
		$tag = str_replace("_>", "->", $tag);

        //Assign data to it
        eval('$this->'. $tag .'->tagData .= $data;');
    }
}
class XMLTag
{   
    var $tagAttrs; 
    var $tagName;   
    var $tagData;
    var $tagChildren;  
    var $tagParents;
    function XMLTag($name, $attrs = array(), $parents = 0)
    {
        //Make the keys of the attr array lower case, and store the value
        $this->tagAttrs = array_change_key_case($attrs, CASE_LOWER);      
        
        //Make the name lower case and store the value
        $this->tagName = strtolower($name);        
        
        //Set the number of parents
        $this->tagParents = $parents;
        
        //Set the types for children and data
        $this->tagChildren = array();
        $this->tagData = '';
    }
    function AddChild($name, $attrs, $parents)
    {    
        
        if(!isset($this->$name))
            $this->$name = array();

       
        if(!is_array($this->$name))
        {
            trigger_error('You have used a reserved name as the name of an XML tag. Please consult the documentation (http://www.thousandmonkeys.net/xml_doc.php) and rename the tag named '.$name.' to something other than a reserved name.', E_USER_ERROR);

            return;
        }

        //Create the child object itself
        $child = new XMLTag($name, $attrs, $parents);
        
        //Add the reference of it to the end of an array member named for the tag's name
        $this->{$name}[] =& $child;
        
        //Add the reference to the children array member
        $this->tagChildren[] =& $child;
    }
   
    function GetXML()
    {
        //Start a new line, indent by the number indicated in $this->parents, add a <, and add the name of the tag
        $out = "\n".str_repeat("\t", $this->tagParents).'<'.$this->tagName;

        //For each attribute, add attr="value"
        foreach($this->tagAttrs as $attr => $value)
            $out .= ' '.$attr.'="'.$value.'"';
        
        //If there are no children and it contains no data, end it off with a />
        if(empty($this->tagChildren) && empty($this->tagData) && ($this->tagData == ""))
        {
            $out .= ">";
            $out .= '</'.$this->tagName.'>';
        }
        //Otherwise...
        else
        {    
            //If there are children
            if(!empty($this->tagChildren))        
            {
                //Close off the start tag
                $out .= '>';
                
                //For each child, call the GetXML function (this will ensure that all children are added recursively)
                foreach($this->tagChildren as $child)
                    $out .= $child->GetXML();

                //Add the newline and indentation to go along with the close tag
                $out .= "\n".str_repeat("\t", $this->tagParents);
            }
            
            //If there is data, close off the start tag and add the data
            elseif($this->tagData != "")
                $out .= '>'.htmlspecialchars($this->tagData, ENT_QUOTES);
                //$out .= '>'.$this->tagData;
            
            //Add the end tag    
            $out .= '</'.$this->tagName.'>';
        }
        
        //Return the final output
        return $out;
    }
}
?>