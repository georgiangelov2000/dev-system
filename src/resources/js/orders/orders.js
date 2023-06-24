import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog } from '../helpers/action_helpers';

$(function () {
    $('.selectAction, .selectType, .selectCustomer').selectpicker();

    const bootstrapCustomer = $('.bootstrap-select .selectCustomer');
    const bootstrapOrderStatus = $('.bootstrap-select .selectType');
    const bootstrapSelectAction = $('.bootstrap-select .selectAction');
    const modal = $('#transaction_modal');
    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    let dateRange = $('input[name="datetimes"]').val();

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());

    const applyBtn = $('.applyBtn');
    applyBtn.bind('click', function () {
        dateRange = $('input[name="datetimes"]').val();
        dataTable.ajax.reload(null, false);
    })

    let table = $('#ordersTable');

    let dataTable = table.DataTable({
        serverSide: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'csv',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'excel',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'pdf',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'print',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            }
        ],
        ajax: {
            url: ORDER_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    'customer': bootstrapCustomer.val(),
                    'status': bootstrapOrderStatus.val(),
                    'search': builtInDataTableSearch ? builtInDataTableSearch.val().toLowerCase() : '',
                    'date_range': dateRange
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
                width: '10%',
                orderable: false,
                name: "product",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + EDIT_PRODUCT_ROUTE.replace(':id', row.product.id) + '">' + row.product.name + '</a>';
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
                width: '7%',
                orderable: false,
                name: "total_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.total_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "original_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.original_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                name: "discount_percent",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '6%',
                orderable: false,
                name: "date_of_sale",
                data: "date_of_sale"
            },
            {
                width: '5%',
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
                    }
                }
            },
            {
                width: '2%',
                orderable: false,
                name: 'created_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.created_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                width: '2%',
                orderable: false,
                name: 'updated_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.updated_at).format('YYYY-MM-DD')}<span>`
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
                width: '1%',
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
            {
                width: '15%',
                orderable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    let editButton = '';
                    let dropdown = '';

                    let deleteFormTemplate = "\
                    <form style='display:inline-block;' id='delete-form' action=" + ORDER_DELETE_ROUTE.replace(':id', row.id) + " method='POST' data-name=" + row.invoice_number + ">\
                        <input type='hidden' name='_method' value='DELETE'>\
                        <input type='hidden' name='id' value='" + row.id + "'>\
                        <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteOrder(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                    <form/>\ ";

                    if (!row.is_paid && row.status !== 'Received') {
                        editButton = '<a href=' + ORDER_EDIT_ROUTE.replace(':id', row.id) + ' data-id=' + row.id + 'class="btn p-1" title="Edit"><i class="fa-light fa-pen text-warning"></i></a>';
                        dropdown = `
                        <div class="dropdown d-inline">
                            <button class="btn text-info p-0" title="Change status" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-light fa-rotate-right"></i>
                            </button>
                            <div id="changeStatus" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button type="button" order-id=${row.id} value="3" onclick="changeStatus(this)" class="dropdown-item">Pending</button>
                                <button type="button" order-id=${row.id} value="4" onclick="changeStatus(this)" class="dropdown-item">Ordered</button>
                            </div>
                        </div>`
                    }

                    let previewButton = '<a title="Review" class="btn p-1"><i class="text-primary fa-sharp fa-thin fa-magnifying-glass"></i></a>'

                    return `${deleteFormTemplate} ${editButton} ${dropdown} ${previewButton}`;
                }
            },
        ],
        order: [[1, 'asc']]
    });

    const builtInDataTableSearch = $('#ordersTable_filter input[type="search"]');

    //ACTIONS
    builtInDataTableSearch.bind('keyup', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapCustomer.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapOrderStatus.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectAction.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleOrders();
                break;
            default:
        }
    });

    $('.selectCustomer input[type="text"]').on('keyup', function () {
        let text = $(this).val();
        bootstrapCustomer.empty();

        APICaller(CUSTOMER_API_ROUTE, { 'search': text }, function (response) {
            let customers = response.data;
            if (customers.length > 0) {
                bootstrapCustomer.append('<option value="" style="display:none;"></option>');
                $.each(customers, function ($key, customer) {
                    bootstrapCustomer.append(`<option value="${customer.id}"> ${customer.name} </option>`)
                })
            }
            bootstrapCustomer.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    })

    $(document).on('change', ".selectAll", function () {
        if (this.checked) {
            $('.actions').removeClass('d-none');
            $(':checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.actions').addClass('d-none');

            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });

    // Window actions
    window.changeStatus = function (e) {
        let status = $(e).attr('value');
        let order = $(e).attr('order-id');

        APIPOSTCALLER(ORDER_UPDATE_STATUS.replace(':id', order), { 'status': status }, function (response) {
            toastr['success'](response.message);
            table.DataTable().ajax.reload();
        }, function (error) {
            toastr['error']('Order has not been updated');
        })
    }

    window.deleteOrder = function (e) {
        let form = $(e).closest('form');

        let name = form.attr('data-name');
        let url = form.attr('action');

        const template = swalText(name);

        showConfirmationDialog('Selected items!', template, function () {
            APIDELETECALLER(url, function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error']('Order has not been deleted');
            });
        });
    };

    window.deleteMultipleOrders = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        showConfirmationDialog('Selected items!', template, function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(ORDER_DELETE_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                }, function (error) {
                    toastr['error']('Order has not been deleted');
                });
            });
        });

    };

    window.selectOrder = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    $('.modalCloseBtn').on('click', function () {
        modal.modal('hide');
    });

});