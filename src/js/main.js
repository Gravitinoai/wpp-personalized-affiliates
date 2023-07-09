//main.js
class TextProcessor {
  constructor() {
    this.postId = postID.post_id;
    this.postTitle = postTitle.post_title;
    this.postCategory = postCategory.category_name;
    this.catViews = cat_views.views;
    this.viewId = viewID.view_id;
    this.affiliate_partners = affiliatePartners.partners;

    console.log("Post id: " + this.postId);
    console.log("Post title: " + this.postTitle);
    console.log("Post category: " + this.postCategory);
    console.log("Post views: " + this.catViews);
    console.log("View id: " + this.viewId);
    console.log("Affiliate partners: " + this.affiliate_partners);

    this.originalText = "";
    this.modifiedText = "";
    this.isPersonalized = false;
    this.personalInterests = "";

    document.addEventListener("onOriginalText", this.handleOriginalText.bind(this));
    document.addEventListener("onModifiedText", this.handleModifiedText.bind(this));
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

  async handleDomContentLoaded(event) {
    const googleTopics = await getGoogleTopics();
    const catViewsStr = Object.entries(this.catViews)
        .map(([key, value]) => `${key}: ${value}`)
        .join(", ");
    const topicsElement = document.createElement("p");
    this.personalInterests = "Personal intersts:\n\n" +
        googleTopics.join(", ") +
        "\nPost views by cathegory:" +
        catViewsStr;

    let onInterestsChangedEvent = new CustomEvent("onInterestsChanged", {
        detail: this.personalInterests,
    });
    document.dispatchEvent(onInterestsChangedEvent);

    const paffBlock = document.getElementById("paff");
    this.originalText = paffBlock.innerHTML;

    let onOriginalTextObtained = new CustomEvent("onOriginalText", {
        detail: this.originalText,
    });
    document.dispatchEvent(onOriginalTextObtained);

    promptModel(this.originalText, this.personalInterests, this.affiliate_partners)
    .then(response => {
        console.log('Original paragraph:\n' + this.originalText + '\n\nPersonalized paragraph:\n' + response);
        this.modifiedText = response;
        
        let onModifiedTextObtained = new CustomEvent("onModifiedText", {
            detail: this.modifiedText,
        });
        document.dispatchEvent(onModifiedTextObtained);

        this.updateTextDisplay(); // Make sure to update the display after modifying the text
    })
    .catch(error => {
        console.error(error);
        this.modifiedText = this.originalText;
    });

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