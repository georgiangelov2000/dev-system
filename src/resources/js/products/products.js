import {deleteProduct} from './ajaxFunctions.js';
$(document).ready(function () {
    let table = $('table#purchasedProducts');

    $('.selectAction').selectpicker();

    let dataTable = table.DataTable({
        ajax: {
            url: PRODUCT_API_ROUTE
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onchange="selectProduct(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '1%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.status === "enabled") {
                        return "<i title=" + row.status + " class='fa-solid fa-circle text-success'></i>"
                    } else {
                        return "<i title=" + row.status + " class='fa-solid fa-circle text-danger'></i>"
                    }
                }
            },
            {
                width: '1%',
                orderable: true,
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "image",
                render: function (data, type, row) {
                    if (row.images) {
                        return "<img class='rounded mx-auto d-block w-100' src=" + row.images.path + row.images.name + " />"
                    } else {
                        return "<img class='rounded mx-auto d-block w-100' src='https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg'/>";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "name",
                data: "name"
            },
            {
                width: '10%',
                orderable: true,
                name: "price",
                data: "price"
            },
            {
                width: '10%',
                orderable: true,
                name: "total_price",
                data: "total_price"
            },
            {
                width: '5%',
                orderable: true,
                name: "quantity",
                data: "quantity"
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.notes) {
                        return '<div class="notes">' + row.notes + '</div>';
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.suppliers) {
                        return "<span>" + row.suppliers.name + "</span>"
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.categories.length > 0) {
                        var categoryNames = row.categories.map(function (category) {
                            return "<span> " + category.name + " </span>";
                        });
                        return categoryNames.join(', ');
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.categories.length > 0) {
                        var subCategoryNames = row.subcategories.map(function (subcategory) {
                            return "<span> " + subcategory.name + " </span>";
                        });
                        return subCategoryNames.join(', ');
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.brands.length > 0) {
                        var brandNames = row.brands.map(function (brand) {
                            return "<span> " + brand.name + " </span>";
                        });
                        return brandNames.join(', ');
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.code + '</span>';
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    return '<span>' + moment(row.created_at).format('YYYY-MM-DD') + '</span>';
                }
            },

            {
                orderable: false,
                width: '5%',
                render: function (data, type, row) {
                    let deleteFormTemplate = "\
                        <form style='display:inline-block;' id='delete-form' action=" + REMOVE_PRODUCT_ROUTE.replace(':id', row.id) + " method='POST' data-name="+row.name+">\
                            <input type='hidden' name='_method' value='DELETE'>\
                            <input type='hidden' name='id' value='" + row.id + "'>\
                            <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCurrentProduct(this);'><i class='fa-solid fa-trash text-danger'></i></button>\
                        <form>\
                    ";

                    let editButton = '<a href='+EDIT_PRODUCT_ROUTE.replace(':id',row.id)+' data-id=' + row.id + ' class="btn p-1" title="Edit"><i class="fa-solid fa-pencil text-warning"></i></a>';
                    return `${deleteFormTemplate} ${editButton}`;
                }
            }

        ],
        lengthMenu: [[10], [10]],
        pageLength: 10,
        order: [[1, 'asc']]

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

    $('.bootstrap-select .selectAction').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleProducts();
                break;
            default:
        }
    });

    window.selectProduct = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentProduct = function (e) {
        let form = $(e).closest('form');
    
        let name = form.attr('data-name');
        let url = form.attr('action');
    
        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            deleteProduct(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error']('Product has not been deleted');
            });
        });
    };

    window.deleteMultipleProducts = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function(id,index){
                deleteProduct(REMOVE_PRODUCT_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error']('Supplier has not been deleted');
                });
            });
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