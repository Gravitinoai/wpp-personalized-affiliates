//main.js
class TextProcessor {
  constructor() {
    this.postId = postID.post_id;
    this.postTitle = postTitle.post_title;
    this.postCategory = postCategory.category_name;
    this.catViews = cat_views.views;
    this.viewId = viewID.view_id;

    this.catViewsStr = Object.entries(this.catViews)
      .map(([key, value]) => `${key}: ${value}`)
      .join(", ");

    this.originalText = "";
    this.modifiedText = "";
    this.isPersonalized = false;
    this.personalInterests = "";
    this.interestsFull = "";

    document.addEventListener("onOriginalText", this.handleOriginalText.bind(this));
    document.addEventListener("onModifiedText", this.handleModifiedText.bind(this));
    document.addEventListener("onInterestsChanged", this.callModel.bind(this));
    document.addEventListener("DOMContentLoaded", this.handleDomContentLoaded.bind(this));
  }

  getOriginalText() {
    return this.originalText;
  }

  getModifiedText() {
    return this.modifiedText;
  }

  getPersonalInterests() {
    return this.personalInterests;
  }

  togglePersonalization(isPersonalized) {
    this.isPersonalized = isPersonalized;
    this.updateTextDisplay();
  }

  handleOriginalText(event) {
    this.originalText = event.detail;
    this.updateTextDisplay();
  }

  handleModifiedText(event) {
    this.modifiedText = event.detail;
    this.updateTextDisplay();
  }

  async setRandomInterests(){
    const googleTopics = await getGoogleTopics();
    this.setPersonalInterests(googleTopics);
  }

  setPersonalInterests(interests) {
    this.personalInterests = interests;
    this.interestsFull = "Personal intersts:\n\n" +
      interests.join(", ") +
        "\nPost views by cathegory:" +
        this.catViewsStr;
      
    let onInterestsChangedEvent = new CustomEvent("onInterestsChanged", {
        detail: this.interestsFull,
    });
    document.dispatchEvent(onInterestsChangedEvent);
}

  async handleDomContentLoaded(event) {
    await this.setRandomInterests();
    const paffBlock = document.getElementById("paff");
    this.originalText = paffBlock.innerHTML;

    let onOriginalTextObtained = new CustomEvent("onOriginalText", {
        detail: this.originalText,
    });
    document.dispatchEvent(onOriginalTextObtained);

    this.modifiedText = "aaaaaaaa";
    let onModifiedTextObtained = new CustomEvent("onModifiedText", {
        detail: this.modifiedText,
    });
    document.dispatchEvent(onModifiedTextObtained);

    // // Once you have this method uncommented, make sure that it correctly handles setting modified_text
    // promptModel(this.original_text, this.personalInterests)
    //     .then(response => {
    //         console.log('Original paragraph:\n' + original_text + '\n\nPersonalized paragraph:\n' + response);
    //         modified_text = response;
    //         document.dispatchEvent(onModifiedTextObtained);
    //     })
    //     .catch(error => {
    //         console.error(error);
    //         this.modifiedText = this.originalText;
    //     });

    this.updateTextDisplay();

    let onContentLoaded = new CustomEvent("onContentLoaded", {
        detail: this.view_id
    });
    document.dispatchEvent(onContentLoaded);
}

  callModel(event) {
    if (this.interestsFull=="" || this.originalText=="") {
      return;
    }
    console.log("Calling model with interests: " + this.interestsFull);
    // prompting the model here
  }

  updateTextDisplay() {
    const paffBlock = document.getElementById("paff");
    paffBlock.innerHTML = this.isPersonalized ? this.modifiedText : this.originalText;
    let onTextChanged = new CustomEvent("onTextChanged", {
        detail: this.isPersonalized ? this.modifiedText : this.originalText,
    });
    document.dispatchEvent(onTextChanged);
  }
}

const textProcessor = new TextProcessor();
