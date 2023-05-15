import { APIPOSTCALLER } from '../ajax/methods';

$(function () {
    $('.selectSupplier').selectpicker('refresh').val('').trigger('change');

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

         $('#loader').show();

        APIPOSTCALLER(SUMMARY, { "supplier": supplier, 'date': date }, function (response) {
            let respData = response;

            if (respData) {
                summaryTemplate(respData);
            }

            $('#loader').hide();
        }, function (error) {
            console.log(error);
        })
    })

    const summaryTemplate = function (data) {
        const summaryContainer = $('#summary-container');
        const tableClass = 'table-summary';

        let template = '';

        template += '<div class="summary">';

            template +=`
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
                    ${data.total_sales != 0  ?  '<strong class="text-danger">  - '+ data.total_sales +' </strong>' : '<strong>' + data.total_sales + ' </strong>'}
                </div>`;

                template+=`
                <div style="background-color:rgb(244, 246, 249);" class="p-4 rounded m-2">
                    <table class="${tableClass} table table-sm table-bordered table-without-border table-hover col-6">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Single price</th>
                                <th>Total price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${summaryTable(data.products)}
                        </tbody>
                    </table>
                </div>
            </div>`;

        summaryContainer.html(template); 
   
        const dataTables = document.getElementsByClassName(tableClass);

        for (let i = 0; i < dataTables.length; i++) {
            new DataTable(dataTables[i]);
        }

    }

    const summaryTable = function (products) {
        let tableHtml = '';

        products.forEach((product)=>{
            tableHtml += `
            <tr>
                <td>${product.name}</td>
                <td>${product.price} €</td>
                <td>${product.total_price} €</td>
                <td>${product.quantity}</td>
            </tr>`
        });

        return tableHtml;
    }
    
});