import { deleteProduct, APICaller } from './ajaxFunctions.js';
$(document).ready(function () {
    let table = $('table#purchasedProducts');

    $('.selectAction').selectpicker('refresh').val('');
    $('.selectSupplier').selectpicker('refresh').val('');
    $('.selectCategory').selectpicker('refresh').val('');
    $('.selectSubCategory').selectpicker('refresh').val('');
    $('.selectBrands').selectpicker('refresh').val('');
    $('.selectPrice').selectpicker('refresh').val('');

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
                        return "<img class='rounded mx-auto w-100' src=" + row.images.path + row.images.name + " />"
                    } else {
                        return "<img class='rounded mx-auto w-100' src='https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg'/>";
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
                        return "<a href="+EDIT_SUPPLIER_ROUTE.replace(":id",row.suppliers.id)+">" + row.suppliers.name + "</a>"
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    console.log(row.categories);
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
                        <form style='display:inline-block;' id='delete-form' action=" + REMOVE_PRODUCT_ROUTE.replace(':id', row.id) + " method='POST' data-name=" + row.name + ">\
                            <input type='hidden' name='_method' value='DELETE'>\
                            <input type='hidden' name='id' value='" + row.id + "'>\
                            <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCurrentProduct(this);'><i class='fa-solid fa-trash text-danger'></i></button>\
                        <form>\
                    ";

                    let editButton = '<a href=' + EDIT_PRODUCT_ROUTE.replace(':id', row.id) + ' data-id=' + row.id + ' class="btn p-1" title="Edit"><i class="fa-solid fa-pencil text-warning"></i></a>';
                    return `${deleteFormTemplate} ${editButton}`;
                }
            }

        ],
        lengthMenu: [[10], [10]],
        pageLength: 10,
        order: [[1, 'asc']]

    });
    
    customTotalPrice.on('keyup', function () {
        let price = $(this).val();
        APICaller(PRODUCT_API_ROUTE, { "single_total_price": price }, function (response) {
            console.log(response.data);
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    })

    bootstrapSelectTotalPrice.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let total_price = $(this).val();
        let priceParts = total_price.split("-");

        let start_price = priceParts[0];
        let end_price = priceParts[1];

        APICaller(PRODUCT_API_ROUTE, {
            "start_total_price": start_price,
            'end_total_price': end_price
        }, function (response) {
            console.log(response.data);
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapSelectSupplier.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let supplierId = $(this).val();
        $('.selectCategory').selectpicker('val', '');

        APICaller(PRODUCT_API_ROUTE, { "supplier": supplierId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapSelectCategory.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let categoryId = $(this).val();
        $('.selectSupplier').selectpicker('val', '');
        bootstrapSelectSubCategory.empty();

        APICaller(PRODUCT_API_ROUTE, { "category": categoryId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();

                APICaller(CATEGORY_ROUTE, { "category": categoryId }, function (response) {
                    let subCategories = response.data;
                    console.log(subCategories);
                    if (subCategories.length > 0) {
                        $.each(subCategories, function (key, subCategory) {
                            bootstrapSelectSubCategory.append(`<option value="${subCategory.id}">${subCategory.name}</option>`);
                        });
                    } else {
                        bootstrapSelectSubCategory.append('<option value="">Nothing selected</option>');
                    }
                    bootstrapSelectSubCategory.selectpicker('refresh');
                }, function (error) {
                    console.log(error);
                });
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapSelectSubCategory.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let subCategoryId = $(this).val();
        $('.selectSupplier').selectpicker('refresh').val('');

        APICaller(PRODUCT_API_ROUTE, { "sub_category": subCategoryId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapSelectBrands.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let brandId = $(this).val();
        APICaller(PRODUCT_API_ROUTE, { "brand": brandId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    applyBtn.on('click', function () {
        let date = $('input[name="datetimes"]').val();
        let dateParts = date.split(" - ");
        let startDate = dateParts[0];
        let endDate = dateParts[1];
        APICaller(PRODUCT_API_ROUTE, { "start_date": startDate, 'end_date': endDate }, function (response) {
            console.log(response.data);
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    })

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
            searchedIds.forEach(function (id, index) {
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