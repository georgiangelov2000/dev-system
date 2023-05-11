import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';

$(function () {
    let table = $('#packagesTable');

    $('.selectPackageType, .selectDelieveryMethod, .selectCustomer, .selectAction')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    let bootstrapPackageType = $('.bootstrap-select .selectPackageType');
    let bootstrapDelieveryMethod = $('.bootstrap-select .selectDelieveryMethod');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');

    let dataTable = table.dataTable({
        ajax: {
            url: PACKAGE_API_ROUTE
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
                orderable: false,
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
                width: "5%",
                name: 'tracking_number',
                data: 'tracking_number',
            },
            {
                orderable: false,
                width: "10%",
                name: 'package_type',
                data: 'package_type',
            },
            {
                orderable: false,
                width: "10%",
                name: 'delievery_method',
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
                orderable: false,
                width: "10%",
                name: 'package_price',
                render: function (data, type, row) {
                    return `${row.package_price} <i class="fa-light fa-euro-sign"></i>`
                }
            },
            {
                orderable: false,
                width: "10%",
                name: 'delievery_date',
                data: 'delievery_date',
            },
            {
                orderable: false,
                width: "5%",
                name: 'orders_count',
                data: 'orders_count',
            },
            {
                orderable:false,
                width: "15%",
                name:'customer_notes',
                render: function (data, type, row) {
                    return `<div class="notes">${row.customer_notes}</div>`
                }
            },
            {
                orderable:false,
                width: "15%",
                name:'package_notes',
                render: function (data, type, row) {
                    return `<div class="notes">${row.package_notes}</div>`
                }
            },
            {
                orderable: false,
                width: '20%',
                name: 'actions',

                render: function (data, type, row) {

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
                        <button class="btn text-info p-0" title="Change delievery" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

                    let packageDropdown = `
                    <div class="dropdown d-inline">
                        <button class="btn text-primary p-0" title="Change package" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-light fa-cube"></i>
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

                    return ` ${deleteFormTemplate} ${editButton} ${packageDropdown} ${delieveryDropdown}`;
                }
            }
        ]
    })

    bootstrapPackageType.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let packageVal = $(this).val();
        APICaller(PACKAGE_API_ROUTE, { "package": packageVal }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapDelieveryMethod.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let delieveryVal = $(this).val();
        APICaller(PACKAGE_API_ROUTE, { "delievery": delieveryVal }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapSelectCustomer.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let customerVal = $(this).val();
        APICaller(PACKAGE_API_ROUTE, { "customer": customerVal }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
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
                    table.DataTable().ajax.reload();
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
                    table.DataTable().ajax.reload();
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
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
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error']('Package has not been deleted');
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

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(PACKAGE_DELETE_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error'](response.message);
                });
            });
        });

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

    let swalText = function (params) {
        let text = '<div class="col-12 d-flex flex-wrap justify-content-center">';

        if (Array.isArray(params)) {
            params.forEach(function (name, index) {
                text += `<p class="font-weight-bold m-0">${index !== params.length - 1 ? name + ', ' : name}</p>`;
            });
        } else {
            text += `<p class="font-weight-bold m-0">${params}</p>`;
        }

        text += '</div>';

        return text;
    };

    let confirmAction = function (title, message, confirmButtonText, cancelButtonText, callback) {
        Swal.fire({
            title: title,
            html: message,
            icon: 'warning',
            background: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    };

})