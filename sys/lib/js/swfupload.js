function uploadClose(bReload)
{
    $("body").removeChild($("swfuo"));
    if (bReload == false) $("body").removeChild($("swfu_xxx"));
    else window.location.reload();
}


function frow(id, name, size, status)
{
    var css = "style='font-size:11px'";
    if (status == "limit" || status == "quota") css = " style='font-size:11px; color:#ccc'";
    var h = "<table width=100% " + css + " border=0><tr><td>" + name + "</td><td width=50px align=right>" + $size(size) + "</td><td width=50px align=right><div id=status" + id + ">" + status + "</div></td></tr></table>";
    return "<div id=file" + id + " style='border-bottom:1px solid #ccc;'>" + h + "</div>";
}



var SWFUpload = function (init_settings) {
	this.initSWFUpload(init_settings);
};

SWFUpload.prototype = {
    initSWFUpload: function (init_settings) {
    	try {
    		this.settings = {};
    		this.eventQueue = [];
    		this.movieName = "SWFUpload_" + SWFUpload.movieCount++;
    		this.swf = null;
    		SWFUpload.instances[this.movieName] = this;
    		
    		this.initSettings(init_settings);
    		this.loadFlash();
    	} catch (ex2) {
    		alert(1 +  ex2);
    	}
    },
    
    initSettings: function (init_settings) {
    	// Upload backend settings
    	this.addSetting("upload_url",		 		init_settings.upload_url,		  		"");
    	this.addSetting("file_post_name",	 		init_settings.file_post_name,	  		"Filedata");
    	this.addSetting("post_params",		 		init_settings.post_params,		  		{});
    	
    	this.addSetting("quota",                    init_settings.quota,                    "1024000");
        this.addSetting("left",                     init_settings.left  ,                   "0");
        this.addSetting("top",                      init_settings.top  ,                    "0");
        this.addSetting("type",                     init_settings.type  ,                   "attach");
    	// File Settings
    	this.addSetting("file_types",			  	init_settings.file_types,				"*.*");
    	this.addSetting("file_types_description", 	init_settings.file_types_description, 	"All Files");
    	this.addSetting("file_size_limit",		  	init_settings.file_size_limit,			"1024");
    	this.addSetting("flash_url",		  	    init_settings.flash_url,			    "swfupload.swf");
    

    	// Event Handlers
    	this.fileDialogComplete_handler	= SWFUpload.fileDialogComplete;
    	this.uploadStart_handler		= SWFUpload.uploadStart;
    	this.uploadProgress_handler		= SWFUpload.uploadProgress;
    	this.uploadComplete_handler		= SWFUpload.uploadComplete;
    	
    	
    	
    	this.flashReady_handler         = SWFUpload.flashReady;	// This is a non-overrideable event handler
    	this.swfUploadLoaded_handler    = SWFUpload.swfUploadLoaded;
    	this.fileDialogStart_handler	= SWFUpload.fileDialogStart;
    	this.fileQueued_handler			= SWFUpload.fileQueued;
    	this.fileQueueError_handler		= SWFUpload.fileQueueError;
    	
    	this.uploadError_handler		= SWFUpload.uploadError;
    	this.uploadSuccess_handler		= SWFUpload.uploadSuccess;
    	this.debug_handler				= SWFUpload.debug;
    },
    
    loadFlash: function () {
    	var html, target_element, container;
    	target_element = document.getElementsByTagName("body")[0];
    	container = document.createElement("div");
    	container.id = "swfuo";
    	container.style.width = "1px";
    	container.style.height = "1px";
    
    	target_element.appendChild(container);
    	container.innerHTML = this.getFlashHTML();
    	this.swf = $(this.movieName);
    },
    
    getFlashHTML: function () {
    	var html = "";
    
    	if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) {
    		html = '<embed type="application/x-shockwave-flash" src="' + this.getSetting("flash_url") + '" width="1px" height="1px"';
    		html += ' id="' + this.movieName + '" name="' + this.movieName + '" ';
    		html += 'bgcolor="#FFFFFF" quality="high" menu="false" flashvars="';
    		html += this.getFlashVars();
    		html += '" />';
    	} else {
    		// Build the basic Object tag
    		html = '<object id="' + this.movieName + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="1px" height="1px">';
    		html += '<param name="movie" value="' + this.getSetting("flash_url") + '">';
    		html += '<param name="bgcolor" value="#FFFFFF" />';
    		html += '<param name="quality" value="high" />';
    		html += '<param name="menu" value="false" />';
    		html += '<param name="flashvars" value="' + this.getFlashVars() + '" />';
    		html += '</object>';
    	}
    	return html;
    },
    
    getFlashVars: function () {
    	var param_string = this.buildParamString();
    	var html = "";
    	html += "movieName=" + encodeURIComponent(this.movieName);
    	html += "&uploadURL=" + encodeURIComponent(this.getSetting("upload_url"));
    	html += "&params=" + encodeURIComponent(param_string);
    	html += "&filePostName=" + encodeURIComponent(this.getSetting("file_post_name"));
    	html += "&fileTypes=" + encodeURIComponent(this.getSetting("file_types"));
    	html += "&fileTypesDescription=" + encodeURIComponent(this.getSetting("file_types_description"));
    	html += "&fileSizeLimit=" + encodeURIComponent(this.getSetting("file_size_limit"));
    	html += "&fileUploadLimit=0";
    	html += "&fileQueueLimit=0";
    	html += "&debugEnabled=false";
    
    	return html;
    },
    
    buildParamString: function () {
    	var post_params = this.getSetting("post_params");
    	var param_string_pairs = [];
    	var i, value, name;
    
    	if (typeof(post_params) === "object") {
    		for (name in post_params) {
    			if (post_params.hasOwnProperty(name)) {
    				if (typeof(post_params[name]) === "string") {
    					param_string_pairs.push(encodeURIComponent(name) + "=" + encodeURIComponent(post_params[name]));
    				}
    			}
    		}
    	}
    
    	return param_string_pairs.join("&");
    },
    
    flashReady: function () {
        var swf = $(this.movieName);
    	if (swf === null || typeof(swf.StartUpload) !== "function") {
    		alert("ExternalInterface methods failed to initialize.");
    		return;
    	}
    	
    	var self = this;
    	if (typeof(self.flashReady_handler) === "function") {
    		this.eventQueue[this.eventQueue.length] = function() { self.flashReady_handler(); };
    		setTimeout(function () { self.execNextEvent();}, 0);
    	} else {
    		alert("flashReady_handler event not defined");
    	}
    },


    
    addSetting:     function(name, value, dft) { this.settings[name] = (value) ? value : dft; },
    getSetting:     function(name) { return (this.settings[name]) ? this.settings[name] : ""; },

    execNextEvent:  function() { var  f = this.eventQueue.shift(); if (typeof(f) === "function") f(); },



    fileDialogStart: function() {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileDialogStart_handler(); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },
    
    fileQueued: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileQueued_handler(file); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },
    
    fileQueueError: function(file, error_code, message)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() {  self.fileQueueError_handler(file, error_code, message); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },

    fileDialogComplete: function(num_files_selected)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileDialogComplete_handler(num_files_selected); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },
    
    uploadStart: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.returnUploadStart(self.uploadStart_handler(file)); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    returnUploadStart: function(return_value) { this.swf.ReturnUploadStart(return_value); },
    uploadProgress: function (file, bytes_complete, bytes_total)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadProgress_handler(file, bytes_complete, bytes_total); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadError: function(file, error_code, message)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadError_handler(file, error_code, message); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadSuccess: function(file, server_data)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadSuccess_handler(file, server_data); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadComplete: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadComplete_handler(file); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    debug: function (message)
    {
        var self = this;
    	setTimeout(function () { self.execNextEvent();}, 0);
    },


    
    selectFile:     function () { this.swf.SelectFile(); },
    selectFiles:    function () { this.swf.SelectFiles(); },
    
    startUpload:    function(file_id) { var swf = this.swf; setTimeout( function () { swf.StartUpload(file_id); }, 0 ); },
    cancelUpload:   function(file_id) { this.swf.CancelUpload(file_id); },
    stopUpload:     function() { this.swf.StopUpload(); },
    getStats:       function() { return this.swf.GetStats(); },
    getFile:        function(file_id) { return (typeof(file_id) === "number") ? this.swf.GetFileByIndex(file_id) : this.swf.GetFile(file_id); }
}




