<?php

/*
    影片格式
    1. mp4   : 320x240 ~ 640x480.mp4  (一般播放)
    2. mp4_hd: 640x480 ~ 1024x768.mp4 (高解析)
    3. swf   : .swf (for PowerCam, .fs 的原始大小)
    4. fs    : .fs  (PowerCam 原始格式)
    5. src   : .xxx (video 原始格式)
    
    PowerCam 上傳
    1. mp4_hd
    2. swf (如果可以轉 swf)
    3. fs
    
    
    影片上傳
    1. mp4    (640x480，一般播放)
    2. mp4_hd (640x480 ~ 1024x768)
    3. src    (原始格式)
    
    
    影片路徑
    1. base_path
        - lms     /sysdata/$vol/$courseID/doc/$pathID          (course)
                  /sysdata/user/$vol/$account/blog/doc/$pathID (blog)
        - mod     /sysdata/$vol/$account/doc/$pathID
        - speech  /sysdata/$vol/$account/doc/$pathID
        - tms     /sysdata/$vol/$courseID/doc/$pathID
        - p.cc    /sysdata/$vol/$account/doc/$pathID
    
    2. PowerCam
        - text/      media.fs
                     slides/1.jpg, 2.jpg (縮圖，沒用了?)
        - text.swf/  slides/1.swf, 2.swf
                     slides/1.jpg, 2.jpg (縮圖) 
        - video/     video_hd.mp4 (****)
                     thumb.jpg, thumb_m.jpg, video.jpg (縮圖)
    
    3. Video
        - video/     video.mp4      (320x240 ~ 640x480)
                     video_hd.mp4   (640x480 ~ 1024x768)
                     src.ext        (原始格式)

*/

abstract class fsMedia {
	protected $media;
    protected $db_table;
	protected $swf_id;
	protected $base_host;
    protected $base_path;    
	protected $play_type;
	protected $player_width;
    protected $player_height;
	protected $player_wmode;
	protected $log_id;
	
    abstract protected function setBasePath();
	abstract protected function show();
	abstract protected function showEmbed();
    
    public function __construct($media, $table="content", $swf_id="swfMedia", $log_id=0)
    {
		$this->media = $media;
        $this->db_table = $table;
		$this->swf_id = $swf_id;
		$this->log_id = $log_id;
        $this->setBasePath();
		if ($this->media->playtype == "swf")
		{
			if ($this->media->mp4 || $this->media->mp4_hd) $this->media->playtype = "fs2mp4";
		}
    }	

	/*
	need do javascript
	function editIdx(id)
	function editSlideTitle(id)
	*/
	public function showIndex($admin=0)
	{
		global $_SERVER, $msgIndex, $msgSlideTitle, $msgEdit;				
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   	
		$isIphone = (strpos(" $user_agent", "iPad") > 0 || strpos(" $user_agent", "iPhone") > 0) ? 1 : 0;
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;	
		$html = "";		
		$slideTitles = "";
		$id = $this->media->id;
		$db_table;
		if ($this->media->slideTitles != "" && !$isIphone)
		{
			$st = explode("\n", $this->media->slideTitles);
			$sh = "";
			$l1 = $l2 = 0;
			$nSlide = count($st);
			$bExpend = ($nSlide > 20) ? 1 : 0;
			for ($i=0; $i<$nSlide; $i++)
			{
				if (substr($st[$i], 0, 3) != "...")
				{
					$l1 ++;
					$l2 = 0;
					$css = "outline";
					$no = "$l1. ";
					$st[$i] = $st[$i];
				}
				else
				{
					$l2 ++;
					$css = "outline indent";
					$no = "$l1.$l2 ";
					$st[$i] = substr($st[$i], 3);
				}
				$num = $i+1;					
				$css = "class='$css'";								
				$sTitle = htmlspecialchars($st[$i], ENT_QUOTES);				
				if ($bExpend && $i==12) $sh .= "<div id=hiddenOutline_{$id} style='display:none'>";
				if ($this->media->playtype == "fs")
					$href = "javascript:{$this->swf_id}.openFS(\"".($i+1)."\")" ;
				else
					$href = "javascript:{$this->swf_id}.playSWF($num)";
				
				$sh .= "<div id=slide_{$id}_{$num} $css>
							<div>$no<a title='$sTitle' href='$href'>$sTitle</a></div>
						</div>";				
			}
			if ($bExpend) $sh = "$sh</div><div id=moreslide_{$id} style='cursor:pointer; font-weight:bold; font-size:11px'>... [more]</div>";
			
			$editSlideTitle = "";
			if ($admin)
			{
				if (!$this->media->remoteURL)
				{
					if ($this->media->playtype == "video" || $this->media->playtype == "mp4")
						$editSlideTitle = "<span class=hint>[<a href='javascript:editIdx($id)'>$msgEdit</a>]</span>";
					else
						$editSlideTitle = "<span class=hint>[<a href='javascript:editSlideTitle($id)'>$msgEdit</a>]</span>";
				}
			}
			/*
			if ($this->db_table != "content")
			{
				$from = "<input type=hidden id=mediaIndex_dbtable_{$id} name=mediaIndex_dbtable_{$id} value='{$this->db_table}'>";
			}*/
			$idx_title = ($this->media->playtype == "video" || $this->media->playtype == "mp4") ? "$msgIndex" : "$msgSlideTitle";
			$slideTitles = "<div id=medieIndex_{$id} name=medieIndex_{$id}>
							<div id=slideTitle>
								<div class=title>
									<div class=em>$idx_title $editSlideTitle</div>
								</div>
								<div>$sh</div>
							</div>				
							</div>
							<script>								
								\$j(document).ready(function(){	
									\$j('#moreslide_{$id}').click(function(){
										{$this->swf_id}.expendSlide();
									})
									currTimer = setTimeout(function(){{$this->swf_id}.checkCurr()}, 1000);	
								});
							</script>
							";							
		}
		return $slideTitles;
	}	
	
