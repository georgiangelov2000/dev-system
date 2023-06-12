import { APIDELETECALLER, APIPOSTCALLER } from '../ajax/methods';
import {
    swalText,
    ajaxResponse,
    showConfirmationDialog,
    openModal,
    submit

} from '../helpers/action_helpers';

$(function(){

    $('.selectAction')
    .selectpicker('refresh')
    .val('')
    .trigger('change');

    let table = $('#brandsTable');
    //Global category variables

    let createModal = $('#createModal');
    let editModal = $('#editModal');

    let editForm = editModal.find('form');
    let createBrand = $('.createBrand');


    table.DataTable({
        serverSide:true,
        ajax: {
            url: BRAND_ROUTE,
            data: function(d) {
                let orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                let orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({},d,{
                    "search": d.search.value,
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir // send the sorting direction (asc or desc)
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onchange="selectBrand(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '5%',
                name: "id",
                ordering: true,
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
                name: "purchases_count",
                render: function(data,type,row){
                    return `<span>${row.purchases_count}</span>`
                }
            },
            {
                orderable: false,
                name: "description",
                render: function(data,type,row) {
                    if(row.description !== null) {
                        return `<span>${row.description}</span>`
                    } else {
                        return ''
                    }
                }
            },
            {
                orderable: false,
                width: '15%',
                render: function (data, type, row) {

                    const deleteFormTemplate = `
                    <form style="display:inline-block;" data-name="${row.name}"  id="delete-form" action="${REMOVE_BRAND_ROUTE.replace(':id', row.id)}" method="POST" data-name="${row.name}">
                      <input type="hidden" name="_method" value="DELETE">
                      <input type="hidden" name="id" value="${row.id}">
                      <button type="submit" data-name="${row.name}" data-id="${row.id}" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteBrand(this);"><i class="fa-light fa-trash text-danger"></i></button>
                    </form>
                  `;

                    const editButton = '<a data-id=' + row.id + ' class="btn p-1" onclick="editBrand(this)" title="Edit"><i class="fa-light fa-pencil text-warning"></i></a>';
                    const purchasesButton = '<a href=' + BRAND_PURCHASES.replace(':id',row.id) + ' data-id=' + row.id + ' class="btn p-1" title="Products"><i class="fa-light fa-cart-shopping text-primary"></i></a>';
                    return `${deleteFormTemplate} ${editButton} ${purchasesButton}`;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    //ACTIONS
    createBrand.on("click", function () {
        openModal(createModal,STORE_BRAND_ROUTE);
    });

    $('#submitForm').on("click", function (e) {
        e.preventDefault();
        submit(e,createModal,table);
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
                    toastr['error'](response.responseJSON.message);
                    ajaxResponse(error.responseJSON.errors);
                }
        );
    });

    $('.selectAction').on('change', function () {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleBrands();
                break;
            default:
        }
    });

    // Window events
    window.deleteBrand = function (e) {
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

    window.editBrand = function (e) {
        let id = $(e).attr('data-id');
        $.ajax({
            method: "GET",
            url: EDIT_BRAND_ROUTE.replace(':id', id),
            contentType: 'application/json',
            success: function (data) {
                $('#editModal').modal('show');
                editForm.attr('action', UPDATE_BRAND_ROUTE.replace(':id', id));
                editForm.find('input[name="name"]').val(data.name);
                editForm.find('textarea[name="description"]').val(data.description);
            },
            error: function (errors) {
                toastr['error'](errors.message);
            }
        });
    };

    window.selectBrand = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    // Variable functions
    let deleteMultipleBrands = function () {
        let searchedIds = [];
        let searchedNames = [];

        $('input:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        showConfirmationDialog('Selected Items!',template,function(){
            searchedIds.forEach(function(id,index){
                APIDELETECALLER(REMOVE_BRAND_ROUTE.replace(':id',id),function(response){
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                },function(error){
                    toastr['error'](error.message);
                });
            })
        })
    };

});