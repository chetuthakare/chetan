/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

require(['jquery', 'Magento_Ui/js/modal/modal','jquery/ui', 'mage/translate', 'jquery/validate']
    , function ($) {
        $(function () {
               
                jQuery('#templatedesigns_template_grid_customization_template').val('Customization Template');
                jQuery("#templatedesigns_template_grid_image_path").change(function () {

                    if (this.files && this.files[0]) {
                        var reader = new FileReader();
                        reader.onload = imageIsLoaded;
                        reader.readAsDataURL(this.files[0]);
                    }
                });
                $( "#save" ).click(function() {        
                    if(!$('#edit_form').validation('isValid')){
                        return false;
                    }
                    else{
                        $('#save').attr('disabled','disabled');
                    }
                });
                $( "#saveAndContinueEdit" ).click(function() {        
                    if(!$('#edit_form').validation('isValid')){
                        return false;
                    }
                    else{
                        $('#save').attr('disabled','disabled');
                    }
                });
                function imageIsLoaded(e) {
                    if(jQuery('#templatedesigns_template_grid_image_path_image').length){
                        $('#templatedesigns_template_grid_image_path_image').attr('src',e.target.result);
                    }
                    else{
                        if(!(jQuery('.small-image-preview').length)){
                            jQuery("<img id'templatedesigns_template_grid_image_path_image' src='"+e.target.result+"' class='small-image-preview v-middle' width='25' height='25'>").insertBefore(jQuery( "#templatedesigns_template_grid_image_path" ));
                        }
                        else{
                            $('.small-image-preview').attr('src',e.target.result);
                        }
                    }

                };

                jQuery('#templatedesigns_template_grid_add_new_images').click(function () {
                   
                    var html_image = '<input id="templatedesigns_template_grid_image_path" name="image_path2" data-ui-id="templatedesigns-template-tab-fieldset-element-file-image-path" value="Personalization/Template/h/t/httpswww.lijnenspecialist.nlm2imagesprr220_4.jpg" title="Image" type="file" class="input-file">';
                    
                    $('.admin__field.field-add_new_images').append(html_image); 
                    
                });
        });


});
