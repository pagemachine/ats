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
            'paging' : false,
        });

        var applicationsTable = $('.applications-ajax-list').DataTable({
            serverSide: true,
            ajax: {
                url: TYPO3.settings.ajaxUrls['ats_applications_list'],
                data: function(d) {
                    d.statusValues = {};
                    $("#applications-ajax-filter input:checkbox[name=status]").each(function(index){
                        if (this.checked) {
                            d.statusValues[this.value] = 1;
                        }
                        else {
                            d.statusValues[this.value] = 0;
                        }
                    });
                }
            },
            columns: [
                {name: 'uid', data : 'uid'},
                {name: 'crdate', data : 'crdate'},
                {name: 'name', data : 'surname'},
            ]
        });

         $("#applications-ajax-filter input").change(function(){
            applicationsTable.ajax.reload();
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

        // Destroy the dataTable on submit to keep the right order in the BFCache
        $('.checkbox-table-form').on('submit', function(){
            $(this).find('.applications-list').dataTable().fnDestroy();
        })

		$('.selectAll').click(function(){
			if($(this).is(":checked")){
				$('.checkbox').prop('checked', true);
			}else{
				$('.checkbox').prop('checked', false);
			}
		});

        $('.newApplications').click(function(){
            if($(this).is('.active')){
                $('input.status_10').prop('checked', false);
                $(this).removeClass('active btn-primary');
            }else{
                $('input.status_10').prop('checked', true);
                $(this).addClass('active btn-primary');
            }
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

});
