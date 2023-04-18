import { APIPOSTCALLER } from './ajaxFunctions.js';

$(function () {
    $('.selectSupplier').selectpicker('refresh').val('');

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectSupplier = $('.bootstrap-select .selectSupplier');
    let form = $('#filterForm');

    disabledOption.on('click', function () {
        if ($(this).is(':checked')) {
            dateRangeCol.addClass('d-none');
            dateRangePicker.addClass('d-none').prop('disabled', true).val(null);
        } else {
            dateRangeCol.removeClass('d-none');
            dateRangePicker.removeClass('d-none').prop('disabled', false);
            dateRangePicker.data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
            dateRangePicker.data('daterangepicker').setEndDate(moment().startOf('hour'));
        }
    });

    form.on('submit', function (e) {
        e.preventDefault();
        let supplier = bootstrapSelectSupplier.selectpicker('val');
        let date = dateRangePicker.val();

        APIPOSTCALLER(SUMMARY, { "supplier": supplier, 'date': date }, function (response) {
            let respData = response;

            if (respData) {
                summaryTemplate(respData);
            }
        }, function (error) {
            console.log(error);
        })
    })

    let summaryTemplate = function (data) {
        let template = '';

        template += `<div class="summary">
        <div>
            <span>Total products:</span>
            <strong>${data.products_count}</strong>
        </div>

        <div>
            <span>Date range: </span>
            <strong>${data.date}</strong>
        </div>
    
        <div>
            <span>Due amount: </span>
            <strong class="text-danger"> - ${data.total_sales} <i class="fa fa-eur text-dark" aria-hidden="true"></i> </strong>
        </div>`;

        template+=`<table class="table table-sm table-bordered table-hover col-6">
        <thead>
            <tr>
                <th>Product</th>
                <th>Single price</th>
                <th>Total price</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>`;

        data.products.forEach(product => {
            template += `
                <tr>
                    <td>${product.name}</td>
                    <td>${product.price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                    <td>${product.total_price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                    <td>${product.quantity}</td>
                </tr>`;
        })

        template += `</tbody></table>`;

        template += `</div>`;

        // Append the template to a container element
        document.getElementById('summary-container').innerHTML = template;
    };
});