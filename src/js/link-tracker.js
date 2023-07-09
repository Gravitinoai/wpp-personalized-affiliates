var jq = jQuery.noConflict();

// Event 1: Custom onContentLoaded event
jq(document).on('onContentLoaded', function() {
    var $firstLink = jq('p#paff:first a:first');
    var link = $firstLink.attr('href');

    sendAjaxRequest('page_loaded', link, $firstLink);
});

// Event 2: Click event on the first link of the first p#paff
jq(document).on('click', 'p#paff:first a', function(e) {
    e.preventDefault(); // Prevent the default action of the click

    var $firstLink = jq(this);
    var link = $firstLink.attr('href');

    sendAjaxRequest('link_click', link, $firstLink);
});

function sendAjaxRequest(action, link, $element) {
    jq.post(
        link_tracker.ajax_url,
        {
            action : action,
            link : link,
            originalText : textProcessor.getOriginalText(),
            modifiedText : textProcessor.getModifiedText(),
            isPersonalized : textProcessor.isPersonalized,
            personalInterests : textProcessor.getPersonalInterests(),
            linkText : $element.text(),
            sourceUrl : window.location.href,
            timestamp : new Date().getTime(),
            userAgent : navigator.userAgent,
            postId : textProcessor.postId,
            postTitle : textProcessor.postTitle,
            postCategory : textProcessor.postCategory,
            viewId : textProcessor.viewId
        },
        function(response) {
            window.open(link, '_blank');
        }
    ).fail(function() {
        console.log("Ajax request failed");
        window.open(link, '_blank');
    });
}
