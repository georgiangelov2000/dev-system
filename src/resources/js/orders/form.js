import { APICaller } from '../ajax/methods';

$(function () {
  $('.selectCustomer,.selectUser,.selectType,.productFilter').selectpicker();

  $('.datepicker').datepicker({
    format: 'mm/dd/yyyy'
  })

  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapProduct = $('.bootstrap-select .productFilter');
  let bootstrapSelectUser = $('.bootstrap-select .selectUser');

  let table = $('.productOrderTable');
  let submitBtn = $('#orderForm button[type="submit"]');
  let generateCodeBtn = $('#generateCode');

  let totalPriceProducts = 0;
  let availableQuantity = 0;
  let counter = 0;

  // Utility function to generate a random tracking number
  function generateRandomTrackingNumber(length) {
    let charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let randomString = '';
    for (let i = 0; i < length; i++) {
      let randomIndex = Math.floor(Math.random() * charset.length);
      randomString += charset[randomIndex];
    }
    return randomString;
  }

  // Event handler for generating tracking number
  generateCodeBtn.on('click', function () {
    let randomTrackingNumber = generateRandomTrackingNumber(20);
    $('input[name="tracking_number"]').val(randomTrackingNumber);
  });


  table.DataTable({
    ordering: false,
    columns: [
      { class: 'text-center', width: '1%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '1%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
      { class: 'text-center', width: '5%' },
    ],
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

    APICaller(PRODUCT_API_ROUTE,
      {
        'search': text,
        'out_of_stock': 0,
        'select_json': 1,
      }, function (response) {
        let purchases = response;

        if (purchases.length > 0) {
          bootstrapProduct.append('<option value="" style="display:none;"></option>');
          $.each(purchases, function ($key, purchase) {
            bootstrapProduct.append(`<option value="${purchase.id}"> ${purchase.name} </option>`)
          })
        }
        bootstrapProduct.selectpicker('refresh');
      }, function (error) {
        console.log(error);
      })
  })

  $('.selectUser input[type="text"]').on('keyup', function () {
    let text = $(this).val();
    bootstrapSelectUser.empty();

    if (text === '') {
      bootstrapSelectUser.selectpicker('refresh');
      return;
    }

    APICaller(USER_API_ROUTE, {
      'search': text,
      'role_id': 2,
      'no_datatable_draw':1,
    }, function (response) {
      let users = response;
      if (users.length > 0) {
        $.each(users, function ($key, user) {
          bootstrapSelectUser.append(`<option value="${user.id}"> ${user.username} </option>`)
        })
      }
      bootstrapSelectUser.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  })

  bootstrapProduct.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

    let id = $(this).val();
    bootstrapProduct.empty().selectpicker('refresh');

    APICaller(PRODUCT_API_ROUTE, {
      'id': id,
      'select_json': 1,
    }, function (response) {
      let purchase = response[0];

      let id = purchase.id;
      let totalPrice = purchase.total_price;
      let quantity = purchase.quantity;

      let row = table.find(`tr[data-id="${id}"]`);

      if (!row.length) {
        totalPriceProducts += parseFloat(totalPrice);
        availableQuantity += parseInt(quantity);

        counter++;
        renderData(purchase);
      }

      // Destroy the Bootstrap Select to remove its functionality
      $(this).empty()
      $(this).selectpicker('refresh');

    })
  });

  function calculateOrderPrice(row) {
    const quantity = parseInt(row.find('input[data-manipulation-name="sold_quantity"]').val()) || 0;
    const price = parseFloat(row.find('input[data-manipulation-name="single_sold_price"]').val()) || 0;
    const discount = parseFloat(row.find('input[data-manipulation-name="discount_percent"]').val()) || 0;

    let discountPrice = 0;

    let totalPrice = (quantity * price);

    if (!isNaN(discount)) {
      discountPrice = totalPrice - (totalPrice * discount) / 100;
    }

    discountPrice = parseFloat(discountPrice.toFixed(2));
    totalPrice = parseFloat(totalPrice.toFixed(2));

    let formattedDiscountPrice = discountPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    let formattedTotalPrice = totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });


    row.find('span[name="original_price"]').html(formattedDiscountPrice);
    row.find('span[name="regular_price"]').html(formattedTotalPrice);
  }

  window.removeRow = function (button) {
    let tr = $(button).closest('tr');

    let rowTotalPrice = tr.find('.totalPrice').text();
    let rowQuantity = tr.find('.purchaseQuantity').text();

    totalPriceProducts -= parseFloat(rowTotalPrice);
    availableQuantity -= parseInt(rowQuantity);
    table.DataTable().row(tr).remove().draw();
  }

  window.handleSinglePrice = function (e) {
    const row = $(e).closest('tr');
    calculateOrderPrice(row);
  };

  window.handleOrderQuantity = function (e) {
    const row = $(e).closest('tr');
    calculateOrderPrice(row);
  };

  window.handleDiscountChange = function (e) {
    const row = $(e).closest('tr');
    calculateOrderPrice(row);
  };

  function renderData(data) {
    let images = '';

    if (data.images.length > 0) {
      const imageTags = data.images.map((element) => {
        return `<img src="${element.path}/${element.name}" />`;
      });
      images = imageTags.join('');
    }

    let subcategoryNames = data.subcategories.length > 0
      ? data.subcategories.map(subcategory => subcategory.name).join(', ')
      : '';

    let categoryNames = data.categories.length > 0
      ? data.categories.map(category => category.name).join(', ')
      : '';

    let brandNames = data.brands.length > 0
      ? data.brands.map(brand => brand.name).join(', ')
      : '';


    let template = `
          <tr data-id="${data.id}">
              <input type="hidden" value='${data.id}' name="purchase_id[]" />
            <td>
              <button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
                <i class="fa-light fa-trash text-danger"></i>
              </button>
            </td>
            <td>${images}</td>
            <td>${data.name}</td>
            <td>€${data.price}</td>
            <td>${data.quantity}</td>
            <td>${categoryNames}</td>
            <td>${subcategoryNames}</td>
            <td>${brandNames}</td>
            <td>
              <div class="form-group col-12">
                <input 
                  name="sold_quantity[]" 
                  type='number'
                  data-manipulation-name="sold_quantity"
                  max='${data.quantity}'
                  min="1" 
                  class='form-control form-control-sm' 
                  value="0" 
                  onkeyup="handleOrderQuantity(this)" 
                />
                <span name="sold_quantity.${counter - 1}" class="text-danger"></span>
              </div>
            </td>
            <td>
              <div class="form-group col-12">
                <input 
                  type='text'
                  name="single_sold_price[]"
                  data-manipulation-name="single_sold_price"
                  class='form-control form-control-sm' 
                  value="0"
                  min="0"
                  onkeyup="handleSinglePrice(this)" 
                />
                <span name="single_sold_price.${counter - 1}" class="text-danger"></span>
              </div>
            </td>
            <td> 
              <div class="form-group col-12">
                <input 
                  type='text' 
                  value="0"
                  min="0"
                  class='form-control form-control-sm' 
                  data-manipulation-name="discount_percent"
                  name="discount_percent[]"
                  onkeyup="handleDiscountChange(this)" 
                />
                <span name="discount_percent.${counter - 1}" class="text-danger"></span>
              </div>
            </td>
            <td>
              <span name="original_price">0.00</span>
            </td>
            <td>
              <span name="regular_price">0.00</span>
            </td>
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
    console.log(formData);
    $.ajax({
      type: 'POST',
      url: url,
      data: formData,
      success: function (response) {
        console.log(response);
        toastr['success'](response.message);
      },
      error: function (xhr, status, error) {
        console.log(error);
        if (xhr.status === 422) {
          toastr['error'](xhr.responseJSON.message);
          var errors = xhr.responseJSON.errors;
          $.each(errors, function (field, fieldErrors) {
            console.log(field);
            var errorSpan = $('span[name="' + field + '"]');
            console.log(errorSpan);
            errorSpan.text(fieldErrors[0]);
          });
        }
      },
    });
  })

})