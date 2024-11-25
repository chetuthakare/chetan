require(['jquery', 'Magento_Ui/js/modal/modal','jquery/ui', 'mage/translate', 'jquery/validate']
    , function ($) {
        $(function () {
            window.setTimeout(function(){
                if(jQuery('#templatedesigns_template_font_font_data').length > 0){
                    var html = '<span style="padding: 5px;background-color: #eb5202;margin-right: 10px;color: #eee;">&#10004;</span>';
                    $('#templatedesigns_template_font_font_file').before(html); 
                }
            },500)
        });

});
