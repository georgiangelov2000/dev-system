import {
    openModal,
    closeModal,
    swalText,
    submit,
    showConfirmationDialog,
} from '../helpers/action_helpers';

import {
    APIDELETECALLER,
    APICallerWithoutData,
    APIPOSTCALLER
} from '../ajax/methods';

$(function () {

    const table = $('#subCategoriesTable');
    
    const editModal = $('#editModal');
    const createModal = $('#createModal');

    const selectAll = $('.selectAll');
    const selectAction = $('.selectAction');
    const createSubCategory = $('.createSubCategory');
    const closeModalBtn = $('.modalCloseBtn');

    $('.selectAction')
    .selectpicker('refresh')
    .val('')
    .trigger('change');

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: SUB_CATEGORY_API_ROUTE,
            data: function(d) {
                var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({},d, {
                    "search":d.search.value,
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
                    return `<div class='form-check'> 
                    <input name='checkbox' class='form-check-input' type='checkbox' onchange='selectSubCategory(this)' 
                           data-id='${row.id}' data-name='${row.name}' />
                    </div>`;
                }
            },
            {
                width: '1%',
                name: "id",
                ordering:true,
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
                    return row.category ? `<span>${row.category.name}</span>` : '';
                }
            },
            {
                orderable: true,
                name: 'purchases_count',
                class: 'text-center',
                width: '1%',
                render: function (data, type, row) {
                    return `<span>${row.purchases_count}</span>`;
                }
            },
            {
                orderable: false,
                width: '5%',
                render: function (data, type, row) {
                    const deleteFormTemplate = `
                    <form style="display:inline-block;" action="${DELETE_SUB_CATEGORY_ROUTE.replace(':id', row.id)}" id="delete-form" method="POST" data-name="${row.name}">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="id" value="${row.id}">
                      <button type="submit" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteSubCategory(this);"><i class="fa-light fa-trash text-danger"></i></button>
                    </form>
                `;

                const editButton = `<a onclick="editSubCategory(this)" data-id="${row.id}" class="btn editSubCategory" title="Edit"><i class="fa-light fa-pencil text-primary"></i></a>`;

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

            showConfirmationDialog('Selected items!', template, 'Yes, delete it!', function () {
                APIDELETECALLER(url, function (response) {
                    if(response.responseJSON) {
                        toastr['warning'](response.responseJSON.error);
                    } else {
                        toastr['success'](response.message);
                    }    
                    dataTable.ajax.reload(null,false);
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
            const isChecked = $('tbody input[type="checkbox"]:checked').length > 0;
            $('.actions').toggleClass('d-none', !isChecked);
        }
        
    // Window events end

    // Variable functions
        let deleteMultipleSubCategories = function() {
            const searchedIds = $('tbody input[type="checkbox"]:checked').map(function () {
                return $(this).attr('data-id');
            }).get();
        
            const searchedNames = $('tbody input[type="checkbox"]:checked').map(function () {
                return $(this).attr('data-name');
            }).get();

            let template = swalText(searchedNames);

            showConfirmationDialog('Selected items!',template, 'Yes, delete records!',function(){
                searchedIds.forEach(function(id,index){            
                    let searchedIds = [];
                    let searchedNames = [];
        
                    $('tbody tr input[type="checkbox"]:checked').each(function () {
                        searchedIds.push($(this).attr('data-id'));
                        searchedNames.push($(this).attr('data-name'));
                    });
                    APIDELETECALLER(DELETE_SUB_CATEGORY_ROUTE.replace(':id',id),function(response){
                        if(response.responseJSON) {
                            toastr['warning'](response.responseJSON.error);
                        } else {
                            toastr['success'](response.message);
                        }    
                        dataTable.ajax.reload();
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

    $('#submitForm, #updateForm').on("click", function (e) {
        e.preventDefault();

        const visibleModal = $('.modal:visible');
        const modalForm = visibleModal.find('form');
        const actionUrl = modalForm.attr('action');
        const formData = new FormData(modalForm[0]);

        APIPOSTCALLER(actionUrl, formData,
            (response, xhr) => {
                if (xhr === 'success') {
                    toastr['success'](response.message);
                    visibleModal.modal('toggle');
                    modalForm.trigger('reset');
                    dataTable.ajax.reload(null, false);
                }
            },
            (error) => {
                toastr['error'](response.responseJSON.message);
                ajaxResponse(error.responseJSON.errors);
            }
        );
    });

    closeModalBtn.on('click',function(){
        closeModal($('.modal'));
    })

    selectAll.on('change',function() {
        const isChecked = this.checked;
        $('.actions').toggleClass('d-none', !isChecked);
        $(':checkbox').prop('checked', isChecked);
    })

    selectAction.on('change',function(){
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleSubCategories();
                break;
            default:
        }
    })

    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
})