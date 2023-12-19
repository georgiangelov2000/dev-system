import { APICaller } from '../ajax/methods';
import { paymentMethods } from '../helpers/statuses';
import { handleErrors } from "../helpers/action_helpers";

$(function(){

    $('select[name="package_id"]').selectpicker();

    let bootstrapSelectPackage = $('.bootstrap-select select[name="package_id"]');

    let pendingStatuses = [2];

    let dataTable = $('#formOperations').DataTable({
        columns: [
            { orderable: false, width: '1%'},
            { orderable: false, width: '1%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
            { orderable: false, width: '8%', class:'text-center' },
            { orderable: false, width: '8%', class:'text-center' },
            { orderable: false, width: '10%', class:'text-center' },
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

        APICaller(ORDERS_API_ROUTE, {'package': val, 'status': pendingStatuses}, response => {
            renderDataInDataTable(dataTable, response.data);
        }, error => {
            console.log(error);
        });

    });

    function renderDataInDataTable(dataTable, data) {
        dataTable.clear();
    
        for (let i = 0; i < data.length; i++) {
            let paymentMethodTemplate = `<select name="payment_method" class="form-control selectpicker" >`;
    
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


            let deliveryTemplateOptions = `<select class="form-control selectpicker" name="is_it_delivered.${i}">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
            <span name="is_it_delivered.${i}" class="text-danger"></span>
            `

            let dateOfPaymentTemplate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control datepicker" name="date_of_payment.${i}">
            </div>
            <span name="date_of_payment.${i}" class="text-danger"></span>`;

            let deliveryDateTemplate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control datepicker" name="delivery_date.${i}">
            </div>
            <span name="delivery_date.${i}" class="text-danger"></span>`;

            let invoiceDate = `
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control datepicker" name="invoice_date.${i}">
            </div>
            <span name="invoice_date.${i}" class="text-danger"></span>`;

            let invNumberTemplate = `
            <input type="text" class="form-control" name="invoice_number.${i}" />
            <span name="invoice_number.${i}" class="text-danger"></span>
            `;
        
            let select = `<div div class="form-check">\n\
                <input name="checkbox" class="form-check-input" value="${data[i].id}" data-id='${data[i].id}' type="checkbox"> \n\
            </div>`
            

            dataTable.row.add([
                select,
                data[i].id,
                data[i].purchase.name,
                data[i].payment.quantity,
                data[i].payment.price,
                data[i].tracking_number,
                paymentMethodTemplate,
                deliveryTemplateOptions,
                dateOfPaymentTemplate,
                deliveryDateTemplate,
                invNumberTemplate,
                invoiceDate
            ]);
        }
    
        dataTable.draw();

        $('.selectpicker').selectpicker();
        $('.datepicker').datepicker({format: 'mm/dd/yyyy'})
        $('#submitWrapper').html(
            `<button class="btn btn-primary" type="button" onclick="saveChanges(this)">Save changes</button>`
        )
    }

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
            'is_it_delivered': [],
            'date_of_payment': [],
            'delivery_date': [],
            'invoice_number': [],
            'invoice_date': []
        };
        
        rows.each(function () {
            var id = $(this).find('td:eq(1)').text();
            var paymentMethod = $(this).find('select[name^="payment_method"]').val();
            var delivered = $(this).find('select[name^="is_it_delivered"]').val();
            var dateOfPayment = $(this).find('input[name^="date_of_payment"]').val();
            var deliveryDate = $(this).find('input[name^="delivery_date"]').val();
            var invoiceNumber = $(this).find('input[name^="invoice_number"]').val();
            var invoiceDate = $(this).find('input[name^="invoice_date"]').val();
        
            formData['order_id'].push(id);
            formData['payment_method'].push(paymentMethod);
            formData['is_it_delivered'].push(delivered);
            formData['date_of_payment'].push(dateOfPayment);
            formData['delivery_date'].push(deliveryDate);
            formData['invoice_number'].push(invoiceNumber);
            formData['invoice_date'].push(invoiceDate);
        });
        
        console.log(formData);

        $.ajax({
            url: action,
            method: method,
            data: formData,
            success: function (response) {
                console.log(response);
            },
            error: (xhr, status, error) => {
                toastr.error(xhr.responseJSON.message);
                handleErrors(xhr.responseJSON.errors);
            }
        });
    };
    
})