    public function getHost()
	{		
		return $this->base_host;		
	}
	
    public function getPath($type)
    {
        switch ($type)
        {
            case "mp4":
                return "{$this->base_path}/video";
            case "mp4_hd":
                return "{$this->base_path}/video";
			case "fs":
                return "{$this->base_path}/text";				
            case "swf":
                return "{$this->base_path}/text.swf";           
			case "flv":
                return "{$this->base_path}/text.flv";
            case "src":
                return "{$this->base_path}/video/{$this->media->filename}";
			default:
				return "";
        }			
    }  

    public function showSWF($area_size, $embed=0, $page=0, $fixed_size=0, $autoplay=0, $link_back='')
    {
        global $WEB_ROOT, $_SERVER;
		list($area_w, $area_h) = explode('x', $area_size);
		$area_w = $area_w - 4;		
		$this->play_type = $this->media->playtype;					      
        $autoplay = ($autoplay == 1) ? "&autoplay=yes" : "";
		
		$link = (!$link_back) ? "" : "&link={$link_back}";			

		$script = ($embed == 0) ? "&script=1" : "";		
		$ad = "";
		$src = "";
		$toPage = ""; 
		$maxratio = "";		
		$recommend = "";
		
		if ($this->play_type == "mp4")
		{
			$video_w = 640;
			$video_h = 360;
		}
		else
		{
			$video_w = 640;
			$video_h = 480;
		}
		if ($this->play_type == "mp4" || $this->play_type == "fs2mp4")
		{
			$player = "mp4.swf";
			$playtype = "mp4";
			//$playtype = ($this->play_type == "mp4") ? "video" : "flv";
			//$maxratio = ($this->play_type == "mp4") ? "" : "&maxratio=1";
			$wmode = "window";
			$src = "&url=";
			if ($this->media->mp4)
			{
				list($mp4_w, $mp4_h) = explode('x', $this->media->mp4);
				$video_w = $mp4_w;
				$video_h = $mp4_h;				
			}
			if ($this->media->mp4_hd)
			{
				list($mp4_hd_w, $mp4_hd_h) = explode('x', $this->media->mp4_hd);
				if ($mp4_hd_w > 640 && $area_w > 640)
				{				
					$video_w = $mp4_hd_w;
					$video_h = $mp4_hd_h;
					$fileindex = "&fileindex=1";
				}
				else if(!$this->media->mp4)
				{
					$video_w = 640;
					$video_h = intval($mp4_hd_h * 640 / $mp4_hd_w);
				}
			}
			/*
			if ($this->play_type == "mp4")
			{
				$project = "$WEB_ROOT/" . $this->base_path . "/video/prj/project.xml";
				$toc = (file_exists($project)) ? "&toc=1" : "";
			}*/
			$recommend = "&recommend=http://{$_SERVER['HTTP_HOST']}/sys/recommend.php?id={$this->media->id}";
			if ($this->media->advertiseID)
			{
				$ad =  "&ad=/sysdata/ad/{$this->media->advertiseID}/video.mp4";
			}				
		}
		else if ($this->play_type == "video")
		{
			$player = "flv.swf";						
			$playtype = "video";
			$wmode = "window";
			$src = "&url=";
			$video_w = 640;
			$video_h = 360;
			
			$project = "$WEB_ROOT/" . $this->base_path . "/video/prj/project.xml";
			$toc = (file_exists($project)) ? "&toc=1" : "";
			$recommend = "&recommend=http://{$_SERVER['HTTP_HOST']}/sys/recommend.php?id={$this->media->id}";
			if ($this->media->advertiseID)
			{
				$ad =  "&ad=/sysdata/ad/{$this->media->advertiseID}/video.flv";
			}
		}
		else if ($this->play_type == "flv")
		{
			$player = "flv.swf";						
			$playtype = "flv";
			$wmode = "window";					
			$maxratio = "&maxratio=1";
			$src = "&url=";
			$video_w = 640;
			$video_h = 480;
			$recommend = "&recommend=http://{$_SERVER['HTTP_HOST']}/sys/recommend.php?id={$this->media->id}";
			if ($this->media->advertiseID)
			{
				$ad =  "&ad=/sysdata/ad/{$this->media->advertiseID}/video.flv";
			}
		}
		else  //fs or ptt or swf
		{
			$player = "slideshow2.swf";
			$toPage = "&slide=$page";
			$playurl = "&playurl=http://{$_SERVER['HTTP_HOST']}/slide.php%3fid={$this->media->id}%26from={$this->db_table}%26logID={$this->log_id}";
			$playtype = $this->play_type;
			if (!$playtype) $playtype = 'fs';
			$wmode = "transparent";
			$src = "&src=";
			if ($this->play_type == "swf")
			{
				if ($this->media->swf)
				{
					list($swf_w, $swf_h) = explode('x', $this->media->swf);
					$video_w = $swf_w;
					$video_h = $swf_h;
				}
			}
			else //fs or ptt
			{
				if ($this->media->fs)
				{
					list($fs_w, $fs_h) = explode('x', $this->media->fs);
					$video_w = $fs_w;
					$video_h = $fs_h;
				}
			}			
		}			
		if ($fixed_size)
		{
			$this->player_width = $area_w;
			$this->player_height = $area_h;
		}
		else
		{
			//if ($area_w > 720) $area_w = 720;				
			$shift = ($player == 'slideshow2.swf') ? 4 : 23;
			$this->player_width = $area_w + 4;		
			$this->player_height = intval($video_h * ($area_w / $video_w)) + $shift;
		}		
   		$slideshow_php = ($embed == 1) ? "slideshow.php" : "slideshow2.php";
        $src = $src . "http://{$_SERVER['HTTP_HOST']}/{$slideshow_php}%3fid={$this->media->id}%26from={$this->db_table}";	
		
		if ($playtype == "swf" && $this->base_host)
			$player_url = "http://{$this->base_host}/{$player}";
		else
			$player_url = "http://{$_SERVER['HTTP_HOST']}/{$player}";
		
		$url = "{$player_url}?playtype={$playtype}{$src}{$toPage}{$maxratio}{$autoplay}{$script}{$fileindex}{$link}{$playurl}{$recommend}{$ad}{$toc}";
		//if ($embed == 1) return $url;
		$swf_id = $this->swf_id; 
		$embed = "<embed width='$this->player_width' height='$this->player_height' name={$swf_id} menu='false' wmode='$wmode' src='$url' allowfullscreen='true' allowScriptAccess='always' type='application/x-shockwave-flash'></embed>";
        return "<object width='$this->player_width' height='$this->player_height' id={$swf_id} classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,12,0'><param name='allowScriptAccess' value='always'></param><param name='allowFullScreen' value='true'></param><param name='movie' value='$url'></param><param name='bgcolor' value='#fff'></param><param name='WMode' value='$wmode'></param>$embed</object>";
    }  
	
