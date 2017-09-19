var languageskills = (function(window) {

    var formSelector = "#tx-ats-languageform";
    var languagesSelector = "#languages"
    var languageFormPart = undefined;

    var elementPrefix = "tx_ats[application][languageSkills]";
    var elementCount = 99;

    /**
     * Init
     * Takes the prepared form part to add new languages, stores it and removes it from DOM
     *
     * @return {undefined}
     */
    function init() {

        languageFormPart = window.document.querySelector(formSelector + " .tx-ats-addlanguage");
        if (languageFormPart != undefined) {
            languageFormPart.remove();
        }
        
    }

    /**
     * Shows the form elements for a new languageskill
     *
     * @return {undefined}
     */
    function addLanguageFormPart() {
        var newFormPart = languageFormPart.cloneNode(true);

        newFormPart.dataset.language = elementCount;
        newFormPart.getElementsByClassName("language-select")[0].setAttribute("name", elementPrefix + "[" + elementCount + "]" + "[language]");
        newFormPart.getElementsByClassName("textlanguage")[0].setAttribute("name", elementPrefix + "[" + elementCount + "]" + "[textLanguage]");
        newFormPart.getElementsByClassName("level-select")[0].setAttribute("name", elementPrefix + "[" + elementCount + "]" + "[level]");
        newFormPart.getElementsByClassName("remove-button")[0].dataset.language = elementCount;

        window.document.querySelector(formSelector + " " + languagesSelector).appendChild(newFormPart);
        elementCount++;
    }

    /**
     * Removes a language
     *
     * @param  {Number} languageId
     * @return {undefined}
     */
    function removeLanguage(languageId) {


        var languageElement = window.document.querySelector(formSelector + " #languages *[data-language='" + languageId + "']");

        if (languageElement != undefined) {

            languageElement.remove();
        }
    }

    return {
        init : init,
        addLanguageFormPart : addLanguageFormPart,
        removeLanguage : removeLanguage
    };

}(window));