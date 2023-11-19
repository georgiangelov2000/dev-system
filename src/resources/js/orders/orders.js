import { APICaller,APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog, mapButtons } from '../helpers/action_helpers';
import { numericFormat } from '../helpers/functions';
import { deliveryStatusesWithIcons, statusPaymentsWithIcons } from '../helpers/statuses';

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
                    return row.payment.payment_status === 2 ? '<div div class="form-check">\n\
                    <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.tracking_number + ' type="checkbox"> \n\
                    </div>' : ''
                }
            },
            {
                width:"1%",
                render:function(data,type,row) {
                    return `<span class="font-weight-bold">${row.id}</span>`;
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
                width: '8%',
                orderable: false,
                name: "purchase",
                class: "text-center",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + EDIT_PRODUCT_ROUTE.replace(':id', row.purchase.id) + '">' + row.purchase.name + '</a>';
                }
            },
            {
                width: '5%',
                orderable: false,
                class: "text-center",
                name: "tracking_number",
                render: function(data,type,row) {
                    return `<b class="text-primary ">${row.tracking_number}</b>`;
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
                width: '5%',
                orderable: false,
                class:'text-center',
                name: "single_sold_price",
                render: function (data, type, row) {
                    
                    return `<span>${numericFormat(row.single_sold_price)}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                class:'text-center',
                name: "total_sold_price",
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.total_sold_price)}</span>`
                }
            },
            {
                width: '7%',
                orderable: false,
                class:'text-center',
                name: "original_sold_price",
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.original_sold_price)}</span>`
                }
            },        
            {
                width: '1%',
                orderable: false,
                class:'text-center',
                name: "discount_percent",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '1%',
                orderable: false,
                class:'text-center',
                name:'is_it_delivered',
                render: function (data, type, row) {
                  return row.is_it_delivered ? 
                  `<i class="fa-light fa-check text-success"></i>` : 
                  `<i class="fa-light fa-x text-danger"></i>`;
                }
            },
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
                render: function (data, type, row) {
                    const isDelivered = row.is_it_delivered;
                    let deliveryDate = '';

                    if(isDelivered) {
                        deliveryDate = row.delivery_date;
                    }

                    const formattedDate = deliveryDate ? moment(deliveryDate).format('MMM DD, YYYY') : '';

                    return `<span>${formattedDate}</span>`;
                }
            },
            {
                width: '10%',
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
            {
                width: '5%',
                orderable: false,
                class: 'text-center',
                name: 'package',
                render: function (data, type, row) {
                    return row.package
                        ? `<a href=${PACKAGE_EDIT_ROUTE.replace(':id', row.package.id)}>${row.package.package_name}</a>`
                        : '';
                }
            },            
            {
                width: '7%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    const statusData = deliveryStatusesWithIcons[row.payment.delivery_status] || { text: "Unknown", iconClass: "fal fa-question" };

                    return `
                    <div title="${statusData.label}" class="status">
                    <span class="icon"><i class="${statusData.iconClass}"></i></span>
                    </div>`;
                }
            },
            {
                width: '5%',
                orderable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    let edit = `
                    <a class="dropdown-item text-primary" href="${ORDER_EDIT_ROUTE.replace(':id', row.id)}">
                        <i class="fa-light fa-pen text-primary"></i> Edit
                    </a>`;

                    let detachPackage =``;
                    let deleteForm = ``;

                    if(row.package && !row.is_it_delivered) {
                        detachPackage = `
                        <form onsubmit="detachOrder(event)" class="dropdown-item" style='display:inline-block;' id='detach-form' action="${ORDER_UPDATE_STATUS.replace(':id', row.id)}" method='PUT'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0 text-danger'>
                                    <i class="fa-light fa-boxes-packing text-danger"></i> Detach package
                                </button>
                        </form>`;
                    }

                    if(!row.is_it_delivered) {
                        deleteForm = `
                            <form style='display:inline-block;' data-name="${row.id}-${row.purchase.name}" class="dropdown-item" id='delete-form' action="${ORDER_DELETE_ROUTE.replace(':id', row.id)}" method='POST'>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0 text-danger' title='Delete' onclick='event.preventDefault(); deleteOrder(this);'>
                                    <i class='fa-light fa-trash text-danger'></i> Delete
                                </button>
                            </form>`;
                    }

                    return `
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa-light fa-list" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu" role="menu">
                            ${edit}
                            ${detachPackage}
                            ${deleteForm}
                        </div>
                    </div>`;
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
        const isChecked = this.checked;
        $('.actions').toggleClass('d-none', !isChecked);
        $(':checkbox').prop('checked', isChecked);
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
        const searchedIds = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).attr('data-id');
        }).get();
    
        const searchedNames = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).attr('data-name');
        }).get();

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
        const isChecked = $('tbody input[type="checkbox"]:checked').length > 0;
        $('.actions').toggleClass('d-none', !isChecked);
    };

});