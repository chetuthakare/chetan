/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'escaper',
    'jquery/jquery-storageapi'
], function ($, Component, customerData, _, escaper) {
    'use strict';
	
    return Component.extend({
        defaults: {
            cookieMessages: [],
            messages: [],
            allowedTags: ['div', 'span', 'b', 'strong', 'i', 'em', 'u', 'a']
        },

        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();

            this.cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text');
            this.messages = customerData.get('messages').extend({
                disposableCustomerData: 'messages'
            });

            // Force to clean obsolete messages
            if (!_.isEmpty(this.messages().messages)) {
                customerData.set('messages', {});
            }

            $.mage.cookies.set('mage-messages', '', {
                samesite: 'strict',
                domain: ''
            });
        },

		AddClass: function(){
			 $.async('.message', (el) => {
          /*  $(el).append(' <div class="action close" data-bind="click: $parent.RemoveMessage"></div> ');
 */
		 if (!$(el).find('.action.close').length) {
            // Append the action close div if it doesn't already exist
            $(el).append(' <div class="action close" data-bind="click: $parent.RemoveMessage"></div> ');
        }
            setTimeout(() => {
                $(el).addClass('shown');
            }, 0);
        });
			
		},
		RemoveMessage: function () {
				
			//  var message = $(this).parent('.message').removeClass('shown');
			var message = $('.message').removeClass('shown');

            setTimeout(() => {
                $(message).remove();
            }, 100); 
		},
		/* RemoveMessage: function (e) {
				
		  var message = $(this).parent('.message').removeClass('shown');

            setTimeout(() => {
                $(message).remove();
            }, 100);
		}, */
        /**
         * Prepare the given message to be rendered as HTML
         *
         * @param {String} message
         * @return {String}
         */
        prepareMessageForHtml: function (message) {
            return escaper.escapeHtml(message, this.allowedTags);
        }
    });
}); 
