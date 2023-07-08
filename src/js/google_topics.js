function getGoogleTopics() {
    const runAsync = async () => {
        if ('browsingTopics' in document) {
            console.log('document.browsingTopics():\n\n✅ Supported');
        } else {
            console.log('⚠️ document.browsingTopics() not supported. Returning random topics.');
            return genDummyTopics();
        }
  
        if (document.featurePolicy.allowsFeature('browsing-topics')) {
            console.log('browsing-topics:\n\n✅ Allowed');
        } else {
            console.log('⚠️ browsing-topics not allowed. Returning random topics.');
            return genDummyTopics();
        }
  
        const topicsData = await (document).browsingTopics();
        let returnedTopics = [];
        for (const topic of topicsData) {
            returnedTopics.push(taxonomy[topic.topic]);
        }
        if (returnedTopics.length > 0) {
            console.log(returnedTopics);
            return returnedTopics;
        }

        console.log('⚠️ document.browsingTopics() not supported. Returning random topics.');
        return genDummyTopics();
      }
    
    return runAsync();
}

function genDummyTopics(){
    let dummyTopics = [];
    for (let i = 0; i < 3; i++) {
        dummyTopics.push(taxonomy[Math.floor(Math.random() * 349)]);
    }
    return dummyTopics;
}