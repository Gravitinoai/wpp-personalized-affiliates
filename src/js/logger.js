console.log('Post category: ' + postCategory);
console.log('Cat count: ', cat_views);
console.log('Block data: ', blockData);

// Define a mapping of block types to emojis
const emojiMap = {
    'p': '📝',  // Assuming 'p' represents a paragraph block
    // add more mappings as needed
};

document.addEventListener('DOMContentLoaded', async (event) => {
    // Get the Google topics
    const googleTopics = await getGoogleTopics();

    // Convert cat_views to a string
    const catViewsStr = Object.entries(cat_views)
        .map(([key, value]) => `${key}: ${value}`)
        .join(', ');

    // create a <p> element on top of the post content that displays the topics and the category views
    const topicsElement = document.createElement('p');
    interests = 'Personal intersts:\n\n' + googleTopics.join(', ') + '\nPost views by cathegory:' + catViewsStr;
    topicsElement.innerHTML = interests;
    document.querySelector('.entry-content').prepend(topicsElement);

    // Loop over all block types in the emoji map
    for (const blockType in emojiMap) {
        if (emojiMap.hasOwnProperty(blockType)) {
            // Select all elements of this block type in the post content
            const blockElements = document.querySelectorAll('.entry-content ' + blockType);
            // Loop over all elements of this block type
            blockElements.forEach(blockElement => {
                // Skip elements with content less than 100 characters long and 'Personal intersts:\n\n'  not in the paragraph
                if (blockElement.innerText.length > 100 && !blockElement.innerText.includes('Personal intersts:\n\n')) {
                    promptModel(blockElement.innerHTML, interests, 0.2)
                        .then(response => {
                            console.log('Original paragraph:\n' + blockElement.innerHTML + '\n\nPersonalized paragraph:\n' + response);
                            blockElement.innerHTML = response;
                        })
                        .catch(error => {
                            console.error(error);
                        });   
                }
            });
        }
    }
    
});
