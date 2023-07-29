import { APICaller, APICallerWithoutData } from '../ajax/methods';

$(function () {
    let table = $('#customersTable');
    $('.selectAction, .selectCountry, .selectState').selectpicker('refresh').val('').trigger('change');

    const selectCountry = $('.bootstrap-select .selectCountry');
    const selectState = $('.bootstrap-select .selectState');

    let dataTable = table.DataTable({
        serverSide: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'csv',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'excel',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'pdf',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            },
            {
                extend: 'print',
                class: 'btn btn-outline-secondary',
                exportOptions: {
                    columns: [1, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            }
        ],
        ajax: {
            url: CUSTOMER_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    'country': selectCountry.val(),
                    'state': selectState.val(),
                    "search": builtInDataTableSearch ? builtInDataTableSearch.val().toLowerCase() : '',
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectCustomer(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
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
                width: '5%',
                orderable: false,
                class:'text-center',
                name: "image",
                render: function (data, type, row) {
                    if (row.image) {
                        return "<img class='rounded mx-auto w-100' src=" + row.image.path + '/' + row.image.name + " />"
                    } else {
                        return "<img class='rounded mx-auto w-100' src='https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg'/>";
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                name: "name",
                data: "name"
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                name: "email",
                data: "email",
            },
            {
                width: '10%',
                orderable: false,
                name: "phone",
                data: "phone"
            },
            {
                width: '10%',
                orderable: false,
                name: "address",
                data: "address"
            },
            {
                width: '7%',
                orderable: false,
                name: "website",
                render: function (data, type, row) {
                    return `<a target="_blank" href="${row.website}">${row.website}<a>`
                }
            },
            {
                width: '6%',
                orderable: false,
                name: "zip",
                data: "zip"
            },
            {
                width: '5%',
                orderable: false,
                name: "country",
                render: function (data, type, row) {
                    if (row.country) {
                        return `<span title="${row.country.name}" class="flag-icon flag-icon-${row.country.short_name.toLowerCase()}"></span>`
                    } else {
                        return ``;
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'state',
                render: function (data, type, row) {
                    return row.state ? row.state.name : "";
                }
            },
            {
                width: '19%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    let paidOrders = row.paid_orders_count;
                    let overdueOrders = row.overdue_orders_count;
                    let pendingOrders = row.pending_orders_count;
                    let refundOrders = row.refund_orders_count;

                    return `<div>
                        <span class="text-success">${paidOrders} paid</span> /
                        <span class="text-danger">${overdueOrders} overdue</span> /
                        <span class="text-primary">${pendingOrders} pending</span> /
                        <span class="text-dark">${refundOrders} refund</span>
                    </div>`
                }
            },
            {
                orderable: false,
                width: '27%',
                class:'text-center',
                render: function (data, type, row) {
                    let deleteButton = '';
                    if (!row.orders_count) {
                        deleteButton = '<a data-id=' + row.id + ' onclick="deleteCurrentCustomer(this)" data-name=' + row.name + ' class="btn p-1" title="Delete"><i class="fa-light fa-trash text-danger"></i></a>';
                    }
                    let editButton = '<a data-id=' + row.id + ' href="' + CUSTOMER_EDIT_ROUTE.replace(':id', row.id) + '" class="btn p-1" title="Edit"><i class="fa-light fa-pen text-warning"></i></a>';
                    let massEdit = '<a data-id=' + row.id + ' href="' + CUSTOMER_ORDERS_ROUTE.replace(':id', row.id) + '" class="btn p-1" title="Mass edit"> <i class="fa-light fa-pen-to-square text-primary" aria-hidden="true"></i>  </a>';
                    let orders = '<a data-id=' + row.id + ' href="' + CUSTOMER_ORDERS_ROUTE.replace(':id', row.id) + '" class="btn p-1" title="Orders"> <i class="fa fa-light fa-shopping-cart text-primary" aria-hidden="true"></i>  </a>';
                    return `${deleteButton} ${editButton} ${massEdit} ${orders}`;
                }
            }

        ],
        order: [[1, 'asc']]
    });

    const builtInDataTableSearch = $('#customersTable_filter input[type="search"]');

    //ACTIONS

    builtInDataTableSearch.bind('keyup', function () {
        dataTable.ajax.reload(null, false);
    })

    selectCountry.bind('changed.bs.select', function () {
        let countryId = $(this).val();
        dataTable.ajax.reload(null, false);
        selectState.empty();

        if (countryId !== '0') {
            APICaller(STATE_ROUTE.replace(':id', countryId), function (response) {
                if (response.length > 0) {
                    selectState.append('<option value="">All</option>');
                    $.each(response, function (key, value) {
                        selectState.append('<option value=' + value.id + '>' + value.name + '</option>');
                    });
                } else {
                    selectState.append('<option value="0" disabled>Nothing selected</option>');
                }
                selectState.selectpicker('refresh');
            }, function (error) {
                console.log(error);
            });
        } else {
            selectState.selectpicker('refresh');
        }
    });

    selectState.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    });

    window.selectCustomer = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentCustomer = function (e) {
        let id = $(e).attr('data-id');
        let name = $(e).attr('data-name');
        let url = CUSTOMER_DELETE_ROUTE.replace(':id', id);

        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            APICallerWithoutData(url, function (response) {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error']('Customer has not been deleted');
            });
        });
    };

    window.deleteMultipleCustomers = function (e) {
        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function (id, index) {
                APICallerWithoutData(CUSTOMER_DELETE_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                }, function (error) {
                    console.log(error);
                    toastr['error']('Customer has not been deleted');
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

    $('.bootstrap-select .selectAction').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleCustomers();
                break;
            default:
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

});