SWFUpload.instances = {};
SWFUpload.movieCount = 0;
SWFUpload.type = "attach";


SWFUpload.flashReady = function () {
	try {
		if (typeof(this.swfUploadLoaded_handler) === "function") {
			this.swfUploadLoaded_handler();
		}
	} catch (ex) {
		alert(4 + ex);
	}
};




SWFUpload.fileDialogComplete = function (num_files_selected)
{
    if (num_files_selected == 0) return;

    var swfFilesSize = 0;
    var maxFileSize = this.getSetting("file_size_limit") * 1024;
    var swfMaxFilesSize = this.getSetting("quota") * 1024;
	
    var el = $E("div");
    $("body").appendChild(el);
    
    el.id = "swfu_xxx";
    var st = el.style;
    st.background = "#efe";
    st.padding = "5px";
    st.border = "1px solid #333";
    st.position = "absolute";
    st.fontSize = "11px";
    st.width = "360px";
    st.overflowY = "auto";
    st.left = this.getSetting("left");
    st.top = this.getSetting("top");
    el.innerHTML  = "<table width=100% style='font-size:11px' border=0><tr><td>file</td><td width=50px>size</td><td width=50px>status</td></tr></table>";
    el.innerHTML += "<div id=swfu_area style='height:190px; overflow-y:auto; background:#fff; border:1px solid #aaa;'></div>";
	

    var h = "";
    var cnt = 0;
    var fquota = false;
    var flimit = false;
    for (i=0; ; i++)
    {
        var f = this.getFile(i);
        if (!f) break;
        
        var fsize = parseInt(f.size, 10);
        if (swfFilesSize+fsize > swfMaxFilesSize)
        {
            h += frow(f.id, f.name, f.size, "quota");
            this.cancelUpload(f.id);
            fquota = true;
            continue;
        }
            
        if (fsize > maxFileSize)
        {
            h += frow(f.id, f.name, f.size, "limit");
            flimit = true;
            continue;
        }
             
        swfFilesSize += fsize;
        cnt ++;
        h += frow(f.id, f.name, f.size, "");
    }
    $("swfu_area").innerHTML = h;
    
    
    var info = "<div style='padding:2px; font-size:11px; color:#000'>" + cnt + " files (" + $size(swfFilesSize) + ")";
    if (fquota) info += ", quota=" + $size(swfMaxFilesSize);
    if (flimit) info += ", size limit=" + $size(maxFileSize);
    info += "</div>";
    $("swfu_xxx").innerHTML += info;
    
    if (cnt == 0)
    {
        $("swfu_xxx").innerHTML += "<div style='text-align:center'><input type=button onclick='uploadClose(false)' value=close></div>";
        return;
    }
	this.startUpload();
}

