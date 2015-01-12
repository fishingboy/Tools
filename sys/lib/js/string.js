function trim(str)
{
    return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}
            
function space2comma(str)
{
    return trim(str).replace(/\s+/g, ', ');;
}

function comma2space(str)
{
    return trim(str.replace(/\s*,\s*/g, ' ').replace(/\s+/g, ' '));
}

function formatKeywords(str)
{
    return trim(str.replace(/\s*,\s*/g, ', ').replace(/\s+/g, ' '));
}