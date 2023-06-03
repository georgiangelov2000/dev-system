import { APIDELETECALLER , APICaller } from '../ajax/methods';

$(function () {
    let table = $('table#purchasedProducts');
    let paymentModal = $('#purchases_modal');
    let closeModal = $('.modalCloseBtn');

    $('.selectAction, .selectSupplier, .selectCategory, .selectSubCategory, .selectBrands, .selectPrice')
    .selectpicker('refresh')
    .val('')
    .trigger('change');

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // selectpickers
    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let bootstrapSelectCategory = $('.bootstrap-select .selectCategory');
    let bootstrapSelectSubCategory = $('.bootstrap-select .selectSubCategory');
    let bootstrapSelectBrands = $('.bootstrap-select .selectBrands');
    let bootstrapSelectTotalPrice = $('.bootstrap-select .selectPrice');
    let customTotalPrice = $('.customPrice');
    let applyBtn = $('.applyBtn');

    let publishingValue = $('input[name="datetimes"]').val();

    applyBtn.bind('click',function(){
        let date = $('input[name="datetimes"]').val();
        publishingValue = date;
        dataTable.ajax.reload(null, false);
    })

    let dataTable = table.DataTable({
        serverSide: true,
        dom: 'Bfrtip',
        buttons: [
            {
              extend: 'copy',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [2,4,5,6,7,9,10,11,12,13,14]
              }
            },
            {
              extend: 'csv',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [2,4,5,6,7,9,10,11,12,13,14]
              }
            },
            {
              extend: 'excel',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [2,4,5,6,7,9,10,11,12,13,14] 
              }
            },
            {
              extend: 'pdf',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [2,4,5,6,7,9,10,11,12,13,14]
              }
            },
            {
              extend: 'print',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [2,4,5,6,7,9,10,11,12,13,14]
              }
            }
          ],
        ajax: {
            url: PRODUCT_API_ROUTE,  
            data: function(d) {
                return $.extend({},d, {
                    'supplier': bootstrapSelectSupplier.val().toLowerCase(),
                    "single_total_price": customTotalPrice.val().toLowerCase(),
                    "total_price_range": bootstrapSelectTotalPrice.val().toLowerCase(),
                    "category" : bootstrapSelectCategory.val().toLowerCase(),
                    'publishing': publishingValue,
                    'sub_category' : bootstrapSelectSubCategory.val(), //array of key => value
                    'brand': bootstrapSelectBrands.val(), //array of key => value
                    "search": builtInDataTableSearch ? builtInDataTableSearch.val().toLowerCase() : ''
                });
            }
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
                    if (row.images && row.images.length > 1) {
                        // Generate carousel HTML with multiple images
                        let carouselItems = row.images.map((image, index) => {
                            let isActive = index === 0 ? 'active' : ''; // Set first image as active
                            return `<div class="carousel-item ${isActive}">
                                        <img class="d-block w-100" src="${image.path + image.name}" alt="Slide ${index + 1}">
                                    </div>`;
                        }).join('');
                    
                        return `<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        ${carouselItems}
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>`;
                    } else if (row.images && row.images.length === 1) {
                        // Generate HTML with a single image
                        let imagePath = row.images[0].path + row.images[0].name;
                        let stockStatus = row.quantity ? 'In stock' : 'Out of stock';
                        let stockColor = row.quantity ? 'success' : 'danger';
                    
                        return `<div class="position-relative previewImageWrapper">
                                    <img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${imagePath}" />
                                    <div class="ribbon-wrapper ribbon-lg">
                                        <div class="ribbon bg-${stockColor}">${stockStatus}</div>
                                    </div>
                                </div>`;
                    } else {
                        // Generate HTML for a placeholder image
                        return `<img class="rounded mx-auto w-100" src="https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg"/>`;
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
                width: '6%',
                orderable: true,
                name: "price",
                data: "price"
            },
            {
                width: '6%',
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
                width: '7%',
                orderable: true,
                name: "initial_quantity",
                data: 'initial_quantity'
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
                    if (row.supplier) {
                        return "<a href="+EDIT_SUPPLIER_ROUTE.replace(":id",row.supplier.id)+">" + row.supplier.name + "</a>"
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
                width: '2%',
                orderable: false,
                name: "is_paid",
                render: function (data, type, row) {
                    if(row.is_paid) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {
                orderable: false,
                width: '6%',
                render: function (data, type, row) {
                    let deleteFormTemplate = "\
                        <form style='display:inline-block;' id='delete-form' action=" + REMOVE_PRODUCT_ROUTE.replace(':id', row.id) + " method='POST' data-name=" + row.name + ">\
                            <input type='hidden' name='_method' value='DELETE'>\
                            <input type='hidden' name='id' value='" + row.id + "'>\
                            <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deleteCurrentProduct(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                        <form>\
                    ";

                    let previewLink = "<a title='Preview' href="+PREVIEW_ROUTE.replace(':id', row.id)+" class='btn p-0'><i class='fa fa-light fa-eye text-info' aria-hidden='true'></i></a>"

                    let editButton = '<a href=' + EDIT_PRODUCT_ROUTE.replace(':id', row.id) + ' data-id=' + row.id + ' class="btn p-0" title="Edit"><i class="fa-light fa-pencil text-warning"></i></a>';

                    let payButton = `<a  onclick="openPaymentModal(this)" purchase-price=${row.price} purchase-id=${row.id} class='btn p-0' title="Payment"><i class="fa-thin fa-cash-register"></i></a>`;

                    return `${deleteFormTemplate} ${editButton} ${previewLink} ${[payButton]}`;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    let builtInDataTableSearch = $('#purchasedProducts_filter input[type="search"]');
    
    customTotalPrice.bind('keyup',function(){
        dataTable.ajax.reload( null, false );
    });

    builtInDataTableSearch.bind('keyup',function(){
        dataTable.ajax.reload( null, false );
    })

    bootstrapSelectTotalPrice.bind('changed.bs.select',function(){
        dataTable.ajax.reload( null, false );
    })

    bootstrapSelectSupplier.bind('changed.bs.select',function(){
        dataTable.ajax.reload( null, false );
    })

    bootstrapSelectCategory.bind('changed.bs.select',function(e, clickedIndex, isSelected, previousValue){
        dataTable.ajax.reload( null, false );
        bootstrapSelectSubCategory.empty();
        let category = $(this).val();

        APICaller(CATEGORY_ROUTE, { "category": category }, function (response) {
            let subCategories = response.data;
            bootstrapSelectSubCategory.empty();

            if (subCategories.length > 0) {
                $.each(subCategories, function (key, subCategory) {
                    bootstrapSelectSubCategory.append(`<option value="${subCategory.id}">${subCategory.name}</option>`);
                });
            } 
            bootstrapSelectSubCategory.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        });

    })

    bootstrapSelectSubCategory.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue){
        dataTable.ajax.reload( null, false );
    })

    bootstrapSelectBrands.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue){
        dataTable.ajax.reload( null, false );
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
            APIDELETECALLER(url, function (response) {
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
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(REMOVE_PRODUCT_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error']('Supplier has not been deleted');
                });
            });
        });

    };

    window.openPaymentModal = function(e) {
        paymentModal.modal('show');
        let purchase =$(e).attr('purchase-id');
        // let price = $(e).attr('order-price');
        // let customer = $(e).attr('customer-id');

        // modal.find('form input[name="price"]').val(price);
        // modal.find('form input[name="order_id"]').val(order);
        // modal.find('form input[name="customer_id"]').val(customer);
    }

    closeModal.on('click', function () {
        closeModal(paymentModal);
    });

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