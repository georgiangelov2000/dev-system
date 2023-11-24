import {numericFormat} from '../helpers/functions';
import { deliveryStatusesWithIcons } from '../helpers/statuses';


$(function () {
    $('select[name="package_id"],select[name="user_id"]').selectpicker();

    let formData = {
        'discount_percent': null,
        'expected_date_of_payment': null,
        'expected_delivery_date': null,
        'package_id' : null,
        'single_sold_price':null,
        'sold_quantity': null,
        'user_id': null
    };

    $('.datepicker').datepicker({format: 'mm/dd/yyyy'});

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
            { orderable: false, width: "1%", render: (data, type, row) => !row.is_it_delivered ? `<div class="form-check">\n<input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=${row.id} data-name=${row.tracking_number} type="checkbox">\n</div>` : '' },
            { width: '1%', name: "id", render: (data, type, row) => `<span class="font-weight-bold">${row.id}</span>${typeof CUSTOMER !== 'undefined' ? `<input type="hidden" value="${row.id}" />` : ''}` },
            {
                width: '5%',
                orderable: false,
                name: "customer",
                class: "text-center",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + CUSTOMER_EDIT_ROUTE.replace(':id', row.customer.id) + '" >' + row.customer.name + '</a>';
                }
            },
            { width: '1%', orderable: false, name: 'user', class: "text-center", render: (data, type, row) => row.user ? `<a href="${USER_EDIT.replace(":id",row.user.id)}">${row.user.username}</a>` : '' },
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
                width: '6%',
                orderable: false,
                name: "tracking_number",
                class: "text-center",
                render: function(data,type,row) {
                    return `<b class="text-primary ">${row.tracking_number}</b>`;
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
                    return `<span>${numericFormat(row.single_sold_price)}</span>`
                }
            },
            {
                width: '8%',
                orderable: false,
                name: "discount_single_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.discount_single_sold_price)}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "total_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.total_sold_price)}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "original_sold_price",
                class: "text-center",
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.original_sold_price)}</span>`
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
            { width: '5%', orderable: false, name: 'package', class: "text-center", render: (data, type, row) => row.package ? `<a href=${PACKAGE_EDIT_ROUTE.replace(':id', row.package.id)}>${row.package.package_name}</a>` : '' },
            {
                width: '8%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    const dateToDisplay = row.package_extension_date 
                        ? row.package_extension_date 
                        : row.expected_delivery_date;
                    
                    const formattedDate = dateToDisplay ? moment(dateToDisplay).format('MMM DD, YYYY') : '';

                    return `<span>${formattedDate}</span>`;
                }
            },
            {
                width: '8%',
                orderable: false,
                class:'text-center',
                name: 'delivery_delay',
                render: function (data, type, row) {
                    const isDelivered = row.is_it_delivered;
                    const expectedDate = row.package_extension_date ? moment(row.package_extension_date) : moment(row.expected_delivery_date);
                    const deliveryDate = moment(row.delivery_date);
                    const currDate = moment();
                    const diffDays = currDate.diff(expectedDate, 'days');
                    
                    if (!isDelivered) {
                        return `<span class="text-${currDate.isAfter(expectedDate) ? 'danger' : 'info'}">${diffDays} days ${currDate.isAfter(expectedDate) ? 'delay' : 'left'}</span>`;
                    } else {
                        const deliveryDiffDays = deliveryDate.diff(expectedDate, 'days');
                        return `<span class="text-${deliveryDate.isAfter(expectedDate) ? 'danger' : 'success'}">${deliveryDiffDays} ${deliveryDate.isAfter(expectedDate) ? 'days delay in delivery' : 'days delay, Delivered on time'}</span>`;
                        
                    }                    
                }
            },
        ],
        order: [[1, 'asc']],
    });

    function packageData(d) {
        const orderColumnIndex = d.order[0].column;
        const orderColumnName = d.columns[orderColumnIndex].name;
        return {
            search: d.search.value,
            order_column: orderColumnName,
            order_dir: d.order[0].dir,
            limit: d.custom_length = d.length,
            customer: typeof CUSTOMER !== 'undefined' ? CUSTOMER : bootstrapCustomer.val(),
            status: typeof STATUS !== 'undefined' ? STATUS : bootstrapOrderStatus.val()
        };
    }

    window.updateOrders = function (e) {
        e.preventDefault();
        let form = e.target;
        let method = e.target.getAttribute('method');

        let serializedData = $(form).serializeArray().reduce((acc, obj) => {
            acc[obj.name] = obj.value;
            return acc;
        }, {});
                
        Object.keys(serializedData).forEach(key => {
            if (formData.hasOwnProperty(key)) {
                formData[key] = serializedData[key];
            }
        });
        
        let searchedIds = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).data('id');
        }).get();
            
        formData['ids'] = searchedIds;
        
        if (searchedIds.length) {
            $.ajax({
                url: MASS_UPDATE_ORDERS,
                method: method,
                data: {
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
        $('.actions').toggleClass('d-none', $('tbody input[type="checkbox"]:checked').length === 0);
    };
    
});