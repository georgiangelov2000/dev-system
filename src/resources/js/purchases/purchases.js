import { APIDELETECALLER, APICaller } from '../ajax/methods';
import { swalText, showConfirmationDialog, mapButtons } from '../helpers/action_helpers';
import { numericFormat } from '../helpers/functions';
import { statusPaymentsWithIcons, paymentStatuses } from '../helpers/statuses';

$(function () {
    let table = $('table#purchasedProducts');

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
                var orderColumnIndex = d.order[0].column; 
                var orderColumnName = d.columns[orderColumnIndex].name; 

                return $.extend({}, d, {
                    'supplier': bootstrapSelectSupplier.val(),
                    "category": bootstrapSelectCategory.val(),
                    'brand': bootstrapSelectBrands.val(),
                    'sub_category': bootstrapSelectSubCategory.val(),
                    "total_price_range": bootstrapSelectTotalPrice.val(),
                    "single_total_price": customTotalPrice.val(),
                    'status': bootstrapPurchaseStatus.val(),
                    'out_of_stock': bootstrapSelectStock.val(),
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
                width: '1%',
                render: function (data, type, row) {
                    const statusIcon = row.quantity > 0 
                    ? '<i title="Enabled" class="fa-solid fa-circle text-success"></i>' 
                    : '<i title="Disabled" class="fa-solid fa-circle text-danger"></i>';
                    return statusIcon;
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
                name: "image_path",
                render: function (data, type, row) {
                    return row.image_path ? `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${row.image_path}" />` : `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png"/>`;
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
                    return `<span>${numericFormat(row.price)}</span>`
                }
            },
            {
                width: '6%',
                name: "discount_price",
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.discount_price)}</span>`
                }
            },
            {
                width: '7%',
                name: 'total_price',
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.total_price)}</span>`
                }
            },
            {
                width: '10%',
                name: 'original_price',
                class:'text-center',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.original_price)}</span>`
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
                class:'text-center',
                render: function (data, type, row) {
                    return row.is_it_delivered ? 
                    `<i class="fa-light fa-check text-success"></i>` : 
                    `<i class="fa-light fa-x text-danger"></i>`;
                }
            },
            {
                width: '7%',
                orderable: false,
                render: function (data, type, row) {
                    return row.supplier ? `<a href="${EDIT_SUPPLIER_ROUTE.replace(":id", row.supplier.id)}">${row.supplier.name}</a>` : '';
                }
            },
            {
                width: '7%',
                orderable: false,
                render: function (data, type, row) {
                    return row.categories.length > 0 ? row.categories.map(category => `<span> ${category.name} </span>`).join(', ') : '';
                }
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    const expected = moment(row.expected_delivery_date);
                    const deliveryDate = moment(row.delivery_date);
                    const isDelivered = row.is_it_delivered;
                    
                    if (!isDelivered) {
                        let currDate = moment();
                        let diffDays = currDate.diff(expected, 'days');
                        return (currDate.isAfter(expected))
                            ? `<span class="text-danger">${diffDays} days delay</span>`
                            : `<span class="text-info">${diffDays} days left</span>`;
                    } else {
                        let diffDays = deliveryDate.diff(expected, 'days');
                        return (deliveryDate.isAfter(expected))
                            ? `<span class="text-danger">${diffDays} days delay in delivery</span>`
                            : `<span class="text-success">Delivered on time (${diffDays} days)</span>`;
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    return row.brands.length > 0 ? row.brands.map(brand => `<span> ${brand.name} </span>`).join(', ') : '';
                }
            },
            {
                width: '1%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    const statusInfo = statusPaymentsWithIcons[row.payment.payment_status] || { label: "Unknown", iconClass: "fal fa-question-circle" };
            
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

                    const isPendingPayment = paymentStatuses[row.payment.payment_status] ? row.payment.payment_status : 0 ; 

                    const deleteFormTemplate = isPendingPayment === 2
                        ? `<form class="dropdown-item" style='display:inline-block;' id='delete-form' action=${REMOVE_PRODUCT_ROUTE.replace(':id', row.id)} method='POST' data-name=${row.name}>
                                <input type='hidden' name='_method' value='DELETE'>
                                <input type='hidden' name='id' value='${row.id}'>
                                <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); deleteCurrentProduct(this);'>
                                    <i class='fa-light fa-trash text-danger'></i> Delete
                                </button>
                            </form>`
                        : '';

                    // const refundedFormTemplate = isPendingPayment === 2
                    // ? `<form class="dropdown-item" style='display:inline-block;' action=${UPDATE_PRODUCT_STATUS_ROUTE.replace(':id', row.id)} method='POST' data-name=${row.name}>
                    //     <input type='hidden' name='_method' value='DELETE'>
                    //     <input type='hidden' name='id' value='${row.id}'>
                    //     <button type='submit' class='btn p-0' title='Delete' onclick='event.preventDefault(); updateCurrentProduct(this);'>
                    //         <i class="fa-light fa-undo text-danger" aria-hidden="true"></i> Refunded
                    //     </button>
                    // </form>`: '';

                    const previewLink = `<a  title='Preview' href="${PREVIEW_ROUTE.replace(':id', row.id)}" class='btn dropdown-item'>
                                            <i class='fa-light fa-magnifying-glass text-info' aria-hidden='true'></i> Preview
                                        </a>`;

                    const orderCart = `<a title='Orders' href="${ORDERS.replace(':id', row.id)}" class='btn dropdown-item'>
                                        <i class='text-success fa-light fa-basket-shopping' aria-hidden='true'></i> Orders
                                    </a>`;

                    const editButton = `<a href=${EDIT_PRODUCT_ROUTE.replace(':id', row.id)} data-id=${row.id} class="btn dropdown-item" title="Edit">
                                            <i class="fa-light fa-pencil text-primary"></i> Edit
                                        </a>`;

                    const dropdownContent = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa-light fa-list" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                ${deleteFormTemplate}
                                ${previewLink}
                                ${orderCart}
                                ${editButton}
                            </div>
                        </div>
                    `;

                    return `${dropdownContent}`;
                }
            }
        ],
        order: [[3, 'asc']]
    });

    // Searchable

    $('.selectSupplier input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
        bootstrapSelectSupplier.empty().append('<option value="" class="d-none"></option>');
    
        if (text === '') {
            bootstrapSelectSupplier.append('<option value="">All</option>').selectpicker('refresh');
            return;
        }
    
        APICaller(SUPPLIER_API_ROUTE, { "search": text }, function (response) {
            const suppliers = response.data;
            if (suppliers.length) {
                suppliers.forEach(supplier => {
                    bootstrapSelectSupplier.append(`<option value="${supplier.id}"> ${supplier.name} </option>`);
                });
            }
            bootstrapSelectSupplier.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));
    

    $('.selectCategory input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
        bootstrapSelectCategory.empty().append('<option value="" class="d-none"></option>');
    
        if (text === '') {
            bootstrapSelectCategory.append('<option value="">All</option>').selectpicker('refresh');
            return;
        }
    
        APICaller(CATEGORY_API_ROUTE, { "search": text }, response => {
            const categories = response.data;
            if (categories.length > 0) {
                categories.forEach(category => {
                    bootstrapSelectCategory.append(`<option value="${category.id}"> ${category.name} </option>`);
                });
            }
            bootstrapSelectCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));
    

    $('.selectBrands input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
        bootstrapSelectBrands.empty();
    
        if (text === '') {
            bootstrapSelectBrands.selectpicker('refresh');
            return;
        }
    
        APICaller(BRAND_API_ROUTE, { "search": text }, response => {
            const brands = response.data;
            if (brands.length > 0) {
                bootstrapSelectBrands.append('<option value="" class="d-none"></option>');
                brands.forEach(brand => {
                    bootstrapSelectBrands.append(`<option value="${brand.id}"> ${brand.name} </option>`);
                });
            }
            bootstrapSelectBrands.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));

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

    bootstrapSelectCategory.on('changed.bs.select', function () {
        const category = $(this).val();
        dataTable.ajax.reload(null, false);
        bootstrapSelectSubCategory.empty().selectpicker('refresh');

        if(!category) {
            return;
        }
        
        APICaller(CATEGORY_ROUTE, { "category": category, 'select_json': true }, response => {
            const subCategories = response;
    
            if (subCategories.length) {
                subCategories.forEach(subCategory => {
                    bootstrapSelectSubCategory.append(`<option value="${subCategory.id}">${subCategory.name}</option>`);
                });
            }
            
            bootstrapSelectSubCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    });    

    bootstrapSelectSubCategory.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

    bootstrapSelectBrands.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    });

    $(document).on('change', ".selectAll", function () {
        const isChecked = this.checked;
        $('.actions').toggleClass('d-none', !isChecked);
        $(':checkbox').prop('checked', isChecked);
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

    window.selectProduct = function () {
        const isChecked = $('tbody input[type="checkbox"]:checked').length > 0;
        $('.actions').toggleClass('d-none', !isChecked);
    };
    

    window.deleteCurrentProduct = function (element) {
        const form = $(element).closest('form');
        const name = form.attr('data-name');
        const url = form.attr('action');
    
        const template = swalText(name);
    
        showConfirmationDialog('Selected purchases!', template, () => {
            APIDELETECALLER(url, response => {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, error => {
                toastr['error'](error.message);
            });
        });
    };
    

    window.deleteMultipleProducts = function () {
        const searchedIds = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).attr('data-id');
        }).get();
    
        const searchedNames = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).attr('data-name');
        }).get();
    
        const template = swalText(searchedNames);
    
        showConfirmationDialog('Selected purchases!', template, () => {
            searchedIds.forEach((id, index) => {
                const apiUrl = REMOVE_PRODUCT_ROUTE.replace(':id', id);
                APIDELETECALLER(apiUrl, response => {
                    const message = response.status === 500 ? response.responseJSON.message : response.message;
                    toastr[response.status === 500 ? 'error' : 'success'](message);
                    table.DataTable().ajax.reload();
                }, error => {
                    toastr['error'](error.message);
                });
            });
        });
    };
    

});