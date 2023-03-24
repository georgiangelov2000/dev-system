import {
deleteSupplier,
        getStates,
        detachSupplierCategory,
        apiSuppliers
} from './ajaxFunctions.js';

$(document).ready(function () {
    let table = $('#suppliersTable');

    $('.selectCategory, .selectCountry, .selectState, .selectAction').selectpicker();

    let dataT = table.DataTable({
        ajax: {
            url: SUPPLIER_ROUTE_API_ROUTE
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
                width: '10%',
                orderable: false,
                name: "image",
                render: function (data, type, row) {
                    if (row.image) {
                        return "<img class='img-thumbnail rounded mx-auto d-block w-100' src=" + row.image.path + row.image.name + " />"
                    } else {
                        return "<img class='img-thumbnail rounded mx-auto d-block w-100' src='https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg'/>";
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "name",
                data: "name"
            },
            {
                orderable: false,
                name: "email",
                data: "email",
            },
            {
                width: '5%',
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
                data: "website"
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
                    return row.country ? row.country.name : "";
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'state',
                render: function (data, type, row) {
                    return row.states ? row.states.name : "";
                }
            },
            {
                width: '10%',
                orderable: false,
                name: 'categories',
                render: function (data, type, row) {
                    if (row.categories) {
                        var categoryNames = row.categories.map(function (category) {
                            return "<span> " + category.name + " </span>";
                        });
                        return categoryNames.join(', ');
                    } else {
                        return '';
                    }
                }
            },
            {
                width: '15%',
                orderable: false,
                name: "notes",
                render: function (data, type, row) {
                    return "<div class='notes'>" + row.notes + "</div>"
                }
            },
            {
                orderable: false,
                width: '32%',
                render: function (data, type, row) {
                    let deleteButton = '<a data-id=' + row.id + ' onclick="deleteCurrentSupplier(this)" data-name=' + row.name + ' class="btn p-1" title="Delete"><i class="fa-solid fa-trash-can text-danger"></i></a>';
                    let editButton = '<a data-id=' + row.id + ' href=' + EDIT_SUPPLIER_ROUTE.replace(":id", row.id) + ' class="btn p-1" title="Edit"><i class="fa-solid fa-pen text-warning"></i></a>';
                    let productsButton = '<a data-id=' + row.id + ' class="btn p-1" title="Products"><i class="fa-solid fa-box-open text-primary"></i></a>';
                    let categories = "<button data-toggle='collapse' data-target='#categories_" + row.id + "' title='Categories' class='btn btn-outline-muted showCategories p-1'><i class='fa-solid fa-list' aria-hidden='true'></i></button>";
                    return `${categories} ${deleteButton} ${editButton} ${productsButton}`;
                }
            }

        ],
        lengthMenu: [[10], [10]],
        pageLength: 10,
        order: [[1, 'asc']]

    });

    //ACTIONS

    $('.bootstrap-select .selectCategory').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let categoryId = $(this).val();
        $('.selectCountry').val('9999').selectpicker('refresh');
        apiSuppliers(SUPPLIER_ROUTE_API_ROUTE, {"category": categoryId}, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        });
    });


    $('.bootstrap-select .selectCountry').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let countryId = $(this).val();
        let selectState = $('.bootstrap-select .selectState');
        selectState.empty();

        $('.selectCategory').val('').selectpicker('refresh');

        apiSuppliers(SUPPLIER_ROUTE_API_ROUTE, {"country": countryId}, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();

                getStates(STATE_ROUTE.replace(":id", countryId), function (response) {
                    if (response.length !== 0) {
                        selectState.append('<option value="">All</option>');
                        $.each(response, function (key, value) {
                            selectState.append('<option value=' + value.id + '>' + value.name + '</option>');
                        });
                    } else {
                        selectState.append('<option value="" disabled>Nothing selected</option>');
                    }
                    selectState.selectpicker('refresh');
                }, function (error) {
                    console.log(error);
                });
            }
            ;
        }, function (error) {
            console.log(error);
        });
    });

    $('.bootstrap-select .selectState').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let stateId = $(this).val();
        $('.selectCategory').val('').selectpicker('refresh');

        let countryId = $('select.selectCountry')
                .find('option:checked')
                .val();

        apiSuppliers(SUPPLIER_ROUTE_API_ROUTE, {"country": countryId, 'state': stateId}, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        });
    });

    $('tbody').on('click', '.showCategories', function () {
        var tr = $(this).closest('tr');
        var row = dataT.row(tr);

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
                        '<td><button category-id=' + category.id + ' onclick=detachCategory(this) class="btn"><i class="fa-solid fa-trash text-danger"></i></button></td>' +
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
        let related_categoryId = $(e).attr('category-id');
        let currentTr = $(e).closest('tr');
        
        detachSupplierCategory(DETACH_CATEGORY.replace(':id', related_categoryId), function (response) {
            toastr['success'](response.message);
            currentTr.remove();
            table.DataTable().ajax.reload();
        }, function (error) {
            toastr['error']('Category has not been detached');
        });
    };

    window.selectSupplier = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentSupplier = function (e) {
        let id = $(e).attr('data-id');
        let name = $(e).attr('data-name');
        let url = REMOVE_SUPPLIER_ROUTE.replace(':id', id);

        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            deleteSupplier(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error']('Supplier has not been deleted');
            });
        });
    };

    window.deleteMultipleSupplier = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function(id,index){
                deleteSupplier(REMOVE_SUPPLIER_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error']('Supplier has not been deleted');
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
                deleteMultipleSupplier();
                break;
            default:
        }
    });

    let swalText = function (params) {
        let text = '<div class="col-12 d-flex flex-wrap justify-content-center">';

        if (Array.isArray(params)) {
            params.forEach(function (name, index) {
                text += `<p class="font-weight-bold m-0">${index !== params.length - 1 ? name + ', ' : name }</p>`;
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