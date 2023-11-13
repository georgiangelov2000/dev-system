import { APICaller } from '../ajax/methods';
import { numericFormat } from '../helpers/functions';
import { paymentStatuses, statusPaymentsWithIcons} from '../helpers/statuses';

$(function () {
    let table = $("#massEditPurchases");

    $('select[name="category_id"],select[name="brand_id"], select[name="sub_category_ids"]').selectpicker().val('').trigger('change');

    const bootstrapSubCategory = $('select[name="sub_category_ids"]');
    const bootstrapCategory = $('select[name="category_id"]');

    const statuses = [2];

    let dataTable = table.DataTable({
        serverSide: true,
        ordering: false,
        ajax: {
            url: PURCHASE_API,
            data: function (d) {
                return $.extend({}, d, {
                    'supplier': SUPPLIER_ID,
                    'search': d.search.value,
                    'limit': d.custom_length = d.length,
                    'status': statuses,
                });
            }
        },
        columns: [
            {
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '1%',
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '1%',
                orderable: false,
                name: "image",
                render: function (data, type, row) {
                    return row.image_path ? `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${row.image_path}" />` : `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png"/>`;
                }
            },
            {
                width: '10%',
                class:'text-center',
                render: function (data, type, row) {
                    return `<a href=${PURCHASE_EDIT.replace(':id', row.id)}>${row.name}</a>`
                }
            },
            {
                width: '6%',
                name: "price",
                class:'text-center',
                render:function(data,type,row) {
                    return `<span>${numericFormat(row.price)}</span>`
                }
            },
            {
                width: '6%',
                name: "discount_price",
                class:'text-center',
                render:function(data,type,row) {
                    return `<span>${numericFormat(row.discount_price)}</span>`
                }
            },
            {
                orderable: true,
                width: '5%',
                name:'total_price',
                render: function (data, type, row) {
                    return `<span>${numericFormat(row.total_price)}</span>`
                }
            },
            {
                orderable:true,
                width:'5%',
                name:'original_price',
                render:function(data,type,row) {
                    return `<span>${numericFormat(row.original_price)}</span>`
                }
            },
            {
                width: '5%',
                orderable: true,
                class:'text-center',
                name: "quantity",
                data: "quantity"
            },
            {
                width: '7%',
                orderable: true,
                class:'text-center',
                name: "initial_quantity",
                data: 'initial_quantity'
            },
            {
                width: '2%',
                name: "discount_percent",
                orderable: true,
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>${row.discount_percent}%</span>`
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
                    let expected = moment(row.expected_delivery_date);
                    let deliveryDate = moment(row.delivery_date);
                    let isDelivered = row.is_it_delivered;
                    
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
                width: '5%',
                orderable: false,
                name: "status",
                class: "text-center",
                render: function (data, type, row) {
                    const statusInfo = statusPaymentsWithIcons[row.payment.payment_status] || { label: "Unknown", iconClass: "fal fa-question-circle" };
            
                    return `<div title="${statusInfo.label}" class="status">
                        <span class="icon"><i class="${statusInfo.iconClass}"></i></span>
                    </div>`;
                }
            }
            
        ]
    });

    // Actions
    bootstrapCategory.on('changed.bs.select', function () {
        const selectedCategory = $(this).val();
        
        bootstrapSubCategory.empty();

        APICaller(SUB_CATEGORY_API_ROUTE, { 'category': selectedCategory,'select_json':true }, function (response) {
            let sub_categories = response;
            console.log(sub_categories);
            
            if (sub_categories.length > 0) {
                $.each(sub_categories, function ($key, sub_category) {
                    bootstrapSubCategory.append(`<option value="${sub_category.id}"> ${sub_category.name} </option>`)
                })
            }
            bootstrapSubCategory.selectpicker('refresh');
        },(function (error) {
            console.log(error);
        }))
    })

    window.updatePurchases = function (e) {
        e.preventDefault();
        let form = e.target;
        let method = e.target.getAttribute('method');
        let data = $(form).serialize();
        let formData = {};

        data.split('&').forEach(function (keyValue) {
            let pair = keyValue.split('=');
            let key = decodeURIComponent(pair[0]);
            let value = decodeURIComponent(pair[1] || '');

            if (formData[key]) {
                if (Array.isArray(formData[key])) {
                    formData[key].push(value);
                } else {
                    formData[key] = [formData[key], value];
                }
            } else {
                formData[key] = value;
            }
        });

        let price = formData.price;
        let quantity = formData.quantity;
        let brands = Array.isArray(formData.brand_id) ? formData.brand_id : (formData.brand_id ? [formData.brand_id] : []);
        let category = formData.category_id;
        let subCategoryIds = Array.isArray(formData.sub_category_ids) ? formData.sub_category_ids : (formData.sub_category_ids ? [formData.sub_category_ids] : []);
        let discount_percent = formData.discount_percent;

        let searchedIds = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
        });

        if (searchedIds.length > 0) {
            let action = PURCHASE_UPDATE;

            $.ajax({
                url: action,
                method: method,
                data: {
                    'purchases': searchedIds,
                    'price':price,
                    'quantity':quantity,
                    'category_id':category,
                    'brands':brands,
                    'sub_category_ids':subCategoryIds,
                    'discount_percent':discount_percent
                },
                success: function (response) {
                    toastr['success'](response.message);
                    dataTable.ajax.reload(null, false);
                },
                error: function (xhr, status, error) {
                    toastr['error'](xhr.responseJSON.message);
                }
            })

        } else {
            toastr['error']("Please select purchases");
        }
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

});