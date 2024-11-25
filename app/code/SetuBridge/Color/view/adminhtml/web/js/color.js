/** Setubridge Technolabs
* http://www.setubridge.com/
* @author SetuBridge
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
**/

var colorChange;
require(['jquery'],function ($) {
    $(document).ready(function(){
        colorChange= window.setTimeout(colorEffect, 500);
    });
});
function colorEffect(){
    require(['jquery'],function ($) {
        var selector="table tr td.color-code";
        if($(selector).length){
            jQuery(selector).each(function(){
            
            var color_code = $.trim($(this).children('.data-grid-cell-content').text());
            $(this).children('.data-grid-cell-content').css({ "backgroundColor": '#'+color_code,"color" : "white","padding": "0 5px"}); 
            });
            
            window.clearTimeout(colorChange);
        }
        else{
            colorChange= window.setTimeout(colorEffect, 500);
        }
    });
}