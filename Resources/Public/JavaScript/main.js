$(document).ready(function() {

    languageskills.init();

    $("#tx-ats-languageform").on("click", '*[data-action="addLanguage"]', function(e) {
        languageskills.addLanguageFormPart();
    });

    $("#tx-ats-languageform").on("click", '*[data-action="removeLanguage"]', function(e) {
        languageskills.removeLanguage($(e.target).data("language"));
    });
});