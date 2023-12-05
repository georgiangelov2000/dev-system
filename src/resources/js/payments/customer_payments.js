import { APICaller, APIDELETECALLER } from '../ajax/methods';
import { handleErrors, swalText, showConfirmationDialog } from '../helpers/action_helpers';
import { numericFormat } from "../helpers/functions";
import { statusPaymentsWithIcons, paymentMethods, deliveryStatusesWithIcons, paymentStatuses, deliveryStatuses } from "../helpers/statuses";

$(function () {
  $('.selectCustomer').selectpicker('refresh').val('').trigger('change')

  $('input[name="datetimes"]').daterangepicker({
    autoUpdateInput: false,
    locale: {
      cancelLabel: 'Clear'
    }
  });

  $('.datepicker').datepicker({
    format: "yyyy-mm-dd",
  });

  let disabledOption = $('.disabledDateRange');
  let dateRangePicker = $('input[name="datetimes"]');
  let dateRangeCol = $('.dateRange');
  let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
  let btnFilter = $('#filter');
  let modalInvoice = $('#modalInvoice');
  let submitForm = $('#submitForm');

  let dataTable;

  dateRangePicker.on('apply.daterangepicker', function (ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });

  disabledOption.on('click', function () {
    const checked = $(this).is(':checked');
    dateRangeCol.toggleClass('d-none', checked);
    dateRangePicker.toggleClass('d-none').prop('disabled', checked).val(null);
  
    if (!checked) {
      const picker = dateRangePicker.data('daterangepicker');
      picker.setStartDate(moment().subtract(1, 'year')).setEndDate(moment().startOf('hour'));
    }
  });

  btnFilter.bind('click', function (e) {
    $('#loader').show();
     loadDataTable();
});


  function updateUI(response) {
    const customer = response.customer;
    const sum = response.sum;
    const date = response.date ? response.date : '';
    const amountDue = date ? `Amount Due: ${date}` : ''

    $('h4[data-target="name"]').text(customer.name);
    $('h4[data-target="date"]').text(date);
    $('p[data-target="lead-date"]').text(amountDue);
    $('span[data-target="address"]').text(customer.address);
    $('span[data-target="phone"]').text(customer.phone);
    $('span[data-target="email"]').text(customer.email);
    $('span[data-target="country"]').text(customer.country.name);
    $('span[data-target="city"]').text(customer.state.name);
    $('span[data-target="zip"]').text(customer.zip);
    $('#amountDueTable td[data-td-target="sum"]').text(numericFormat(sum));
    $('#amountDueTable td[data-td-target="records"]').text(response.data.length);
    $('img[id="customerImage"]').attr('src', customer.image_path);
  }

  // Function to populate the select element with options
  function populateSelectOptions(selectElement, template, statuses) {
    const select = template.find(selectElement);
    
      const emptyOption = $("<option selected></option>").attr("value", "").text("Select All"); // Customize the text as needed
      select.append(emptyOption);

    // Iterate over statuses and create options
    for (const key in statuses) {
        if (statuses.hasOwnProperty(key)) {
            const option = $("<option></option>").attr("value", key).text(statuses[key].label);
            select.append(option);
        }
    }

    select.selectpicker("refresh");
  }

  function loadDataTable() {

    let template = $(`
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
            <div class="col-12">
              <p class="bg-dark p-2 font-weight-bold filters">
                <i class="fa-solid fa-filter"></i> Filters
              </p>
            </div>
            <div class="form-group col-3">
              <label>Group by payment status</label>
              <select class="form-control paymentStatus"></select>
            </div>
            <div class="form-group col-3">
            <label>Group by delivery status</label>
            <select class="form-control deliveryStatus"></select>
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
                          <th>Amount</th>
                          <th>Discount</th>
                          <th>Method</th>
                          <th>Payment Status</th>
                          <th>Delivery Status</th>
                          <th>Reference</th>
                          <th>Exp Delivery date</th>
                          <th>Delivery date</th>
                          <th>Exp date of payment</th>
                          <th>Date of payment</th>
                          <th>Payment Delay</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
              </table>
          </div>
        </div>

        <div class="row justify-content-end">
          <div class="col-6">
            <img class="img-fluid w-25 rounded" id="customerImage" src=""  />
          </div>
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

    </div>`);

    // Call the function to populate the select elements
    populateSelectOptions('.paymentStatus', template, paymentStatuses);
    populateSelectOptions('.deliveryStatus', template, deliveryStatuses);

    $('#paymentTemplate').removeClass('d-none').html(template);

    // let bootstrapSelectPackage = template.find('.selectPackage').parent().find('.bootstrap-select .selectPackage');

    let bootstrapSelectPaymentStatus = template.find('.paymentStatus').parent().find('.bootstrap-select .paymentStatus');
    let bootstrapSelectDeliveryStatus = template.find('.deliveryStatus').parent().find('.bootstrap-select .deliveryStatus');

    dataTable = $('#paymentsTable').DataTable({
      serverSide: true,
      ordering: false,
      ajax: {
        url: ORDER_PAYMENT_API,
        data: function (data) {
          data.user = bootstrapSelectCustomer.val();
          data.date = dateRangePicker.val();
          data.type = TYPE;
          data.delivery_status = bootstrapSelectPaymentStatus.val();
          data.payment_status = bootstrapSelectDeliveryStatus.val();
          data.custom_length = data.length; // Corrected the property assignment
          data.search.value = data.search.value; // Corrected the property assignment
        },
        dataSrc: function (response) {
          $('#loader').hide();
          updateUI(response);
          return response.data;
        },
        // Add an error handler to hide loader in case of an error
        error: function (xhr, error, thrown) {
          $('#loader').hide();
        },
      },
      columns: [
        { 
          width: "1%", 
          orderable: true, 
          data: "id" 
        },
        {
          name: 'name',
          width: "10%",
          orderable: false,
          class: "text-center",
          render: function (data, type, row) {
            return `<a href="${ORDER_EDIT_ROUTE.replace(':id', row.order.id)}">${row.order.purchase.name}</a>`
          }
        },
        {
          width: "10%",
          name: 'tracking_number',
          class:'text-center',
          render: function (data, type, row) {
            return `<span class="text-break">${row.order.tracking_number}</span>`
          }
        },
        {
          width: "1%",
          name: 'price',
          class:'text-center',
          render: function (data, type, row) {
            return `<span>${numericFormat(row.price)}</span>`;
          }
        },
        {
          width: "1%",
          class: "text-center",
          name: "quantity",
          orderable: false,
          render: function (data, type, row) {
              return `<span>${row.quantity}</span>`;
          },
        },
        {
          width: "1%",
          class: "text-center",
          name: "discount_percent",
          orderable: false,
          render: function (data, type, row) {
              return `<span>${row.order.discount_percent}%</span>`;
          },
        },
        {
          width: "1%",
          name: 'payment_method',
          class:'text-center',
          render: function (data, type, row) {
            const status = paymentMethods[row.payment_method] || "";
            return `<span>${status}</span>`;
          }
        },
        {
          width: "8%",
          name: 'payment_status',
          class:'text-center',
          orderable: false,
          render: function (data, type, row) {
            const statusData = statusPaymentsWithIcons[row.payment_status] || { label: "", iconClass: "" };
            return `
                <div title="${statusData.label}" class="status">
                    <span class="icon"><i class="${statusData.iconClass}"></i></span>
                </div>`;
          }
        },
        {
          width: "8%",
          name: 'delivery_status',
          class:'text-center',
          orderable: false,
          render: function (data, type, row) {
            const statusData = deliveryStatusesWithIcons[row.payment_status] || { label: "", iconClass: "" };
            return `
                <div title="${statusData.label}" class="status">
                    <span class="icon"><i class="${statusData.iconClass}"></i></span>
                </div>`;
          }
        },
        {
          width: "3%",
          name: 'payment_reference',
          orderable: false,
          class:'text-center',
          render: function (data, type, row) {
            return `<p class="text-break">${row.payment_reference ? row.payment_reference : ""}</p>`
          }
        },
        {
          width: "13%",
          name: "expected_delivery_date",
          class: "text-center",
          orderable: false,
          render: function (data, type, row) {
              const formattedDate = row.order.expected_delivery_date ? moment(row.order.expected_delivery_date).format('MMM DD, YYYY') : '';
              return `<span>${formattedDate}</span>`;
          },
        },
        {
          width: "11%",
          name: 'delivery_date',
          class: "text-center",
          orderable: false,
          render: function (data, type, row) {
            const formattedExpectedDate = row.order.delivery_date ? moment(row.order.delivery_date).format('MMM DD, YYYY') : '';
            return `<span>${formattedExpectedDate}</span>`;
          }
        },
        {
          width: "13%",
          name: "expected_date_of_payment",
          class: "text-center",
          orderable: false,
          render: function (data, type, row) {
              const formattedExpectedDate = row.expected_date_of_payment ? moment(row.expected_date_of_payment).format('MMM DD, YYYY') : '';
              return `<span>${formattedExpectedDate}</span>`;
          },
        },
        {
          width: "11%",
          name: 'date_of_payment',
          class: "text-center",
          orderable: false,
          render: function (data, type, row) {
            const formattedDate = row.date_of_payment ? moment(row.date_of_payment).format('MMM DD, YYYY') : '';
            return `<span>${formattedDate}</span>`
          }
        },
        {
          width: "8%",
          class: "text-center",
          orderable: false,
          render: function (data, type, row) {
              let status = row.payment_status;
              if (status === 1 || status === 4) {
                  let dateOfPayment = moment(row.date_of_payment); // Assuming you're using Moment.js
                  let expectedDateOfPayment = moment(
                      row.expected_date_of_payment
                  );

                  let daysDifference = dateOfPayment.diff(
                      expectedDateOfPayment,
                      "days"
                  );

                  if (daysDifference > 0) {
                      return `<span class="text-danger">${daysDifference} days delay in payment</span>`;
                  } else {
                      return `<span class="text-success">Payment made on time</span>`;
                  }
              } else {
                  return "";
              }
          },
        },
        {
          render: function (data, type, row) {
            let edit = `
              <a class="dropdown-item" href="${ORDER_PAYMENT_EDIT_ROUTE.replace(":payment", row.id).replace(':type', 'order')}">
                  <i class="fa-light fa-pen text-primary"></i> Edit
              </a>
              `;

            let invoice = `
              <a class="dropdown-item" data-id="${row.invoice.id}" onclick="editInvoice(this)"> 
                <i class="fa-light fa-file-invoice text-primary"></i> Invoice
              </a>`;

            let deleteForm = `
            <form data-name=${row.date_of_payment} class="dropdown-item"  style='display:inline-block;' id='delete-form' action="${ORDER_PAYMENT_DELETE_ROUTE.replace(":payment", row.id).replace(':type', 'order')}" method='POST'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0' onclick='event.preventDefault(); deletePayment(this);'>
                                    <i class='fa-light fa-trash text-primary'></i> Delete
                                </button>
                            </form>
            `;

            return `<div class="btn-group">
              <button type="button" class="btn btn-sm btn-outline-primary rounded" data-toggle="dropdown" aria-expanded="false">
                  <i class="fa-light fa-list" aria-hidden="true"></i>
              </button>
              <div class="dropdown-menu" role="menu">
                  ${edit}
                  ${invoice}
                  ${deleteForm}
              </div>
            </div>`;
          }
        }
      ]
    });

    bootstrapSelectPaymentStatus.bind('changed.bs.select', function () {
      dataTable.ajax.reload(null, false);
    })

    bootstrapSelectDeliveryStatus.bind('changed.bs.select', function () {
      dataTable.ajax.reload(null, false);
    })

  }

  // Window actions
  window.deletePayment = function (e) {
    let form = $(e).closest('form');

    let name = form.attr('data-name');
    let url = form.attr('action');

    const template = swalText(name);

    showConfirmationDialog('Selected items!', template, 'Yes, delete it!', function () {
      APIDELETECALLER(url, function (response) {
        toastr['success'](response.message);
        dataTable.ajax.reload(null, false);
      }, function (error) {
        toastr['error']('Order payment has not been deleted');
      });
    });
  };

  window.editInvoice = function (e) {
    let id = $(e).attr('data-id');
    APICaller(ORDER_INVOICE_API_ROUTE, { invoice: id, select_json:1, type:TYPE }, function (response) {
      let invoice = response;

      modalInvoice.modal('show');

      modalInvoice.find('form').attr('action', ORDER_INVOICE_UPDATE_ROUTE.replace(":type",TYPE).replace(":id", id))

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