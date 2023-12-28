import { APICaller } from '../ajax/methods';
import { paymentMethods } from '../helpers/statuses';
import { handleErrors } from "../helpers/action_helpers";

$(function(){

    $('select[name="package_id"]').selectpicker();

    let bootstrapSelectPackage = $('.bootstrap-select select[name="package_id"]');

    let dataTable = $('#formOperations').DataTable({
        columns: [
            { orderable: false, width: '1%'},
            { orderable: false, width: '1%', class:'text-center' },
            { orderable: false, width: '5%', class:'text-center' },
            { orderable: false, width: '8%', class:'text-center' },
            { orderable: false, width: '8%', class:'text-center' },
            { orderable: false, width: '5%', class:'text-center' },
            { orderable: false, width: '1%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '1%', class:'text-center' },
            { orderable: false, width: '1%', class:'text-center' },
            { orderable: false, width: '10%',class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
        ]
    });

    bootstrapSelectPackage.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let val = clickedIndex;

        APICaller(ORDERS_API_ROUTE, {
            'package': val, 
            'is_it_delivered': 0,
        }, response => {
            renderDataInDataTable(dataTable, response.data);
        }, error => {
            console.log(error);
        });

    });

    function renderDataInDataTable(dataTable, data) {
        dataTable.clear();
    
        for (let i = 0; i < data.length; i++) {
            let paymentMethodTemplate = `<select disabled name="payment_method" class="form-control form-control-sm selectpicker" >`;
    
            for (const key in paymentMethods) {
                if (paymentMethods.hasOwnProperty(key)) {
                    paymentMethodTemplate += `
                        <option value="${key}">
                            ${paymentMethods[key]}
                        </option>
                    `;
                }
            }

            paymentMethodTemplate += '</select>';

            let dateOfPaymentTemplate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input disabled type="text" class="form-control form-control-sm datepicker" name="date_of_payment">
            </div>
            <span name="date_of_payment" class="text-danger"></span>`;

            let packageTemplate = `
                <span>${data[i].package.package_name}</span>
            `; 

            let deliveryDateTemplate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input disabled type="text" class="form-control form-control-sm datepicker" name="delivery_date">
            </div>
            <span name="delivery_date" class="text-danger"></span>`;

            let invoiceDate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input disabled type="text" class="form-control form-control-sm datepicker" name="invoice_date">
            </div>
            <span name="invoice_date" class="text-danger"></span>`;

            let invNumberTemplate = `
            <input disabled type="text" class="form-control form-control-sm" name="invoice_number" />
            <span name="invoice_number" class="text-danger"></span>
            `;
        
            let select = `<div div class="form-check">\n\
                <input name="checkbox" class="form-check-input" value="${data[i].id}" data-id='${data[i].id}' type="checkbox"> \n\
            </div>`

            let purchaseName = `<a target="_blank" href="${EDIT_PRODUCT_ROUTE.replace(':id', data[i].purchase.id)}">${data[i].purchase.name}</a>`;
            
            let paymentReference = 
            `<input disabled type="text" class="form-control form-control-sm" name="payment_reference" />
            <span name="payment_reference" class="text-danger"></span>`;

            dataTable.row.add([
                select,
                data[i].id,
                purchaseName,
                moment(data[i].package_extension_date).format('DD MMMM YYYY'),
                moment(data[i].package.delivery_date).format('DD MMMM YYYY'),
                moment(data[i].package_extension_date).fromNow(),
                packageTemplate,
                data[i].tracking_number,
                data[i].payment.quantity,
                data[i].payment.price,
                paymentMethodTemplate,
                dateOfPaymentTemplate,
                deliveryDateTemplate,
                invNumberTemplate,
                paymentReference,
                invoiceDate,
            ]);
        }
    
        dataTable.draw();

        // Initialize Bootstrap Select for dynamically added elements
        $('.selectpicker').selectpicker('refresh');

        // Initialize Datepicker for dynamically added elements
        $('.datepicker').datepicker({ format: 'mm/dd/yyyy' });

        // Check if there are any rows in the DataTable
        if (dataTable.rows().count() > 0) {
            // Show the submit button
            $('#submitWrapper').html(
                `<button class="btn btn-primary" type="button" onclick="saveChanges(this)">Save changes</button>`
            );
        } else {
            // No rows, so hide the submit button
            $('#submitWrapper').html('');
        }

    }

    $("#formOperations tbody").on("change", 'input[type="checkbox"]', function () {
        let rows = ( $('tbody input[type="checkbox"]:checked').length - 1 );
        console.log(rows);
        
        var $currentRow = $(this).closest("tr");
        var $elementsToEnable = $currentRow.find("input:not([type='checkbox']), select, textarea, span.text-danger");

        if ($(this).is(":checked")) {
            $elementsToEnable.prop("disabled", false);
            $elementsToEnable.filter(".selectpicker").selectpicker("refresh"); // Refresh selectpicker
        } else {
            $elementsToEnable.prop("disabled", true);
            $elementsToEnable.filter(".selectpicker").selectpicker("refresh"); // Refresh selectpicker
        }
    
        // Set the index for the input fields
        $elementsToEnable.each(function () {
            var $input = $(this);
            var nameAttribute = $input.attr("name");
    

            if (nameAttribute) {
                // Check if the name attribute already has an index
                if (nameAttribute.indexOf('.') !== -1) {
                    // Replace the existing index with the current index
                    nameAttribute = nameAttribute.replace(/\.\d+/, '');
                } 
                else {
                    // Append the index to the name attribute
                    nameAttribute += '.' + rows;
                }
                
                // Update the name attribute
                $input.attr("name", nameAttribute);
            }
        });
    });    

    $("#formOperations thead").on("change", '.selectAll', function () {
        var $checkboxes = $('#formOperations tbody input[type="checkbox"]');
        var $rows = $('#formOperations tbody tr');
        
        if ($(this).is(":checked")) {
            $rows.each(function () {
                var $inputsToEnable = $(this).find("input:not([type='checkbox']), select, textarea");
                $inputsToEnable.prop("disabled", false);
                $inputsToEnable.filter(".selectpicker").selectpicker("refresh");
            });
        } else {
            $rows.each(function () {
                var $inputsToDisable = $(this).find("input:not([type='checkbox']), select, textarea");
                $inputsToDisable.prop("disabled", true);
                $inputsToDisable.filter(".selectpicker").selectpicker("refresh");
            });
        }
        
        // Optionally: Update the state of individual checkboxes
        $checkboxes.prop("checked", $(this).is(":checked"));
    });

    window.saveChanges = function(e) {
        let rows = $('tbody input[type="checkbox"]:checked').map(function () {
            return $(this).closest('tr');
        });
        
        let form = $('#saveOrders');
        let method = form.attr('method');
        let action = form.attr('action');

        let formData = {
            'order_id': [],
            'payment_method': [],
            'delivery_date': [],
            'date_of_payment': [],
            'invoice_number': [],
            'payment_reference': [],
            'invoice_date': []
        };
        
        if(!rows.length) {
            toastr.error('Please select row');
            return;
        }

        rows.each(function () {

            var id = $(this).find('td:eq(1)').text();
            var paymentMethod = $(this).find('select[name^="payment_method"]').val();
            var dateOfPayment = $(this).find('input[name^="date_of_payment"]').val();
            var deliveryDate = $(this).find('input[name^="delivery_date"]').val();
            var invoiceNumber = $(this).find('input[name^="invoice_number"]').val();
            var invoiceDate = $(this).find('input[name^="invoice_date"]').val();        
            var paymentReference = $(this).find('input[name^="payment_reference"]').val();        

            formData['order_id'].push(id);
            formData['payment_method'].push(paymentMethod);
            formData['date_of_payment'].push(dateOfPayment);
            formData['delivery_date'].push(deliveryDate);
            formData['invoice_number'].push(invoiceNumber);
            formData['payment_reference'].push(paymentReference);
            formData['invoice_date'].push(invoiceDate);
        });
        
        console.log(formData);

        $.ajax({
            url: action,
            method: method,
            data: formData,
            success: function (response) {
                toastr.success(response.message);
                setTimeout(function () {
                    location.reload();
                }, 2000);
            },
            error: (xhr, status, error) => {
                toastr.error(xhr.responseJSON.message);
                handleErrors(xhr.responseJSON.errors);
            }
        });
    };
    
})
