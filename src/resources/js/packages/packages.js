import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import { showConfirmationDialog, openModal, swalText, closeModal, submit } from '../helpers/action_helpers';

$(function () {

    $('input[name="delivery_date"]').datepicker({
        format: 'yyyy-mm-dd'
    });

    let table = $('#packagesTable');

    $('.selectPackageType, .selectDelieveryMethod, .selectCustomer, .selectAction')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    let editModal = $('#editModal');
    let editSubmitButton = editModal.find('#submitForm');
    let closeModalBtn = $('.modalCloseBtn');
    let deliveryRange;

    let bootstrapPackageType = $('.bootstrap-select .selectPackageType');

    let bootstrapDelieveryMethod = $('.bootstrap-select .selectDelieveryMethod');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: PACKAGE_API_ROUTE,
            data: function (d) {
                let orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                let orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index
                return $.extend({}, d, {
                    "search": d.search.value,
                    'package': bootstrapPackageType.val(),
                    'delivery': bootstrapDelieveryMethod.val(),
                    'customer': bootstrapSelectCustomer.val(),
                    'delivery_date': deliveryRange,
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
                    'limit': d.custom_length = d.length,
                })

            }
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectPackage(this)" data-id=' + row.id + ' data-name= ' + row.tracking_number + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                orderable: true,
                width: "1%",
                name: "id",
                render: function (data, type, row) {
                    return `<strong>${row.id}</strong>`
                }
            },
            {
                orderable: false,
                class:'text-center',
                width: "10%",
                name: 'package_name',
                data: 'package_name',
            },
            {
                orderable: false,
                width:'10%',
                class:'text-center',
                name:'tracking_number',
                data:'tracking_number',
            },
            {
                orderable: false,
                width: "1%",
                name: 'package_type',
                render: function (data, type, row) {
                    let badge = '';
                    switch (row.package_type) {
                        case 1:
                            badge = 'Standart'
                            break;
                        case 2:
                            badge = 'Express'
                            break;
                        case 3:
                            badge = 'Overnight'
                            break;
                        default:
                            badge = 'Invalid status please check the system!';
                    }
                    return `<span>${badge}</span>`
                }
            },
            {
                orderable: false,
                width: "5%",
                name: 'delivery_method',
                class: 'text-center',
                render: function (data, type, row) {
                    let badgeIcon = '';
                    switch (row.delivery_method) {
                        case 1:
                            badgeIcon = '<i title="Ground" class="fa-light fa-truck"></i>';
                            break;
                        case 2:
                            badgeIcon = '<i title="Air" class="fa-light fa-plane"></i>';
                            break;
                        case 3:
                            badgeIcon = '<i title="Sea" class="fa-light fa-water"></i>';
                            break;
                        default:
                            badgeIcon = 'Invalid method please check the system!';
                            break;
                    }

                    return `${badgeIcon}`;
                },
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    let paidPercentage = 0;

                    let ordersCount = row.orders_count;
                    let paidOrdersCount = (row.overdue_orders_count + row.paid_orders_count);

                    paidPercentage = (paidOrdersCount / ordersCount) * 100;

                    paidPercentage = Math.round(paidPercentage, 2);

                    let progressHTML = `
                        <div class="progress" title="${paidPercentage}% paid orders">
                            <div 
                                class="bg-success progress-bar progress-bar-striped progress-bar-animated" 
                                role="progressbar" 
                                aria-valuenow="${paidPercentage}%" 
                                aria-valuemin="0" 
                                aria-valuemax="100" 
                                style="width: ${paidPercentage}%"
                            >
                                <span class="sr-only">${paidPercentage}% Complete (success)</span>
                            </div>
                    `;

                    return `${progressHTML}`;
                }
            },
            {
                width: '5%',
                orderable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    let orders = row.orders_count;

                    return `<span >${orders}</span>`;
                }
            },
            {
                orderable: false,
                width: "15%",
                class:'text-center',
                name: 'expected_delivery_date',
                data: 'expected_delivery_date',
            },
            {
                orderable: false,
                width: "10%",
                name: 'delivery_date',
                data: 'delivery_date',
            },
            {
                orderable: false,
                width: "20%",
                name: 'expired',
                class: 'text-center',
                render: function (data, type, row) {
                    let expectedDeliveryDate = moment(row.expected_delivery_date);
                    let officialDeliveryDate = moment(row.delivery_date);
                    let currentDate = moment();
                    let delayInDays;
                    let delayMessage = '';

                    if (officialDeliveryDate.isValid() && expectedDeliveryDate.isValid()) {
                        delayInDays = officialDeliveryDate.diff(expectedDeliveryDate, 'days');

                        if (delayInDays > 0) {
                            delayMessage = 'Package has been delivered with a delay of ' + delayInDays + ' day(s).';
                        } else {
                            delayMessage = 'Package was delivered on time.';
                        }
                    } else {
                        delayInDays = expectedDeliveryDate.diff(currentDate, 'days');

                        if (delayInDays > 0) {
                            delayMessage = 'Package has not been delivered with a delay of ' + delayInDays + ' day(s).';
                        } else {
                            delayMessage = 'Expected Delivery Date is ' + moment().to(expectedDeliveryDate);
                        }
                    }

                    return delayMessage;
                }
            },
            {
                orderable: false,
                width: '1%',
                name: 'is_it_delivered',
                class: 'text-center',
                render: function (data, type, row) {
                    if (row.is_it_delivered) {
                        return `<span class="text-success">Yes</span>`
                    } else {
                        return `<span class="text-danger">No</span>`
                    }
                }
            },
            {
                orderable: false,
                width: '52%',
                name: 'actions',
                class: 'text-center',
                render: function (data, type, row) {
                    let deliveredBtn = '';
                    let deleteFormTemplate = '';

                    let editButton = '<a href=' + PACKAGE_EDIT_ROUTE.replace(':id', row.id) + ' data-id=' + row.id + 'class="btn p-1" title="Edit"><i class="fa-light fa-pen text-primary"></i></a>';

                    let delieveryDropdown = `
                    <div class="dropdown d-inline">
                        <button class="btn text-primary p-0" title="Change method" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-light fa-truck-ramp"></i>
                        </button>
                        <div id="changeDelieveryMethod" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form method="POST" id="delivery-form">
                                <input type="hidden" name="order_id" value="${row.id}">
                                <button type="button" value="1" class="dropdown-item change-delivery-method-btn">Ground</button>
                                <button type="button" value="2" class="dropdown-item change-delivery-method-btn">Air</button>
                                <button type="button" value="3" class="dropdown-item change-delivery-method-btn">Sea</button>
                            </form>
                        </div>
                    </div>
                `;

                    let orders = `<a href="${PACKGE_MASS_DELETE_ORDERS.replace(":id", row.id)}" class="btn p-1" title="Orders"><i class="text-primary fa fa-light fa-shopping-cart" aria-hidden="true"></i></a>`;

                    let packageDropdown = `
                    <div class="dropdown d-inline">
                        <button class="btn text-primary p-0" title="Change type" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-light fa-rotate-right"></i>
                        </button>
                        <div id="changePackageType" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form method="POST" id="package-form">
                                <input type="hidden" name="order_id" value="${row.id}">
                                <button type="button" order-id=${row.id} value="1" class="dropdown-item change-package-type-btn">Standart</button>
                                <button type="button" order-id=${row.id} value="2" class="dropdown-item change-package-type-btn">Express</button>
                                <button type="button" order-id=${row.id} value="3" class="dropdown-item change-package-type-btn">Overnight</button>
                            </form>success
                        </div>
                    </div>
                    `;

                    if (!row.is_it_delivered) {
                        deliveredBtn = `<button data-id=${row.id} title="Mark as delivered" class="btn p-0 text-primary delivered-btn" type="button"><i class="fa-light fa-check"></i></button>`;
                        deleteFormTemplate = "\
                        <form style='display:inline-block;' action=" + PACKAGE_DELETE_ROUTE.replace(':id', row.id) + " id='delete-form' method='POST' data-name='" + row.package_name + "' >\
                            <input type='hidden' name='_method' value='DELETE'>\
                            <input type='hidden' name='id' value='" + row.id + "'>\
                            <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCurrentPackage(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                        </form>\
                        "
                    }

                    return `${deleteFormTemplate} ${editButton} ${orders} ${packageDropdown}${delieveryDropdown} ${deliveredBtn}`;
                }
            }
        ],
        order: [[1, 'asc']]
    })

    $('input[name="datetimes"]').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');

        deliveryRange = startDate + ' - ' + endDate;
        $(this).val(deliveryRange);

        dataTable.ajax.reload();
    });

    // Actions

    bootstrapPackageType.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapDelieveryMethod.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    });

    bootstrapSelectCustomer.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    });

    closeModalBtn.on('click', function () {
        closeModal(editModal);
    })

    editSubmitButton.on('click', function (e) {
        e.preventDefault();
        submit(e, editModal, table);
    })

    $(document).on('click', '.change-delivery-method-btn', function (e) {
        e.preventDefault();

        let deliveryMethod = $(this).val();
        let form = $('#delivery-form');
        let orderId = form.find('[name="order_id"]').val();

        $.ajax({
            url: PACKAGE_UPDATE_STATUS_ROUTE.replace(':id', orderId),
            type: 'PUT',
            data: {
                delivery_method: deliveryMethod
            },
            success: function (response) {
                if (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '.change-package-type-btn', function (e) {
        e.preventDefault();

        let package_type = $(this).val();
        let form = $('#package-form');
        let orderId = form.find('[name="order_id"]').val();

        $.ajax({
            url: PACKAGE_UPDATE_STATUS_ROUTE.replace(':id', orderId),
            type: 'PUT',
            data: {
                package_type: package_type
            },
            success: function (response) {
                if (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });


    // Window actions

    $(document).on('click', '.delivered-btn', function (e) {
        openModal(editModal, PACKAGE_UPDATE_ROUTE.replace(':id', $(this).data('id')));
    });

    window.selectPackage = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentPackage = function (e) {
        let form = $(e).closest('form');

        let name = form.attr('data-name');
        let url = form.attr('action');

        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            APIDELETECALLER(url, function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error'](error.message);
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

        showConfirmationDialog('Selected packages', template, function () {
            APIDELETECALLER(PACKAGE_DELETE_ROUTE.replace(':id', id), function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error'](error.message);
            });
        }, function (error) {
            console.log(error);
        })
    };

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
})