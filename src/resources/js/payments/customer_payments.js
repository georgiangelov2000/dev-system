import { APICaller } from '../ajax/methods';
import { handleErrors } from '../helpers/action_helpers';
$(function () {
  $('.selectCustomer').selectpicker('refresh').val('').trigger('change')

  $('input[name="datetimes"]').daterangepicker({
    autoUpdateInput: false,
    locale: {
      cancelLabel: 'Clear'
    }
  });

  $('.datepicker').datepicker({
    format: 'mm/dd/yyyy'
  });

  let disabledOption = $('.disabledDateRange');
  let dateRangePicker = $('input[name="datetimes"]');
  let dateRangeCol = $('.dateRange');
  let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
  let btnFilter = $('#filter');
  let modalInvoice = $('#modalInvoice');
  let submitForm = $('#submitForm');

  let dataTable;
  let supplierData;

  dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });

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

  btnFilter.bind('click', function (e) {
    loadDataTable()
  });
  function loadDataTable() {
    let template = `
      <div class="p-3 mb-3">

        <div class="row">
          <div class="col-12 d-flex flex-wrap align-items-center justify-content-between">
            <h4 data-target="name"></h4>
            <h4 data-target="date" class="float-right"></h4>
          </div>
        </div>

        <div class="row invoice-info mb-3">
          <div class="col-sm-4 invoice-col">
              <span class="font-weight-bold">Address:</span> <span data-target="address"></span> <br>
              <span class="font-weight-bold">Phone:</span> <span data-target="phone"></span> <br>
              <span class="font-weight-bold">Email:</span> <span data-target="email"></span>
          </div>

          <div class="col-sm-4 invoice-col">
              <span class="font-weight-bold">Country:</span> <span data-target="country"></span> <br>
              <span class="font-weight-bold">City:</span> <span data-target="city"></span> <br>
              <span class="font-weight-bold">Zip code:</span> <span data-target="zip"></span> <br>
          </div>
        </div>

        <div class="row">
          <div class="col-12 table-responsive">
              <table id="paymentsTable" class="table table-hover table-sm">
                  <thead>
                      <tr>
                          <th>ID</th>
                          <th>Order</th>
                          <th>Tracking number</th>
                          <th>Price</th>
                          <th>Quantity</th>
                          <th>Method</th>
                          <th>Status</th>
                          <th>Reference</th>
                          <th>Date of payment</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
              </table>
          </div>
        </div>

        <div class="row justify-content-end">
          <div class="col-6">
              <p class="lead" data-target="lead-date"></p>
              <div class="table-responsive">
                  <table id="amountDueTable" class="table">
                      <tbody>
                          <tr>
                              <th style="width:50%">Final price:</th>
                              <td data-td-target="sum"></td>
                          </tr>
                          <tr>
                              <th style="width:50%">Records:</th>
                              <td data-td-target="records"></td>
                          </tr>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
      
      <div class="row no-print">
          <div class="col-12">
              <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                  <i class="fas fa-print"></i> Print
              </button>
              <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                  <i class="fas fa-download"></i> Generate PDF
              </button>
          </div>
      </div>

    </div>`;

    $('#paymentTemplate').removeClass('d-none').html(template);

    dataTable = $('#paymentsTable').DataTable({
      serverSide: true,
      ordering: false,
      ajax: {
        url: ORDER_PAYMENT_API,
        data: function (data) {
          data.customer = bootstrapSelectCustomer.val();
          data.date = dateRangePicker.val();
        },
        dataSrc: function (response) {

          let customer = response.customer;
          let sum = response.sum;
          let date = response.date ? response.date : '';
          let amountDue = date ? `Amount Due: ${date}` : ''

          $('h4[data-target="name"]').text(customer.name);
          $('h4[data-target="date"]').text(date);
          $('p[data-target="lead-date"]').text(amountDue);
          $('span[data-target="address"]').text(customer.address);
          $('span[data-target="phone"]').text(customer.phone);
          $('span[data-target="email"]').text(customer.email);
          $('span[data-target="country"]').text(customer.country.name);
          $('span[data-target="city"]').text(customer.state.name);
          $('span[data-target="zip"]').text(customer.zip);
          $('#amountDueTable td[data-td-target="sum"]').text('€' + sum);
          $('#amountDueTable td[data-td-target="records"]').text(response.data.length);

          return response.data;
        }
      },
      columns: [
        {
          name: 'id',
          data: 'id',
        },
        {
          name: 'name',
          render: function (data, type, row) {
            return `<a href="${ORDER_EDIT_ROUTE.replace(':id', row.order.id)}">${row.order.purchase.name}</a>`
          }
        },
        {
          name: 'tracking_number',
          render: function (data, type, row) {
            return `<span>${row.order.tracking_number}</span>`
          }
        },
        {
          name: 'price',
          render: function (data, type, row) {
            return `<span>€${row.price}</span>`
          }
        },
        {
          name: 'quantity',
          render: function (data, type, row) {
            return `<span>${row.quantity}</span>`
          }
        },
        {
          name: 'payment_method',
          render: function (data, type, row) {
            return `<span>${row.payment_method ? row.payment_method : ''}</span>`
          }
        },
        {
          name: 'payment_status',
          render: function (data, type, row) {
            if (row.payment_status === 'Paid') {
              return `<i title="Paid" class="fa-light fa-check"></i>`
            }
            else if (row.payment_status === 'Pending') {
              return `<i title="Pending" class="fa-light fa-loader"></i>`
            }
            else if (row.payment_status === 'Overdue') {
              return `<i title="Overdue" class="fa-light fa-exclamation"></i>`
            } else if (row.payment_status === 'Refunded') {
              return `<i title="Refunded" class="fa-light fa-rotate-left"></i>`
            }
            else {
              return '';
            }
          }
        },
        {
          name: 'payment_reference',
          render: function (data, type, row) {
            return `<span>${row.payment_reference ? row.payment_reference : ''}</span>`
          }
        },
        {
          name: 'date_of_payment',
          render: function (data, type, row) {
            return `<span>${row.date_of_payment ? row.date_of_payment : ''}</span>`
          }
        },
        {
          render: function (data, type, row) {

            let edit = `
              <a href="${ORDER_PAYMENT_EDIT_ROUTE.replace(":id", row.id)}" title="Edit" class="btn p-0" href="">
                  <i class="fa-light fa-pen text-primary"></i>
              </a>
              `;

            let invoice = `
              <a type="button" data-id="${row.invoice.id}" onclick="editInvoice(this)" title="Invoice" class="btn p-0"> 
                <i class="fa-light fa-file-invoice text-primary"></i>
              </a>`;

            return `${edit} ${invoice}`;
          }
        }
      ]
    });

  }

  // Window actions
  window.editInvoice = function (e) {
    let id = $(e).attr('data-id');
    APICaller(ORDER_INVOICE_API_ROUTE, { 'id': id }, function (response) {
      let invoice = response.data[0];

      modalInvoice.modal('show');

      modalInvoice.find('form').attr('action', ORDER_INVOICE_UPDATE_ROUTE.replace(":id", id))

      modalInvoice.find('form input').each(function () {
        let inputName = $(this).attr('name');

        if (inputName && invoice.hasOwnProperty(inputName)) {
          $(this).val(invoice[inputName]);
        }
      });

    }, function (error) {
      toastr['error'](error.message);
    });
  }

  submitForm.on('click', function (e) {
    e.preventDefault();

    let actionUrl = modalInvoice.find('form').attr('action');
    let method = modalInvoice.find('form').attr('method');
    let data = modalInvoice.find('form').serialize();

    $.ajax({
      url: actionUrl,
      method: method,
      data: data,
      success: function (response) {
        console.log(response.message);
        toastr['success'](response.message);
        modalInvoice.find('form').trigger('reset');
        modalInvoice.modal('toggle');
        dataTable.ajax.reload(null, false);
      },
      error: function (xhr, status, error) {
        if (xhr.status === 422) {
          toastr['error'](xhr.responseJSON.message);
          var errors = xhr.responseJSON.errors;
          handleErrors(errors);
        }
      }
    })

  })

})