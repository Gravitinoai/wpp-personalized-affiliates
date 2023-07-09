const paragraphPersonalizationPrompt = (originalParagraph, interests, affiliate_partners, temperature=0.7) => {
    const configuration_prompt = "You are an AI agent, that adapts content on the blog, based on the interests of a person. You are giving the topic and categories the person is interested in, the original content of one paragraph of the blog post, and a list of affiliate partners and links. Using this info you need to adapt the content of the paragraph of a post to try to sell a product of an affiliate partner based on one personal interest or topic. Add an affiliate link to one of the affiliate partners in the paragraph and integrate the link seemlessly into the content of the paragraph. Pick the affiliate partner who is most suitable to the interests of the user. include the link as an html href. e.g. <a href='https://www.amazon.com'>Amazon</a>"
    const prompt = configuration_prompt + "\n\n" + interests + "\n\nAffiliate Partners: " + affiliate_partners + "\n\nOriginal Paragraph: " + originalParagraph + "\n\n[Subtly personalized paragraph]"
    console.log("Prompt: " + prompt);
    return prompt;
}

const promptModel = (originalParagraph, interests, affiliate_partners, temperature=0.7) => {
    const prompt = paragraphPersonalizationPrompt(originalParagraph, interests, affiliate_partners, temperature);

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
