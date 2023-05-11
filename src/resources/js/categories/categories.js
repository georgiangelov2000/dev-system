/* global Swal, CATEGORY_ROUTE */
import { 
    APIPOSTCALLER, 
    APICallerWithoutData, 
    APICaller,
    APIDELETECALLER 
} from '../ajax/methods';
$(document).ready(function () {

    let table = $('#categoriesTable');
    //Global category variables

    let createModal = $('#createModal');
    let editModal = $('#editModal');

    let createForm = createModal.find('form');
    let editForm = editModal.find('form');

    $('.selectSubCategory,.selectAction').selectpicker();

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

                    let deleteFormTemplate = "\
                    <form style='display:inline-block;' id='delete-form' action=" + REMOVE_CATEGORY_ROUTE.replace(':id', row.id) + " method='POST' data-name=" + row.name + ">\
                        <input type='hidden' name='_method' value='DELETE'>\
                        <input type='hidden' name='id' value='" + row.id + "'>\
                        <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCategory(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                    <form>\
                ";
                    let editButton = '<a data-id=' + row.id + ' class="btn editCategory" onclick="editCategory(this)" title="Edit"><i class="fa-light fa-pencil text-warning"></i></a>';
                    let productsButton = '<a onclick="getCategoryProducts(this)" data-id=' + row.id + ' class="btn p-1" title="Products"><i class="fa-light fa-box-open text-primary"></i></a>';
                    let subCategories = "<button data-toggle='collapse' data-target='#subcategories_" + row.id + "' title='Sub categories' class='btn btn-outline-muted showSubCategories'><i class='fa-light fa-list' aria-hidden='true'></i></button>";

                    return `${subCategories} ${deleteFormTemplate} ${editButton} ${productsButton}`;
                }
            }

        ],
        order: [[1, 'asc']]
    });

    $('tbody').on('click', '.showSubCategories', function () {
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
        // `d` is the original data object for the row
        let tableRows = "";
    
        if (d.sub_categories.length > 0) {
            d.sub_categories.forEach(function (subcategory) {
                let deleteFormTemplate = `
                <form 
                    style='display:inline-block;' 
                    id='delete-form' 
                    action='${SUBCATEGORY_ROUTE.replace(':id', subcategory.pivot.id)}' 
                    method='POST'>
                    <input type='hidden' name='_method' value='DELETE'>
                    <input type='hidden' name='id' value='${subcategory.pivot.id}'>
                    <button 
                        type='submit' 
                        class='btn p-1' 
                        title='Delete'
                        data-related-subcategory-id='${subcategory.pivot.id}'
                        data-category-id='${d.id}' 
                        onclick='event.preventDefault(); detachSubCategory(this);'>
                        <i class='fa-light fa-trash text-danger'></i>
                    </button>
                </form>
                `;
    
                tableRows += '<tr>' +
                    '<td>' + subcategory.name + '</td>' +
                    '<td>' +
                    deleteFormTemplate +
                    '</td>' +
                    '</tr>';
            });
    
            return '<table class="subTable subcategories w-100" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
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

    //ACTIONS

    $('#submitForm').on("click", function (e) {
        e.preventDefault();

        var actionUrl = createForm.attr('action');
        var data = createForm.serialize();

        APIPOSTCALLER(actionUrl, data,
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

        APIPOSTCALLER(actionUrl, data,
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
        let form = $(e).closest('form');
        let url = form.attr('action');

        APIDELETECALLER(url, function (response) {
            toastr['success'](response.message);
            table.DataTable().ajax.reload();
        }, function (error) {
            toastr['error'](error.message);
        });
    };

    window.deleteCategory = function (e) {
        let form = $(e).closest('form');
        let url = form.attr('action');
        let name = form.attr('data-name');        
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
                APIDELETECALLER(url, function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error'](error.message);
                });
            }
        });
    };

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form').find('select').empty();
        $(this).find('form')[0].reset();
    });

    window.getCategoryProducts = function (e) {
        let id = $(e).attr('data-id');
        APICaller(CATEGORY_ROUTE,{'category':id,'category_product':true},function(response){
            console.log(response);
        },function(error){
            console.log(error);
        })
    }

    window.editCategory = function (e) {
        let id = $(e).attr('data-id');

        APICallerWithoutData(EDIT_CATEGORY_ROUTE.replace(':id', id), function (data) {
            let responseDataSubCategories = data.allSubCategories;
    
            editModal.modal('show');
            editForm.attr('action', UPDATE_CATEGORY_ROUTE.replace(':id', id));
            editForm.find('input[name="name"]').val(data.category.name);
            editForm.find('textarea[name="description"]').val(data.category.description);
            
            APICaller(CATEGORY_ROUTE, { "category": id }, function (response) {
                    let relatedSubCategories = response.data[0].sub_categories;

                        for (let index = 0; index < responseDataSubCategories.length; index++) {
                            let idToCheck = responseDataSubCategories[index].id;
                            let matchingObj = relatedSubCategories.find(obj => obj.id == idToCheck ) ? 'selected' : "";
                            
                            editModal.find('.bootstrap-select .selectSubCategory').append(`<option ${matchingObj} value="${idToCheck}">${responseDataSubCategories[index].name}</option>`);
                        }
                        editModal.find('.bootstrap-select .selectSubCategory').selectpicker('refresh');
    
                }, function (error) {
                    toastr['error'](error.message);
                })
        }, function (error) {
            toastr['error'](error.message);
        })
    
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

        $('tbody tr input[type="checkbox"]:checked').each(function () {
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
                    APIDELETECALLER(REMOVE_CATEGORY_ROUTE.replace(':id', id), function (response) {
                        toastr['success'](response.message);
                        table.DataTable().ajax.reload();
                    }, function (error) {
                        toastr['error'](error.message);
                    });
                });
            }
        });
    };

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
