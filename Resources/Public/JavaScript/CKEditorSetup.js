/**
  * CK Editor Setup (backend modules)
  */
require(['jquery', 'TYPO3/CMS/Ats/ckeditor/ckeditor'], function($) {

    var CKEditorSetup = {};

    CKEditorSetup.editorSettings = {
        'placeholders': {
            'reply': [
                ['Applicant Title', 'application.title'],
                ['Applicant Firstname', 'application.firstname'],
                ['Applicant Surname', 'application.surname'],
                ['Signature', 'backenduser.signature']
            ],
            'invite': [
                ['Applicant Title', 'application.title'],
                ['Applicant Firstname', 'application.firstname'],
                ['Applicant Surname', 'application.surname'],
                ['Date', 'fields.date'],
                ['Time', 'fields.time'],
                ['Confirmation Date', 'fields.confirmDate'],
                ['Building', 'fields.building'],
                ['Room', 'fields.room'],
                ['Signature', 'backenduser.signature']
            ],
            'acknowledge': [
                ['Applicant Title', 'application.title'],
                ['Applicant Firstname', 'application.firstname'],
                ['Applicant Surname', 'application.surname'],
                ['Signature', 'backenduser.signature']
            ],
            'attestation': [
                ['Applicant Title', 'application.title'],
                ['Applicant Firstname', 'application.firstname'],
                ['Applicant Surname', 'application.surname'],
                ['Signature', 'backenduser.signature']
            ],
            'reject': [
                ['Applicant Title', 'application.title'],
                ['Applicant Firstname', 'application.firstname'],
                ['Applicant Surname', 'application.surname'],
                ['Signature', 'backenduser.signature']
            ]
        }
    };

    //Setup ckeditor
    CKEditorSetup.initializeCKEditor = function() {

        //Add selectboxes to ckeditor placeholder plugin
        CKEDITOR.on('dialogDefinition', function(event) {
          if ('placeholder' == event.data.name) {
            var input = event.data.definition.getContents('info').get('name');
            input.type = 'select';
            //input.items = [['Title', 'title']];
            input.items = CKEditorSetup.editorSettings.placeholders[$('#ats-ck-body').data("placeholders")];
            input.setup = function() {
              this.setValue(CKEditorSetup.editorSettings.placeholders[$('#ats-ck-body').data("placeholders")][0][1]);
            };
          }
        });
    }

    $(document).ready(function() {

        CKEditorSetup.initializeCKEditor();
    });

});