SWFUpload.uploadStart = function(file)
{
    $("swfu_area").scrollTop = $area($("file" + file.id)).top - 68;
    return true;
}

SWFUpload.uploadProgress = function(fileObj, bytesLoaded)
{
	var percent = Math.ceil((bytesLoaded / fileObj.size) * 100);
    $("status" + fileObj.id).innerHTML = percent + "%";
    
    var w = $("swfu_area").offsetWidth;
    $("file" + fileObj.id).style.background = "#ffa url(/sys/res/icon/progbar.png) no-repeat -" + (w - percent) + "px 0";
    
    if (percent == 100)
    {
    	$("file" + fileObj.id).style.background = "#ffc url(/sys/res/icon/progbar.png) no-repeat -" + (w - percent) + "px 0";
	    $("status" + fileObj.id).innerHTML = "<img src='/sys/res/icon/checkmark.gif'>";
	}
}

SWFUpload.uploadComplete = function(fileObj) {
	if (this.getStats().files_queued > 0) {
		this.startUpload();
	} else {
	    if (this.getSetting("type") == "zip")
	    {
	        alert("upload complete!");
	        $("body").removeChild($("swfu_xxx"));
	        $("body").removeChild($("swfuo"));
	        return;
	    }
	    
	    $("swfu_xxx").innerHTML += "<div style='text-align:center'><input type=button onclick='uploadClose(true)' value=close></div>";
	}
}






SWFUpload.swfUploadLoaded = function () { };
SWFUpload.fileDialogStart = function () { };
SWFUpload.fileQueued = function (file) { };
SWFUpload.fileQueueError = function (file, error_code, message) {};

SWFUpload.uploadSuccess = function (file, server_data) { };
SWFUpload.uploadError = function (file, error_code, message) { };

