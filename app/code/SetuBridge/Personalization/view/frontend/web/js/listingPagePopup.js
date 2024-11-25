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
            var options = {
                type: "popup",
                responsive: true,
                innerScroll: false,
                modalClass: 'product-designer-modal',
                title: $.mage.__("Designer"),
                buttons: [
                    {
                        text: $.mage.__("Close"),
                        class: "close-product-designer-modal",
                        click: function () {
                            this.closeModal();
                        },
                    },
                ],
            };
            var popup = modal(options, $("#quickViewContainer" + product_id));
            $("#quickViewButton" + product_id).on("click", function () {
                openQuickViewModal();
            });
            var openQuickViewModal = function () {
                var modalContainer = $("#quickViewContainer" + product_id);

                if(modalContainer.find('iframe').length < 1){
                    $("body").trigger('processStart');
                    $("#quickViewButton" + product_id).attr("disabled", true);
                    var button_text = $("#quickViewButton" + product_id+" span").text();
                    $("#quickViewButton" + product_id+" span").text($t("Please wait ..."));
                    $("#quickViewButton" + product_id).attr('title', $t("Please wait ..."));
                    modalContainer.html(createIframe());
                    var iframearea = "#new_frame" + product_id;
                    $(iframearea).on("load", function () {
                        modalContainer.addClass("product-quickview");
                        modalContainer.modal("openModal");
                        $("body").trigger('processStop');
                        $("#quickViewButton" + product_id).attr("disabled", false);
                        $("#quickViewButton" + product_id+" span").text(button_text);
                        $("#quickViewButton" + product_id).attr('title', button_text);
                    });
                }
                else{
                    modalContainer.modal("openModal");
                }
            };
            var createIframe = function () {
                return $("<iframe />", { id: "new_frame" + product_id, src: product_url, scrolling: 'yes',
                    frameborder:0,
                    width:'100%',
                    height:'100%',
                    style:'overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;top:0px;right:0;left:0;width:100%;bottom:0px;position:absolute;', 
                });
            };
        };
});
