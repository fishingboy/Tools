var ac_userKeyword = "";
var ac_suggestions = 0;
var ac_isKeyUpDownPressed = false;
var ac_autocompletedKeyword = "";
var ac_position = -1;
var ac_oCache = new Object();
var ac_input = "";
var ac_url = "";

function ac_noenter() { return !(window.event && window.event.keyCode == 13); }
function ac_start(id, url)
{
    if (id && url)
    {
        ac_input = $(id);
	    ac_input.setAttribute("autocomplete","off");
        ac_url = url;
        $addEvt(ac_input, "keyup", ac_handleKeyUp);
    }
    $addEvt(document, "click", ac_hideSuggestions);

    var s, el = $E("iframe");
    el.id = "ac_frame";
    el.frameBorder = 0;

    s = el.style;
    s.visibility = "hidden";
    s.position = "absolute";
    s.width = "200px";
    s.height = "0px";
    s.zIndex = "100";
    el.className = "ac_iframe";
    $append(el);

    el = $E("div");
    el.id = "ac_scroll";

    s = el.style;
    s.visibility = "hidden";
    s.position = "absolute";
    s.width = "200px";
    s.zIndex = "101";
    s.textAlign = "left";
    s.border = "1px solid #aaa";
    el.innerHTML = "<div id='ac_suggest'></div>";
    $append(el);

}

function ac_checkForChanges()
{
    var keywords = ac_input.value;  // x, x
    var tokens = keywords.split(","); // tokens[0]=x, tokens[0]= " x"
    var keyword = tokens[tokens.length-1]; // " x"
    keyword = keyword.replace(/^\s+|\s+$/g, ""); // "x"

    if(keyword == "")
    {
        ac_hideSuggestions();
        ac_userKeyword = "";
    }

    if((ac_userKeyword != keyword) && (!ac_isKeyUpDownPressed))
    {
        ac_getSuggestions(keyword);
    }
}

function ac_getSuggestions(keyword)
{
    if(keyword == "" && ac_isKeyUpDownPressed) return;
    ac_userKeyword = keyword;
    $load(ac_url, {keyword: keyword}, ac_onLoad);
}

function ac_onLoad(jsonArray)
{
    if(jsonArray) ac_displayResults(ac_userKeyword, jsonArray);
}

function ac_displayResults(keyword, results_array)
{    
    if(results_array.length == 0) return;

    ac_position = -1;
    ac_isKeyUpDownPressed = false;
    ac_suggestions = results_array.length;

    var div = "";
    for(var i=0; i<results_array.length; i++)
    {
        div += "<div style='padding:2px; cursor:pointer; background:#fff; font-size:12px; border-bottom:1px solid #eee' id=tr" + i + " onclick='ac_updateKeywordValue(this);' onmouseover='ac_mouseover(this);' onmouseout='ac_mouseout(this);'>" + results_array[i] + "</div>";
    }
    $("ac_suggest").innerHTML = div;
   
    var p = $area(ac_input);
    var e1 = $("ac_scroll");
    e1.style.visibility = "visible";
    e1.style.left = p.left + "px";
    e1.style.top = p.top + p.height + "px";

    var e2 = $("ac_frame");
    e2.style.height = e1.offsetHeight + "px";
    e2.style.visibility = "visible";
    e2.style.left = p.left + "px";
    e2.style.top = p.top + p.height + "px";
    
    if (is_smart_device() && parent.frames["if1"]) modifyHeight("if1", parent);
    
    if(results_array.length > 0) ac_autocompleteKeyword();
}

