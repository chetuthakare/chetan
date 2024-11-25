/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/
require(['jquery', 'Magento_Ui/js/modal/modal','jquery/ui', 'mage/translate', 'jquery/validate']
    , function ($) {
        $(function () {
            if(jQuery('#templatedesigns_template_templatedesigns_designs_image_image').length){
                $('.delete-image').remove();
            }
            $( "#save" ).click(function() {        
                if(!$('#edit_form').validation('isValid')){
                    return false;
                }
                else{
                    $('#save').attr('disabled','disabled');
                }
            });
            jQuery("#templatedesigns_template_templatedesigns_designs_image").change(function () {

                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                }
            });
            function imageIsLoaded(e) {
                if(jQuery('#templatedesigns_template_templatedesigns_designs_image_image').length){
                    $('#templatedesigns_template_templatedesigns_designs_image_image').attr('src',e.target.result);
                }
                else{
                    if(!(jQuery('.small-image-preview').length)){
                        jQuery("<img src='"+e.target.result+"' class='small-image-preview v-middle' width='25' height='25'>").insertBefore(jQuery( "#templatedesigns_template_templatedesigns_designs_image" ));
                    }
                    else{
                        $('.small-image-preview').attr('src',e.target.result);
                    }
                }

            };

        });

});
