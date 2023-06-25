import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';
import {showConfirmationDialog,mapButtons,swalText} from '../helpers/action_helpers';

$(function () {
    let table = $('#packagesTable');

    $('.selectPackageType, .selectDelieveryMethod, .selectCustomer, .selectAction')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    let bootstrapPackageType = $('.bootstrap-select .selectPackageType');
    
    let bootstrapDelieveryMethod = $('.bootstrap-select .selectDelieveryMethod');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');

    let startDate = moment().startOf('month').format('YYYY-MM-DD')
    let endDate = moment().endOf('month').format('YYYY-MM-DD')
    let deliveryRange = startDate + ' - ' + endDate;

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: PACKAGE_API_ROUTE,
            data: function (d){
                let orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                let orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index
                
                return $.extend({},d,{
                    "search": d.search.value,
                    'package': bootstrapPackageType.val(),
                    'delievery': bootstrapDelieveryMethod.val(),
                    'customer' : bootstrapSelectCustomer.val(),
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
                       <input name="checkbox" class="form-check-input" onclick="selectPackage(this)" data-id=' + row.id + ' data-name= ' + row.invoice_number + ' type="checkbox"> \n\
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
                width: "10%",
                name: 'package_name',
                data: 'package_name',
            },
            {
                orderable: false,
                width: "15%",
                name: 'tracking_number',
                data: 'tracking_number',
            },
            {
                orderable: false,
                width: "1%",
                name: 'package_type',
                data: 'package_type',
            },
            {
                orderable: false,
                width: "5%",
                name: 'delievery_method',
                class: 'text-center',
                render: function (data, type, row) {
                    if (row.delievery_method === 'Air') {
                        return '<i title="Air" class="fa-light fa-plane"></i>';
                    } else if (row.delievery_method === 'Ground') {
                        return '<i title="Ground" class="fa-light fa-truck"></i>'
                    } else if (row.delievery_method === 'Sea') {
                        return '<i title="Sea" class="fa-light fa-water"></i>';
                    }
                },
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    let paidPercentage = row.paid_percentage;
            
                    let progressHTML = `
                        <div class="progress" title=${paidPercentage}>
                            <div 
                                class="bg-success progress-bar progress-bar-striped progress-bar-animated" 
                                role="progressbar" 
                                aria-valuenow="${paidPercentage}" 
                                aria-valuemin="0" 
                                aria-valuemax="100" 
                                style="width: ${paidPercentage}%"
                            >
                                <span class="sr-only">${paidPercentage}% Complete (success)</span>
                            </div>
                    `;
            
                    return progressHTML;
                }
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    let paidOrdersCount = row.paid_orders_count;
                    let unpaidOrdersCount = row.unpaid_orders_count;
            
                    let displayText = `<a class='text-success' ">${paidOrdersCount} paid</a> / <a class='text-danger'>${unpaidOrdersCount} unpaid</a>`;
            
                    return displayText;
                }
            },
            {
                orderable: false,
                width: "10%",
                name: 'expected_delivery_date',
                data: 'expected_delivery_date',
            },
            {
                orderable: false,
                width: "10%",
                name: 'delievery_date',
                data: 'delievery_date',
            },
            {
                orderable:false,
                width: "5%",
                name:'created_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.created_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                orderable:false,
                width: "5%",
                name: 'updated_at',
                render: function (data, type, row) {
                    return `<span>${moment(row.updated_at).format('YYYY-MM-DD')}<span>`
                }
            },
            {
                orderable:false,
                width: "5%",
                name: 'expired',
                class:'text-center',
                render: function (data, type, row) {
                    var dateOfDelivery = moment(row.expected_delivery_date);
                    var currentDate = moment();
                    var daysRemaining = dateOfDelivery.diff(currentDate, 'days');

                    if (currentDate.isAfter(dateOfDelivery, 'day') && !row.is_it_delivered) {
                        return `<span class="badge badge-danger p-2">Overdue by ${Math.abs(daysRemaining)} days</span>`;
                    } else if (row.status === 'Received') {
                        return `<span class="badge badge-success p-2">Package delievered</span>`;
                    }
                    else {
                        var badgeClass = daysRemaining > 5 ? 'badge-success' : 'badge-warning';
                        return `<span class="badge ${badgeClass} p-2">${daysRemaining} days remaining</span>`;
                    }

                }
            },
            {
                orderable:false,
                width:'1%',
                name:'is_it_delivered',
                class: 'text-center',
                render:function(data,type,row) {
                    if(row.is_it_delivered){
                        return `<span class="text-success">Yes</span>`
                    } else {
                        return `<span class="text-danger">No</span>`
                    }
                }
            },
            {
                orderable: false,
                width: '50%',
                name: 'actions',
                class:'text-center',
                render: function (data, type, row) {
                    let deliveredBtn = '';

                    let deleteFormTemplate = "\
                    <form style='display:inline-block;' action=" + PACKAGE_DELETE_ROUTE.replace(':id', row.id) + " id='delete-form' method='POST' data-name='" + row.package_name + "' >\
                        <input type='hidden' name='_method' value='DELETE'>\
                        <input type='hidden' name='id' value='" + row.id + "'>\
                        <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCurrentPackage(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                    </form>\
                    ";                    

                    let editButton = '<a href='+PACKAGE_EDIT_ROUTE.replace(':id',row.id)+' data-id=' + row.id + 'class="btn p-1" title="Edit"><i class="fa-light fa-pen text-warning"></i></a>';

                    let delieveryDropdown = `
                    <div class="dropdown d-inline">
                        <button class="btn text-info p-0" title="Change method" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

                let orders = `<a href="${PACKGE_MASS_DELETE_ORDERS.replace(":id",row.id)}" class="btn p-1" title="Orders"><i class="text-success fa fa-light fa-shopping-cart" aria-hidden="true"></i></a>`;

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
                            </form>
                        </div>
                    </div>
                    `;

                    if(!row.is_it_delivered) {
                        deliveredBtn = `<button class="btn p-0 text-success" type="button"><i class="fa-light fa-check"></i></button>`;
                    }

                    return ` ${deleteFormTemplate} ${editButton} ${orders} ${packageDropdown}${delieveryDropdown} ${deliveredBtn}`;
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

    bootstrapPackageType.bind('changed.bs.select',function(e, clickedIndex, isSelected, previousValue){
        dataTable.ajax.reload(null, false);
    })

    bootstrapDelieveryMethod.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    });

    bootstrapSelectCustomer.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    });

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

        showConfirmationDialog('Selected packages',template,function(){
            APIDELETECALLER(PACKAGE_DELETE_ROUTE.replace(':id', id), function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error'](error.message);
            });
        },function(error){
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