function ac_handleKeyUp(e, id, url)
{
    ac_input = (id) ? $(id) : ac_input;
    ac_url = (url) ? url : ac_url;
    
    var e = (!e) ? window.event : e;
    var target = (!e.target) ? e.srcElement: e.target;
    if(target.nodeType == 3)
        target = target.parentNode;
    var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
    
	if(e.type == "keyup")
    {
        ac_isKeyUpDownPressed = false;

        if (code == 27)
        {
            ac_hideSuggestions();
            return;
        }

        if(code == 13)      // enter;
        {
            if(ac_position >= 0)
            {
                 var newTR = $("tr" + ac_position);
                 ac_updateKeywordValue(newTR);
            }
            ac_hideSuggestions();
        }
        else if(code == 40)      // down
        {

            var newTR = $("tr" + (++ac_position));
            var oldTR = $("tr" + (--ac_position));

            if(ac_position >= 0 && ac_position < ac_suggestions - 1)
                oldTR.style.background = "#fff";

            if(ac_position < ac_suggestions-1)
            {
				ac_position++;
                newTR.style.background = "#ffc";
    //            ac_updateKeywordValue(newTR);
            }

            e.cancelBubble = true;
            e.returnValue = false;
            ac_isKeyUpDownPressed = true;
        }
        else if(code == 38)      // up
        {

            var newTR = $("tr" + (--ac_position));
            var oldTR = $("tr" + (++ac_position));
			
            if(ac_position >= 0 && ac_position <= ac_suggestions-1)
                oldTR.style.background = "#fff";
			
			
			if(ac_position == 0) ac_position--;
            if(ac_position > 0)
            {
                newTR.style.background = "#ffc";
    //           ac_updateKeywordValue(newTR);
                ac_position--;
            }
	
            e.cancelBubble = true;
            e.returnValue = false;
            ac_isKeyUpDownPressed = true;
        }
        else
        {
            ac_position = -1;
            ac_checkForChanges();
        }
    }
}

function ac_updateKeywordValue(el)
{
    var tokens = ac_input.value.split(",");
    var prior_keywords = "";
	
    for(var i = 0; i < tokens.length-1; i++)
    {
        if(tokens.length == 0) prior_keywords += Trim(tokens[i]);
        else 
		{
			if(i==0) prior_keywords += Trim(tokens[i]);
			else prior_keywords += ", " + Trim(tokens[i]);
		}
    }

    ac_input.value = (prior_keywords == "") ? el.innerHTML : prior_keywords + "," + el.innerHTML;
	
	if(ac_input.value.search(",") != -1)
	{
		tokens = ac_input.value.split(",");
		var t = tokens.length - 1;
		if(t != 0 )
		{
			prior_keywords = "";
			for(var j = 0; j < t; j++)
			{
				if(Trim(tokens[t]) != Trim(tokens[j]))
				{	
					if(j == 0) prior_keywords += tokens[j];
					else if( tokens[j] != "" )prior_keywords += ", " + tokens[j];
				}
			}
			if( prior_keywords != "" ) prior_keywords += ", " + tokens[t];
			else prior_keywords += tokens[t];
			ac_input.value = Trim(Trim(prior_keywords));
		}
	}
}

function Trim(strvalue) 
{ 
	return strvalue.replace(/^(,)|(^\s*)|(\s*$)/g,""); 
} 


function ac_mouseover(el)
{
    el.style.background = "#ffc";
    ac_position = el.id.substring(2);
}

function ac_mouseout(el)
{
    el.style.background = "#fff";
    ac_position = -1;
}

function ac_hideSuggestions()
{
    $("ac_frame").style.visibility = "hidden";
    $("ac_frame").style.top = 0;
    $("ac_frame").style.left = 0;
    
    $("ac_scroll").style.visibility = "hidden";
    $("ac_scroll").style.top = 0;
    $("ac_scroll").style.left = 0;
    $("ac_suggest").innerHTML = "";
    
    if (is_smart_device() && parent.frames["if1"]) reduceModalHeight("if1", parent);
}

function ac_autocompleteKeyword()
{
    var tokens = ac_input.value.split(",");
    ac_position = -1;
    ac_autocompletedKeyword = tokens[tokens.length-1].replace(/^\s+|\s+$/g, "");
}