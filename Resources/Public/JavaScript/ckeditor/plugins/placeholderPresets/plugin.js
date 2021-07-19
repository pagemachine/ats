CKEDITOR.plugins.add("placeholderPresets", {
    lang: "en,de",
    version: 1.0,
    requires: 'placeholder',
    bbcodePluginLoaded: false,
    init: function (editor) {

        var CKEditorSetup = {};
        var lang = editor.lang.placeholderPresets;

        var defaultConfig = {
            placeholderKey: 'all', //Which placeholders to load
            useKeyAsSelector: false //If set to true, they key will not be used directly but as a selector. It should have the attribute data-placeholders
        }
        var config = CKEDITOR.tools.extend(defaultConfig, editor.config.placeholderPresets || {}, true);

        CKEditorSetup.editorSettings = {
            'placeholders': {
                'all': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.fields.date, 'fields.date'],
                    [lang.markers.fields.time, 'fields.time'],
                    [lang.markers.fields.confirmDate, 'fields.confirmDate'],
                    [lang.markers.fields.building, 'fields.building'],
                    [lang.markers.fields.room, 'fields.room'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ],
                'reply': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ],
                'invite': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.fields.date, 'fields.date'],
                    [lang.markers.fields.time, 'fields.time'],
                    [lang.markers.fields.confirmDate, 'fields.confirmDate'],
                    [lang.markers.fields.building, 'fields.building'],
                    [lang.markers.fields.room, 'fields.room'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ],
                'acknowledge': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ],
                'reject': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ],
                'videoInvitation': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.application.job.jobNumber, 'application.job.jobNumber'],
                    [lang.markers.fields.date, 'fields.date'],
                    [lang.markers.fields.appointmentFrom, 'fields.appointmentFrom'],
                    [lang.markers.fields.appointmentUntil, 'fields.appointmentUntil'],
                    [lang.markers.fields.url, 'fields.url'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ]
            }
        };

        var key = config.placeholderKey;
        if (config.useKeyAsSelector == true) {
            key = document.querySelector(key).getAttribute('data-placeholders');
        }

        CKEDITOR.on('dialogDefinition', function(event) {
          if ('placeholder' == event.data.name) {
            var input = event.data.definition.getContents('info').get('name');
            input.type = 'select';
            input.items = CKEditorSetup.editorSettings.placeholders[key];
            input.setup = function() {
              this.setValue(CKEditorSetup.editorSettings.placeholders[key][0][1]);
            };
          }
        });
    }
});
