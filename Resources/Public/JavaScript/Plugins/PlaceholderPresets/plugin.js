CKEDITOR.plugins.add("placeholderPresets", {
    lang: "en,de",
    version: 1.0,
    requires: 'placeholder',
    bbcodePluginLoaded: false,
    init: function (editor) {

        var CKEditorSetup = {};
        var lang = editor.lang.placeholderPresets;

        CKEditorSetup.editorSettings = {
            'placeholders': {
                'all': [
                    [lang.markers.application.salutation, 'application.salutation'],
                    [lang.markers.application.title, 'application.title'],
                    [lang.markers.application.firstname, 'application.firstname'],
                    [lang.markers.application.surname, 'application.surname'],
                    [lang.markers.application.job.title, 'application.job.title'],
                    [lang.markers.fields.date, 'fields.date'],
                    [lang.markers.fields.time, 'fields.time'],
                    [lang.markers.fields.confirmDate, 'fields.confirmDate'],
                    [lang.markers.fields.building, 'fields.building'],
                    [lang.markers.fields.room, 'fields.room'],
                    [lang.markers.backenduser.signature, 'backenduser.signature']
                ]
            }
        };
        CKEDITOR.on('dialogDefinition', function(event) {
          if ('placeholder' == event.data.name) {
            var input = event.data.definition.getContents('info').get('name');
            input.type = 'select';
            input.items = CKEditorSetup.editorSettings.placeholders['all'];
            input.setup = function() {
              this.setValue(CKEditorSetup.editorSettings.placeholders['all'][0][1]);
            };
          }
        });
    }
});
