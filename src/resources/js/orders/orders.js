import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog, mapButtons } from '../helpers/action_helpers';

$(function () {
    $('.selectAction, .selectType, .selectCustomer, select[name="package_id"], select[name="package_id"] ').selectpicker();

    const bootstrapCustomer = $('.bootstrap-select .selectCustomer');
    const bootstrapOrderStatus = $('.bootstrap-select .selectType');
    const bootstrapSelectAction = $('.bootstrap-select .selectAction');

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

                    if (row.is_paid == false && row.status === 'Ordered') {
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
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    let payment;

                    if (row.order_payments) {
                        payment = `<a href=${PAYMENT_API.replace(':id', row.order_payments.id)}>${row.order_payments.date_of_payment}</a>`
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
                width: '7%',
                orderable: false,
                name: "product",
                class:"text-center",
                render: function (data, type, row) {
                    return '<a target="_blank" href="' + EDIT_PRODUCT_ROUTE.replace(':id', row.purchase.id) + '">' + row.purchase.name + '</a>';
                }
            },
            {
                width: '1%',
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
                width: '8%',
                orderable: false,
                name: "discount_single_sold_price",
                render: function (data, type, row) {
                    return `<span>€${row.discount_single_sold_price}</span>`
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
                render: function (data, type, row) {
                    let date;

                    if (row.package_extension_date) {
                        date = moment(row.package_extension_date);
                    } else {
                        date = moment(row.date_of_sale);
                    }

                    let currentDate = moment();
                    let daysRemaining = date.diff(currentDate, 'days');

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
                width: '8%',
                orderable: false,
                render: function (data, type, row) {

                    let dateOfSale;

                    if (row.package_extension_date) {
                        dateOfSale = moment(row.package_extension_date);
                    } else {
                        dateOfSale = moment(row.date_of_sale);
                    }

                    let dateOfPayment;
                    let delayMessage = '';

                    if (row.order_payments) {
                        dateOfPayment = moment(row.order_payments.date_of_payment);

                        // Calculate the delay in days (if any)
                        let delayInDays = dateOfPayment.diff(dateOfSale, 'days');

                        // Check if there is a delay in payment
                        if (delayInDays > 0) {
                            delayMessage = 'Payment is delayed by ' + delayInDays + ' day(s).';
                        } else {
                            delayMessage = 'Order was paid on time.';
                        }
                    }

                    return delayMessage;
                }
            },
            {
                width: '8%',
                orderable: false,
                name: 'payment.date_of_payment',
                render: function (data, type, row) {
                    let date = '';

                    if (row.order_payments) {
                        date = row.order_payments.date_of_payment;
                    }

                    return date;
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'package',
                render: function (data, type, row) {
                    return `<a href= ${PACKAGE_EDIT_ROUTE.replace(':id', row.package_id)}>${row.package}</a>`;
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
                width: '1%',
                orderable: false,
                name: "is_paid",
                render: function (data, type, row) {
                    if ((row.is_paid == 1) && (row.status == 'Paid' || row.status == 'Overdue')) {
                        return '<span class="text-success">Yes</span>';
                    } else if ((row.is_paid == 3) && (row.status == 'Refund')) {
                        return '<span class="text-warning">Refund</span>';
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
                    let deleteFormTemplate = '';

                    if (row.package && !row.is_paid && (row.status === 'Ordered' || row.status === 'Pending')) {
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

        if (typeof IS_PAID !== 'undefined') {
            data.is_paid = IS_PAID;
        }

        if (typeof PACKAGE !== 'undefined') {
            data.package = PACKAGE;
        }

        if (typeof PRODUCT_ID !== 'undefined') {
            data.product_id = PRODUCT_ID;
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

    //Reusable functionallity for mass update on orders
    let form = $('#massUpdateOrders');

    form.on('submit', function (e) {
        e.preventDefault();
        let form = e.target;
        let method = e.target.getAttribute('method');
        let action = e.target.getAttribute('action');
        let data = $(form).serialize();
        let formData = {};

        data.split('&').forEach(function (keyValue) {
            let pair = keyValue.split('=');
            let key = decodeURIComponent(pair[0]);
            let value = decodeURIComponent(pair[1] || '');

            if (formData[key]) {
                if (Array.isArray(formData[key])) {
                    formData[key].push(value);
                } else {
                    formData[key] = [formData[key], value];
                }
            } else {
                formData[key] = value;
            }
        });
        
        let price = formData.price;
        let discount = formData.discount_percent;
        let quantity = formData.sold_quantity;
        let package_id = formData.package_id;
        let date_of_sale = formData.date_of_sale;

        let searchedIds = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
        });

        if(searchedIds.length > 0) {
            $.ajax({
                url: action,
                method: 'POST',
                data: {
                    'order_id': searchedIds,
                    'price':price,
                    'sold_quantity':quantity,
                    'discount_percent':discount,
                    'date_of_sale':date_of_sale,
                    'package_id':package_id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-HTTP-Method-Override': 'PUT' // Set the X-HTTP-Method-Override header
                },
                success:function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null,false);
                },
                error: function(xhr,status,error) {
                    toastr['error'](response.message);
                }
            })
        } else {
            toastr['error']("Please select orders");
        }

    });

});