import { APIPOSTCALLER } from './ajaxFunctions.js';

$(function () {
    
    $('.selectCustomer').selectpicker('refresh').val('').trigger('change')

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
    let form = $('#filterForm');
    let modal = $('#transaction_modal');

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
            </div>`;

        if (data.date) {
            template += `
                <div>
                    <span>Date range: </span>
                    <strong>${data.date}</strong>
                </div>`
        }

        template += `
            <div>
                <span>Total sales: </span>
                <strong class="text-success"> + ${data.total_sales} <i class="fa fa-eur text-dark" aria-hidden="true"></i> </strong>
            </div>`

        template+=`<div>`

        for(let key in data.products){
            template+=`
                <h6 title="Package" class="mb-0 mt-2">
                    <strong class="badge bg-primary">
                        ${key}
                    </strong>
                </h6>
                <div>
                    <span>Orders count:</span>
                    <strong> ${data.products[key].orders_count}</strong>
                </div>
                <div>
                    <span>Total sales: </span>
                    <strong class="text-success"> + ${data.products[key].sum} <i class="fa fa-eur text-dark" aria-hidden="true"></i></strong>
                </div>`;
            
                if (data.products[key].status) {
                    let keys = Object.keys(data.products[key].status);
                    keys.forEach(status => {
                      let products = data.products[key].status[status].products;
                      let sum = data.products[key].status[status].sum;
                      let orders_counts = data.products[key].status[status].orders_count;

                      if (products.length > 0) {
                        template += `
                          <div>
                            <h6 title="Status">
                              <strong class="badge bg-secondary">
                                ${status} (${products.length})
                              </strong>
                            </h6>
                            <div>
                                <span>Orders count:</span>
                                <strong> ${orders_counts}</strong>
                            </div>
                            <div>
                                <span>Total sales:</span>
                                <strong class="text-success"> ${sum}  <i class="fa fa-eur text-dark" aria-hidden="true"></i></strong>
                            </div>
                            <table class="table table-sm table-bordered table-without-border table-hover col-6">
                              <thead>
                                <tr>
                                  <th>Name</th>
                                  <th>Single price</th>
                                  <th>Total sold price</th>
                                  <th>Sold quantity</th>
                                  <th>Total mark up</th>
                                  <th>Single mark up</th>
                                  <th>Actions</th>
                                </tr>
                              </thead>
                              <tbody>`;
                        products.forEach(product => {
                          template += `
                            <tr data_id='${product.id}' data_price ='${product.total_sold_price}' >
                              <td>${product.name}</td>
                              <td>${product.single_sold_price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                              <td>${product.total_sold_price} <i class="fa fa-eur" aria-hidden="true"></i></td>
                              <td>${product.sold_quantity}</td>
                              <td class="text-success font-weight-bold"> + ${product.total_markup} <i class="fa fa-eur text-dark" aria-hidden="true"></i></td>
                              <td class="text-success font-weight-bold"> + ${product.single_markup} <i class="fa fa-eur text-dark" aria-hidden="true"></i></td>
                              <td>
                                <a title='Preview' href="${PREVIEW_ROUTE.replace(':id', product.main_product_id)}" class='btn p-0'><i class='fa fa-eye text-info' aria-hidden='true'></i></a>
                                <a onclick="payment(this)" class='btn p-0' title="Payment"><i class="fa-thin fa-money-check-dollar-pen"></i></a>
                              </td>
                            </tr>`;
                        });
                        template += `
                              </tbody>
                            </table>
                          </div>`;
                      }
                    });
                  }       
        }
        template+='</div>';
        template += `</div>`;

        // Append the template to a container element
        document.getElementById('summary-container').innerHTML = template;
    }

    window.payment = function(e) {
        modal.modal('show');
        let price = $(e).closest('tr').attr('data_price');
        modal.find('input[id="price"]').val(price);
    }

    $('.modalCloseBtn').on('click', function () {
        modal.modal('hide');
    });

});