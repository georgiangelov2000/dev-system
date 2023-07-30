import { APICaller } from '../ajax/methods';

$(function () {
  $('.purchaseFilter, .selectCustomer, .packageType, .deliveryMethod, .selectSupplier').selectpicker();

  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapOrder = $('.bootstrap-select .purchaseFilter');

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });

  let table = $('.productOrderTable');

  let dataTable = table.DataTable({
    ordering: false,
    columnDefs: [
      { width: "1%", targets: 0, class:'text-center' },
      { width: "5%", targets: 1, class: "text-center" },
      { width: "5%", targets: 2, class: "text-center" },
      { width: "5%", targets: 3, class: "text-center" },
      { width: "5%", targets: 4, class: "text-center" },
      { width: "5%", targets: 5, class: "text-center" },
      { width: "5%", targets: 6, class: "text-center" },
      { width: "5%", targets: 7, class: "text-center" },
      { width: "5%", targets: 8, class: "text-center" },
      { width: "5%", targets: 9, class: "text-center" },
      { width: "5%", targets: 10, class: "text-center" },
    ]
  });

  // Action filters
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
        bootstrapCustomer.append('<option value="" style="display:none;"></option>');
        $.each(customers, function ($key, customer) {
          bootstrapCustomer.append(`
          <option 
            value="${customer.id}"
            data-name="${customer.name}"
          > 
            ${customer.name} 
          </option>`)
        })
      }
      bootstrapCustomer.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })

  })

  bootstrapCustomer.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    bootstrapOrder.empty();

    APICaller(ORDER_API_ROUTE, {
      'customer': bootstrapCustomer.val(),
      'select_json': 1,
      'without_package': 1,
      'is_paid': 0,
      'status': [6]
    }, function (response) {
      let orders = response;
      let ordersLength = orders.length;

      if(!ordersLength) {
        toastr['error']('No orders found. Please try again later.');
      } else {
        toastr['success'](`${ordersLength} ${ordersLength > 1 ? "orders" : 'order'} was fetched.`);
        bootstrapOrder.append('<option value="" style="display:none;"></option>');
        $.each(orders, function ($key, order) {
          bootstrapOrder.append(`<option value="${order.id}"> ${order.purchase.name} </option>`);
        });
      }

      bootstrapOrder.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  });

  function overview() {
    let tbody = table.find('tbody tr[data-id]');
    let totalSum = 0;
    
    tbody.each(function () {
      let row = $(this);
      let totalSoldPriceCell = row.find('td[data-price]').attr('data-price');
    
      // Parse the value as a floating-point number
      let priceValue = parseFloat(totalSoldPriceCell);
    
      // Check if the value is a valid number and then accumulate the total
      if (!isNaN(priceValue)) {
        totalSum += priceValue;
      }
    });
        
    $('.ordersCount').html(tbody.length);
    $('.packagePrice').html(totalSum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
  }

  overview();

  window.removeRow = function (button) {
    let row = $(button).closest('tr');
    table.row(row).remove().draw();
    overview();
  };

  bootstrapOrder.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let id = $(this).val();
    bootstrapOrder.empty().selectpicker('refresh');

    APICaller(ORDER_API_ROUTE, {
      "id": id,
      'select_json': 1,
    }, function (response) {
      let order = response[0];

      let id = order.id;

      let row = table.find(`tr[data-id="${id}"]`);

      if (row.length) {
        toastr['error']('This order is already in the table. Please select a different order.');
        return false;
      }

      renderData(order);

      overview();
    })

    function renderData(data) {
      console.log(data);

      let images = '';

      if (data.purchase.images.length > 0) {
        let imageTags = data.purchase.images.map((element) => {
          return `<img src="${element.path}/${element.name}" />`;
        });
        images = imageTags.join('');
      }

        let template = `
        <tr data-id="${data.id}">
            <input type="hidden" value='${data.id}' name="order_id[]" />
          <td>
            <button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
              <i class="fa-light fa-trash text-danger"></i>
            </button>
          </td>
          <td>${images}</td>
          <td>${data.customer.name}</td>
          <td>${data.purchase.name}</td>
          <td>${data.sold_quantity}</td>
          <td>€${data.single_sold_price}</td>
          <td>€${data.discount_single_sold_price}</td>
          <td data-price="${data.total_sold_price}">€${data.total_sold_price}</td>
          <td>€${data.original_sold_price}</td>
          <td>${data.discount_percent}</td>
          <td>${data.date_of_sale}</td>
        </tr>
      `;

      table.DataTable().row.add($(template)).draw();

    }

  });

})