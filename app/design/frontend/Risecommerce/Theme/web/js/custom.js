require(['jquery', 'mage/apply/main'], function($) {
    'use strict';

    function updateFilterOptions() {
        const elements = document.querySelectorAll('.sidebar .filter-options .filter-options-item.inactive');
        elements.forEach(function(element) {
            if (element.classList.contains('inactive')) {
                element.classList.remove('inactive');
                element.classList.add('active');
            }
        });  
    }

    // Run on initial page load
    $(document).ready(function() {  
        updateFilterOptions();
    });  

    // Run after any AJAX requests that update the page content
    $(document).on('contentUpdated', function() {
        updateFilterOptions();
    });
});  
