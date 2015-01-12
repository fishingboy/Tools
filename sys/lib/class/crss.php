<?
class RSSParser 
{
    var $channel_title = "";
    var $channel_link = "";
    var $channel_description = "";
	var $insideitem = false;
	var $tag = "";
	var $title = "";
	var $description = "";
	var $link = "";
	var $listCount = 8;   //max
	var $count = 0;
	var $icon = "";
	var $items = array();
	var $tags = array();
    var $isRss = false;


	function startElement($parser, $tagName, $attrs) 
	{		
        array_push($this->tags, $tagName);
        
		if ($this->insideitem) 
		{
			$this->tag = $tagName;
		} 
		elseif ($tagName == "ITEM") 
		{			
			$this->insideitem = true;
		}
	}

	function endElement($parser, $tagName) 
	{		
        array_pop($this->tags);
        
		if (($tagName == "ITEM")  && ($this->insideitem))
		{			
			$title = htmlspecialchars(trim($this->title));
            $description = htmlspecialchars(trim($this->description));
            
            if (!$this->listCount || $this->count < $this->listCount)
            {
                $this->items[$this->count] = array(
                                        "title" => $title,
                                        "link" => "$this->link",
                                        "description" => $description,
                                        "pubdate" => "$this->pubdate"
                                        );
            }
			
			
			$this->title = "";
			$this->description = "";
			$this->link = "";
			$this->pubdate = "";
			$this->insideitem = false;
			$this->count++;
		}
	}

	function characterData($parser, $data) 
	{
		if ($this->insideitem) 
		{
			switch ($this->tag) 
			{
				case "TITLE":
						$this->title .= $data;
						break;
				case "DESCRIPTION":
						$this->description .= $data;
						break;
				case "LINK":
						$this->link .= $data;
						break;
				case "PUBDATE":
						$this->pubdate = $data;
						break;
				case "DC:DATE":
						$this->pubdate = $data;
						break;
			}
		}
        else
        {
            $path = $this->getTag();
            switch ($path)
            {
                case "RSS.CHANNEL.TITLE" :
                    $this->channel_title .= $data;
                    $this->isRss = True;
                    break;
                case "RSS.CHANNEL.LINK" :
                    $this->channel_link .= $data;
                    $this->isRss = True;
                    break;
                case "RSS.CHANNEL.DESCRIPTION" :
                    $this->channel_description .= $data;
                    $this->isRss = True;
                    break;
            }
        }
	}
	
    function getTag()
    {
        for($i=0; $i<count($this->tags); $i++)
        {
            $tag .= ($tag) ? ".{$this->tags[$i]}" : $this->tags[$i];
        }
        return $tag;    
    }

	function getItems() 
	{
	    return $this->items;
    }
}
?>