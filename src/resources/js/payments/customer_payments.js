import { APIPOSTCALLER } from '../ajax/methods';

$(function () {
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

  var buttonNames = ['copy', 'csv', 'excel', 'pdf', 'print'];
  var buttons = buttonNames.map(function (name) {
    return {
      extend: name,
      class: 'btn btn-outline-secondary',
      exportOptions: {
        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
      }
    };
  });

  form.on('submit', function (e) {
    e.preventDefault();
    let customer = bootstrapSelectCustomer.selectpicker('val');
    let date = dateRangePicker.val();
    let url = form.attr('action');

    $('#loader').show();

    APIPOSTCALLER(url, { "customer": customer, 'date': date }, function (response) {
      let respData = response.html;

      if (respData) {
        $('#paymentTemplate').html(respData);

        $('body').find('#paymentsTable').DataTable({
          ordering: false,
          dom: 'Bfrtip',
          buttons: buttons,
        });

      }

      $('#loader').hide();
    }, function (error) {
      console.log(error);
    })

  })


})