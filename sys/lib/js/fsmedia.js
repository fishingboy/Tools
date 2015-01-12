//for flash player //
function fsmedia(args)
{
	if (!args) var args = {};
	this.doc_id = args.doc_id;
	this.swf_id = args.swf_id;	
	this.from = args.from || 'content';
	this.log_id = args.log_id || 0;
	var _this = this;	
	
	this.checkCurr = function(){		
		var n = _this.getCurrSlideNum();	
		if (n > 0) _this.setCurrSlide(n);			
		setTimeout(function(){_this.checkCurr()}, 500);
	}
	
	this.getCurrSlideNum = function(){		
		var n=0;
		var flashMovie = window.document[_this.swf_id];		
		if (flashMovie)
		{
			try {
				n = flashMovie.jsplaySlide();
			} catch(err) {}		
		}		
		return n;
	}	
	
	this.setCurrSlide = function(page){
		var jid = "#medieIndex_" + _this.doc_id;	
		currSlide = $j(jid + " .curr");
		if (currSlide)
		{
			currSlide.removeClass("curr");
		}
		var obj = $j("#slide_" + _this.doc_id + "_" + page);
		if (!obj) return;
		obj.addClass("curr");	
	}
	
	this.getPlayIsFinish = function(){	
		var bool=0;
		var flashMovie = window.document[_this.swf_id];
		if (flashMovie)
		{
			try {
				bool = flashMovie.jsIsFinish();
			} catch(err) {}			
		}	
		return bool;	
	}
	
	this.getCurrSlide = function(id){
		var flashMovie = window.document[_this.swf_id];
		var n = 0;
		try {
			n = flashMovie.jsplaySlide();
		} catch(err) {}
		_this.openFS(n, true);
	}
	
	// need do slide.php //
	this.openFS = function(page, nojsplay){
		var ie = /*@cc_on!@*/false;
		var nojsplay = nojsplay | false;
		if (!ie)
		{
			alert("Only IE browser is supported to play PowerCam raw format '.fs'!");
			return;
		}	
		if (page > 0)
		{
			var flashMovie = window.document[_this.swf_id];
			try {
				if (!nojsplay) flashMovie.jsplay(page);
				flashMovie.jsstop();	
			} catch (err){}
		}
		//bgsound 
		BGS = 'Off';
		if (ie)
		{		
			var _e = $j("#bgs").get(0);
			if (_e)
			{
				_e.volume = -10000;
				_e.src = "";
			}
			
		}
		var divBGS = $j('#divBGS').get(0);
		if (divBGS) divBGS.innerHTML = '';
		var btBGS = $j('#btBGS').get(0);
		if (btBGS) btBGS.src='/sys/res/icon/soundOff.gif';
		
		var from_var = '&from=' + _this.from;
		var log_id_var = '&logID=' + _this.log_id;
		
		//open window
		var url = "/slide.php?id=" + _this.doc_id + from_var + log_id_var + "&slide=" + page; 
		var width = screen.width;
		var height = screen.height;
		var left=0, top=0;
		if ((width == 1024 && height == 768) || (width == 1280 && height == 1024))
		{
			window.open(url, null, "width=" + width + ", height=" + height + ", left=" + left + ", top=" + top + ", menubar=no, resizable=yes, status=yes, titlebar=no, toolbar=no");
			return;
		}
		// 50px: powercam inner title, 80px IE url and status bar height
		width = screen.availWidth - 10;  //1024
		height = Math.floor(((screen.availWidth - 270)/4)*3) + 50;
		if (height + 80 > screen.availHeight) //wide screen PC
		{
			height = screen.availHeight - 80;
			width = Math.floor(((height - 50)*4)/3) + 270;
			left = (screen.width - width) / 2;
		}
		window.open(url, null, "width=" + width + ", height=" + height + ", left=" + left + ", top=0, menubar=no, resizable=yes, status=yes, titlebar=no, toolbar=no");
	}
	
	this.playSWF = function(n){	
		this.setCurrSlide(n);	
		var flashMovie = window.document[_this.swf_id];
		try {
			flashMovie.jsplay(n);			
		} catch (err){}
	}	
	
	this.expendSlide = function(){			
		var _o = $j('#hiddenOutline_' + _this.doc_id).get(0);
		if (_o.style.display == 'none')
		{
			_o.style.display = 'block';
			$j('#moreslide_' + _this.doc_id).get(0).innerHTML = '[less]';
		}
		else
		{
			_o.style.display = 'none';
			$j('#moreslide_' + _this.doc_id).get(0).innerHTML = '... [more]';
		}
	}
	
	this.getPos = function(){
		var pos = 0;
		var flashMovie = window.document[_this.swf_id];
		try {
			pos = flashMovie.getpos();
		} catch (err){}
		return pos;
	}
	
	this.setPos = function(pos){
		
		var flashMovie = window.document[_this.swf_id];
		try {
			flashMovie.setpos(pos);				
		} catch (err){}
	}
}




