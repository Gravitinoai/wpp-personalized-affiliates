# Adaptive affiliate promotion

This is a repository of a WordPress plugin that uses LLMs (large language AI models) and the reader's interestest to naturally embed the affiliate promotion text into the blogpost individual to every user.

- You can see it in action here: http://vladislavs2.sg-host.com/
- The hackathon sumbmition video: https://lablab.ai/event/google-vertex-ai-hackathon/rldf/hypertarget-personalization-revolution

## How to
Please, reach out to me (vlad@gravitinoai.com) to get the latest version of the production ready plugin or fill out the form on the website.

However, if you want to play around with the plugin yourself:

1. Colone the repository and make sure you have PHP and composer installed
2. In the terminal run `$make all`, which will generate `pers_content_plugin.zip`
3. Upload zip file to you WordPress plugins (Admin->Plugins->Add New->Upload)
4. Then, you need to generate google api credentials:\
    a. Go [here](https://cloud.google.com/vertex-ai) and enable your google vertex APIs\
    b. Then follow [this](https://stackoverflow.com/questions/46287267/how-can-i-get-the-file-service-account-json-for-google-translate-api) instructions to generate a json key
5. In WordPress admin got to Settings->Affiliate Personalization. Paste the content of the json into the settings field, add google project id and add some affiliate parterns.
6. Enjoy!