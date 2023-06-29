import { APICaller } from '../ajax/methods';
import { handleErrors } from '../helpers/action_helpers';

$(function () {
    $('select[name="customer_id"],select[name="package_id"]').selectpicker()

    let bootstrapCustomer = $('.bootstrap-select select[name="customer_id"]');
    let bootstrapPackage = $('.bootstrap-select select[name="package_id"]');
    let btnSearch = $('#search');
    let table = $('#orders');
    let form = $('#packagePayment');

    let dataTable = table.DataTable({
        ordering: false,
        columnDefs: [
            { width: "1%", targets: 0 },
            { width: "1%", targets: 1, class:"text-center" },
            { width: "8%", targets: 2, class:"text-center" },
            { width: "10%", targets: 3, class:"text-center" },
            { width: "10%", targets: 4, class:"text-center" },
            { width: "7%", targets: 5, class:"text-center" },
            { width: "12%", targets: 6, class:"text-center" },
            { width: "8%", targets: 7, class:"text-center" },
            { width: "12%", targets: 8, class:"text-center" },
            { width: "1%", targets: 9, class:"text-center" },
            { width: "14%", targets: 10, class:"text-center" },
            { width: "8%", targets: 11, class:"text-center" },
            { width: "10%", targets: 12, class:"text-center" },
            // add more targets and widths as needed
          ]
    });

    bootstrapCustomer.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let customer = $(this).val();

        APICaller(PACKAGE_API_ROUTE, {
            'customer': customer,
            'select_json': true,
            'no_paid_orders': true
        }, function (response) {
            let packages = response;
            if (packages.length > 0) {
                bootstrapPackage.append('<option value="">Please select</option>');

                $.each(packages, function ($key, pack) {
                    bootstrapPackage.append(`<option value="${pack.id}"> ${pack.package_name} </option>`)
                })
            }
            bootstrapPackage.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    })

    btnSearch.on('click', function () {
        let packId = bootstrapPackage.val();

        if (packId) {
            APICaller(ORDER_API_ROUTE, {
                'package': packId,
                'is_paid': 0,
                'select_json': true
            }, function (response) {
                loadDataTable(response);
            }, function (error) {
                console.log(error);
            })
        } else {
            toastr['error']('Package has not been selected');
        }

    })

    function loadDataTable(data) {
        dataTable.clear().draw();
        let counter = 0;
        
        if (data.length > 0) {
            let rows = data.map(function (item) {
                let orderStatus;
                if (item.status === 'Pending') {
                    orderStatus = '<i title="Pending" class="fa-light fa-loader"></i>';
                } else if (item.status === 'Ordered') {
                    orderStatus = '<i title="Ordered" class="fa-light fa-truck"></i>';
                } else {
                    orderStatus = item.status;
                }

                return [
                    '<div div class="form-check">\n\
                        <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + item.id + ' data-name= ' + item.invoice_number + ' type="checkbox"> \n\
                    </div>',
                    item.id + `<input type="hidden" value="${item.id}" name="ids">`,
                    `<a href='${PRODUCT_EDIT_ROUTE.replace(":id",item.product.id)}'>${item.product.name}</a>`,
                    item.invoice_number,
                    item.tracking_number,
                    item.single_sold_price,
                    `
                    <input data-type="active" type="text" name="price" class="form-control form-control-sm" value="${item.total_sold_price}">
                    <span data-active="true" name="price" class="text-danger"> </span>
                    `,
                    item.original_sold_price,
                    `
                    <input data-type="active" type="number" name="quantity" class="form-control form-control-sm" value="${item.sold_quantity}">
                    <span data-active="true" name="quantity" class="text-danger"> </span>
                    `,
                    orderStatus,
                    `
                <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" name="date_of_payment" class="form-control form-control-sm">
                <span data-active="true" name="date_of_payment" class="text-danger"> </span>
              `,
                    item.date_of_sale,
                    item.package_extension_date
                ];
            });

            dataTable.rows.add(rows).draw();

            $('.submitWrapper').html('<button type="submit" class="btn btn-primary">Save changes</button>');

            $('input[name="date_of_payment"]').datepicker({
                format: 'yyyy-mm-dd'
            });

        }
    }

    window.selectOrder = function (e) {
        let row = $(e).closest('tbody > tr');
        let isChecked = $(e).is(':checked');
        
        let checkedOptions = row.closest('tbody').find('input[type="checkbox"]:checked');

        row.find('span[data-active="true"]').each(function() {
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
    
    form.on('submit', function (e) {
        e.preventDefault();
        let selectedData = table.find('tbody input[type="checkbox"]:checked').length;

        if(selectedData <= 0) {
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
                let newVal;

                if(value !== '' && !isNaN(value)) {
                    newVal = parseInt(value);
                } else {
                    newVal = value;
                }

                if (formData[key]) {
                    if (Array.isArray(formData[key])) {
                        formData[key].push(newVal);
                    } else {
                        formData[key] = [newVal];
                    }
                } else {
                    formData[key] = [newVal];
                }

            })
        })

        $.ajax({
            type:'POST',
            url:url,
            data:{
                'id':formData.ids,
                'date_of_payment': formData.date_of_payment,
                'quantity': formData.quantity,
                'price': formData.price,
            },
            success:function(response) {
                console.log(response);
                toastr['success'](response.message);
                dataTable.clear().draw();
            },
            error:function(xhr,status,error){
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