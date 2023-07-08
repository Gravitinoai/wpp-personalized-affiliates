//main.js
console.log('Post category: ' + postCategory);
console.log('Cat count: ', cat_views);

class TextProcessor {
  constructor() {
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

  togglePersonalization() {
    this.isPersonalized = !this.isPersonalized;
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
    const catViewsStr = Object.entries(cat_views)
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

    this.modifiedText = "aaaaaaaa";
    let onModifiedTextObtained = new CustomEvent("onModifiedText", {
        detail: this.modifiedText,
    });
    document.dispatchEvent(onModifiedTextObtained);

    // Once you have this method uncommented, make sure that it correctly handles setting modified_text
    // promptModel(original_text, interests)
    //     .then(response => {
    //         console.log('Original paragraph:\n' + original_text + '\n\nPersonalized paragraph:\n' + response);
    //         modified_text = response;
    //         document.dispatchEvent(onModifiedTextObtained);
    //     })
    //     .catch(error => {
    //         console.error(error);
    //     });

    this.updateTextDisplay();
}

  updateTextDisplay() {
    const paffBlock = document.getElementById("paff");
    paffBlock.innerHTML = this.isPersonalized ? this.modifiedText : this.originalText;
  }
}

const textProcessor = new TextProcessor();
