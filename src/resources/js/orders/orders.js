import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog, mapButtons } from '../helpers/action_helpers';

$(function () {
    $('.selectAction, .selectType, .selectCustomer, .selectPackage, .selectDriver').selectpicker();

    const bootstrapCustomer = $('.bootstrap-select .selectCustomer');
    const bootstrapSelectDriver = $('.bootstrap-select .selectDriver');
    const bootstrapOrderStatus = $('.bootstrap-select .selectType');
    const bootstrapSelectAction = $('.bootstrap-select .selectAction');
    const bootstrapSelectPackage = $('.bootstrap-select .selectPackage');

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    let dateRange = $('input[name="datetimes"]').val();

    $('input[name="datetimes"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        dateRange = $('input[name="datetimes"]').val();
        dataTable.ajax.reload(null, false);
    });

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    });

    let table = $('#ordersTable');
    let buttons = mapButtons([1, 2, 3, 4, 5, 6, 7, 8, 9]);

    let dataTable = table.DataTable({
        serverSide: true,
        dom: 'Bfrtip',
        buttons,
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
                width: '3%',
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
                class: 'text-center',
                name: "customer",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + CUSTOMER_EDIT_ROUTE.replace(':id', row.customer.id) + '" >' + row.customer.name + '</a>';
                }
            },
            {
                width: '10%',
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
                class: "text-center",
                name: "sold_quantity",
                data: "sold_quantity"
            },
            {
                width: '7%',
                orderable: false,
                class:'text-center',
                name: "single_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.single_sold_price}</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                name: "discount_single_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.discount_single_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                class:'text-center',
                name: "total_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.total_sold_price}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                class:'text-center',
                name: "original_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.original_sold_price}</span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                class:'text-center',
                name: "discount_percent",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '6%',
                orderable: false,
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
                class:'text-center',
                name: 'expired',
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
                width: '8%',
                orderable: false,
                class:'text-center',
                name: 'payment.date_of_payment',
                render: function (data, type, row) {
                    let date = '';

                    if (row.payment) {
                        date = row.payment.date_of_payment;
                    }

                    return date;
                }
            },
            {
                width: '8%',
                orderable: false,
                class:'text-center',
                name: 'package',
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
            {
                width: '15%',
                orderable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    const statusMap = {
                        1: { text: "Paid", iconClass: "fal fa-check-circle" },
                        2: { text: "Pending", iconClass: "fal fa-hourglass-half" },
                        3: { text: "Partially Paid", iconClass: "fal fa-money-bill-alt" },
                        4: { text: "Overdue", iconClass: "fal fa-exclamation-circle" },
                        5: { text: "Refunded", iconClass: "fal fa-undo-alt" },
                        6: { text: "Ordered", iconClass: "fal fa-shopping-cart" },
                    };

                    const statusInfo = statusMap[row.status] || { text: "Unknown", iconClass: "fal fa-question" };

                    let buttons = [];
                    let deleteFormTemplate = '';
                    let detachPackage = '';

                    if (row.package && (statusInfo.text === 'Ordered')) {
                        detachPackage = `
                            <form onsubmit="detachOrder(event)" style='display:inline-block;' id='detach-form' action="${ORDER_UPDATE_STATUS.replace(':id', row.id)}" method='PUT'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0' title="Detach package">
                                    <i class="fa-light fa-boxes-packing text-danger"></i>
                                </button>
                            </form>`;
                    }

                    buttons.push(`<a href="${ORDER_EDIT_ROUTE.replace(':id', row.id)}" class="btn p-0" title="Edit"><i class="fa-light fa-pen text-primary"></i></a>`);

                    if (statusInfo.text === 'Ordered') {
                        deleteFormTemplate = `
                            <form style='display:inline-block;' id='delete-form' action="${ORDER_DELETE_ROUTE.replace(':id', row.id)}" method='POST'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deleteOrder(this);'>
                                    <i class='fa-light fa-trash text-danger'></i>
                                </button>
                            </form>`;
                    }

                    let previewButton = '<a title="Review" class="btn p-0"><i class="text-primary fa-sharp fa-thin fa-magnifying-glass"></i></a>'

                    return `${deleteFormTemplate} ${detachPackage} ${buttons.join(' ')} ${previewButton}`;
                }
            }
        ],
        order: [[1, 'asc']],
    });

    function packageData(d) {
        var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
        var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

        var data = {
            "search": d.search.value,
            'date_range': dateRange,
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

        if (typeof PACKAGE !== 'undefined') {
            data.package = PACKAGE;
        }

        if (typeof PRODUCT_ID !== 'undefined') {
            data.product_id = PRODUCT_ID;
        }

        if (bootstrapSelectPackage.val()) {
            data.package = bootstrapSelectPackage.val();
        }

        return data;
    }

    bootstrapSelectDriver.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapCustomer.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapOrderStatus.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectPackage.bind('changed.bs.select', function () {
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

    $('.selectPackage input[type="text"]').on('keyup', function () {

        let text = $(this).val();
        bootstrapSelectPackage.empty();

        APICaller(PACKAGE_API_ROUTE, {
            'search': text,
            'select_json': 1,
        }, function (response) {
            let packages = response;
            if (packages.length > 0) {
                bootstrapSelectPackage.append('<option value="">All</option>');
                $.each(packages, function ($key, pack) {
                    bootstrapSelectPackage.append(`<option value="${pack.id}"> ${pack.package_name} </option>`)
                })
            }
            bootstrapSelectPackage.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    })

    $('.selectDriver input[type="text"]').on('keyup', function () {
        let text = $(this).val();
        bootstrapSelectPackage.empty();

        APICaller(USER_API_ROUTE, {
            'search': text,
            'role_id': 2,
            'no_datatable_draw': 1,
        }, function (response) {
            let packages = response;
            if (packages.length > 0) {
                bootstrapSelectPackage.append('<option value="" style="display:none;"></option>');
                $.each(packages, function ($key, pack) {
                    bootstrapSelectPackage.append(`<option value="${pack.id}"> ${pack.package_name} </option>`)
                })
            }
            bootstrapSelectPackage.selectpicker('refresh');
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

    window.detachOrder = function (event) {
        event.preventDefault();
        let form = event.target;
        let url = form.getAttribute('action');
        let method = form.getAttribute('method');

        $.ajax({
            url: url,
            method: method,
            data: {
                detach_package: true
            },
            success: function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            },
            error: function (error) {
                toastr['error'](error.message);
            }
        })
    }

    window.changeStatus = function (e) {
        let form = $(e).closest('form');
        let status = $(e).attr('value');
        let url = form.attr('action');
        let method = form.attr('method');


        $.ajax({
            url: url,
            method: method,
            data: {
                status: status
            },
            success: function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            },
            error: function (error) {
                toastr['error'](error.message);
            }
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

});