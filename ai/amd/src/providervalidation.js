import Ajax from 'core/ajax';
export const getConnectionStatus = (providername) => Ajax.call([{
    methodname: 'core_ai_test_connection',
    args: {providername},
}])[0];

const Selectors = {
    VALIDATE: '[data-action="validate-ai-provider"]',
    VALIDATE_RESULTS: '[data-role="validate-ai-results"]',
};
const AIProviderValidator = class {
    /**
     * AI Provider type.
     */
    providerType;

    // /**
    //  * Context ID.
    //  * @type {Integer}
    //  */
    // contextId;

    /**
     *
     * @param {string} providerType
     // * @param {Integer }contextId
     */
    constructor(providerType) {//, contextId) {
        this.providerType = providerType;
        // this.contextId = contextId;
        window.console.log(this.providerType);
        this.registerEventListeners();
    }
    registerEventListeners() {

        const providerResultSelector = Selectors.VALIDATE_RESULTS + '[data-providertype="'+this.providerType+'"]';
        const resultsArea = document.querySelector(providerResultSelector);
        const validateButton = document.querySelector(Selectors.VALIDATE+ '[data-providertype="'+this.providerType+'"]');
        window.console.log(validateButton);
        if (validateButton) {
            window.console.log(`Adding click handler for ${this.providerType}`);
            validateButton.addEventListener('click', async (e) => {
                window.console.log("Clicked on provider validation ");
                window.console.log(this.providerType);
                e.preventDefault();
                const request = {
                    methodname: 'core_ai_test_connection',
                    args: {
                        providertype: this.providerType,
                    }
                };
                try {
                    const resultObj = await Ajax.call([
                        request
                    ])[0];
                    if (resultObj.error) {
                        window.console.log(resultObj.error);
                        return;
                    } else {
                        window.console.log(resultObj);
                        resultsArea.innerHTML = resultObj.message;
                    }
                } catch (error) {
                    window.console.log(error);
                }
                window.console.log("Test connection finished");
            });
        } else {
            window.console.log(`No validate button found for ${providerResultSelector}`);
        }
    }
};

export default AIProviderValidator;
