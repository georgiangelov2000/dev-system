import { APIDELETECALLER, APICaller } from '../ajax/methods';
import { swalText, showConfirmationDialog, mapButtons } from '../helpers/action_helpers';

$(function () {
    let table = $('table#purchasedProducts');

    const statusMap = {
        1: { label: "Paid", iconClass: "fal fa-check-circle" },
        2: { label: "Pending", iconClass: "fal fa-hourglass-half" },
        3: { label: "Partially Paid", iconClass: "fal fa-money-bill-alt" },
        4: { label: "Overdue", iconClass: "fal fa-exclamation-circle" },
        5: { label: "Refunded", iconClass: "fal fa-undo-alt" },
    };

    $('.selectAction, .selectSupplier, .selectCategory, .selectSubCategory, .selectBrands, .selectPrice, .selectStock, .selectType')
        .selectpicker('refresh')
        .trigger('change');

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    // select pickers
    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let bootstrapSelectCategory = $('.bootstrap-select .selectCategory');
    let bootstrapSelectSubCategory = $('.bootstrap-select .selectSubCategory');
    let bootstrapSelectBrands = $('.bootstrap-select .selectBrands');
    let bootstrapSelectTotalPrice = $('.bootstrap-select .selectPrice');
    let bootstrapSelectStock = $('.bootstrap-select .selectStock')
    let bootstrapPurchaseStatus = $('.bootstrap-select .selectType');
    let customTotalPrice = $('.customPrice');

    let buttons = mapButtons([6, 7, 9, 10, 11, 12, 13, 14, 15, 16]);

    let dataTable = table.DataTable({
        serverSide: true,
        dom: 'Bfrtip',
        buttons,
        ajax: {
            url: PRODUCT_API_ROUTE,
            data: function (d) {
                var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({}, d, {
                    'supplier': bootstrapSelectSupplier.val() === null ? '' : bootstrapSelectSupplier.val().toLowerCase(),
                    "single_total_price": customTotalPrice.val().toLowerCase(),
                    "total_price_range": bootstrapSelectTotalPrice.val().toLowerCase(),
                    "category": bootstrapSelectCategory.val() === null ? '' : bootstrapSelectCategory.val().toLowerCase(),
                    'sub_category': bootstrapSelectSubCategory.val(), //array of key => value
                    'brand': bootstrapSelectBrands.val(), //array of key => value
                    'status': bootstrapPurchaseStatus.val(),
                    "search": d.search.value,
                    'out_of_stock': bootstrapSelectStock.val(),
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
                    'limit': d.custom_length = d.length,
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: '1%',
                render: function (data, type, row) {
                    let statusStr = ''
                    if (row.quantity > 0) {
                        statusStr = '<i title="Enabled" class="fa-solid fa-circle text-success"></i>'
                    } else {
                        statusStr = '<i title="Disabled" class="fa-solid fa-circle text-danger"></i>'
                    }
                    return statusStr;
                }
            },
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
                orderable: false,
                width: '2%',
                class:'text-center',
                render: function (data, type, row) {
                    if (row.payment) {
                        return `<a href="${PAYMENT.replace(':payment', row.payment.id).replace(':type','purchase')}">${row.payment.alias}</a>`
                    } else {
                        return ``;
                    }
                }
            },
            {
                width: '1%',
                orderable: true,
                name: 'id',
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '1%',
                orderable: false,
                name: "image",
                render: function (data, type, row) {
                    if (row.image_path) {
                        return `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${row.image_path}" />`;

                    } else {
                        return `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png"/>`;
                    }
                }
            },
            {
                width: '12%',
                class:'text-center',
                orderable: false,
                name: "name",
                data: "name"
            },
            {
                width: '6%',
                class:'text-center',
                name: "price",
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.price}</span>`
                }
            },
            {
                width: '6%',
                name: "discount_price",
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.discount_price}</span>`
                }
            },
            {
                width: '7%',
                name: 'total_price',
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.total_price}</span>`
                }
            },
            {
                width: '10%',
                name: 'original_price',
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.original_price}</span>`
                }
            },
            {
                width: '5%',
                orderable: true,
                name: "quantity",
                data: "quantity",
                class:'text-center'
            },
            {
                width: '7%',
                orderable: true,
                name: "initial_quantity",
                data: "initial_quantity",
                class:"text-center"
            },
            {
                width: '2%',
                name: "discount_percent",
                orderable: true,
                class:"text-center",
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`
                }
            },
            {
                width: '4%',
                orderable: false,
                render: function (data, type, row) {
                    let stockStatus = row.quantity ? 'In stock' : 'Out of stock';
                    let stockColor = row.quantity ? 'text-success' : 'text-danger';

                    return `<span class="${stockColor}">${stockStatus}</span>`
                }
            },
            // {
            //     width: '10%',
            //     orderable: false,
            //     render: function (data, type, row) {
            //         let paidOrders = row.paid_orders_count;
            //         let overdueOrders = row.overdue_orders_count;
            //         let pendingOrders = row.pending_orders_count;
            //         let refundOrders = row.refund_orders_count;

            //         return `<div>
            //             <span class="text-success">${paidOrders} paid</span> /
            //             <span class="text-danger">${overdueOrders} overdue</span> /
            //             <span class="text-primary">${pendingOrders} pending</span> /
            //             <span class="text-dark">${refundOrders} refund</span>
            //         </div>`
            //     }
            // },
            {
                width: '7%',
                orderable: false,
                render: function (data, type, row) {
                    if (row.supplier) {
                        return "<a href=" + EDIT_SUPPLIER_ROUTE.replace(":id", row.supplier.id) + ">" + row.supplier.name + "</a>"
                    } else {
                        return "";
                    }
                }
            },
            {
                width: '7%',
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
                width: '1%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    const statusInfo = statusMap[row.payment.payment_status] || { label: "Unknown", iconClass: "fal fa-question-circle" };
            
                    return `<div title="${statusInfo.label}" class="status">
                        <span class="icon"><i class="${statusInfo.iconClass}"></i></span>
                    </div>`;
                }
            },
            {
                orderable: false,
                width: '20%',
                class: 'text-center',
                render: function (data, type, row) {
                    let deleteFormTemplate = '';
                    
                    if (statusMap[row.payment.payment_status].label === 'Pending') {
                        deleteFormTemplate = `
                        <form style='display:inline-block;' id='delete-form' action=${REMOVE_PRODUCT_ROUTE.replace(':id', row.id)} method='POST' data-name=${row.name}>
                            <input type='hidden' name='_method' value='DELETE'>
                            <input type='hidden' name='id' value='${row.id}'>
                            <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deleteCurrentProduct(this);'>
                                <i class='fa-light fa-trash text-danger'></i>
                            </button>
                        </form>
                    `;
                    }

                    let previewLink = `
                    <a title='Preview' href="${PREVIEW_ROUTE.replace(':id', row.id)}" class='btn p-0'>
                        <i class='fa-light fa-magnifying-glass text-info' aria-hidden='true'></i>
                    </a>
                `;

                    let orderCart = `
                    <a title='Orders' href="${ORDERS.replace(':id', row.id)}" class='btn p-0'>
                        <i class='text-success fa-light fa-basket-shopping' aria-hidden='true'></i>
                    </a>
                `;

                    let editButton = `
                    <a href=${EDIT_PRODUCT_ROUTE.replace(':id', row.id)} data-id=${row.id} class="btn p-0" title="Edit">
                        <i class="fa-light fa-pencil text-primary"></i>
                    </a>
                `;

                    return `${deleteFormTemplate} ${editButton} ${previewLink} ${orderCart}`;
                }
            }
        ],
        order: [[3, 'asc']]
    });

    // Searchable

    $('.selectSupplier input[type="text"]').on('keyup', _.debounce(function () {
        let text = $(this).val();
        bootstrapSelectSupplier.empty();

        bootstrapSelectSupplier.append('<option value="" class="d-none"></option>');

        if (text === '') {
            bootstrapSelectSupplier.append('<option value="">All</option>');
            bootstrapSelectSupplier.selectpicker('refresh');
            return;
        }

        APICaller(SUPPLIER_API_ROUTE, { "search": text }, function (response) {
            let suppliers = response.data;
            if (suppliers.length > 0) {
                $.each(suppliers, function ($key, supplier) {
                    bootstrapSelectSupplier.append(`<option value="${supplier.id}"> ${supplier.name} </option>`)
                })
            }
            bootstrapSelectSupplier.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    }, 300));

    $('.selectCategory input[type="text"]').on('keyup', _.debounce(function () {
        let text = $(this).val();
        bootstrapSelectCategory.empty();

        bootstrapSelectCategory.append('<option value="" class="d-none"></option>');

        if (text === '') {
            bootstrapSelectCategory.append('<option value="">All</option>');
            bootstrapSelectCategory.selectpicker('refresh');
            return;
        }

        APICaller(CATEGORY_API_ROUTE, { "search": text }, function (response) {
            let categories = response.data;
            if (categories.length > 0) {
                $.each(categories, function ($key, category) {
                    bootstrapSelectCategory.append(`<option value="${category.id}"> ${category.name} </option>`)
                })
            }
            bootstrapSelectCategory.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    }, 300));

    $('.selectBrands input[type="text"]').on('keyup', _.debounce(function () {
        let text = $(this).val();
        bootstrapSelectBrands.empty();

        if (text === '') {
            bootstrapSelectBrands.selectpicker('refresh');
            return;
        }

        APICaller(BRAND_API_ROUTE, { "search": text }, function (response) {
            let brands = response.data;
            if (brands.length > 0) {
                bootstrapSelectCategory.append('<option value="" class="d-none"></option>');
                $.each(brands, function ($key, brand) {
                    bootstrapSelectBrands.append(`<option value="${brand.id}"> ${brand.name} </option>`)
                })
            }
            bootstrapSelectBrands.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    }, 300))

    // Actions

    customTotalPrice.bind('keyup', function () {
        dataTable.ajax.reload(null, false);
    });

    bootstrapPurchaseStatus.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectStock.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectTotalPrice.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectSupplier.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectCategory.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
        bootstrapSelectSubCategory.empty();
        let category = $(this).val();

        APICaller(CATEGORY_ROUTE, { "category": category, 'select_json': true }, function (response) {
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

    bootstrapSelectSubCategory.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectBrands.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
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

    // Window actions

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

        const template = swalText(name);

        showConfirmationDialog('Selected purchases!', template, function () {
            APIDELETECALLER(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error'](error.message);
            });
        })
    };

    window.deleteMultipleProducts = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        const template = swalText(searchedNames);

        showConfirmationDialog('Selected purchases!', template, function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(REMOVE_PRODUCT_ROUTE.replace(':id', id), function (response) {
                    if (response.status === 500) {
                        toastr['error'](response.responseJSON.message);
                    } else {
                        toastr['success'](response.message);
                    }
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error'](error.message);
                });
            });
        })
    };

});