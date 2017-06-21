<?php
	class RSSFeed {	
		var $m_RSSversion	=	'2.0';
		var $m_XMLversion	=	'1.0';
        var $m_encoding     =   null;
		var	$m_channel		=	null;
		var $m_FeedItem		=	'';
		var $m_channelItem	=	'';
		var $m_image		=	'';
		
		function RSSFeed($encoding='UTF-8') 
		{
			$this->m_encoding   =   $encoding;
			$this->m_channel	=	"<?xml version=\"".$this->m_XMLversion."\" encoding=\"".$this->m_encoding."\"  standalone=\"yes\" ?>\n";
			$this->m_channel	.=	"<rss version=\"".$this->m_RSSversion."\">\n";
		}				
		
		
		function addChannel($ChannelTitle, $ChannelDescription, $ChannelLanguage, $ChannelURL, $ChannelPublisher, $ChannelCopyright, $ChannelWebmaster) 
		{
			$this->m_channel	.=	"\t<channel>\n";
			$this->m_channel	.=	"\t\t<title>".$ChannelTitle."</title>\n";
			$this->m_channel	.=	"\t\t<description>".$ChannelDescription."</description>\n";
			$this->m_channel	.=	"\t\t<language>".$ChannelLanguage."</language>\n";
        }		

									
		function addChannelLink($ChannelLink) 
		{
			$this->m_channel	.=	"\t\t<link>".$ChannelLink."</link>\n";
		}		

							
		function addImage($ImageURL, $ImageTitle, $ImageLink) 
		{
			$this->m_image	.=	"\t\t<image rdf:about=\"".$ImageURL."\">\n";
			$this->m_image	.=	"\t\t\t<title>".$ImageTitle."</title>\n";
			$this->m_image	.=	"\t\t\t<url>".$ImageURL."</url>\n";
			$this->m_image	.=	"\t\t\t<link>".$ImageLink."</link>";
			$this->m_image	.=	"\t\t</image>\n";
		}		
		
		
		function addChannelItem($ChannelItem) 
		{
			$this->m_channelItem	.=	"\t\t\t\t<rdf:li resource=\"".$ChannelItem."\" />\n";
		}
		
	
		function addFeedItem($ItemTitle, $ItemURL, $ItemDescription, $pubDate="") 
		{
			$this->m_FeedItem	.=	"\t<item>\n";
			$this->m_FeedItem	.=	"\t\t<title>".$ItemTitle."</title>\n";
			$this->m_FeedItem	.=	"\t\t<link>".$ItemURL."</link>\n";
			$this->m_FeedItem	.=	"\t\t<description>".$ItemDescription."</description>\n";
			if ($pubDate)
                $this->m_FeedItem   .=  "\t\t<pubDate>".$pubDate."</pubDate>\n";
			$this->m_FeedItem	.=	"\t</item>\n";
		}		
		
		function releaseFeed() 
		{
			header("Content-Type: text/xml");
			print $this->m_channel;
			if(strlen($this->m_channelItem) >= 1) 
			{
				print "\t\t<items>\n
      					\t\t\t<rdf:Seq>\n"
							.$this->m_channelItem
        				."\t\t\t</rdf:Seq>\n
    				   \t\t</items>\n";
			} 
			
			
			print $this->m_FeedItem;
			
			print "\t</channel>\n";
			print "\t</rss>\n";
		}
	}	
?>
