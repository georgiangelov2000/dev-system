import { APIPOSTCALLER } from '../ajax/methods';

$(function(){
    $('.selectCustomer').selectpicker('refresh').val('').trigger('change')

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
    let form = $('#filterForm');

    disabledOption.on('click', function () {
        if ($(this).is(':checked')) {
            dateRangeCol.addClass('d-none');
            dateRangePicker.addClass('d-none').prop('disabled', true).val(null);
        } else {
            dateRangeCol.removeClass('d-none');
            dateRangePicker.removeClass('d-none').prop('disabled', false);
            dateRangePicker.data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
            dateRangePicker.data('daterangepicker').setEndDate(moment().startOf('hour'));
        }
    });

    form.on('submit', function (e) {
        e.preventDefault();
        let customer = bootstrapSelectCustomer.selectpicker('val');
        let date = dateRangePicker.val();
        let url = form.attr('action');

        $('#loader').show();

        APIPOSTCALLER(url, {"customer": customer, 'date': date }, function (response) {
            let respData = response.html;

            if(respData) {
                $('#paymentTemplate').html(respData);

                $('body').find('#paymentsTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                          extend: 'copy',
                          class: 'btn btn-outline-secondary',
                          exportOptions: {
                            columns: [1,2,3,4,5,6,7,8,9]
                          }
                        },
                        {
                          extend: 'csv',
                          class: 'btn btn-outline-secondary',
                          exportOptions: {
                            columns: [1,2,3,4,5,6,7,8,9]
                          }
                        },
                        {
                          extend: 'excel',
                          class: 'btn btn-outline-secondary',
                          exportOptions: {
                            columns: [1,2,3,4,5,6,7,8,9] 
                          }
                        },
                        {
                          extend: 'pdf',
                          class: 'btn btn-outline-secondary',
                          exportOptions: {
                            columns: [1,2,3,4,5,6,7,8,9]
                          }
                        },
                        {
                          extend: 'print',
                          class: 'btn btn-outline-secondary',
                          exportOptions: {
                            columns: [1,2,3,4,5,6,7,8,9]
                          }
                        }
                      ],
                    columns:[
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false},
                        {orderable:false}
                    ]
                });
            }            

            $('#loader').hide();
        }, function (error) {
            console.log(error);
        })

    })


})