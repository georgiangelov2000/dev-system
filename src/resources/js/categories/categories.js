/* global Swal, CATEGORY_ROUTE */
import { 
    APIPOSTCALLER, 
    APICallerWithoutData, 
    APIDELETECALLER 
} from '../ajax/methods';
import {
    openModal,
    closeModal,
    swalText,
    submit,
    update,
    ajaxResponse,
    showConfirmationDialog,

} from '../helpers/action_helpers';
$(function(){

    let table = $('#categoriesTable');
    //Global category variables

    let createModal = $('#createModal');
    let editModal = $('#editModal');

    let submitForm = $('#submitForm');
    let updateForm = $('#updateForm');

    let createForm = createModal.find('form');
    let editForm = editModal.find('form');
    

    $('.selectSubCategory,.selectAction')
    .selectpicker('refresh')
    .val('')
    .trigger('change');

    var dataT = table.DataTable({
        ajax: {
            url: CATEGORY_ROUTE
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = 
                    `<div class='form-check'> 
                        <input 
                            name = 'checkbox'
                            class='form-check-input' 
                            type='checkbox' 
                            onchange='selectCategory(this)'
                            data-id = '${row.id}'
                            data-name = '${row.name}'
                        />
                    </div>`;

                    return `${checkbox}`;
                }
            },
            {
                width: '2%',
                name: "id",
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${row.id}</span>`;
                }
            },
            {
                orderable: false,
                name: 'name',
                width: '15%',
                render: function (data, type, row) {
                    return `<span>${row.name}</span>`;
                }
            },
            {
                orderable: false,
                name: "description",
                render: function (data, type, row) {
                    return `<span>${row.description}</span>`;
                }
            },
            {
                width: '25%',
                orderable: false,
                name: 'subcategories',
                render: function(data,type,row) {
                    if (!row.sub_categories) {
                        return '';
                    }
                    const subcategoryNames = row.sub_categories.map((subcategory) => {
                        return `<span class="font-weight-bold">${subcategory.name}</span>`;
                      });
                    return subcategoryNames.join(', ');
                }
            },
            {
                orderable: false,
                width: '15%',
                render: function (data, type, row) {

                const deleteFormTemplate = `
                    <form style="display:inline-block;" id="delete-form" action="${REMOVE_CATEGORY_ROUTE.replace(':id', row.id)}" method="POST" data-name="${row.name}">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="id" value="${row.id}">
                      <button type="submit" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteCategory(this);"><i class="fa-light fa-trash text-danger"></i></button>
                    </form>
                  `;

                  const editButton = `<a data-id="${row.id}" class="btn editCategory" onclick="editCategory(this)" title="Edit"><i class="fa-light fa-pencil text-warning"></i></a>`;
                  const productsButton = `<a data-id="${row.id}" class="btn p-1" title="Products"><i class="fa-light fa-box-open text-primary"></i></a>`;
                  const subCategories = `<button data-toggle="collapse" data-target="#subcategories_${row.id}" title="Sub categories" class="btn btn-outline-muted showSubCategories"><i class="fa-light fa-list" aria-hidden="true"></i></button>`;
              
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
            row.child(format(row.data().sub_categories)).show();
            tr.addClass('shown');
        }
    });

    function format(d) {
        // `d` is the original data object for the row
        let tableRows = "";
    
        if (d.length > 0) {
            d.forEach(function (subcategory) {
                
                let deleteFormTemplate = `
                <form 
                    style='display:inline-block;' 
                    id='delete-form' 
                    action='${SUBCATEGORY_ROUTE.replace(':id', subcategory.id)}' 
                    method='POST'>
                    <input type='hidden' name='_method' value='DELETE'>
                    <input type='hidden' name='id' value='${subcategory.id}'>
                    <button 
                        type='submit' 
                        class='btn p-1' 
                        title='Delete'
                        data-related-subcategory-id='${subcategory.id}'
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

    submitForm.on("click", function (e) {
        e.preventDefault();

        var actionUrl = createForm.attr('action');
        var data = createForm.serialize();

        APIPOSTCALLER(actionUrl, data,
            function (response,xhr) {
                const status = xhr;

                if(status == 'success'){
                    toastr['success'](response.message);
                    createForm.trigger('reset');
                    createModal.modal('toggle');
                    table.DataTable().ajax.reload();
                } else {
                    toastr['error'](response.message);
                    ajaxResponse(response.responseJSON, createModal);
                }
            },
            function (error) {
                toastr['error'](error.message)
            }
        );
    });

    $('#updateForm').on("click", function (e) {
        e.preventDefault();

        var actionUrl = editForm.attr('action');
        var data = editForm.serialize();

        APIPOSTCALLER(actionUrl, data,
            function (response,xhr) {
                const status = xhr;

                if(status == 'success') {
                    toastr['success'](response.message);
                    editForm.trigger('reset');
                    editModal.modal('toggle');
                    table.DataTable().ajax.reload();
                } else {
                    toastr['error'](response.message);
                    ajaxResponse(response.responseJSON, editModal);
                }
            },
            function (error) {
                toastr['error'](error.message)
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

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form').find('select').empty();
        $(this).find('form')[0].reset();
    });

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

    //Window events
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
        const form = $(e).closest('form');
        const url = form.attr('action');
        const name = form.attr('data-name');        

        const template = swalText(name);

        showConfirmationDialog('Selected items!', template, function () {
            APIDELETECALLER(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error'](error.message);
            });
        });        
    };

    window.editCategory = function(e) {
        let id = $(e).attr('data-id');
        APICallerWithoutData(EDIT_CATEGORY_ROUTE.replace(':id', id), function(data) {
            let category = data.category;
            let allSubCategories = data.allSubCategories;
            let relatedSubCategories = data.relatedSubCategory;

            editModal.modal('show');
            editForm.attr('action', UPDATE_CATEGORY_ROUTE.replace(':id', id));
            editForm.find('input[name="name"]').val(category.name);
            editForm.find('textarea[name="description"]').val(category.description);

            for (let index = 0; index < allSubCategories.length; index++) {
                const idToCheck = allSubCategories[index].id;
                let matchingSubCategory = relatedSubCategories.find(obj => obj == idToCheck ) ? 'selected' : "";

                editModal.find('.bootstrap-select .selectSubCategory')
                .append(`
                <option 
                    ${matchingSubCategory} 
                    value="${idToCheck}"
                >
                    ${allSubCategories[index].name}
                </option>`);
            }
            editModal.find('.bootstrap-select .selectSubCategory').selectpicker('refresh');

        },function(error){
            toastr['error'](error.message);
        });
    }

    window.selectCategory = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    // Variable functions
    let deleteMultipleCategories = function () {
        let searchedIds = [];
        let searchedNames = [];

        $('tbody tr input[type="checkbox"]:checked').each(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });
        
        let template = swalText(searchedNames);

        showConfirmationDialog('Selected items!',template,function(){
            searchedIds.forEach(function(id,index){
                APIDELETECALLER(REMOVE_CATEGORY_ROUTE.replace(':id',id),function(response){
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                },function(error){
                    toastr['error'](error.message);
                });
            })
        })
    };


});
