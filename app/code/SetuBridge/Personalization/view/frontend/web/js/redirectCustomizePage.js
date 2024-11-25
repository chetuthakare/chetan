define([
    "jquery", 
    "Magento_Ui/js/modal/modal", 
    "mage/loader",
    "Magento_Customer/js/customer-data",
    'mage/translate'
    ], 
    function ($, modal, loader, customerData, $t) {
        "use strict";
        return function (config, node) {
            var product_id = jQuery(node).data("id");
            var product_url = jQuery(node).data("url");
            var configOptionsUpdateValuesArray = {};

            $("#customize-product-button").on("click", function (event) {
                event.preventDefault(); 

              //  if(jQuery("#product_addtocart_form").valid()){
                    $("body").trigger('processStart');
                    $("#customize-product-button").attr("disabled", true);
                    var button_text = $("#customize-product-button span").text();
                    $("#customize-product-button span").text($t("Please wait ..."));
                    $("#customize-product-button").attr('title', $t("Please wait ..."));


                    productConfigOptionsSetValues();

                    if(!$.isEmptyObject(configOptionsUpdateValuesArray)){

                        var configOptionsUpdateValuesAllProducts = {};
                        if(jQuery.type( JSON.parse(localStorage.getItem("configOptionsUpdateValuesArray"))) != "object"){
                            localStorage.removeItem('configOptionsUpdateValuesArray');
                        }
                        if(!jQuery.isEmptyObject(JSON.parse(localStorage.getItem("configOptionsUpdateValuesArray")))){
                            configOptionsUpdateValuesAllProducts = JSON.parse(localStorage.getItem("configOptionsUpdateValuesArray"));
                        }
                        configOptionsUpdateValuesAllProducts[product_id] = configOptionsUpdateValuesArray;
                        localStorage.setItem("configOptionsUpdateValuesArray", JSON.stringify(configOptionsUpdateValuesAllProducts));

                    }

                    /*var url = product_url + "data/"+JSON.stringify(configOptionsUpdateValuesArray);*/
                    window.location.href = product_url;
               // }
            });

            var productConfigOptionsSetValues = function () {

                jQuery('#product_addtocart_form .field.configurable select').each(function() {

                    var selectedAttrId = $(this).attr('id');
                    var selectedValue =  $(this).val();
                    var attributeId = selectedAttrId.replace(/[a-z]*/, '');

                    configOptionsUpdateValuesArray[attributeId] = selectedValue;
                });


            };        

        };
});
