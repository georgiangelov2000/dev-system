import { APICaller } from '../ajax/methods';
import { handleErrors } from '../helpers/action_helpers';
$(function () {
    $('.selectSupplier').selectpicker('refresh').val('').trigger('change')

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let btnFilter = $('.filter');
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
        let table = `
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
                                    <th>Purchase</th>
                                    <th>Code</th>
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
                    <img class="img-fluid w-25 rounded" id="supplierImage" src=""  />
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
    </div>`;

        $('#paymentTemplate').removeClass('d-none').html(table);

        dataTable = $('#paymentsTable').DataTable({
            serverSide: true,
            ajax: {
                url: SUPPLIER_PAYMENTS_API,
                data: function (data) {
                    data.user = bootstrapSelectSupplier.val();
                    data.date = dateRangePicker.val();
                    data.type = TYPE
                },
                dataSrc: function (response) {

                    const supplier = response.user;
                    const sum = response.sum;
                    const date = response.date ? response.date : '';
                    const amountDue = date ? `Amount Due: ${date}` : ''

                    $('h4[data-target="name"]').text(supplier.name);
                    $('h4[data-target="date"]').text(date);
                    $('p[data-target="lead-date"]').text(amountDue);
                    $('span[data-target="address"]').text(supplier.address);
                    $('span[data-target="phone"]').text(supplier.phone);
                    $('span[data-target="email"]').text(supplier.email);
                    $('span[data-target="country"]').text(supplier.country.name);
                    $('span[data-target="city"]').text(supplier.state.name);
                    $('span[data-target="zip"]').text(supplier.zip);
                    $('#amountDueTable td[data-td-target="sum"]').text('€' + sum);
                    $('#amountDueTable td[data-td-target="records"]').text(response.data.length);
                    $('img[id="supplierImage"]').attr('src', supplier.image_path);
                    return response.data;
                }
            },
            columns: [
                { orderable: true, data: 'id' },
                {
                    name: 'name',
                    orderable: false,
                    class:'text-center',
                    render: function (data, type, row) {
                        return `<span>${row.purchase.name}</span>`;
                    }
                },
                {
                    name: 'code',
                    orderable: false,
                    class:'text-center',
                    render: function (data, type, row) {
                        return `<span>${row.purchase.code}</span>`;
                    }
                },
                {
                    name: 'price',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>€${row.price}</span>`;
                    }
                },
                {
                    name: 'quantity',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>${row.quantity}</span>`;
                    }
                },
                {
                    name: 'payment_method',
                    render: function (data, type, row) {
                        const paymentMethods = {
                            1: "Cash",
                            2: "Bank Transfer",
                            3: "Credit Card",
                            4: "Cheque",
                            5: "Online Payment"
                        };

                        const status = paymentMethods[row.payment_method] || "";
                        return `<span>${status}</span>`;
                    }
                },
                {
                    name: 'payment_status',
                    render: function (data, type, row) {
                        const paymentStatuses = {
                            1: { label: "Paid", iconClass: "fal fa-check-circle" },
                            2: { label: "Pending", iconClass: "fal fa-hourglass-half" },
                            3: { label: "Partially Paid", iconClass: "fal fa-money-bill-alt" },
                            4: { label: "Overdue", iconClass: "fal fa-exclamation-circle" },
                            5: { label: "Refunded", iconClass: "fal fa-undo-alt" },
                            6: { label: "Ordered", iconClass: "fal fa-shopping-cart" }
                        };

                        const statusData = paymentStatuses[row.payment_status] || { label: "", iconClass: "" };

                        return `
                            <div title="${statusData.label}" class="status">
                                <span class="icon"><i class="${statusData.iconClass}"></i></span>
                            </div>`;
                    }
                },
                { 
                    data: 'payment_reference',
                    orderable: false 
                },
                {
                    name: 'date_of_payment',
                    render: function (data, type, row) {
                        return `<span>${row.date_of_payment}</span>`;
                    }
                },
                {
                    orderable: false,
                    render: function (data, type, row) {
                        let edit = `
                        <a title="Edit" class="btn p-0" href="${PURCHASE_PAYMENT_EDIT.replace(':payment', row.id).replace(':type', 'purchase')}">
                            <i class="fa-light fa-pen text-primary"></i>
                        </a>
                        `;

                        let invoice = `
                        <a data-id="${row.invoice.id}" onclick="editInvoice(this)" title="Invoice" class="btn p-0"> 
                            <i class="fa-light fa-file-invoice text-primary"></i> 
                        </a>`;

                        let deleteForm = `
                        <form data-name=${row.date_of_payment}  style='display:inline-block;' id='delete-form' action="${PURCHASE_PAYMENT_DELETE_ROUTE.replace(":payment", row.id).replace(':type', 'purchase')}" method='POST'>
                                            <input type='hidden' name='_method' value='DELETE'>
                                            <input type='hidden' name='id' value='${row.id}'>
                                            <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deletePayment(this);'>
                                                <i class='fa-light fa-trash text-primary'></i>
                                            </button>
                                        </form>
                        `;

                        return `${edit} ${invoice} ${deleteForm}`;
                    }
                }
            ]
        });
    }

    // Window actions
    window.editInvoice = function (e) {
        console.log(e);
        let id = $(e).attr('data-id');
        APICaller(PURCHASE_INVOICE_API_ROUTE, { 'id': id }, function (response) {
            let invoice = response.data[0];

            modalInvoice.modal('show');

            modalInvoice.find('form').attr('action', PARCHASE_INVOICE_UPDATE_ROUTE.replace(":id", id))

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
