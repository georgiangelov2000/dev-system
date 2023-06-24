import { APICaller } from '../ajax/methods';

$(function () {
    let table = $("#massEditPurchases");

    $('select[name="category_id"],select[name="brand_id"], select[name="sub_category_ids"]').selectpicker().val('').trigger('change');

    const bootstrapSubCategory = $('select[name="sub_category_ids"]');
    const bootstrapCategory = $('select[name="category_id"]');

    let dataTable = table.DataTable({
        serverSide: true,
        ordering: false,
        ajax: {
            url: PURCHASE_API,
            data: function (d) {
                return $.extend({}, d, {
                    'supplier': SUPPLIER_ID,
                    'search': d.search.value,
                    'out_of_stock': 1
                });
            }
        },
        columns: [
            {
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
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '5%',
                render: function (data, type, row) {
                    return `<a href=${PURCHASE_EDIT.replace(':id', row.id)}>${row.name}</a>`
                }
            },
            {
                width: '6%',
                name: "price",
                data: "price"
            },
            {
                width: '5%',
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
                width: '6%',
                name: "total_price",
                data: "total_price"
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
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    return '<span>' + moment(row.created_at).format('YYYY-MM-DD') + '</span>';
                }
            },
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

    // Window actions
    window.selectPurchase = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

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
        let brandIds = formData.brand_id;
        let category = formData.category_id;
        let subCategoryIds = Array.isArray(formData.sub_category_ids) ? formData.sub_category_ids : [];

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
                    'purchase_ids': searchedIds,
                    'price': price,
                    'quantity': quantity,
                    'category_id': category,
                    'brand_ids': brandIds,
                    'sub_category_ids':subCategoryIds
                },
                success: function (response) {
                    toastr['success'](response.message);

                    setTimeout(() => {
                        location.reload();
                    }, 1500);

                },
                error: function (xhr, status, error) {
                    toastr['error'](response.message);
                }
            })

        } else {
            toastr['error']("Please select purchases");
        }

    }

});