	public function showHTML5($area_size, $fixed_size=0)
	{
		global $WEB_ROOT, $_SERVER, $msgNoIphone;
		$url = "";
		$image = "";
		$path = $this->getPath('mp4');	
/*		
		if (!file_exists("$WEB_ROOT/$path/video.mp4") 
			&& !file_exists("$WEB_ROOT/$path/video_hd.mp4"))
		{
			return "<span style='color:#f00'>$msgNoIphone</span>";
		}
*/
		list($area_w, $area_h) = explode('x', $area_size);		
		if ($this->play_type == "mp4")
		{
			$video_w = 640;
			$video_h = 360;
		}
		else
		{
			$video_w = 640;
			$video_h = 480;
		}			
		if ($this->media->mp4)
		{
			list($mp4_w, $mp4_h) = explode('x', $this->media->mp4);
			$video_w = $mp4_w;
			$video_h = $mp4_h;		
			$url = "/$path/video.mp4";
			$image = "/$path/video.jpg";			
		}		
		if ($this->media->mp4_hd)
		{
			list($mp4_hd_w, $mp4_hd_h) = explode('x', $this->media->mp4_hd);
			$url = "/$path/video_hd.mp4";
			$image = "/$path/video_hd.jpg";
			if ($mp4_hd_w > 640 && $area_w > 640)
			{				
				$video_w = $mp4_hd_w;
				$video_h = $mp4_hd_h;							
			}
			else if(!$this->media->mp4)
			{
				$video_w = 640;
				$video_h = intval($mp4_hd_h * 640 / $mp4_hd_w);				
			}
		}
		if ($url == "")
		{
			return "<span style='color:#f00'>$msgNoIphone</span>";
		}
		if ($this->base_host)
		{
			$image = "http://" . $this->base_host . $image;
			$url = "http://" . $this->base_host . $url;
		}
		if ($fixed_size)
		{
			$this->player_width = $area_w;
			$this->player_height = $area_h;
		}
		else
		{
			//if ($area_w > 720) $area_w = 720;
			$this->player_width = $area_w;
			$this->player_height = intval($video_h * ($area_w / $video_w));
		}
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   	
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;
		
		//preview
		if ($image != "") $poster = "poster='$image'";
		if ($isAndroid)
		{
			return "<div onmouseout='this.className=\"image\"' onmouseover='this.className=\"imageOver\"' style='width: {$this->player_width}px; height: {$this->player_height}px; margin:0 auto' class='image'>
						<a href='$url'>
							<img width='{$this->player_width}px' height='{$this->player_height}px' border='0' align='absmiddle' src='$image'>
						</a>
					</div>";
		}
		else	
		{
			return "<div style='width:{$this->player_width}px; height:{$this->player_height}px; border:1px solid #999; padding:1px;'>
						<video src='$url' width={$this->player_width} height={$this->player_height} $poster controls onclick='this.play()'></video>
					</div>";
		}	
	}
}




abstract class fsConverter {
	protected $media;
    protected $db_table;
    protected $base_path; 
	protected $convert_path;
	protected $xml_path;
	
    abstract protected function setBasePath();
	    
    public function __construct($media, $table = "contents")
    {
		$this->media = $media;
        $this->db_table = $table;        
        $this->setBasePath();		

    }
	
	public function createConvert($playtype, $filename='')
	{
		if($playtype == "fs")
			$this->createFsConvertList();
		else if ($playtype == "wowza")
			$this->createWowzaConvertList($filename);
		else
			$this->createVideoConvertList();
	}
	
	public function getBashPath()
	{
		return $this->base_path;
	}
	
