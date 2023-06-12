$(function () {
    let table = $('#ordersTable');

    let startDate = moment().startOf('month').format('YYYY-MM-DD')
    let endDate = moment().endOf('month').format('YYYY-MM-DD')

    $('.selectType').selectpicker();

    let dateOfSale = startDate + ' - ' + endDate;
    const bootstrapOrderStatus = $('.bootstrap-select .selectType');

    let dateOfPayment;

    $('input[name="date_of_sale"]').daterangepicker({
        timePicker: false,
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('input[name="date_of_payment"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: ORDER_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    'product_id': PRODUCT_ID,
                    "search": d.search.value,
                    'date_range': dateOfSale,
                    'date_of_payment': dateOfPayment,
                    'status': bootstrapOrderStatus.val(),
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.invoice_number + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '3%',
                name: "id",
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "invoice_number",
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.invoice_number + '</span>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "customer",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + CUSTOMER_EDIT_ROUTE.replace(':id', row.customer.id) + '" >' + row.customer.name + '</a>';
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "sold_quantity",
                data: "sold_quantity"
            },
            {
                width: '10%',
                orderable: false,
                name: "single_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.single_sold_price}</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "total_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.total_sold_price}</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "original_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.original_sold_price}</span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "discount_percent",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '7%',
                orderable: false,
                name: 'expired',
                render: function (data, type, row) {
                    var dateOfSale = moment(row.date_of_sale);
                    var currentDate = moment();
                    var daysRemaining = dateOfSale.diff(currentDate, 'days');

                    if (currentDate.isAfter(dateOfSale, 'day') && (row.status === 'Pending' || row.status === 'Ordered')) {
                        return `<span class="badge badge-danger p-2">Overdue by ${Math.abs(daysRemaining)} days</span>`;
                    } else if (row.status === 'Received') {
                        return `<span class="badge badge-success p-2">Order received</span>`;
                    }
                    else {
                        var badgeClass = daysRemaining > 5 ? 'badge-success' : 'badge-warning';
                        return `<span class="badge ${badgeClass} p-2">${daysRemaining} days remaining</span>`;
                    } {

                    }
                }
            },
            {
                width: '7%',
                orderable: false,
                name: 'created_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.created_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "date_of_sale",
                data: "date_of_sale"
            },
            {
                width: '10%',
                orderable: false,
                name: 'date_of_paymemt',
                render: function (data, type, row) {
                    if (row.customer_payments.length) {
                        return `<span>${moment(row.customer_payments[0].date_of_payment).format('YYYY-MM-DD')}<span>`;
                    } else {
                        return ''
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    if (row.status === 'Received') {
                        return '<i title="Reveived" class="fa-light fa-check"></i>';
                    }
                    else if (row.status === 'Pending') {
                        return '<i title="Pending" class="fa-light fa-loader"></i>'
                    }
                    else if (row.status === 'Ordered') {
                        return '<i title="Ordered" class="fa-light fa-truck"></i>'
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "is_paid",
                render: function (data, type, row) {
                    if (row.is_paid) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
        ],
    });

    $('input[name="date_of_sale"], input[name="date_of_payment"]').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');
    
        if ($(this).attr('name') === 'date_of_sale') {
            dateOfSale = startDate + ' - ' + endDate;
        } else if ($(this).attr('name') === 'date_of_payment') {
            dateOfPayment = startDate + ' - ' + endDate;
            $(this).val(dateOfPayment);
        }
    
        table.DataTable().ajax.reload();
    });

    bootstrapOrderStatus.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

});