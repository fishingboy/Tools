/**
 * jQuery template 的擴充
 * 讓 template 直接回傳 html string
 * 使用方法：把原本 $('tmpl_name').tmpl 改為 $('tmpl_name').tmpl_html
 * @author Leo.Kuo
 */
(function($)
{
    $.fn.tmpl_html = function(data)
    {
        return this.template(this.attr('id'))($, {data :data}).join('');
    };
})(jQuery);
