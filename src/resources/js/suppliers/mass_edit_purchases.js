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
                    'out_of_stock': 1,
                    'is_paid':0,
                    'status': 0
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
                width: '1%',
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
                render: function (data, type, row) {
                    return `<a href=${PURCHASE_EDIT.replace(':id', row.id)}>${row.name}</a>`
                }
            },
            {
                width: '6%',
                name: "price",
                render:function(data,type,row) {
                    return `<span>€${row.price}</span>`
                }
            },
            {
                orderable: true,
                width: '5%',
                name:'total_price',
                render: function (data, type, row) {
                    return `<span>€${row.total_price}</span>`
                }
            },
            {
                orderable:true,
                width:'5%',
                name:'original_price',
                render:function(data,type,row) {
                    return `<span>€${row.original_price}</span>`
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
                width:'1%',
                orderable:false,
                name:'is_paid',
                render:function(data,type,row) {
                    if ( (row.is_paid === 1) && (row.status === 1 || row.status === 4)  ) {
                        return '<span class="text-success">Yes</span>';
                    } else if ( (row.is_paid === 3) && (row.status === 3) ) {
                        return '<span class="text-warning">Refund</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    if (row.status === 1) {
                        return '<img style="height:40px;" class="w-50" title="Paid" src = "/storage/images/static/succesfully.png" /> '
                    }
                    else if (row.status === 2) {
                        return '<img style="height:40px;" class="w-50" title= "Pending" src = "/storage/images/static/pending.png" /> '
                    }
                    else if (row.status === 3) {
                        return '<img style="height:40px;" class="w-50" title="Partially Paid" src = "/storage/images/static/partially-payment.png" /> '
                    }
                    else if (row.status === 4) {
                        return '<img style="height:40px;" class="w-50" title="Overdue" src = "/storage/images/static/overdue.png" /> '
                    }
                    else if (row.status === 5) {
                        return '<img style="height:40px;" class="w-50" title="Refunded" src = "/storage/images/static/ordered.png" /> '
                    } else {
                        return '<img style="height:40px;" class="w-50" title="Unpaid" src = "/storage/images/static/unpaid.png" />';
                    }
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