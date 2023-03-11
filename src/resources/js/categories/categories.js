/* global Swal, CATEGORY_ROUTE */
import { createData, updateData, detachSubCategory } from './ajaxFunctions.js';
$(document).ready(function () {

    let table = $('#categoriesTable');
    //Global category variables

    let createModal = $('#createModal');
    let editModal = $('#editModal');

    let createForm = createModal.find('form');
    let editForm = editModal.find('form');

    var dataT = table.DataTable({
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
                width: '25%',
                orderable: false,
                name: 'subcategories',
                render: function (data, type, row) {
                    if (row.sub_categories) {
                        var subcategoryNames = row.sub_categories.map(function (subcategory) {
                            return "<span class='font-weight-bold'> " + subcategory.name + " </span>";
                        });
                        return subcategoryNames.join(', ');
                    } else {
                        return '';
                    }
                }
            },
            {
                orderable: false,
                width: '15%',
                render: function (data, type, row) {
                    let deleteButton = '<a data-id=' + row.id + ' data-name=' + row.name + ' class="btn deleteCategory"- onclick="deleteCategory(this)" title="Delete"><i class="fa-solid fa-trash text-danger"></i></a>';
                    let editButton = '<a data-id=' + row.id + ' class="btn editCategory" onclick="editCategory(this)" title="Edit"><i class="fa-solid fa-pencil text-warning"></i></a>';
                    let productsButton = '<a data-id=' + row.id + ' class="btn" title="Products"><i class="fa-solid fa-box-open text-primary"></i></a>';
                    let subCategories = "<button data-toggle='collapse' data-target='#subcategories_" + row.id + "' title='Sub categories' class='btn btn-outline-muted showSubCategories'><i class='fa-solid fa-list' aria-hidden='true'></i></button>";
                    return `${subCategories} ${deleteButton} ${editButton} ${productsButton}` +
                        "<div class='collapse' id='subcategories_" + row.id + "'></div>";
                }
            }
            
        ],
        order: [[1, 'asc']]
    });
    
    $('tbody').on('click', '.showSubCategories', function(){
        var tr = $(this).closest('tr');
        var row = dataT.row( tr );

        if(row.child.isShown()){
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
        
        // `d` is the original data object for the row
        let tableRows = "";
        
        if(d.sub_categories.length > 0) {
            d.sub_categories.forEach(function(subcategory) {
        
            tableRows += '<tr>'+
                '<td>'+subcategory.name+'</td>'+
                '<td><button data-related-subcategory-id='+subcategory.id+' data-category-id='+d.id+' onclick=detachSubCategory(this) class="btn deleteSubCategory"><i class="fa-solid fa-trash text-danger"></i></button></td>'+
            '</tr>';
        });
        
        return '<table class="subTable subcategories w-100" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
            '<thead>' +
                '<tr>'+
                    '<th>Name</th>'+
                    '<th>Actions</th>'+
                '</tr>'+
            '</thead>' +
            '<tbody>'+
                tableRows +
            '</tbody>'+
        '</table>';

        } else {
            return false;
        }
    }

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
    
     window.detachSubCategory = function (e) {
         let related_subcategoryId = $(e).attr('data-related-subcategory-id');
         let currentTr = $(e).closest('tr'); // get the nearest parent <tr> element
         console.log(SUBCATEGORY_ROUTE.replace(':id',related_subcategoryId));
         
         detachSubCategory(SUBCATEGORY_ROUTE.replace(':id',related_subcategoryId),function(response){
             toastr['success'](response.message);
             currentTr.remove(); // remove the current <tr> element
         },function(error){
             toastr['error']('Subcategory has not been detached');
         });
     };
    
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
                editForm.find('input[name="name"]').val(data.category.name);
                editForm.find('textarea[name="description"]').val(data.category.description);

                data.allSubCategories.forEach(cat => {
                    const option =
                            `<option value="${cat.id}" class="d-flex justify-content-between align-items-center">
                                ${cat.name}
                            </option>`;
                    $('.relatedSubcategories').append(option);
                });

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
