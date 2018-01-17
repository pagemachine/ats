/**
  * Contactmanager main JavaScript for Backend Module
  */
require(
    [
        'jquery',
        'datatables',
        'twbs/bootstrap-datetimepicker',
        'TYPO3/CMS/Ats/ckeditor/ckeditor',
        'TYPO3/CMS/Ats/LanguageFields'
    ],
    function($) {

     var ApplicationsModule = {};

     // Initialize dataTables
     ApplicationsModule.initializeDataTables = function() {
         $('.applications-list').DataTable({
            'searching' : false,
            'paging' : false

        });
     };

     //Initialize datepickers
     ApplicationsModule.initializeDatePickers = function() {
     	$('.ats-datepicker-date').datetimepicker({
     		'format' : 'YYYY-MM-DD'
     	});

     	$('.ats-datepicker-datetime').datetimepicker({
     		'format' : 'YYYY-MM-DD HH:mm'
     	})
     };

     $(document).ready(function() {
         // Initialize the view
         ApplicationsModule.initializeDataTables();
         ApplicationsModule.initializeDatePickers();

        languageskills.init();

        $("#tx-ats-languageform").on("click", '*[data-action="addLanguage"]', function(e) {
            languageskills.addLanguageFormPart();
        });

        $("#tx-ats-languageform").on("click", '*[data-action="removeLanguage"]', function(e) {
            languageskills.removeLanguage($(e.target).data("language"));
        });

        //Replace textarea with editor if it exists
        if ($("#ats-ck-body").length) {
            CKEDITOR.replace("ats-ck-body", {
                extraPlugins: 'placeholderPresets',
                placeholderPresets: {
                    useKeyAsSelector: true,
                    placeholderKey: '#ats-ck-body'
                }
            });
        }

        //Mass Notification module

        $('.pdfDownload').click( function(){
			$(this).remove();
        });

		$('#downloadAll').click( function(){
			$('.pdfDownload').each(function(){
				var win = window.open($(this).attr('href'), '_blank');
				if (win) {
					$(this).click();
				} else {
				    alert('Please allow popups for this website');
				    return false;
				}
			});
		});

		$('.selectAll').click(function(){
			if($(this).is(":checked")){
				$('.checkbox').prop('checked', true);
			}else{
				$('.checkbox').prop('checked', false);
			}
		});

     });

});
