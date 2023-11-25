import { APIDELETECALLER, APIPOSTCALLER } from '../ajax/methods';
import {
    swalText,
    ajaxResponse,
    showConfirmationDialog,
    openModal,
} from '../helpers/action_helpers';

$(function () {
    const table = $('#brandsTable');

    const dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: BRAND_ROUTE,
            data: function (d) {
                const orderColumnIndex = d.order[0].column;
                const orderColumnName = d.columns[orderColumnIndex].name;
                return $.extend({}, d, {
                    "search": d.search.value,
                    'order_column': orderColumnName,
                    'order_dir': d.order[0].dir,
                    'limit': d.custom_length = d.length,
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    const checkbox = `<div class="form-check">
                        <input name="checkbox" class="form-check-input" onchange="selectBrand(this)" data-id=${row.id} data-name=${row.name} type="checkbox">
                    </div>`;
                    return checkbox;
                }
            },
            {
                width: '5%',
                name: "id",
                ordering: true,
                render: function (data, type, row) {
                    return `<span class="font-weight-bold">${row.id}</span>`;
                }
            },
            {
                width: '5%',
                orderable: false,
                class: 'text-center',
                name: "image",
                render: function (data, type, row) {
                    const imageSrc = row.image_path || 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png';
                    return `<img class='rounded mx-auto w-100' src=${imageSrc} />`;
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
                render: function (data, type, row) {
                    return `<span>${row.purchases_count}</span>`;
                }
            },
            {
                orderable: false,
                name: "description",
                render: function (data, type, row) {
                    return row.description !== null ? `<span>${row.description}</span>` : '';
                }
            },
            {
                orderable: false,
                width: '15%',
                render: function (data, type, row) {
                const deleteForm = `<form style="display:inline-block;" data-name="${row.name}" action="${REMOVE_BRAND_ROUTE.replace(':id', row.id)}" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="id" value="${row.id}">
                    <button type="submit" data-name="${row.name}" data-id="${row.id}" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteBrand(this);"><i class="fa-light fa-trash text-danger"></i></button>
                </form>`;
        
                const editButton = `<a data-id=${row.id} class="btn p-1" onclick="editBrand(this)" title="Edit"><i class="fa-light fa-pencil text-primary"></i></a>`;
        
                const removeImageForm = row.image_path ? `<form style="display:inline-block;" action="${REMOVE_BRAND_IMAGE_ROUTE.replace(':id', row.id)}" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="id" value="${row.id}">
                    <button type="submit" data-name="${row.name}" data-id="${row.id}" class="btn p-1" title="Delete" onclick="event.preventDefault(); deleteImage(this);"><i class="fa-light fa-image text-danger fa-lg"></i></button>
                </form>` : '';
        
                return `${deleteForm} ${editButton} ${removeImageForm}`;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    // ACTIONS
    $('.createBrand').on("click", () => openModal($('#createModal'), STORE_BRAND_ROUTE));

    $('input.custom-file-input').on('change', function () {
        const fileName = $(this).val().split('\\').pop() || 'Choose file';
        $(this).siblings('label.custom-file-label').text(fileName);
    });

    const handleFormSubmission = function () {
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
                toastr['error'](error.responseJSON.message);
                ajaxResponse(error.responseJSON.errors);
            }
        );
    };
    
    $('#submitForm, #updateForm').on("click", function (e) {
        e.preventDefault();
        handleFormSubmission();
    });

    $('.selectAction').on('change', function () {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleBrands();
                break;
            default:
        }
    });

    // WINDOW EVENTS

    window.deleteImage = (e) => {
        const form = $(e).closest('form');
        const url = form.attr('action');
    
        APIDELETECALLER(url, (response) => {
            toastr['success'](response.message);
            dataTable.ajax.reload(null, false);
        }, (error) => {
            toastr['error'](error.message);
        });
    };

    window.deleteBrand = (e) => {
        const form = $(e).closest('form');
        const url = form.attr('action');
        const name = form.attr('data-name');
        const template = swalText(name);

        showConfirmationDialog('Selected items!', template, 'Yes, deleted it!', () => {
            APIDELETECALLER(url, (response) => {
                toastr['success'](response.message);
                dataTable.ajax.reload(null, false);
            }, (error) => {
                toastr['error'](error.message);
            });
        });
    };

    window.editBrand = (e) => {
        const id = $(e).attr('data-id');
        const modal = $('#editModal');
        const modalForm = modal.find('form');

        $.ajax({
            method: "GET",
            url: EDIT_BRAND_ROUTE.replace(':id', id),
            contentType: 'application/json',
            success: (data) => {
                $('#editModal').modal('show');
                modalForm.attr('action', UPDATE_BRAND_ROUTE.replace(':id', id));
                modalForm.find('input[name="name"]').val(data.name);
                modalForm.find('textarea[name="description"]').val(data.description);
                modalForm.find('img[id="icon"]').attr('src', data.image_path);
            },
            error: (errors) => {
                toastr['error'](errors.message);
            }
        });
    };

    window.selectBrand = (e) => {
        $('.actions').toggleClass('d-none', $('tbody input[type="checkbox"]:checked').length === 0);
    };

    // Variable functions
    const deleteMultipleBrands = () => {
        const searchedIds = [];
        const searchedNames = [];

        $('input:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        const template = swalText(searchedNames);

        showConfirmationDialog('Selected items!', template, 'Yes, delete records!', () => {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(REMOVE_BRAND_ROUTE.replace(':id', id), (response) => {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                }, (error) => {
                    toastr['error'](error.message);
                });
            });
        });
    };
});