	protected function createItem($id, $type, $dstFile, $dstPath, $size)
	{
		$str  = "\t\t<item id=\"$id\">\n";
		$str .= "\t\t\t<type>$type</type>\n";
		$str .= "\t\t\t<dstFile>$dstFile</dstFile>\n";
		$str .= "\t\t\t<dstPath>$dstPath</dstPath>\n";
		$str .= "\t\t\t<size>$size</size>\n";
		$str .= "\t\t</item>\n";
		return $str;
	}	
	protected function createVideoConvertList()
	{
		global $FS_QUEUE, $FS_DIR, $WEB_ROOT;
		$src_width = 0;
		$src_height = 0;
		if ($this->media->src)
		{
			list($src_w, $src_h) = explode('x', $this->media->src);		
			$src_width = $src_w;
			$src_height = $src_h;
		}
		else if ($this->media->mp4_hd)
		{
			list($mp4_hd_w, $mp4_hd_h) = explode('x', $this->media->mp4_hd);
			$src_width = $mp4_hd_w;
			$src_height = $mp4_hd_h;
		}
		if ($src_width < 640) return;
		//$sysprofile = db_object(db_query("SELECT * FROM profile LIMIT 1"));
		$src_path = "$FS_DIR\\" . $this->convert_path . "\\video";
		$dst_path = "$FS_DIR\\" . $this->convert_path . "\\video";							
		$id = 1;
		$fp = fopen($this->xml_path, "w");		
		$title = xml_specialchars($this->media->title);
		fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		fwrite($fp, "<fsconvert>\n");
		fwrite($fp, "\t<title>$title</title>\n");
		fwrite($fp, "\t<srcPath>$src_path</srcPath>\n");
		fwrite($fp, "\t<srcFile>{$this->media->filename}</srcFile>\n");		
		fwrite($fp, "\t<dstPath>$dst_path</dstPath>\n");		
		fwrite($fp, "\t<dstFiles>\n");
		if ($src_width > 640)
		{
			$str = $this->createItem($id++, "src2video", "video.mp4", "", "640x480");
			fwrite($fp, $str);
		}	
		fwrite($fp, "\t</dstFiles>\n");
		fwrite($fp, "</fsconvert>");				
		fclose($fp);
		chmod($this->xml_path, 0777);			
		exec("chmod -R 777 $WEB_ROOT/" . $this->base_path);
	}

	protected function createFsConvertList()
	{	
		global $FS_QUEUE, $FS_DIR, $WEB_ROOT;		
		$src_path = "$FS_DIR\\" . $this->convert_path . "\\text";
		$dst_path = "$FS_DIR\\" . $this->convert_path . "\\video";
		$dst_path_swf = "$FS_DIR\\" . $this->convert_path . "\\text.swf";
		$id = 1;		
		$fp = fopen($this->xml_path, "w");			
		fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		fwrite($fp, "<fsconvert>\n");
		fwrite($fp, "\t<title></title>\n");
		fwrite($fp, "\t<srcPath>$src_path</srcPath>\n");
		fwrite($fp, "\t<srcFile></srcFile>\n");
		fwrite($fp, "\t<dstPath>$dst_path</dstPath>\n");
		fwrite($fp, "\t<dstFiles>\n");	
		
		$str = $this->createItem($id++, "fs2video", "video_hd.mp4", "", "");
		fwrite($fp, $str);		
		
		$str = $this->createItem($id++, "fs2swf", "", $dst_path_swf, "");		
		fwrite($fp, $str);
		
		fwrite($fp, "\t</dstFiles>\n");
		fwrite($fp, "</fsconvert>");				
		fclose($fp);
		chmod($this->xml_path, 0777);			
		exec("chmod -R 777 $WEB_ROOT/" . $this->base_path);		
	}	
	
	protected function createWowzaConvertList($filename='')
	{
		global $FS_QUEUE, $FS_DIR, $WEB_ROOT;
						
		$src_path = "$FS_DIR\\" . $this->convert_path . "\\video";
		$dst_path = "$FS_DIR\\" . $this->convert_path . "\\video";
		$id = 1;
		
		$srcfilename = (trim($filename) != '') ? trim($filename) : $this->media->filename;
		$title = xml_specialchars($this->media->title);
		$fp = fopen($this->xml_path, "w");		
		fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		fwrite($fp, "<fsconvert>\n");
		fwrite($fp, "\t<title>$title</title>\n");
		fwrite($fp, "\t<srcPath>$src_path</srcPath>\n");
		fwrite($fp, "\t<srcFile>{$srcfilename}</srcFile>\n");		
		fwrite($fp, "\t<dstPath>$dst_path</dstPath>\n");		
		fwrite($fp, "\t<dstFiles>\n");		
		$size = $this->getVideoSize("$WEB_ROOT/" . $this->base_path . "/video/{$srcfilename}");
		if ($size->width > 640)
		{
			$str = $this->createItem($id++, "video", "video_hd.mp4", "", "1024x768");
			fwrite($fp, $str);		
			$str = $this->createItem($id++, "src2video", "video.mp4", "", "640x480");
			fwrite($fp, $str);
		}
		else
		{
			$str = $this->createItem($id++, "video", "video.mp4", "", "640x480");
			fwrite($fp, $str);		
		}		
		fwrite($fp, "\t</dstFiles>\n");
		fwrite($fp, "</fsconvert>");				
		fclose($fp);
		chmod($this->xml_path, 0777);			
		exec("chmod -R 777 $WEB_ROOT/" . $this->base_path);
	}
	protected function getVideoSize($file)
	{
		global $WEB_ROOT, $PLATFORM;
		$output = array();
		$return = "";
		$width = 0;
		$height = 0;
		$ffmpeg = ($PLATFORM == "uniz") ? "$WEB_ROOT/sys/bin/ffmpeg-linux" : "$WEB_ROOT/sys/bin/ffmpeg-win32.exe";	
		exec("$ffmpeg -y -i \"$file\" -f image2 -ss 0.1 $WEB_ROOT/sysdata/tmp/tmpTest.bmp 2>&1", $output, $return);	
		//echo "$ffmpeg -y -i \"$file\" -f image2 -ss 0.1 $WEB_ROOT/sysdata/tmp/tmpTest.bmp 2>&1";
		if ($return  == 1)
		{
			$string = "";
			foreach($output as $str)
			{
				$string .= $str . " ";
			}
			$video_pos = strpos($string, "Video");	
			if ($video_pos !== false)
			{		
				$x_pos = strpos($string, "x", $video_pos);		
				if ($x_pos !== true)
				{
					$end_pos = strpos($string, ",", $x_pos);
					if ($end_pos !== true)
					{			
						$vido_str = substr($string, $video_pos, $end_pos-$video_pos);
						$start_pos = strrpos($vido_str, ",");
						if ($start_pos !== true)
						{
							$size_str = substr($vido_str, $start_pos+1);
							list($width, $height) = explode("x", $size_str);
							$width = intval(trim($width)); 
							$height = intval(trim($height));
						}
					}
				}
			}
		}	
		$dar_pos = strpos($string, "DAR");	
		if ($dar_pos !== false)
		{
			$end_pos = strpos($string, "]", $dar_pos);
			if ($end_pos !== false)
			{
				$dar_str = substr($string, $dar_pos+3, $end_pos-($dar_pos+3));
				list($displayRatioW, $displayRatioH) = explode(":", $dar_str);
				$displayRatioW = intval(trim($displayRatioW)); 
				$displayRatioH = intval(trim($displayRatioH));
				if ($displayRatioW > 0)			
					$height = $width * $displayRatioH / $displayRatioW;									
			}		
		}	
		return (object) array("width" =>$width, "height" => $height);
	}
}


