import { handleErrors } from '../helpers/action_helpers';

$(function () {
    $('select[name="customer_id"]')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    let table = $('table#orders');
    let form = $('#paymentOrders')

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });


    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
    let createdRange;

    // Set the default status array
    let statusArray = [6];

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: ORDER_API_ROUTE,
            data: function (d) {
                var orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                var orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({}, d, {
                    'customer': bootstrapSelectCustomer.val() === null ? '' : bootstrapSelectCustomer.val().toLowerCase(),
                    "search": d.search.value,
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
                    'limit': d.custom_length = d.length,
                    'is_paid': 0,
                    'without_package':0,
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
                       <input name="checkbox" class="form-check-input" onchange="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
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
                    <input type="hidden" value="${row.id}" name="order_id" />
                    `;
                }
            },
            {
                width: '8%',
                name: "purchase.name",
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    return `<a href="#">${row.purchase.name}</a>`
                }
            },
            {
                orderable: false,
                width: '5%',
                name: "single_sold_price",
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>€${row.single_sold_price}</span>`
                }
            },
            {
                orderable: false,
                width: '5%',
                name: "discount_single_sold_price",
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>€${row.discount_single_sold_price}</span>`
                }
            },
            {
                orderable: false,
                width: '8%',
                name:"total_sold_price",
                render: function (data, type, row) {
                    return`
                    <input type="text" max="${row.total_sold_price}" value="${row.total_sold_price}" name="price" class="form-control form-control-sm" />
                    <span data-active="true" class="text-danger" name="price"></span>
                    `;
                }
            },
            {
                width: '8%',
                orderable: false,
                name: "original_sold_price",
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>€${row.original_sold_price}</span>`
                }
            },
            {
                width: '8%',
                orderable: false,
                name:"sold_quantity",
                render: function (data, type, row) {
                    return `
                    <input type="number" max="${row.sold_quantity}" value="${row.sold_quantity}" name="quantity" class="form-control form-control-sm" />
                    <span data-active="true" class="text-danger" name="quantity"></span>
                    `;
                }
            },
            {
                width: '8%',
                orderable: false,
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
                width: '5%',
                orderable: false,
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.tracking_number + '</span>';
                }
            },
            {
                width: '6%',
                orderable: false,
                class:'text-center',
                render: function (data, type, row) {
                    let date;
                    if(row.package_extension_date) {
                        date = '<span>' + moment(row.package_extension_date).format('YYYY-MM-DD') + '</span>';;
                    } else {
                        date = '';
                    }
                    return date;
                }
            },
            {
                width: '6%',
                orderable: false,
                render: function (data, type, row) {
                    return `<span>${moment(row.date_of_sale).format('YYYY-MM-DD')}</span>
                    <a data-target="date_of_payment" value="${row.date_of_sale}" class="text-primary" type="button"><i class="fa-light fa-copy"></i></a>`;
                }
            },        
            {
                width: '1%',
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

    window.selectOrder = function (e) {
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
    bootstrapSelectCustomer.bind('changed.bs.select', function () {
        dataTable.ajax.reload(null, false);
    })


    form.on('submit', function (e) {
        e.preventDefault();
        let selectedData = table.find('tbody input[type="checkbox"]:checked').length;

        if (selectedData <= 0) {
            toastr['error']('Please select orders');
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