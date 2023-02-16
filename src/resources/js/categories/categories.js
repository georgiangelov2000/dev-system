/* global Swal, CATEGORY_ROUTE */
import { createData, updateData } from './ajaxFunctions.js';
$(document).ready(function () {

    let table = $('#categoriesTable');
    //Global category variables

    let createModal = $('#createModal');
    let editModal = $('#editModal');

    let createForm = createModal.find('form');
    let editForm = editModal.find('form');


    table.DataTable({
        ajax: {
            url: CATEGORY_ROUTE
        },
        columns: [
            {
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onchange="selectCategory(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '5%',
                name: "id",
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                orderable: false,
                name: "name",
                data: "name"
            },
            {
                orderable: false,
                name: "description",
                data: "description"
            },
            {
                orderable: false,
                width: '15%',
                render: function (data, type, row) {
                    let deleteButton = '<a data-id=' + row.id + ' data-name=' + row.name + ' class="btn deleteCategory"- onclick="deleteCategory(this)" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>';
                    let editButton = '<a data-id=' + row.id + ' class="btn editCategory" onclick="editCategory(this)" title="Edit"><i class="fa-solid fa-pencil text-warning"></i></a>';
                    let productsButton = '<a data-id=' + row.id + ' class="btn" title="Products"><i class="fa-solid fa-box-open text-primary"></i></a>';
                    return `${deleteButton} ${editButton} ${productsButton}`;
                }
            }
        ],
        order: [[1, 'desc']]
    });

    //ACTIONS

    $('#submitForm').on("click", function (e) {
        e.preventDefault();

        var actionUrl = createForm.attr('action');
        var data = createForm.serialize();

        createData(actionUrl, data,
                function (response) {
                    toastr['success'](response.message);
                    createForm.trigger('reset');
                    createModal.modal('toggle');
                    table.DataTable().ajax.reload();
                },
                function (error) {
                    toastr['error']('Category has not been created');
                    ajaxResponse(error.responseJSON.errors);
                }
        );
    });

    $('#updateForm').on("click", function (e) {
        e.preventDefault();

        var actionUrl = editForm.attr('action');
        var data = editForm.serialize();

        updateData(actionUrl, data,
                function (response) {
                    toastr['success'](response.message);
                    editForm.trigger('reset');
                    editModal.modal('toggle');
                    table.DataTable().ajax.reload();
                },
                function (error) {
                    toastr['error']('Category has not been updated');
                    ajaxResponse(error.responseJSON.errors);
                }
        );
    });

    $('.createCategory').on("click", function () {
        createForm.attr('action', STORE_CATEGORY_ROUTE);
        createModal.modal('show');
    });

    $('.selectAction').on('change', function () {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleCategories();
                break;
            default:
        }
    });

    $('.modalCloseBtn').on('click', function () {
        $('.modal').modal('hide');
    });

    window.deleteCategory = function (e) {
        let id = $(e).attr('data-id');
        let name = $(e).attr('data-name');

        let template = swalText(name);

        Swal.fire({
            title: 'Selected items!',
            html: template,
            icon: 'warning',
            background: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "GET",
                    url: REMOVE_CATEGORY_ROUTE.replace(':id', id),
                    contentType: 'application/json',
                    success: function (response) {
                        toastr['success'](response.message);
                        table.DataTable().ajax.reload();
                    },
                    error: function (errors) {
                        toastr['error'](errors.message);
                    }
                });
            }
        });
    };

    window.editCategory = function (e) {
        let id = $(e).attr('data-id');
        $.ajax({
            method: "GET",
            url: EDIT_CATEGORY_ROUTE.replace(':id', id),
            contentType: 'application/json',
            success: function (data) {
                $('#editModal').modal('show');
                editForm.attr('action', UPDATE_CATEGORY_ROUTE.replace(':id', id));
                editForm.find('input[name="name"]').val(data.name);
                editForm.find('textarea[name="description"]').val(data.description);
            },
            error: function (errors) {
                toastr['error'](errors.message);
            }
        });

    }

    window.selectCategory = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
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

    let deleteMultipleCategories = function () {
        let searchedIds = [];
        let searchedNames = [];

        $('input:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        Swal.fire({
            title: 'Selected items!',
            html: template,
            icon: 'warning',
            background: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                searchedIds.forEach(function (id, index) {
                    $.ajax({
                        method: "GET",
                        url: REMOVE_CATEGORY_ROUTE.replace(':id', id),
                        contentType: 'application/json',
                        success: function (data) {
                            toastr['success']('Category has been deleted');
                            table.DataTable().ajax.reload();
                        },
                        error: function (errors) {
                            toastr['error']('Category has not been deleted');
                            table.DataTable().ajax.reload();
                        }
                    });
                });
            }
        });
    };
    
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

    function ajaxResponse(obj) {
        let form = $('.modal').find('form');

        for (const [key, value] of Object.entries(obj)) {
            form.find('span[id*=' + key + ']').removeClass('d-none').html(value);
            form.find(':input[name*=' + key + ']').addClass('is-invalid');

            setTimeout(function () {
                form.find('span[id*=' + key + ']').addClass('d-none').html('');
                form.find(':input[name*=' + key + ']').removeClass('is-invalid');
            }, 2000);
        }
        ;
    }


});