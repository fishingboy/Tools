$EvtListener(window, "load", $extentFunction);

function $extentFunction()
{
    var txtArea = document.getElementsByTagName("textarea");
    for (var i=0; i<txtArea.length; i++)
    {
        txtArea[i].val = $txtAreaVal;     
    }
}

function $txtAreaVal() { return $nl2br($strip_tags($trim(this.value))); }

function $strip_tags(input, allowed) 
{
    // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); 
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        
    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) 
    {
        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

function $trim(input) { return input.replace(/(^\s*)|(\s*$)/g, ""); }

function $getVal(id) { return $(id).val(); }