//*** lms start ***//
class lms_media extends fsMedia {  
	public function show($page=0, $autoplay=0)
	{
		$id = $this->media->id;		
		return "<div id=swf{$id} style='text-align:center;'></div>
				<script>
					var {$this->swf_id} = new fsmedia({swf_id:'{$this->swf_id}', doc_id:'{$this->media->id}', from:'{$this->db_table}', log_id:'{$this->log_id}'});
					var a = \$area($('swf{$id}'));
                    var w = a.width;
                    var h = a.height;
                    if (w > 724)
                    {
                        w = 724; 
                        h = parseInt(h / a.width * w);
                    }
					var area_size = w + 'x' + h;
					\$j(document).ready(function(){
						\$load('/sys/http_get_media.php', {id:{$id}, db_table:'{$this->db_table}', area_size:area_size, swf_id:'{$this->swf_id}', page:'$page', autoplay:'$autoplay', logID:'$this->log_id'}, 
							function(obj)
							{						
								var ret = obj.ret;
								if (ret.status == 'true')
									$('swf'+ret.id).innerHTML = ret.embed;
							}
						);
					});
				</script>
			   ";
	}
	
	public function showEmbed($area_size='644x484', $link_back='')
	{						
		global $msgSyntax,$mCfg,$msgEmbed,$msgVideoSize,$msgWidth,$msgHeight,$msgRealSize,$msgAspectRatio;
		$rw = $rh = $w = $h = 0;		
		$embed = $this->showSWF($area_size, 1, 0, 0, 0, $link_back);
		if ($this->play_type == 'mp4' || $this->play_type == 'fs2mp4' || $this->play_type == 'flv')
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-23;
		}
		else
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-4;
		}	
		$id = $this->media->id;

		return "<span id=swf_embed_{$id}>
					<a id=showEmbed_{$id} name=showEmbed_{$id} href='javascript:void(0)'>$msgEmbed</a>
				</span>				
				<script>	
					\$j(document).ready(function(){
						\$j('#showEmbed_{$id}').click(function(){
							var _body  = \"<div id=embed_{$id} name=embed_{$id}>\";
								_body += \"<div class=em>$msgSyntax: <input id=emSrc_{$id} name=emSrc_{$id} type=text onclick='this.select()' class=Text style='width:220px; color:#333' value=\\\"$embed\\\" readonly autocomplete=off> <a id=fmEmbed href=\\\"javascript:void($('embedOtpion').style.display=($('embedOtpion').style.display=='none')?'block':'none')\\\"><img align=absmiddle border=0 title='$mCfg' src='/sys/res/icon/tool.gif'></a></div>\";
								_body += \"<div id=embedOtpion style='margin-top:10px; padding:3px; border:1px solid #ccc; display:none'> \";
								_body += \"<div><span class=em>$msgVideoSize</span> <input type=checkbox id=emAspect name=emAspect value='1' checked onclick=\\\"changeAspect('embed_{$id}')\\\" /> $msgAspectRatio</div>\";
								_body += \"<div>$msgWidth <input type=text id=emWidth name=emWidth value='{$w}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 1)\\\"/> $msgHeight <input type=text id=emHeight name=emHeight value='{$h}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 2)\\\"/></div>\";
								_body += \"<div class=em style='padding-top:15px'>$msgRealSize</div>\";
								_body += \"<div>$msgWidth <input type=text id=rWidth name=rWidth value='{$rw}' style='width:40px' disabled/> $msgHeight <input type=text id=rHeight name=rHeight value='{$rh}' style='width:40px' disabled/></div>\";
								_body += \"<input id=emType name=emType value='$this->play_type' type=hidden />\";
								_body += \"<input id=emEmbed name=emEmbed value=\\\"$embed\\\" type=hidden />\";
								_body += \"<input id=emW name=emW value='$w' type=hidden />\";
								_body += \"<input id=emH name=emH value='$h' type=hidden />\";
								_body += \"</div>\";
								_body += \"</div>\";
								var a = \$area($('swf_embed_{$id}'));								
								\$showPopup('$msgEmbed', _body, a.left+a.width-300, a.top+a.height, 300);
						});
					});					
				</script>
				";
	}      
    protected function setBasePath()
    {	
		//global $_SERVER;
		//$this->base_host = $_SERVER['HTTP_HOST'];
        if ($this->db_table == "blog_documents")
		{
			$vol = user_hash($this->media->userID);
			$this->base_path = "sysdata/user/$vol/{$this->media->account}/blog/doc/{$this->media->path}";
		}
        else
		{
			$refCourseID  = ($this->media->refCourseID) ? $this->media->refCourseID : $this->media->courseID;
			$vol = course_hash($refCourseID);		
            $this->base_path = "sysdata/$vol/$refCourseID/doc/{$this->media->pathID}";
		}
    }
}

