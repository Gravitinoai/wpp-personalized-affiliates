//debug-panel.js
console.log('debug-panel.js loaded');

jQuery(document).ready(function($) {
    textProcessor.togglePersonalization($('#toggle-personalization').is(':checked'));

    // Event handler for 'highlight-paff' checkbox
    $('#highlight-paff').change(function() {
        if (this.checked) {
            $("p#paff:first").addClass("highlight");
        } else {
            $("p#paff:first").removeClass("highlight");
        }
    });

    // Event handler for 'toggle-personalization' checkbox
    $('#toggle-personalization').change(function() {
        textProcessor.togglePersonalization(this.checked);
    });
});

document.addEventListener('onInterestsChanged', function(e) {
    setPersonalInterests(e.detail);
});

function setPersonalInterests(interests) {
    let personalInterests = document.getElementById('personal-interests');
    personalInterests.innerHTML = textProcessor.personalInterests;
}

/// Use the beforeunload event to remove the listener when the user is about to leave the page.
window.addEventListener('beforeunload', function() {
    document.removeEventListener('onInterestsChanged', setPersonalInterests);
});
