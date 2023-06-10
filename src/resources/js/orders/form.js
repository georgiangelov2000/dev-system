import { APICaller } from '../ajax/methods';

$(function () {

  $('.selectCustomer').selectpicker();
  $('.selectType').selectpicker();
  $('.productFilter').selectpicker();

  if (typeof DATE_OF_SALE !== 'undefined' && DATE_OF_SALE) {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date(DATE_OF_SALE));
  } else {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());
  }

  let totalPriceAllProducts = 0;
  let availableQuantity = 0;

  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapProduct = $('.bootstrap-select .productFilter');
  let table = $('.productOrderTable');
  let submitBtn = $('#orderForm button[type="submit"]');
  console.log(submitBtn);

  table.DataTable({
    columns: [
      { width: '5%', orderable: false, orderData: false },
      { width: '6%', orderable: false, class: 'text-center' },
      { width: '5%', orderable: false, class: 'text-center' },
      { width: '5%', orderable: false },
      { width: '5%', orderable: false, class: 'text-center' },
      { width: '6%', orderable: false, class: 'text-center' },
      { width: '5%', orderable: false, class: 'text-center' },
      { width: '3%', orderable: false },
      { width: '2%', orderable: false },
      { width: '2%', orderable: false },
    ]
  });

  $('.selectCustomer input[type="text"]').on('keyup', function () {
    let text = $(this).val();
    bootstrapCustomer.empty();

    if (text === '') {
      bootstrapCustomer.selectpicker('refresh');
      return;
    }

    APICaller(CUSTOMER_API_ROUTE, { 'search': text }, function (response) {
      let customers = response.data;
      if (customers.length > 0) {
        $.each(customers, function ($key, customer) {
          bootstrapCustomer.append(`<option value="${customer.id}"> ${customer.name} </option>`)
        })
      }
      bootstrapCustomer.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  })

  $('.productFilter input[type="text"]').on('keyup', function () {
    let text = $(this).val();
    bootstrapProduct.empty();

    if (text === '') {
      bootstrapProduct.selectpicker('refresh');
      return;
    }

    APICaller(PRODUCT_API_ROUTE, { 'search': text, 'out_of_stock': true }, function (response) {
      let products = response.data;

      if (products.length > 0) {
        bootstrapProduct.append('<option value="" style="display:none;"></option>');
        $.each(products, function ($key, product) {
          bootstrapProduct.append(`<option
                     value="${product.id}"
                     data-name="${product.name}"
                     data-quantity="${product.quantity}"
                     data-single-price="${product.price}"
                     data-total-price="${product.total_price}"
                     > ${product.name} </option>`)
        })
      }
      bootstrapProduct.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  })

  $('#generateCode').on('click', function () {
    // Define the character set that you want to use
    let charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let length = 20;

    let randomString = '';
    for (let i = 0; i < length; i++) {
      let randomIndex = Math.floor(Math.random() * charset.length);
      randomString += charset[randomIndex];
    }

    $('input[name="tracking_number"]').val(randomString);

  });

  let counter = 0;
  bootstrapProduct.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let selectedOption = $(this).find('option').eq(clickedIndex);
    let { name, quantity, singlePrice, totalPrice } = selectedOption.data();

    let existingRow = table.find(`tr[data-id="${$(this).val()}"]`);
    if (!existingRow.length) {
      totalPriceAllProducts += parseFloat(totalPrice);
      availableQuantity += parseInt(quantity);
      counter++;
      renderData($(this).val(), name, quantity, singlePrice, totalPrice);
    }

    updateOrderSummary();
  });

  function updateOrderSummary() {
    // console.log(totalPriceAllProducts)
    $('#totalOrdersPrice').html(totalPriceAllProducts.toFixed(2));
    $('#totalOrdersQuantity').html(availableQuantity);
  }

  function updateTotalOrderPrice(row) {
    const orderQuantity = parseInt(row.find('.orderQuantity').val()) || 0;
    const orderPrice = parseFloat(row.find('.orderSinglePrice').val()) || 0;
    const orderTotal = (orderQuantity * orderPrice).toLocaleString('en-US', { minimumFractionDigits: 2 }).replace(",", ".");
    row.find('.totalOrderPrice').html(orderTotal);
  }

  window.removeRow = function (button) {
    let tr = $(button).closest('tr');

    let rowTotalPrice = tr.find('.totalPrice').text();
    let rowQuantity = tr.find('.purchaseQuantity').text();

    totalPriceAllProducts -= parseFloat(rowTotalPrice);
    availableQuantity -= parseInt(rowQuantity);
    table.DataTable().row(tr).remove().draw();
    updateOrderSummary();
  }

  window.handleSinglePrice = function (e) {
    const row = $(e).closest('tr');
    updateTotalOrderPrice(row);
  };

  window.handleOrderQuantity = function (e) {
    const row = $(e).closest('tr');
    updateTotalOrderPrice(row);
  };

  window.handleDiscountChange = function (e) {
    const row = $(e).closest('tr');
    const discount = parseInt($(e).val()) || 0;
    const quantity = parseInt(row.find('.orderQuantity').val()) || 0;
    const singlePrice = parseFloat(row.find('.orderSinglePrice').val()) || 0;

    let discountPrice = 0;

    if (isNaN(quantity) || isNaN(singlePrice) || isNaN(discount)) {
      console.error('Invalid quantity or single price value');
    } else {
      if (discount === '') {
        discountPrice = (quantity * singlePrice);
      } else {
        const orderPrice = (quantity * singlePrice);
        discountPrice = orderPrice - (orderPrice * discount) / 100;
      }
      row.find('.totalOrderPrice').html(discountPrice.toFixed(2));
    }
  };

  function renderData(id, name, quantity, singlePrice, totalPrice) {

    if (table.find(`tr[data-id="${id}"]`).length) {
      return;
    }

    const template = `
          <tr data-id="${id}">
              <input type="hidden" value='${id}' name="product_id[]" />
            <td>
              <button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
                <i class="fa-light fa-trash text-danger"></i>
              </button>
            </td>
            <td>
              <input 
                type="text" 
                class="form-control form-control-sm" 
                name="invoice_number[]" 
              />
              <span name="invoice_number.${counter -1}" class="text-danger"></span>
            </td>
            <td>${name}</td>
            <td>
              <div class="form-group col-12">
                <input 
                  name="sold_quantity[]" 
                  type='number' 
                  max='${quantity}' 
                  class='form-control form-control-sm orderQuantity' 
                  value="0" 
                  onkeyup="handleOrderQuantity(this)" 
                />
                <span class="sold_quantity.${counter -1}" class="text-danger"></span>
              </div>
            </td>
            <td>
              <div class="form-group col-12">
                <input 
                  type='text'
                  name="single_sold_price[]" 
                  class='form-control form-control-sm orderSinglePrice' 
                  value="0" 
                  onkeyup="handleSinglePrice(this)" 
                />
                <span name="single_sold_price.${counter -1}" class="text-danger"></span>
              </div>
            </td>
            <td>
              <span class="totalOrderPrice"> </span>
            </td>
            <td> 
              <div class="form-group col-12">
                <input 
                  type='text' 
                  value="0"
                  class='form-control form-control-sm' 
                  name="discount_percent[]"
                  onkeyup="handleDiscountChange(this)" 
                />
                <span name="discount_percent.${counter -1}" class="text-danger"></span>
              </div>
            </td>
            <td class="purchaseQuantity">${quantity}</td>
            <td>${singlePrice}</td>
            <td class="totalPrice">${totalPrice}</td>
          </tr>
        `;

    table.DataTable().row.add($(template)).draw();
  }

  //Send HTTP POST
  submitBtn.click(function (event) {
    event.preventDefault();
    let form = $('#orderForm');
    let url = form.attr('action');
    let formData = form.serialize();

    $.ajax({
      type: 'POST',
      url: url,
      data: formData,
      success: function (response) {
        toastr['success'](response.message);
      },
      error: function (xhr, status, error) {
        if (xhr.status === 422) {
          toastr['error'](xhr.responseJSON.message);
          var errors = xhr.responseJSON.errors;
          $.each(errors, function(field, fieldErrors) {
              var errorSpan = $('span[name="' + field + '"]');
              errorSpan.text(fieldErrors[0]);
          });
        }
      },
    });
  })


})