class lms_fsConverter extends fsConverter {        
    protected function setBasePath()
    {	
		global $FS_QUEUE;
        if ($this->db_table == "blog_documents")
		{
			$vol = user_hash($this->media->userID);
			$this->base_path = "sysdata/user/$vol/{$this->media->account}/blog/doc/{$this->media->path}";
			$this->convert_path = "sysdata\\user\\$vol\\{$this->media->account}\\blog\\doc\\{$this->media->path}";
			$this->xml_path = "$FS_QUEUE/{$this->media->id}_blog_documents.xml";	
		}
        else
		{
			$refCourseID  = ($this->media->refCourseID) ? $this->media->refCourseID : $this->media->courseID;
			$vol = course_hash($refCourseID);		
            $this->base_path = "sysdata/$vol/$refCourseID/doc/{$this->media->pathID}";
			$this->convert_path = "sysdata\\$vol\\$refCourseID\\doc\\{$this->media->pathID}";			
			$this->xml_path = "$FS_QUEUE/{$this->media->id}.xml";	
		}		
    }
}
//*** lms end***//

//*** www.powercam.cc start***//
class slide_media extends fsMedia {
	public function show($page=0, $autoplay=0, $area_size='724x544', $fixed_size=0)
	{
		global $_SERVER;
		$html = "";
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   	
		$isIphone = (strpos(" $user_agent", "iPad") > 0 || strpos(" $user_agent", "iPhone") > 0) ? 1 : 0;
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;	
		if ($isIphone)
		{
			list($area_w, $area_h) = explode('x', $area_size);			
			$html = $this->showHTML5(($area_w-4).'x'.($area_h-4), $fixed_size);
		}
		else
		{
			$html = $this->showSWF($area_size, 0, $page, $fixed_size, $autoplay);
		}
		$html .= "<script>var {$this->swf_id} = new fsmedia({swf_id:'{$this->swf_id}', doc_id:'{$this->media->id}'});</script>";
		return $html;
	}
	
	public function showEmbed($area_size='644x484', $link_back='')
	{		
		global $msgSyntax,$mCfg,$msgEmbed,$msgVideoSize,$msgWidth,$msgHeight,$msgRealSize,$msgAspectRatio;
		$rw = $rh = $w = $h = 0;		
		$embed = $this->showSWF($area_size, 1, 0, 0, 0, $link_back);
		if ($this->play_type == 'mp4' || $this->play_type == 'fs2mp4' || $this->play_type == 'flv')
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-23;
		}
		else
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-4;
		}	
		$id = $this->media->id;
		return "<span id=swf_embed_{$id}>
					<input type=text id=emSrc_{$id} name=emSrc_{$id} style='width:119px' value=\"$embed\" class=embedsrc onclick='this.select()' readonly autocomplete=off> <a id=showEmbed_{$id} name=showEmbed_{$id}><img align=absmiddle border=0 title='$mCfg' src='/sys/res/icon/tool.gif'></a>
				</span>				
				<script>	
					\$j(document).ready(function(){
						\$j('#showEmbed_{$id}').click(function(){
							var _body  = \"<div id=embed_{$id} name=embed_{$id}>\";
								_body += \"<div><span class=em>$msgVideoSize</span> <input type=checkbox id=emAspect name=emAspect value='1' checked onclick=\\\"changeAspect('{$id}')\\\" /> $msgAspectRatio</div>\";
								_body += \"<div>$msgWidth <input type=text id=emWidth name=emWidth value='{$w}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 1)\\\"/> $msgHeight <input type=text id=emHeight name=emHeight value='{$h}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 2)\\\"/></div>\";
								_body += \"<div class=em style='padding-top:15px'>$msgRealSize</div>\";
								_body += \"<div>$msgWidth <input type=text id=rWidth name=rWidth value='{$rw}' style='width:40px' disabled/> $msgHeight <input type=text id=rHeight name=rHeight value='{$rh}' style='width:40px' disabled/></div>\";
								_body += \"<input id=emType name=emType value='$this->play_type' type=hidden />\";
								_body += \"<input id=emEmbed name=emEmbed value=\\\"$embed\\\" type=hidden />\";
								_body += \"<input id=emW name=emW value='$w' type=hidden />\";
								_body += \"<input id=emH name=emH value='$h' type=hidden />\";
								_body += \"</div>\";
								var a = \$area($('swf_embed_{$id}'));
								\$showPopup('$msgEmbed', _body, a.left-40, a.top + a.height, 194);
						});
					});				
				</script>
				";
	
	}	
	
    protected function setBasePath()
    {		
		global $_SERVER;
		$slide = $this->media;
		$NUM_VOL = 100;		
		switch ($this->media->remoteType)
		{
			case 1://lms
				$vol = $this->media->cid % $NUM_VOL;
				if ($this->media->playtype == "flv")
					$this->base_host = ($this->media->remoteDataURL) ? $this->media->remoteDataURL : $this->media->remoteURL;
				else
					$this->base_host = $this->media->remoteURL;
				$this->base_path = "sysdata/$vol/{$this->media->cid}/doc/{$this->media->pathID}";
				break;
			case 2://lms blog
				$vol = $this->media->posterID % $NUM_VOL;
				if ($this->media->playtype == "flv")
					$this->base_host = ($this->media->remoteDataURL) ? $this->media->remoteDataURL : $this->media->remoteURL;
				else{
					$this->base_host = $this->media->remoteURL;
				}
				$this->base_path = "sysdata/user/$vol/{$this->media->poster}/blog/doc/{$this->media->pathID}";
				break;
			case 3://portal
				$vol = $this->media->posterID % $NUM_VOL;
				$this->base_host = $this->media->remoteURL;
				$this->base_path = "sysdata/$vol/{$this->media->poster}/doc/{$this->media->pathID}";
				break;
			case 4://speech
			case 5:
				$vol = $this->media->posterID % $NUM_VOL;			
				if ($this->media->playtype == "video" || $this->media->playtype == "flv")
					$this->base_host = ($this->media->remoteDataURL) ? $this->media->remoteDataURL : $this->media->remoteURL;
				else	
					$this->base_host = $this->media->remoteURL;					
				$this->base_path = "sysdata/$vol/{$this->media->poster}/doc/{$this->media->pathID}";
				break;
			case 6://mod
				$vol = $this->media->posterID % $NUM_VOL;
				$this->base_host = $this->media->remoteURL;	
				$this->base_path = "sysdata/$vol/{$this->media->poster}/doc/{$this->media->pathID}";
				break;
			default://local
				$vol = $this->media->blogID % $NUM_VOL;				
				$this->base_host = $_SERVER['HTTP_HOST'];	
				$this->base_path = "sysdata/$vol/{$this->media->blog}/doc/{$this->media->pathID}";
				break;
		}
    }
}

