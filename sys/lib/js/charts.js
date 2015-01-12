var chartIDAry = Array();
var chartWidthAry = Array();
var chartHeightAry = Array();	
var chartSrcAry = Array();
var chartAryIdx = 0;

function setChartList(cid, ww, hh, src)
{	    
    chartIDAry[chartAryIdx] = cid;
    chartWidthAry[chartAryIdx] = ww;	
    chartHeightAry[chartAryIdx] = hh;	
    chartSrcAry[chartAryIdx++] = src;		
}

function showChartList()
{
    var cid;
    var ww;
    var hh;
    var src;
    
    for (i=0; i<chartAryIdx; i++)
    {
        cid = chartIDAry[i];
        ww = chartWidthAry[i];
        hh = chartHeightAry[i];
        src = chartSrcAry[i];       
        
        AC_FL_RunContent('ctrl', '_chart' + cid, 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0','width', ww,'height',hh,'src',src,'quality','high', 'wmode', 'opaque', 'pluginspage','http://www.macromedia.com/go/getflashplayer','movie',src);	   
    }
}    