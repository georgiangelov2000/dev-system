import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog,mapButtons } from '../helpers/action_helpers';

$(function () {
    $('.selectAction, .selectType, .selectCustomer').selectpicker();

    const bootstrapCustomer = $('.bootstrap-select .selectCustomer');
    const bootstrapOrderStatus = $('.bootstrap-select .selectType');
    const bootstrapSelectAction = $('.bootstrap-select .selectAction');

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    let dateRange = $('input[name="datetimes"]').val();

    $('.datepicker').datepicker({format: 'mm/dd/yyyy'}).datepicker('setDate', new Date());

    const applyBtn = $('.applyBtn');
    
    applyBtn.bind('click', function () {
        dateRange = $('input[name="datetimes"]').val();
        dataTable.ajax.reload(null, false);
    })

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
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.tracking_number + ' type="checkbox"> \n\
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
                    return '<a target="_blank" href="' + EDIT_PRODUCT_ROUTE.replace(':id', row.purchase.id) + '">' + row.purchase.name + '</a>';
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "sold_quantity",
                data: "sold_quantity"
            },
            {
                width: '7%',
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
                width: '5%',
                orderable: false,
                name: "discount_percent",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '6%',
                orderable: false,
                render: function (data, type, row) {
                    if(row.package_extension_date) {
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
                render: function (data, type, row) {
                    let date;
                    
                    if(row.package_extension_date) {
                        date = moment(row.package_extension_date);
                    } else {
                        date = moment(row.date_of_sale);
                    }

                    let currentDate = moment();
                    let daysRemaining = date.diff(currentDate, 'days');

                    if (currentDate.isAfter(date, 'day') && (row.status === 'Pending' || row.status === 'Ordered')) {
                        return `<span class="badge badge-danger p-2">Overdue by ${Math.abs(daysRemaining)} days</span>`;
                    } else if (row.status === 'Received') {
                        return `<span class="badge badge-success p-2">Order received</span>`;
                    }
                    else {
                        let badgeClass = daysRemaining > 5 ? 'badge-success' : 'badge-warning';
                        return `<span class="badge ${badgeClass} p-2">${daysRemaining} days remaining</span>`;
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'created_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.created_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'updated_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.updated_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'package',
                render: function (data, type, row) {
                   return `<a href= ${PACKAGE_EDIT_ROUTE.replace(':id',row.package_id)}>${row.package}</a>`;
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
                    let detachPackage = '';

                    let deleteFormTemplate = `
                    <form style='display:inline-block;' id='delete-form' action="${ORDER_DELETE_ROUTE.replace(':id', row.id)}" method='POST'>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='id' value='${row.id}'>
                        <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deleteOrder(this);'>
                            <i class='fa-light fa-trash text-danger'></i>
                        </button>
                    </form>`;

                    if(row.package && !row.is_paid && (row.status === 'Ordered' || row.status === 'Pending') ) {
                        detachPackage = `
                        <form onsubmit="detachOrder(event)" style='display:inline-block;' id='detach-form' action="${ORDER_UPDATE_STATUS.replace(':id', row.id)}" method='PUT'>
                            <input type='hidden' name='id' value='${row.id}'>
                            <button type='submit' class='btn p-0' title='Delete'>
                                <i title="Detach order" class="fal fa-unlink text-danger"></i>
                            </button>
                        </form>`;
                    }

                    if (!row.is_paid && row.status !== 'Received') {
                        editButton = '<a href="' + ORDER_EDIT_ROUTE.replace(':id', row.id) + '" class="btn p-0" title="Edit"><i class="fa-light fa-pen text-warning"></i></a>';
                        dropdown = `
                        <div class="dropdown d-inline">
                            <button class="btn text-info p-0" title="Change status" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-light fa-rotate-right"></i>
                            </button>
                            <form class="d-inline-block" action="${ORDER_UPDATE_STATUS.replace(':id', row.id)}" method="PUT">
                                <div id="changeStatus" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <button type="submit" order-id="${row.id}" value="3" onclick="event.preventDefault(); changeStatus(this)" class="dropdown-item">Pending</button>
                                <button type="submit" order-id="${row.id}" value="4" onclick="event.preventDefault(); changeStatus(this)" class="dropdown-item">Ordered</button>
                                </div>
                            </form>
                        </div>`;                    
                    }

                    let previewButton = '<a title="Review" class="btn p-0"><i class="text-primary fa-sharp fa-thin fa-magnifying-glass"></i></a>'

                    return `${deleteFormTemplate} ${detachPackage} ${editButton} ${dropdown} ${previewButton}`;
                }
            },
        ],
        order: [[1, 'asc']],
    });

    function packageData(d) {
        var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
        var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index
        
        var data = {
            'customer': bootstrapCustomer.val(),
            'status': bootstrapOrderStatus.val(),
            "search": d.search.value,
            'date_range': dateRange,
            'order_column': orderColumnName, // send the column name being sorted
            'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
            'limit': d.custom_length = d.length, 
        };
    
        if (typeof PACKAGE !== 'undefined') {
            data.package = PACKAGE;
        }
    
        return data;
    }
    

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

    window.detachOrder = function (event) {
        event.preventDefault();
        let form = event.target;
        let url = form.getAttribute('action');
        let method = form.getAttribute('method');
        
        $.ajax({
            url:url,
            method:method,
            data: {
                detach_package: true
            },
            success: function(response){
                toastr['success'](response.message);
                dataTable.ajax.reload(null,false);
            },
            error: function(error) {
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
            success: function(response){
                toastr['success'](response.message);
                dataTable.ajax.reload(null,false);
            },
            error: function(error) {
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