class slide_fsConverter extends fsConverter {        
    protected function setBasePath()
    {	
		global $FS_QUEUE;
		$NUM_VOL = 100;
		$vol = $this->media->blogID % $NUM_VOL;		
		$this->base_path = "sysdata/$vol/{$this->media->blog}/doc/{$this->media->pathID}";	
		$this->convert_path = "sysdata\\$vol\\{$this->media->blog}\\doc\\{$this->media->pathID}";			
		$this->xml_path = "$FS_QUEUE/{$this->media->id}.xml";	
    }
}
//*** www.powercam.cc end***//



//*** speech start ***//
class speech_media extends fsMedia { 
	public function show($page=0, $autoplay=0, $area_size='724x544', $fixed_size=0)
	{
		global $_SERVER;
		$html = "";
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   	
		$isIphone = (strpos(" $user_agent", "iPad") > 0 || strpos(" $user_agent", "iPhone") > 0) ? 1 : 0;
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;	
		if ($isIphone)
		{
			list($area_w, $area_h) = explode('x', $area_size);			
			$html = $this->showHTML5(($area_w-4).'x'.($area_h-4), $fixed_size);
		}
		else
		{
			$html = $this->showSWF($area_size, 0, $page, $fixed_size, $autoplay);
		}
		$html .= "<script>var {$this->swf_id} = new fsmedia({swf_id:'{$this->swf_id}', doc_id:'{$this->media->id}'});</script>";
		return $html;
	}
	public function showEmbed($area_size='644x484', $link_back='')
	{						
		global $msgSyntax,$mCfg,$msgEmbed,$msgVideoSize,$msgWidth,$msgHeight,$msgRealSize,$msgAspectRatio;
		$rw = $rh = $w = $h = 0;		
		$embed = $this->showSWF($area_size, 1, 0, 0, 0, $link_back);
		if ($this->play_type == 'mp4' || $this->play_type == 'fs2mp4' || $this->play_type == 'flv')
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-23;
		}
		else
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-4;
		}	
		$id = $this->media->id;

		return "<span id=swf_embed_{$id}>
					<a id=showEmbed_{$id} name=showEmbed_{$id} href='javascript:void(0)'>$msgEmbed</a>
				</span>				
				<script>	
					\$j(document).ready(function(){
						\$j('#showEmbed_{$id}').click(function(){
							var _body  = \"<div id=embed_{$id} name=embed_{$id}>\";
								_body += \"<div class=em>$msgSyntax: <input id=emSrc_{$id} name=emSrc_{$id} type=text onclick='this.select()' class=Text style='width:220px; color:#333' value=\\\"$embed\\\" readonly autocomplete=off></div>\";
								_body += \"<div><span class=em>$msgVideoSize</span> <input type=checkbox id=emAspect name=emAspect value='1' checked onclick=\\\"changeAspect('{$id}')\\\" /> $msgAspectRatio</div>\";
								_body += \"<div>$msgWidth <input type=text id=emWidth name=emWidth value='{$w}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 1)\\\"/> $msgHeight <input type=text id=emHeight name=emHeight value='{$h}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 2)\\\"/></div>\";
								_body += \"<div class=em style='padding-top:15px'>$msgRealSize</div>\";
								_body += \"<div>$msgWidth <input type=text id=rWidth name=rWidth value='{$rw}' style='width:40px' disabled/> $msgHeight <input type=text id=rHeight name=rHeight value='{$rh}' style='width:40px' disabled/></div>\";
								_body += \"<input id=emType name=emType value='$this->play_type' type=hidden />\";
								_body += \"<input id=emEmbed name=emEmbed value=\\\"$embed\\\" type=hidden />\";
								_body += \"<input id=emW name=emW value='$w' type=hidden />\";
								_body += \"<input id=emH name=emH value='$h' type=hidden />\";								
								_body += \"</div>\";
								var a = \$area($('fmTools'));								
								\$showPopup('$msgEmbed', _body, a.left, a.top+a.height, 260);
						});
					});					
				</script>
				";
	}	
    protected function setBasePath()
    {	
		global $CACHE_SERVER;
		$vol = user_hash($this->media->posterID);		
		$this->base_path = "sysdata/$vol/{$this->media->poster}/doc/{$this->media->pathID}";
		if ($CACHE_SERVER)
		{			
			if ($this->media->playtype == "swf")
			{		
				$s_url = get_cache_server_url($this->media);
				if ($s_url) $this->base_host = $s_url;
			}
		}		
    }
}

