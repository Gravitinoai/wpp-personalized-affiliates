console.log('debug-panel.js loaded');

jQuery(document).ready(function($) {
    // toggle highlight functionality
    $('#highlight-paff').on('click', function() {
        $("a[name='paff']").parent().addClass('highlight');
    });
});
