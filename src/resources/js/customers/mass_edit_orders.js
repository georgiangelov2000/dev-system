$(function () {
    $('select[name="package_id"],select[name="user_id"]').selectpicker();

    $('input[name="date_of_sale"]').datepicker({
        format: 'mm/dd/yyyy'
    });

    let table = $('#ordersTable');

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: ORDER_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, packageData(d));
            }
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '';

                    if (row.status === 6) {
                        checkbox =
                            '<div div class="form-check">\n\
                            <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.tracking_number + ' type="checkbox"> \n\
                        </div>';
                    }

                    return `${checkbox}`;
                }
            },
            {
                width: '1%',
                name: "id",
                render: function (data, type, row) {
                    let input = '';

                    if (typeof CUSTOMER !== 'undefined') {
                        input = `<input type="hidden" value="${row.id}" name="order_id[]" />`
                    }
                    return `<span class="font-weight-bold">${row.id}</span>${input}`;

                }
            },
            {
                width: '1%',
                class:'text-center',
                orderable: false,
                render: function (data, type, row) {
                    let payment;

                    if (row.payment) {
                        payment = `<a href=${PAYMENT_EDIT.replace(':payment', row.payment.id).replace(':type', 'order')}>${row.payment.alias}</a>`
                    } else {
                        payment = ''
                    }

                    return payment;
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "customer",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + CUSTOMER_EDIT_ROUTE.replace(':id', row.customer.id) + '" >' + row.customer.name + '</a>';
                }
            },
            {
                width:'1%',
                orderable:false,
                name:'user',
                class: "text-center",
                render: function(data,type,row) {
                    let user = '';
                    if(row.user) {
                        user = `<a href="${USER_EDIT.replace(":id",row.user.id)}">${row.user.username}</a>`;
                    }
                    return user
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "purchase",
                class: "text-center",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + EDIT_PRODUCT_ROUTE.replace(':id', row.purchase.id) + '">' + row.purchase.name + '</a>';
                }
            },
            {
                width: '1%',
                orderable: false,
                name: "sold_quantity",
                data: "sold_quantity",
                class: "text-center"
            },
            {
                width: '7%',
                orderable: false,
                name: "single_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>€${row.single_sold_price}</span>`
                }
            },
            {
                width: '8%',
                orderable: false,
                name: "discount_single_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>€${row.discount_single_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "total_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>€${row.total_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "original_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>€${row.original_sold_price}</span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "discount_percent",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '6%',
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.package_extension_date) {
                        return `<span>${moment(row.package_extension_date).format('YYYY-MM-DD')}</span>`
                    } else {
                        return `<span>${moment(row.date_of_sale).format('YYYY-MM-DD')}</span>`
                    }

                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'expired',
                class: "text-center",
                render: function (data, type, row) {
                    const date = row.package_extension_date ? moment(row.package_extension_date) : moment(row.date_of_sale);
                    const currentDate = moment();
                    const daysRemaining = date.diff(currentDate, 'days');

                    let badgeClass = '';
                    let badgeText = '';

                    switch (row.status) {
                        case 6:
                        case 2:
                            badgeClass = 'badge-warning';
                            badgeText = `${daysRemaining} days remaining`;
                            break;
                        case 1:
                        case 3:
                        case 4:
                            badgeClass = 'badge-success';
                            badgeText = 'Order received';
                            break;
                        default:
                            badgeText = 'Invalid status please check the system!';
                    }

                    return `<span class="badge p-2 ${badgeClass}">${badgeText}</span>`;
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'package',
                class: "text-center",
                render: function (data, type, row) {
                    if (row.package) {
                        return `<a href= ${PACKAGE_EDIT_ROUTE.replace(':id', row.package.id)}>${row.package.package_name}</a>`;
                    } else {
                        return '';
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    const statusMap = {
                        1: { text: "Paid", iconClass: "fal fa-check-circle" },
                        2: { text: "Pending", iconClass: "fal fa-hourglass-half" },
                        3: { text: "Partially Paid", iconClass: "fal fa-money-bill-alt" },
                        4: { text: "Overdue", iconClass: "fal fa-exclamation-circle" },
                        5: { text: "Refunded", iconClass: "fal fa-undo-alt" },
                        6: { text: "Ordered", iconClass: "fal fa-shopping-cart" }
                    };

                    const statusInfo = statusMap[row.status] || { text: "Unknown", iconClass: "fal fa-question" };

                    return `<div title="${statusInfo.text}" class="status">
                        <span class="icon"><i class="${statusInfo.iconClass}"></i></span>
                    </div>`;
                }
            },
        ],
        order: [[1, 'asc']],
    });


    function packageData(d) {
        var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
        var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

        var data = {
            "search": d.search.value,
            'order_column': orderColumnName, // send the column name being sorted
            'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
            'limit': d.custom_length = d.length,
        };

        if (typeof CUSTOMER !== 'undefined') {
            data.customer = CUSTOMER;
        } else {
            data.customer = bootstrapCustomer.val()
        }

        if (typeof STATUS !== 'undefined') {
            data.status = STATUS;
        } else {
            data.status = bootstrapOrderStatus.val();
        }

        return data;
    }

    window.updateOrders = function (e) {
        e.preventDefault();
        let form = e.target;
        let method = e.target.getAttribute('method');
        let formData = $(form).serializeArray().reduce((acc, obj) => {
            acc[obj.name] = obj.value;
            return acc;
        }, {});
        let searchedIds = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).data('id');
        }).get();
        
        if (searchedIds.length > 0) {
            $.ajax({
                url: MASS_UPDATE_ORDERS,
                method: 'POST',
                data: {
                    'order_ids': searchedIds,
                    ...formData
                },
                success: function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                },
                error: function (xhr, status, error) {
                    console.log(xhr);
                    toastr['error'](response.message);
                }
            });
        } else {
            toastr['error']("Please select orders");
        }
    }

    window.selectOrder = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };
});