class speech_fsConverter extends fsConverter {        
    protected function setBasePath()
    {	
		global $FS_QUEUE;
		$vol = user_hash($this->media->posterID);
		$this->base_path = "sysdata/$vol/{$this->media->poster}/doc/{$this->media->pathID}";
		$this->convert_path = "sysdata\\$vol\\{$this->media->poster}\\doc\\{$this->media->pathID}";
		$this->xml_path = "$FS_QUEUE/{$this->media->id}.xml";
    }
}
//*** speech end***//
//*** tms start ***//
class tms_media extends fsMedia { 
	public function show($page=0, $autoplay=0, $area_size='724x544', $fixed_size=0)
	{
		global $_SERVER;
		$html = "";
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   	
		$isIphone = (strpos(" $user_agent", "iPad") > 0 || strpos(" $user_agent", "iPhone") > 0) ? 1 : 0;
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;	
		if ($isIphone)
		{
			list($area_w, $area_h) = explode('x', $area_size);			
			$html = $this->showHTML5(($area_w-4).'x'.($area_h-4), $fixed_size);
		}
		else
		{
			$html = $this->showSWF($area_size, 0, $page, $fixed_size, $autoplay);
		}
		$html .= "<script>var {$this->swf_id} = new fsmedia({swf_id:'{$this->swf_id}', doc_id:'{$this->media->id}', log_id:'{$this->log_id}'});</script>";						  
		return $html;
	}
	public function showFull($page=0, $autoplay=0, $area_size='724x544', $fixed_size=0)
	{
		global $_SERVER, $msgOpenFs;
		$user_agent = $_SERVER["HTTP_USER_AGENT"];			   		
		$isIphone = (strpos(" $user_agent", "iPad") > 0 || strpos(" $user_agent", "iPhone") > 0) ? 1 : 0;
		$isAndroid = (strpos(" $user_agent", "Android") !== false) ? 1 : 0;	

		$html = $this->show($page, $autoplay, $area_size, $fixed_size);
		if ($this->media->playtype == "fs" || $this->media->playtype == "swf"
		|| $this->media->playtype == "flv" || $this->media->playtype == "fs2mp4")
		{
			if (!$isIphone)
				$html .= "<div style='width:644px; text-align:right'>$msgOpenFs <a href='javascript:{$this->swf_id}.getCurrSlide(\"$doc->id\")'><img border=0 align=absmiddle src='/sys/res/icon/zoom.jpg'></a></div>";
		}			
		return $html;
	}
	public function showEmbed($area_size='644x484', $link_back='')
	{						
		global $msgSyntax,$mCfg,$msgEmbed,$msgVideoSize,$msgWidth,$msgHeight,$msgRealSize,$msgAspectRatio;
		$rw = $rh = $w = $h = 0;		
		$embed = $this->showSWF($area_size, 1, 0, 0, 0, $link_back);
		if ($this->play_type == 'mp4' || $this->play_type == 'fs2mp4' || $this->play_type == 'flv')
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-23;
		}
		else
		{
			$rw = $this->player_width; $w = $rw-4; 
			$rh = $this->player_height; $h = $rh-4;
		}	
		$id = $this->media->id;

		return "<span id=swf_embed_{$id}>
					<a id=showEmbed_{$id} name=showEmbed_{$id} href='javascript:void(0)'>$msgEmbed</a>
				</span>				
				<script>	
					\$j(document).ready(function(){
						\$j('#showEmbed_{$id}').click(function(){
							var _body  = \"<div id=embed_{$id} name=embed_{$id}>\";
								_body += \"<div class=em>$msgSyntax: <input id=emSrc_{$id} name=emSrc_{$id} type=text onclick='this.select()' class=Text style='width:220px; color:#333' value=\\\"$embed\\\" readonly autocomplete=off></div>\";
								_body += \"<div><span class=em>$msgVideoSize</span> <input type=checkbox id=emAspect name=emAspect value='1' checked onclick=\\\"changeAspect('{$id}')\\\" /> $msgAspectRatio</div>\";
								_body += \"<div>$msgWidth <input type=text id=emWidth name=emWidth value='{$w}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 1)\\\"/> $msgHeight <input type=text id=emHeight name=emHeight value='{$h}' style='width:40px' onkeyup=\\\"createEmbed('{$id}', 2)\\\"/></div>\";
								_body += \"<div class=em style='padding-top:15px'>$msgRealSize</div>\";
								_body += \"<div>$msgWidth <input type=text id=rWidth name=rWidth value='{$rw}' style='width:40px' disabled/> $msgHeight <input type=text id=rHeight name=rHeight value='{$rh}' style='width:40px' disabled/></div>\";
								_body += \"<input id=emType name=emType value='$this->play_type' type=hidden />\";
								_body += \"<input id=emEmbed name=emEmbed value=\\\"$embed\\\" type=hidden />\";
								_body += \"<input id=emW name=emW value='$w' type=hidden />\";
								_body += \"<input id=emH name=emH value='$h' type=hidden />\";								
								_body += \"</div>\";
								var a = \$area($('fmTools'));								
								\$showPopup('$msgEmbed', _body, a.left, a.top+a.height, 260);
						});
					});					
				</script>
				";
	}	
    protected function setBasePath()
    {	
		global $CACHE_SERVER;
		$refCourseID  = ($this->media->refCourseID) ? $this->media->refCourseID : $this->media->courseID;
		$vol = course_hash($refCourseID);				
		$this->base_path = "sysdata/$vol/{$refCourseID}/doc/{$this->media->pathID}";
		if ($CACHE_SERVER)
		{			
			if ($this->media->playtype == "swf")
			{		
				$s_url = get_cache_server_url($this->media);
				if ($s_url) $this->base_host = $s_url;
			}
		}		
    }
}

class tms_fsConverter extends fsConverter {        
    protected function setBasePath()
    {	
		global $FS_QUEUE;		
		$refCourseID  = ($this->media->refCourseID) ? $this->media->refCourseID : $this->media->courseID;
		$vol = course_hash($refCourseID);
		$this->base_path = "sysdata/$vol/{$refCourseID}/doc/{$this->media->pathID}";
		$this->convert_path = "sysdata\\$vol\\{$refCourseID}\\doc\\{$this->media->pathID}";
		$this->xml_path = "$FS_QUEUE/{$this->media->id}.xml";
    }
}
//*** tms end***//
?>