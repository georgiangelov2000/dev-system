import {
    openModal,
    closeModal,
    swalText,
    submit,
    update,
    ajaxResponse,
    showConfirmationDialog,

} from '../helpers/action_helpers';

import {
    APIDELETECALLER,
    APICallerWithoutData
} from '../ajax/methods';

$(function () {
    const table = $('#subCategoriesTable');
    
    const editModal = $('#editModal');
    const createModal = $('#createModal');

    const selectAll = $('.selectAll');
    const selectAction = $('.selectAction');
    const createSubCategory = $('.createSubCategory');
    const closeModalBtn = $('.modalCloseBtn');

    const createSubmitButton = createModal.find('#submitForm');
    const editSubmitButton = editModal.find('#submitForm');

    $('.selectAction')
    .selectpicker('refresh')
    .val('')
    .trigger('change');

    console.log(SUB_CATEGORY_API_ROUTE);
    let dataTable = table.DataTable({
        ajax: {
            url: SUB_CATEGORY_API_ROUTE
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
                            onchange='selectSubCategory(this)'
                            data-id = '${row.id}'
                            data-name = '${row.name}'
                        />
                    </div>`;

                    return `${checkbox}`;
                }
            },
            {
                width: '1%',
                name: "id",
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${row.id}</span>`;
                }
            },
            {
                orderable: false,
                name: 'name',
                width: '10%',
                render: function (data, type, row) {
                    return `<span>${row.name}</span>`;
                }
            },
            {
                orderable: false,
                name: 'category',
                width: '5%',
                render: function (data, type, row) {
                    if (row.category) {
                        return `<span>${row.category.name}</span>`;
                    } else {
                        return '';
                    }
                }
            },
            {
                orderable: false,
                width: '35%',
                render: function (data, type, row) {

                    const deleteFormTemplate = `
                    <form style="display:inline-block;" action="${DELETE_SUB_CATEGORY_ROUTE.replace(':id', row.id)}" id="delete-form" method="POST" data-name="${row.name}">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="id" value="${row.id}">
                      <button type="submit" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteSubCategory(this);"><i class="fa-light fa-trash text-danger"></i></button>
                    </form>
                  `;

                    const editButton = `<a onclick="editSubCategory(this)" data-id="${row.id}" class="btn editSubCategory" title="Edit"><i class="fa-light fa-pencil text-warning"></i></a>`;

                    return `${editButton} ${deleteFormTemplate}`;
                }
            }

        ],
        order: [[1, 'asc']]
    })

    // Window events
        window.deleteSubCategory = function (e) {
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
        }

        window.editSubCategory = function(e) {
            const id = $(e).attr('data-id');
            const editUrl = EDIT_SUB_CATEGORY_ROUTE.replace(':id',id);
            const updateUrl = UPDATE_SUB_CATEGORY_ROUTE.replace(':id',id);

            APICallerWithoutData(editUrl,function(data){
                console.log(data);
                let subCategory = data.name;
                openModal(editModal,updateUrl,subCategory);
            },function(error){
                toastr['error'](error.message);
            });
        }

        window.selectSubCategory = function(e) {
            if ($('tbody input[type="checkbox"]:checked').length === 0) {
                $('.actions').addClass('d-none');
            } else {
                $('.actions').removeClass('d-none');
            }
        }
    // Window events end

    // Variable functions
        let deleteMultipleSubCategories = function() {
            let searchedIds = [];
            let searchedNames = [];

            $('tbody tr input[type="checkbox"]:checked').each(function () {
                searchedIds.push($(this).attr('data-id'));
                searchedNames.push($(this).attr('data-name'));
            });

            let template = swalText(searchedNames);

            showConfirmationDialog('Selected items!',template,function(){
                searchedIds.forEach(function(id,index){
                    APIDELETECALLER(DELETE_SUB_CATEGORY_ROUTE.replace(':id',id),function(response){
                        toastr['success'](response.message);
                        table.DataTable().ajax.reload();
                    },function(error){
                        toastr['error'](error.message);
                    });
                })
            })
        }
    // End variable functions

    createSubCategory.on('click', function () {
        openModal(createModal, STORE_SUB_CATEGORY_ROUTE);
    });      

    createSubmitButton.on('click', function (e) {
        e.preventDefault();
        submit(e, createModal, table);
    });

    editSubmitButton.on('click',function(e){
        e.preventDefault();
        submit(e,editModal,table);
    })

    closeModalBtn.on('click',function(){
        closeModal($('.modal'));
    })

    selectAll.on('change',function() {
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
    })

    selectAction.on('change',function(){
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleSubCategories();
            break;
        
            default:
                break;
        }
    })

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
})