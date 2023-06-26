import {
    APICaller
} from '../ajax/methods';

import {
    handleErrors
} from '../helpers/action_helpers';

$(function () {
    $('select[name="customer_id"],select[name="order_id"]')
        .selectpicker('refresh')
        .val('')
        .trigger('change');

    const bootstrapSelectCustomer = $('.bootstrap-select select[name="customer_id"]');
    const bootstrapSelectOrder = $('.bootstrap-select select[name="order_id"]');
    const dateRange = $('input[name="date_of_sale"]');
    const searchBtn = $('button.filter');

    dateRange.daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.selectCustomer input[type="text"]').on('keyup', function () {
        const text = $(this).val();
        bootstrapSelectCustomer.empty();

        if (text === '') {
            bootstrapSelectCustomer.selectpicker('refresh');
            return;
        }

        APICaller(CUSTOMER_API_ROUTE, {
            'search': text
        }, function (response) {
            const customers = response.data;
            if (customers.length > 0) {
                bootstrapSelectCustomer.append('<option> </option>')
                $.each(customers, function ($key, customer) {
                    bootstrapSelectCustomer.append(`<option value="${customer.id}"> ${customer.name} </option>`)
                })
            }
            bootstrapSelectCustomer.selectpicker('refresh');
        }, function (error) {
            toastr['error'](error.message);
        })
    })

    bootstrapSelectCustomer.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        const customer = $(this).val();
        const date = dateRange.val();

        APICaller(SEARCH_ORDER, {
            'customer': customer,
            'is_paid': 0,
            'date_range': date,
            'select_json': true,
            'withoutPackage': true
        }, function (response) {
            const orders = response;
            if (orders.length > 0) {
                bootstrapSelectOrder.append('<option>Please select</option>')
                $.each(orders, function ($key, order) {
                    bootstrapSelectOrder.append(`<option value="${order.id}"> 
                        ${order.invoice_number} - ${order.product.name}
                    </option>`)
                })
            }
            bootstrapSelectOrder.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    })

    searchBtn.on('click', function (e) {
        e.preventDefault();
        const order = bootstrapSelectOrder.selectpicker('val');

        APICaller(SEARCH_ORDER, {
            'order_id': order,
            'select_json': true,
        }, function (response) {
            const order = response[0];
            getOrderOverview(order);
        }, function (error) {
            console.log(error);
        })
    })

    function getOrderOverview(order) {
        let template = '';
        if (order) {
            template = `
            <div class="col-12">
                <p class="lead">
                    <strong>Order: </strong>
                    ${order.product.name}
                </p>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:50%">Invoice number:</th>
                                <td>${order.invoice_number}</td>
                            </tr>
                            <tr>
                                <th>Total price:</th>
                                <td class="d-flex justify-content-between">$${order.total_sold_price} <a type="button" onclick="setPaymentInput(${order.total_sold_price},'price')" class="text-primary"><i class="fa-light fa-copy"></i></a></td>
                            </tr>
                            <tr>
                                <th>Quantity:</th>
                                <td class="d-flex justify-content-between">${order.sold_quantity} <a type="button" onclick="setPaymentInput(${order.sold_quantity},'quantity')" class="text-primary"><i class="fa-light fa-copy"></i></a></td>
                            </tr>
                            <tr>
                                <th>Discount:</th>
                                <td class="d-flex justify-content-between">${order.discount_percent}%</td>
                            </tr>
                            <tr>
                                <th>Single price:</th>
                                <td>$${order.single_sold_price}</td>
                            </tr>
                            <tr>
                                <th>Original price (without discount):</th>
                                <td>$${order.original_sold_price}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>${order.status}</td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>${moment(order.created_at).format('YYYY-MM-DD')}</td>
                            </tr>
                            <tr>
                                <th>Sale date:</th>
                                <td>${moment(order.date_of_sale).format('YYYY-MM-DD')}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="paymentTemplateForm" class="col-12">
                            <form method="POST" onsubmit="makePayment(event)" action="${MAKE_PAYMENT.replace(':id',order.id)}">
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label for="price">Price</label>
                                        <input id="price" type="text" name="price" class="form-control">
                                        <span class="text-danger" name="price"> </span>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="price">Quantity</label>
                                        <input type="number" name="quantity" class="form-control">
                                        <span class="text-danger" name="quantity"> </span>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="date_of_payment">Payment date</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" id="date_of_payment" class="form-control datepicker" name="date_of_payment">
                                        </div>
                                        <span class="text-danger" name="date_of_payment"> </span>
                                    </div>
                                    <div class="col-3 mt-3">
                                        <button class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
            `
        }
        $('#orderOverview').removeClass('d-none').html(template);
        // $('#orderOverview').find('.order-overview-table').DataTable();

        $('input[name="date_of_payment"]').datepicker({
            format: 'mm/dd/yyyy',
            onSelect: function(dateText, inst) {
              $(this).val(dateText);
            },
            onClose: function(dateText, inst) {
              if (dateText === '') {
                $(this).val('');
              }
            }
          }).datepicker('setDate', new Date());  
    }

    window.makePayment = function(e){
        e.preventDefault();
        let action = e.target.getAttribute('action');
        let method = e.target.getAttribute('method');
        let form = e.target;

        $.ajax({
            url: action,
            method: method,
            data:$(form).serialize(),
            success: function(response){
                toastr['success'](response.message);

                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    toastr['error'](xhr.responseJSON.message);
                    var errors = xhr.responseJSON.errors;
                    handleErrors(errors);
                }
            }
        })
    }

    window.setPaymentInput = function(val,inputName){
        
        let input = $('input[name = "'+inputName+'"]');

        if(input.attr('type') == 'text') {
            input.val(parseFloat(val).toFixed(2));
        } else {
            input.val(parseInt(val));
        }

    }
})