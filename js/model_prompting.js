const paragraphPersonalizationPrompt = (originalParagraph, interests, temperature=0.2) => {
    const configuration_prompt = "You are an AI agent, that adapts content on the blog, based on the interests of a person. You are giving the topic and categories the person is interested in, the original content of the blog post. Using this info you need to adapt the content of the paragraph of a post so that it is more engaging to the person based on their interests. Make sure to preserve the original message, style and information of the post, while making the content more engaging for the reader."
    const prompt = configuration_prompt + "\n\n[Personal Interests]" + interests + "\n\n[Original Paragraph]" + originalParagraph + "\n\n[Subtly personalized paragraph]"
    return prompt;
}

// TODO: refactor this to use the new proxy_request endpoint

const promptModel = (originalParagraph, interests, temperature=0.2) => {
    const prompt = paragraphPersonalizationPrompt(originalParagraph, interests, temperature);

    // Load the jQuery library if it's not already loaded
    if (!window.jQuery) {
        let script = document.createElement('script');
        script.type = "text/javascript";
        script.src = "https://code.jquery.com/jquery-3.7.0.js";
        document.getElementsByTagName('head')[0].appendChild(script);
    }

    return new Promise((resolve, reject) => {
        jQuery(document).ready(($) => {
            // Make the AJAX request
            $.post(
                my_ajax_object.ajax_url, 
                {
                    'action': 'proxy_request',
                    'prompt': prompt
                }, 
                (response) => {
                    // Parse the response as JSON
                    let res = JSON.parse(response);
                
                    // Check if an error occurred
                    if ('error' in res) {
                        // Handle the error
                        console.error(res.error);
                        reject(new Error(res.error));
                    } else {
                        // If no error occurred, resolve the promise with the response content
                        console.log(res.content);
                        resolve(res.content);
                    }
                }
            ).fail((jqXHR, textStatus, errorThrown) => { 
                // Handle failure
                reject(new Error(`Request failed: ${textStatus} ${errorThrown}`)); 
            });
        });
    });
}
