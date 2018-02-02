$(document).ready(function() {

    languageskills.init();

    $("#tx-ats-languageform").on("click", '*[data-action="addLanguage"]', function(e) {
        languageskills.addLanguageFormPart();
    });

    $("#tx-ats-languageform").on("click", '*[data-action="removeLanguage"]', function(e) {
        languageskills.removeLanguage($(e.target).data("language"));
    });

    $("#tx-ats-fileupload").on("change", function(e){
        if (e.target.value != "") {
            $("#tx-ats-fileupload-button").show();
        }
        else {
            $("#tx-ats-fileupload-button").hide();
        }
    });
});
