import {
    APICaller,
    APIDELETECALLER
} from '../ajax/methods.js';

import { swalText, showConfirmationDialog } from '../helpers/action_helpers.js';

$(document).ready(function () {
    let table = $('#suppliersTable');

    $('.selectCategory, .selectCountry, .selectState, .selectAction').selectpicker('refresh').val('').trigger('change');

    const selectCategory = $('.bootstrap-select .selectCategory');
    const selectCountry = $('.bootstrap-select .selectCountry');
    const selectState = $('.bootstrap-select .selectState');

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: SUPPLIER_ROUTE_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    'country': selectCountry.val(),
                    'category': selectCategory.val(),
                    "search": d.search.value,
                    'state': selectState.val()
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectSupplier(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
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
                width: '1%',
                orderable: false,
                name: "image",
                class: 'text-center',
                render: function (data, type, row) {
                    if (row.image_path) {
                        return "<img class='rounded mx-auto w-100' src=" + row.image_path + " />"
                    } else {
                        return "<img class='rounded mx-auto w-100' src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png'/>";
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "name",
                data: "name",
                class: "text-center"
            },
            {
                orderable: false,
                name: "email",
                class: "text-center",
                render: function (data, type, row) {
                    let email = '';
                    if (row.email) {
                        email = `<a href="mailto:${row.email}">${row.email}</a>`
                    }
                    return email;
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "phone",
                data: "phone",
                class: "text-center"
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
                    let website = '';
                    if (row.website) {
                        website = `<a href="${row.website}">${row.website}</a>`;
                    }
                    return website;
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
                width: '15%',
                orderable: false,
                name: 'categories',
                render: function (data, type, row) {
                    if (row.categories) {
                        var categoryNames = row.categories.map(function (category) {
                            return "<span> " + category.name + " </span>";
                        });
                        return '<div class="notes p-0">' + categoryNames.join(', ') + '</div>'
                    } else {
                        return '';
                    }
                }
            },
            {
                width: '20%',
                orderable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    console.log(row);
                    let paidPurchases = row.paid;
                    let pendingPurchases = row.pending;
                    let overduePurchases = row.overdue;
                    let refundPurchases = row.refunded;

                    return `<div>
                        <span class="text-success">${paidPurchases} paid</span> /
                        <span class="text-danger">${overduePurchases} overdue</span> /
                        <span class="text-primary">${pendingPurchases} pending</span> /
                        <span class="text-dark">${refundPurchases} refund</span>
                    </div>`
                }
            },
            {
                orderable: false,
                width: '25%',
                render: function (data, type, row) {
                    let deleteFormTemplate = '';

                    if (row.purchases_count <= 0) {
                        deleteFormTemplate = "\
                        <form method='POST' onsubmit='deleteCurrentSupplier(event)' style='display:inline-block'; id='delete-form' action=" + REMOVE_SUPPLIER_ROUTE.replace(':id', row.id) + " data-name=" + row.name + ">\
                            <input type='hidden' name='_method' value='DELETE'>\
                            <input type='hidden' name='id' value='" + row.id + "'>\
                            <button class='btn p-0' title='Delete'><i class='fa-light fa-trash text-danger'></i></button>\
                        </form>\ ";
                    }

                    let editButton = '<a data-id=' + row.id + ' href=' + EDIT_SUPPLIER_ROUTE.replace(":id", row.id) + ' class="btn p-0" title="Edit"><i class="fa-light fa-pen text-primary"></i></a>';
                    let massEdit = '<a data-id=' + row.id + ' href=' + MASS_EDIT_PURCHASES.replace(":id", row.id) + ' class="btn p-0" title="Mass edit"><i class="fa-light fa-pen-to-square text-primary"></i></a>';
                    let categories = "<button data-toggle='collapse' data-target='#categories_" + row.id + "' title='Categories' class='btn btn-outline-muted showCategories p-0'><i class='fa-light fa-list' aria-hidden='true'></i></button>";
                    return `${categories} ${deleteFormTemplate} ${editButton} ${massEdit}`;
                }
            }

        ],
        order: [[1, 'asc']]
    });

    //ACTIONS

    selectCountry.bind('changed.bs.select', function () {
        let countryId = $(this).val();
        dataTable.ajax.reload(null, false);
        selectState.empty();

        APICaller(LOCATION_API_ROUTE, { 'country_id': countryId }, function (response) {
            let data = response;
            if (data.length > 0) {
                selectState.append('<option value="">All</option>');
                $.each(data, function (key, value) {
                    selectState.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                selectState.append('<option value="0" disabled>Nothing selected</option>');
            }
            selectState.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        });

    });

    selectCategory.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    selectState.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    $('tbody').on('click', '.showCategories', function () {
        var tr = $(this).closest('tr');
        var row = dataTable.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });

    function format(d) {
        let tableRows = "";
        console.log(d.categories);
        if (d.categories.length > 0) {
            d.categories.forEach(function (category) {

                tableRows += '<tr>' +
                    '<td>' + category.name + '</td>' +
                    '<td><button category-id=' + category.id + ' onclick=detachCategory(this) class="btn"><i class="fa-light fa-trash text-danger"></i></button></td>' +
                    '</tr>';
            });

            return '<table class="subTable categories w-100" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<thead>' +
                '<tr>' +
                '<th>Name</th>' +
                '<th>Actions</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' +
                tableRows +
                '</tbody>' +
                '</table>';

        } else {
            return false;
        }
    }

    window.detachCategory = function (e) {
        let category = $(e).attr('category-id');
        let tr = $(e).closest('tr');

        APICaller(DETACH_CATEGORY.replace(':id', category), function (response) {
            tr.remove();
            toastr['success'](response.message);
            dataTable.ajax.reload(null, false);
        }, function (error) {
            toastr['error'](error.message);
        })
    };

    window.selectSupplier = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentSupplier = function (e) {
        e.preventDefault();
        const action = e.target.getAttribute('action');
        const name = e.target.getAttribute('data-name');

        const template = swalText(name);

        showConfirmationDialog('Selected suppliers!', template, function () {
            APIDELETECALLER(action, function (response) {

                if (response.status === 500) {
                    toastr['error'](response.responseJSON.message);
                } else {
                    toastr['success'](response.message);
                }

                dataTable.ajax.reload(null, false);
            }, function (error) {
                toastr['error'](error.message);
            })
        })
    };

    window.deleteMultipleSupplier = function (e) {

        const searchedIds = [];
        const searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        const template = swalText(searchedNames);

        showConfirmationDialog('Selected suppliers', template, function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(REMOVE_SUPPLIER_ROUTE.replace(":id", id), function (response) {
                    if (response.status === 500) {
                        toastr['error'](response.responseJSON.message);
                    } else {
                        toastr['success'](response.message);
                    }
                    dataTable.ajax.reload(null, false);
                }, function (error) {
                    toastr['error'](error.message);
                })
            })
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

    $('.bootstrap-select .selectAction').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleSupplier();
                break;
            default:
        }
    });
});