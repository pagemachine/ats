/**
  * Contactmanager main JavaScript for Backend Module
  */
require(
    [
        'jquery',
        'datatables'
    ],
    function($) {
    var dateOptions = { year: 'numeric', month: 'numeric', day: 'numeric' };

    var query = $('#applications-ajax-filter').data('query');

    var orderColumn = $('#applications-ajax-list th[data-column="' + query.orderBy + '"]').first().index();

    var detailUri = $("#applications-ajax-list").data("detail-uri");

    var applicationsTable = $('#applications-ajax-list').DataTable({
        serverSide: true,
        processing: true,
        language: {
            search: '',
            searchPlaceholder: 'Nach Name, E-Mail oder ID suchen...',
            loadingRecords: '&nbsp;',
            processing: '<div style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.8); text-align: center; padding-top: 25%;">...</div>'
        },
        ajax: {
            url: TYPO3.settings.ajaxUrls['ats_applications_list'],
            type: 'POST',
            data: function(d, settings) {
                query.orderBy = d.columns[d.order[0].column].data;
                query.orderDirection = d.order[0].dir;
                query.offset = d.start;
                query.limit = d.length;
                query.search = d.search.value;
                d.query = query;

                top.TYPO3.Storage.Persistent.set('atsApplications.query', JSON.stringify(query));

                var data = {
                    draw: d.draw,
                    query : d.query
                };
                d = data;
            }
        },
        columns: [
            {name: 'uid', data : 'uid'},
            {
                name: 'crdate',
                data: 'crdate',
                render: function(data, type, row, meta) {
                    date = new Date(data * 1000);
                    return date.toLocaleDateString('de-DE', dateOptions);
                }
            },
            {
                name: 'tstamp',
                data: 'tstamp',
                render: function(data, type, row, meta) {
                    date = new Date(data * 1000);
                    return date.toLocaleDateString('de-DE', dateOptions);
                }
            },
            {
                name: 'name',
                data: 'surname',
                render: function(data, type, row, meta) {
                    return "<b>" + row.surname + ", " + row.firstname + "</b>";
                }
            },
            {
                name: 'job',
                data: 'job',
                render: function(data, type, row, meta) {
                    // Filter out the matching job from global Fluid-provided jobs list
                    var job = jobs.filter(function(obj) {
                      return obj.uid === row.job
                    })[0];
                    return job.job_number + " - " + job.title;
                }
            },
            {
                name: 'status',
                data: 'status',
                render: function(data, type, row, meta) {
                    // Filter out the matching job from global Fluid-provided jobs list
                    return "<span class='status-" + row.status + "'>" + statusValues[row.status] + "</span>";
                }
            },
        ],
        lengthChange: false,
        order: [[orderColumn, query.orderDirection]],
        pageLength: query.limit,
        displayStart: query.offset,
        search: {
            search: query.search,
        },
    });

    // Add detail view link to the full rows
    applicationsTable.on('click', 'tbody td', function() {
      var rowID = parseInt(applicationsTable.cell({ row: this.parentNode.rowIndex - 1, column: 0}).data());
      if (rowID) {
        window.location.href = detailUri + '&tx_ats[application]=' + rowID;
      }
    })

    // Set form input values from model
    $('#applications-ajax-filter input, #applications-ajax-filter select').each(function() {
        $(this).val(query[$(this).data("name")]);
    });

    // Bind form value changes to query model
    $('#applications-ajax-filter input, #applications-ajax-filter select').on('change', function() {
        query[$(this).data("name")] = $(this).val();
        applicationsTable.ajax.reload();
    });

    //Reset button
    $('#applications-ajax-filter #reset').on('click', function(){
        query = JSON.parse(JSON.stringify(defaultQuery));
        // Reset all form inputs
        $('#applications-ajax-filter input, #applications-ajax-filter select').each(function() {
            $(this).val(query[$(this).data("name")]);
        });
        applicationsTable.search(query.search);
        applicationsTable.page(0);
        applicationsTable.order([$('#applications-ajax-list th[data-column="' + query.orderBy + '"]').first().index(), query.orderDirection]);
        applicationsTable.ajax.reload();
    });
});
