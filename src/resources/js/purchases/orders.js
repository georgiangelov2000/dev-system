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
                    return '<span class="font-weight-bold">' + row.tracking_number + '</span>';
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
                width: '5%',
                orderable: false,
                name: 'expired',
                render: function (data, type, row) {
                    let date;

                    if (row.package_extension_date) {
                        date = moment(row.package_extension_date);
                    } else {
                        date = moment(row.date_of_sale);
                    }

                    let currentDate = moment();
                    let daysRemaining = date.diff(currentDate, 'days');
                    
                    console.log(row.status);

                    if (row.status === 'Ordered' && row.is_paid !== true) {
                        return `<span class="badge badge-danger p-2">Overdue by ${Math.abs(daysRemaining)} days</span>`;
                    } else if (row.status === 'Ordered') {
                        let badgeClass = daysRemaining > 5 ? 'badge-success' : 'badge-warning';
                        return `<span class="badge ${badgeClass} p-2">${daysRemaining} days remaining</span>`;
                    } else {
                        return `<span class="badge badge-success p-2">Order received</span>`;
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
                    if (row.order_payments) {
                        return `<span>${moment(row.order_payments.date_of_payment).format('YYYY-MM-DD')}<span>`;
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
                    if (row.status === 'Paid') {
                        return '<img style="height:40px;" class="w-50" title="Paid" src = "/storage/images/static/succesfully.png" /> '
                    }
                    else if (row.status === 'Pending') {
                        return '<img style="height:40px;" class="w-50" title= "Pending" src = "/storage/images/static/pending.png" /> '
                    }
                    else if (row.status === 'Partially Paid') {
                        return '<img style="height:40px;" class="w-50" title="Partially Paid" src = "/storage/images/static/partially-payment.png" /> '
                    }
                    else if (row.status === 'Overdue') {
                        return '<img style="height:40px;" class="w-50" title="Overdue" src = "/storage/images/static/overdue.png" /> '
                    }
                    else if (row.status === 'Refunded') {
                        return '<img style="height:40px;" class="w-50" title="Refunded" src = "/storage/images/static/ordered.png" /> '
                    }
                    else if (row.status === 'Ordered') {
                        return '<img style="height:40px;" class="w-50" title="Ordered" src = "/storage/images/static/refund.png" /> '
                    } else {
                        return '';
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