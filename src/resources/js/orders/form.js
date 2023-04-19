import { APICaller } from './ajaxFunctions';

$(document).ready(function () {
  $('.selectCustomer').selectpicker();
  $('.selectType').selectpicker();
  $('.productFilter').selectpicker();

  let totalPriceAllProducts = 0;
  let availableQuantity = 0;

  let TFOOTquantity = $('.TFOOTquantity');
  let TFOOTtotalPrice = $('.TFOOTtotalPrice');


  if (typeof DATE_OF_SALE !== 'undefined' && DATE_OF_SALE) {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date(DATE_OF_SALE));
  } else {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());
  }


  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapProduct = $('.bootstrap-select .productFilter');
  let table = $('.productOrderTable');

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

    APICaller(PRODUCT_API_ROUTE, { 'search': text }, function (response) {
      let products = response.data;

      if (products.length > 0) {
        bootstrapProduct.append('<option value=""></option>')
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

  bootstrapProduct.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let productId = $(this).val();
    let selectedOption = $(this).find('option').eq(clickedIndex);
    let { name, quantity, singlePrice, totalPrice } = selectedOption.data();

    let existingRow = table.find(`tr[data-id="${productId}"]`);
    if (existingRow.length) {
      return false;
    } else {
      totalPriceAllProducts += parseFloat(totalPrice);
      availableQuantity += parseInt(quantity);
      TFOOTtotalPrice.html(totalPriceAllProducts.toFixed(2));
      TFOOTquantity.html(availableQuantity);
      renderData(productId, name, quantity, singlePrice, totalPrice);
    }

  });

  window.removeRow = function (button) {
    let tr = $(button).closest('tr');

    let rowTotalPrice = tr.find('.totalPrice').text()
    let rowQuantity = tr.find('.purchaseQuantity').text();

    totalPriceAllProducts = totalPriceAllProducts - parseFloat(rowTotalPrice).toFixed(2)
    availableQuantity = availableQuantity - parseInt(rowQuantity);

    TFOOTtotalPrice.html(totalPriceAllProducts.toFixed(2));
    TFOOTquantity.html(availableQuantity);

    tr.remove();
  }

  function updateTotalOrderPrice(row) {
    const orderQuantity = parseFloat(row.find('.orderQuantity').val()) || 0;
    const orderPrice = parseFloat(row.find('.orderSinglePrice').val()) || 0;
    const purchaseTotal = parseFloat(row.find('.totalPrice').text()).toFixed(2);
    const orderTotal = (orderQuantity * orderPrice).toFixed(2);
    console.log(purchaseTotal);

    if (parseFloat(orderTotal) > parseFloat(purchaseTotal)) {
      row.find('.totalOrderPrice').removeClass('text-danger').addClass('text-success');
    } else if (parseFloat(orderTotal) < parseFloat(purchaseTotal)) {
      row.find('.totalOrderPrice').removeClass('text-success').addClass('text-danger');
    } else {
      row.find('.totalOrderPrice').removeClass('text-success text-danger');
    }

    row.find('.totalOrderPrice').html(orderTotal);
    row.find('input[name="total_sold_price[]"]').val(orderTotal);
  }

  window.handleSinglePrice = function (e) {
    const row = $(e).closest('tr');
    const targetPrice = parseFloat($(e).val()) || 0;

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

    if (isNaN(quantity) || isNaN(singlePrice)) {
      console.error('Invalid quantity or single price value');
    } else {
      if (discount === '') {
        discountPrice = quantity * singlePrice;
      } else {
        const orderPrice = quantity * singlePrice;
        discountPrice = orderPrice - (orderPrice * discount) / 100;
      }

      row.find('.totalOrderPrice').html(discountPrice.toFixed(2));
    }
  };

  function renderData(id, name, quantity, singlePrice, totalPrice) {
    if (table.find(`tr[data-id="${id}"]`).length) {
      return; // skip adding the row
    }
    const template = `
          <tr data-id="${id}">
              <input type="hidden" value='${id}' name="product_id[]" />
            <td>
              <button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
            <td>
              <input 
                type="text" 
                class="form-control form-control-sm" 
                name="invoice_number[]" 
              />
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
              </div>
            </td>
            <td>
              <input 
                type='hidden'
                name="total_sold_price[]" 
                value="0" 
              />
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
              </div>
            </td>
            <td class="purchaseQuantity">${quantity}</td>
            <td>${singlePrice}</td>
            <td class="totalPrice">${totalPrice}</td>
          </tr>
        `;

    table.find('tbody').append(template);
  }

})