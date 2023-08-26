import { handleErrors } from '../helpers/action_helpers';

$(function () {
    $('select[name="supplier_id"]')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    let table = $('table#purchases');
    let form = $('#paymentPurchases')

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let createdRange;

    // Set the default status array
    let statusArray = [0];

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: PRODUCT_API_ROUTE,
            data: function (d) {
                var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({}, d, {
                    'supplier': bootstrapSelectSupplier.val() === null ? '' : bootstrapSelectSupplier.val().toLowerCase(),
                    "search": d.search.value,
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
                    'limit': d.custom_length = d.length,
                    'status':statusArray,
                    'publishing': createdRange,
                });
            }
        },
        columns: [
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
                    return `
                    <span class="font-weight-bold">${row.id}</span>
                    <input type="hidden" value="${row.id}" name="purchase_id" />
                    `;
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
                                        <img class="d-block w-100" src="${image.path + "/" + image.name}" alt="Slide ${index + 1}">
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
                        let imagePath = row.images[0].path + "/" + row.images[0].name;
                        return `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${imagePath}" />`;

                    } else {
                        return `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png"/>`;
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "name",
                class:'text-center',
                render: function (data, type, row) {
                    return `<a href="#">${row.name}</a>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "price",
                render: function (data, type, row) {
                    return `<span>€${row.price}</span>`
                }
            },
            {
                width: '8%',
                orderable: false,
                name: "discount_price",
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>€${row.discount_price}</span>`
                }
            },
            {
                width: '5%',
                orderable: false,
                name:'total_price',
                render: function (data, type, row) {
                    return `
                    <span>€${row.total_price}</span>
                    <a data-target="price" value="${row.total_price}" class="text-primary" type="button"><i class="fa-light fa-copy"></i></button>
                    `;
                }
            },
            {
                width: '5%',
                orderable: false,
                name:'original_price',
                render: function (data, type, row) {
                    return `
                    <span>€${row.original_price}</span>
                    <a data-target="price" value="${row.original_price}" class="text-primary" type="button"><i class="fa-light fa-copy"></i></button>
                    `;
                }
            },
            {
                width: '2%',
                orderable: true,
                name:'quantity',
                data: "quantity"
            },
            {
                width: '3%',
                orderable: true,
                name: "initial_quantity",
                render: function (data, type, row) {
                    return `
                    <span>${row.initial_quantity}</span>
                    <a data-target="quantity" value="${row.initial_quantity}" title="Copy" class="text-primary" type="button"><i class="fa-light fa-copy"></i></button>
                    `
                }
            },
            {
                width: '5%',
                orderable: false,
                name:'discount_percent',
                data: "discount_percent"
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    return `
                    <input type="number" name="quantity" max="${row.initial_quantity}" class="form-control form-control-sm" />
                    <span data-active="true" class="text-danger" name="quantity"></span>
                    `
                }
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    return `
                    <input type="text" name="price" max="${row.total_price}" class="form-control form-control-sm" />
                    <span data-active="true" class="text-danger" name="price"></span>
                    `
                }
            },
            {
                width: '10%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    return `
                    <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                        <input type="text" name="date_of_payment" class="form-control form-control-sm" />                    
                    </div>
                    <span data-active="true" class="text-danger" name="date_of_payment"></span>
                    `
                }
            },
            {
                width: '10%',
                orderable: false,
                name: 'expected_date_of_payment',
                class:'text-center',
                render: function (data, type, row) {
                    return '<span>' + moment(row.expected_date_of_payment).format('YYYY-MM-DD') + '</span>';
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
        ],
        order: [[1, 'asc']]
    });

    $('input[name="datetimes"]').on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate.format('YYYY-MM-DD');
        let endDate = picker.endDate.format('YYYY-MM-DD');

        createdRange = startDate + ' - ' + endDate;
        $(this).val(createdRange);

        dataTable.ajax.reload();
    });

    table.on('draw.dt', function () {
        let tableLength = dataTable.rows().data().length

        if (tableLength > 0) {
            $('.submitWrapper').html('<button type="submit" class="btn btn-primary">Save changes</button>');

            $('input[name="date_of_payment"]').datepicker({
                format: 'yyyy-mm-dd'
            });
        }
    });

    table.on('click', 'a[data-target]', function () {
        let target = $(this).data('target');
        let targetValue = $(this).attr('value');
        let row = $(this).closest('tr');
        row.find(`input[name="${target}"]`).val(targetValue);
    });

    window.selectPurchase = function (e) {
        let row = $(e).closest('tbody > tr');
        let isChecked = $(e).is(':checked');

        let checkedOptions = row.closest('tbody').find('input[type="checkbox"]:checked');

        row.find('span[data-active="true"]').each(function () {
            let name = $(this).attr('name');

            if (isChecked) {
                let newName = name + '.' + (checkedOptions.length - 1);
                $(this).attr('name', newName);
            } else {
                let parts = name.split('.');
                let oldName = parts[0]; // Extract the original name without the counter
                $(this).attr('name', oldName);
            }
        });
    };

    $(document).on('change', '.selectAll', function () {
        let rows = table.find('tbody > tr');
        let checkboxes = table.find('tbody input[type="checkbox"]');

        if (this.checked) {
            checkboxes.prop('checked', true);

            rows.each(function () {
                let counter = $(this).index();
                let spans = $(this).find('span[data-active="true"]');

                spans.each(function (index) {
                    let oldName = $(this).attr('name');
                    let newName = oldName + '.' + counter;

                    $(this).attr('name', newName);
                });
            });
        } else {
            checkboxes.prop('checked', false);

            rows.each(function () {
                let spans = $(this).find('span[data-active="true"]');

                spans.each(function () {
                    let parts = $(this).attr('name').split('.');
                    let oldName = parts[0]; // Extract the original name without the counter

                    $(this).attr('name', oldName);
                });
            });
        }
    });

    // ACTIONS
    bootstrapSelectSupplier.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })

    form.on('submit', function (e) {
        e.preventDefault();
        let selectedData = table.find('tbody input[type="checkbox"]:checked').length;

        if (selectedData <= 0) {
            toastr['error']('Please select purchases');
            return false;
        }

        let url = $(this).attr('action');
        let selectedRows = form.find('input[type="checkbox"]:checked').closest('tbody > tr');
        let formData = {};

        $.each(selectedRows, function (index, field) {
            let row = $(this).find(':input[type="text"], :input[type="number"], :input[type="hidden"]').serialize();
            row.split('&').forEach(function (val, index) {
                let pair = val.split('=');
                let key = decodeURIComponent(pair[0]);
                let value = decodeURIComponent(pair[1]);

                if (formData[key]) {
                    if (Array.isArray(formData[key])) {
                        formData[key].push(value);
                    } else {
                        formData[key] = [value];
                    }
                } else {
                    formData[key] = [value];
                }

            })
        })


        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            success: function (response) {
                console.log(response);
                toastr['success'](response.message);
                dataTable.clear().draw();
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    toastr['error'](xhr.responseJSON.message);
                    var errors = xhr.responseJSON.errors;
                    console.log(errors);
                    handleErrors(errors);
                }
            }
        })

    })

})