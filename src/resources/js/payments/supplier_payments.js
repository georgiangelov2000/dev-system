import { APIPOSTCALLER } from '../ajax/methods';

$(function () {
    $('.selectSupplier').selectpicker('refresh').val('').trigger('change')

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let btnFilter = $('.filter');

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
        <div class="card col-12 cardTemplate p-0">
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
                                    <th>Paid price</th>
                                    <th>Paid Quantity</th>
                                    <th>Date of payment</th>
                                    <th>Payment method</th>
                                    <th>Payment reference</th>
                                    <th>Payment status</th>
                                    <th>Notes</th>
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
        </div>
    </div>`;
    
        $('#paymentTemplate').html(table);
    
        dataTable = $('#paymentsTable').DataTable({
            serverSide: true,
            ajax: {
                url: SUPPLIER_PAYMENTS_API,
                data: function (data) {
                    data.supplier = bootstrapSelectSupplier.val();
                    data.date = dateRangePicker.val();
                },
                dataSrc: function (response) {
                    
                    let supplierData = response.supplier;
                    let sum = response.sum;
                    let date = response.date ? response.date : '';
                    let amountDue = date ? `Amount Due: ${date}` : ''

                    $('h4[data-target="name"]').text(supplierData.name);
                    $('h4[data-target="date"]').text(date);
                    $('p[data-target="lead-date"]').text(amountDue);
                    $('span[data-target="address"]').text(supplierData.address);
                    $('span[data-target="phone"]').text(supplierData.phone);
                    $('span[data-target="email"]').text(supplierData.email);
                    $('span[data-target="country"]').text(supplierData.country.name);
                    $('span[data-target="city"]').text(supplierData.state.name);
                    $('span[data-target="zip"]').text(supplierData.zip);
                    $('#amountDueTable td[data-td-target="sum"]').text('€' + sum);

                    return response.data;
                }
            },
            columns: [
                { orderable: true, data: 'id' },
                {
                    name: 'name',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>${row.purchase.name}</span>`;
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
                    name: 'date_of_payment',
                    render: function (data, type, row) {
                        return `<span>${row.date_of_payment}</span>`;
                    }
                },
                { data: 'payment_method', orderable: false },
                { data: 'payment_reference', orderable: false },
                { data: 'payment_status', orderable: false },
                { data: 'notes', orderable: false },
                {
                    orderable:false,
                    render: function (data,type,row) {
                        return `
                        <a href="${SUPPLIER_PAYMENT_EDIT.replace(':id',row.id)}">
                            <i class="fa-light fa-pen text-primary"></i>
                        </a>
                        `;
                    }
                }
            ]
        });
    }
    
})