//for embed //
function changeAspect(id)
{
	var jid = "#embed_" + id; 
	var emType = $j(jid+' #emType').val();
	var shift = (emType == 'mp4' || emType == 'fs2mp4' || emType == 'flv') ? 23 : 4;	
	var w = parseInt($j(jid+' #emW').val());	
	var h = parseInt($j(jid+' #emH').val());
	var ew = parseInt($j(jid+' #emWidth').val());	
	var rw = 0; var rh = 0;		
	eh = parseInt(ew * h / w);
	rw = ew + 4;
	rh = eh + shift;
	$j(jid+' #emHeight').val(eh);
	$j(jid+' #rHeight').val(rh);
	chengeEmbed(id, rw, rh);
}
function createEmbed(id, type)
{
	var jid = "#embed_" + id; 
	var emType = $j(jid+' #emType').val();
	var w = parseInt($j(jid+' #emW').val());	
	var h = parseInt($j(jid+' #emH').val());
	var ew = parseInt($j(jid+' #emWidth').val());
	var eh = parseInt($j(jid+' #emHeight').val());
	var rw = 0; var rh = 0;
	var shift = (emType == 'mp4' || emType == 'fs2mp4' || emType == 'flv') ? 23 : 4;
	var aspect = $j(jid + ' #emAspect').attr('checked');			
	if (aspect)
	{
		if(type == 1)//width
		{                
			eh = parseInt(ew * h / w);
			rh = eh + shift; rw = ew + 4;
			$j(jid + ' #emHeight').val(eh);
			$j(jid + ' #rHeight').val(rh);
			$j(jid + ' #rWidth').val(rw);
		}
		else if (type == 2) //height
		{
			ew = parseInt(eh * w / h);				
			rh = eh + shift; rw = ew + 4;
			$j(jid + ' #emWidth').val(ew);
			$j(jid + ' #rWidth').val(rw);
			$j(jid + ' #rHeight').val(rh);
		}
	}
	else
	{
		rh = eh + shift; rw = ew + 4;
		$j(jid + ' #rHeight').val(rh);
		$j(jid + ' #rWidth').val(rw);
	}               
	chengeEmbed(id, rw, rh);
}

function chengeEmbed(id, rw, rh)
{
	var jid = "#embed_" + id;
	var embed = $j(jid + " #emEmbed").val();
	var ew_str = "width='" + rw + "'";
	var eh_str = "height='" + rh + "'";
	var	start = embed.indexOf("width='", 0);
	var end = embed.indexOf("'", start + 7);
	if (!end) return;
	width_str = embed.slice(start, end + 1);	
	var	start = embed.indexOf("height='", 0);
	var end = embed.indexOf("'", start + 8);
	if (!end) return;
	height_str = embed.slice(start, end + 1);	
	var html = embed;
	if (width_str != ew_str) html = m_replaceAll(embed, width_str, ew_str);
	if (height_str != eh_str) html = m_replaceAll(html, height_str, eh_str);
	$j("#emSrc_" + id).val(html);
}

function m_replaceAll(strOrg,strFind,strReplace){
	var index = 0;
	while(strOrg.indexOf(strFind,index) != -1){
		strOrg = strOrg.replace(strFind,strReplace);
		index = strOrg.indexOf(strFind,index);
	}
	return strOrg
}