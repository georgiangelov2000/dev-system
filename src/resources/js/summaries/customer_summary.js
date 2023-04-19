import { APIPOSTCALLER } from './ajaxFunctions.js';

$(function() {
    $('.selectCustomer').selectpicker('refresh').val('');

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
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
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
        let customer = bootstrapSelectCustomer.selectpicker('val');
        let date = dateRangePicker.val();

        APIPOSTCALLER(SUMMARY, { "customer": customer, 'date': date }, function (response) {
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
                <span>Total orders:</span>
                <strong>${data.orders_count}</strong>
            </div>

            <div>
                <span>Date range: </span>
                <strong>${data.date}</strong>
            </div>
        
            <div>
                <span>Total sales: </span>
                <strong class="text-success"> + ${data.total_sales} <i class="fa fa-eur text-dark" aria-hidden="true"></i> </strong>
            </div>`;

        for (let status in data.products) {
            template += `
                            <h6 title="Status" class="mb-0 mt-2"><strong class="badge bg-primary">${data.products[status].status_name}</strong></h6>

                            <div>
                                <span>Orders count: </span>
                                <strong> ${data.products[status].orders_count}</strong>
                            </div>

                            <div>
                                <span>Total sales: </span>
                                <strong class="text-success"> + ${data.products[status].sum} <i class="fa fa-eur text-dark" aria-hidden="true"></i></strong>
                            </div>

                            <table class="table table-sm table-bordered table-without-border table-hover col-6">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Single price</th>
                                <th>Total sold price</th>
                                <th>Sold quantity</th>
                                <th>Total mark up</th>
                                <th>Single mark up</th>
                            </tr>
                        </thead>
                        <tbody>`;

            data.products[status].products.forEach(product => {
                template += `
                <tr>
                    <td>${product.name}</td>
                    <td>${product.single_sold_price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                    <td>${product.total_sold_price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                    <td>${product.sold_quantity}</td>
                    <td class="text-success font-weight-bold"> + ${product.total_markup} <i class="fa fa-eur text-dark" aria-hidden="true"></i></td>
                    <td class="text-success font-weight-bold"> + ${product.single_markup} <i class="fa fa-eur text-dark" aria-hidden="true"></i></td>
                </tr>`;
            });

            template += `</tbody></table>`;
        }

        template += `</div>`;

        // Append the template to a container element
        document.getElementById('summary-container').innerHTML = template;
    }
});