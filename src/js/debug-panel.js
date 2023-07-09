//debug-panel.js

var jq = jQuery.noConflict();

jq(document).ready(function($) {
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

    populateInterests();
    // Event handler for the 'interests-select' dropdown
    $('#user-interests-selector').change(function() {
        let selectedInterests = $(this).val(); // Returns an array of the selected option values
        textProcessor.setPersonalInterests(selectedInterests.map(id => taxonomy[id]));
    });
});

// Function to populate the interests dropdown from taxonomy
function populateInterests() {
    let interestsSelect = document.getElementById('user-interests-selector');
    for (let key in simple_taxonomy) {
        let option = document.createElement('option');
        option.value = key;
        option.text = simple_taxonomy[key];
        interestsSelect.appendChild(option);
    }
}

document.addEventListener('onInterestsChanged', function(e) {
    setPersonalInterests(e.detail);
});

function setPersonalInterests(interests) {
    let personalInterests = document.getElementById('personal-interests');
    personalInterests.innerHTML = textProcessor.interestsFull;
}

/// Use the beforeunload event to remove the listener when the user is about to leave the page.
window.addEventListener('beforeunload', function() {
    document.removeEventListener('onInterestsChanged', setPersonalInterests);
});

const simple_taxonomy = {
    1: 'Arts & Entertainment',
    57: 'Autos & Vehicles',
    86: 'Beauty & Fitness',
    103: 'Business & Industrial',
    126: 'Computers & Electronics',
    149: 'Finance',
    172: 'Food & Drink',
    180: 'Games',
    196: 'Hobbies & Leisure',
    207: 'Home & Garden',
    215: 'Internet & Telecom',
    226: 'Jobs & Education',
    239: 'Law & Government',
    243: 'News',
    250: 'Online Communities',
    254: 'People & Society',
    263: 'Pets & Animals',
    272: 'Real Estate',
    275: 'Reference',
    279: 'Science',
    289: 'Shopping',
    299: 'Sports',
    332: 'Travel & Transportation'
  };