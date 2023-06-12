import { APIDELETECALLER } from '../ajax/methods';
import { swalText, showConfirmationDialog } from '../helpers/action_helpers';

$(function () {
    let table = $('table#purchasesTable');

    $('.selectStock, .selectAction')
        .selectpicker('refresh')
        .trigger('change');

    let startDate = moment().startOf('month').format('YYYY-MM-DD')
    let endDate = moment().endOf('month').format('YYYY-MM-DD')

    let publishingRange = startDate + ' - ' + endDate;
    let bootstrapSelectStock = $('.bootstrap-select .selectStock')

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: startDate,
        endDate: endDate,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: PURCHASE_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    'brand': BRAND,
                    'limit': d.custom_length = d.length,
                    'out_of_stock': bootstrapSelectStock.val(),
                    'search': d.search.value,
                    'publishing': publishingRange
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: '1%',
                render: function (data, type, row) {
                    let statusStr = ''
                    if (row.status === 'enabled') {
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
                       <input name="checkbox" class="form-check-input" onchange="selectPurchase(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
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
                width: '10%',
                orderable: false,
                name: "image",
                render: function (data, type, row) {
                    if (row.images && row.images.length > 1) {
                        // Generate carousel HTML with multiple images
                        let carouselItems = row.images.map((image, index) => {
                            let isActive = index === 0 ? 'active' : ''; // Set first image as active
                            return `<div class="carousel-item ${isActive}">
                                        <img class="d-block w-100" src="${CONFIG_URL + image.path + "/" + image.name}" alt="Slide ${index + 1}">
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
                        let imagePath = CONFIG_URL + row.images[0].path + "/" + row.images[0].name;
                        return `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${imagePath}" />`;

                    } else {
                        return `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png"/>`;
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "name",
                render: function (data, type, row) {
                    return `<a href=${EDIT_PRODUCT_ROUTE.replace(":id", row.id)}>${row.name}</a>`
                }
            },
            {
                width: '6%',
                name: "price",
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.price}</span>`
                }
            },
            {
                width: '6%',
                orderable: true,
                render: function (data, type, row) {
                    return `<span>€${row.total_price}</span>`
                }
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
                width: '7%',
                orderable: false,
                render: function (data, type, row) {
                    let stockStatus = row.quantity ? 'In stock' : 'Out of stock';
                    let stockColor = row.quantity ? 'text-success' : 'text-danger';

                    return `<span class="${stockColor}">${stockStatus}</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    let paidOrdersCount = row.paid_orders_count;
                    let unpaidOrdersCount = row.unpaid_orders_count;

                    let displayText = `<a class='text-success' ">${paidOrdersCount} paid</a> / <a class='text-danger'>${unpaidOrdersCount} unpaid</a>`;

                    return displayText;
                }
            },
            {
                width: '15%',
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
                width: '3%',
                orderable: false,
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.code + '</span>';
                }
            },
            {
                width: '2%',
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
                    if (row.is_paid) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {
                orderable: false,
                width: '8%',
                render: function (data, type, row) {
                    let detachFormTemplate = `
                    <form style='display:inline-block;' onsubmit='detachPurchase(event)' id='delete-form' action=${DETACH_PRODUCT.replace(':id', BRAND)} method='POST' data-name=${row.name}>
                        <input type='hidden' name='_method' value='DELETE'>
                        <input type='hidden' name='purchase_id' value='${row.id}'>
                        <button type='submit' class='btn p-0' title='Delete'>
                        <i class="text-danger fal fa-unlink"></i>
                        </button>
                    </form>
                `;

                    return `${detachFormTemplate}`;
                }
            }
        ],
        order: [[2, 'asc']]
    });

    $('input[name="datetimes"]').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');

        publishingRange = startDate + ' - ' + endDate;
        $(this).val(publishingRange);

        dataTable.ajax.reload();
    });

    bootstrapSelectStock.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    // window actions

    window.detachPurchase = function (event) {
        event.preventDefault();

        let form = event.target;
        let data = $(form).serialize();
        let formData = Object.fromEntries(new URLSearchParams(data));
        let url = form.getAttribute('action');
        let method = form.getAttribute('method');
        let name = form.getAttribute('data-name');

        const template = swalText(name);


        showConfirmationDialog('Selected purchases!', template, function () {
            $.ajax({
                url: url,
                method: method,
                data: {
                    _method: 'DELETE',
                    purchase_id: parseInt(formData.purchase_id),
                },
                success: function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                },
                error: function (error) {
                    toastr['error'](error.message);
                }
            })
        })

    }

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
                deleteMultiplePurchases();
                break;
            default:
        }
    });

    window.selectPurchase = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteMultiplePurchases = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        const template = swalText(searchedNames);

        showConfirmationDialog('Selected purchases!', template, function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(DETACH_PRODUCT.replace(':id', BRAND),{ keyName: 'purchase_id', value: id }, function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload();
                }, function (error) {
                    toastr['error']('Supplier has not been deleted');
                });
            });
        })
    };

})