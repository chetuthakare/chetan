/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

define([
    'Magento_Ui/js/form/element/abstract',
    'mageUtils',
    'jquery',
    'jquery/colorpicker/js/colorpicker'
    ], function (Element, utils, $) {
        'use strict';

        return Element.extend({
            defaults: {
                visible: true,
                label: '',
                error: '',
                uid: utils.uniqueid(),
                disabled: false,
                links: {
                    value: '${ $.provider }:${ $.dataScope }'
                }
            },

            initialize: function () {
                this._super();
            },

            initColorPickerCallback: function (element) {
                var self = this;

                $(element).ColorPicker({
                    onSubmit: function(hsb, hex, rgb, el) {
                        self.value(hex);
                        $(el).ColorPickerHide();
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor(this.value);
                    },
                    onChange: function(hsb, hex, rgb, el) {
                        self.value(hex);
                        $(el).ColorPickerHide();
                    },
                }).bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });
            }
        });
});
