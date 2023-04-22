import { APICaller } from './ajaxFunctions.js';

$(function () {
  $('.purchaseFilter, .selectCustomer, .packageType, .delieveryMethod').selectpicker();

  $('.datepicker').datepicker({
    format: 'mm/dd/yyyy'
  }).datepicker('setDate', new Date());

  let table = $('.productOrderTable').dataTable({
    ordering: false,
    columnDefs: [
      { width: "5%", targets: 0 },
      { width: "5%", targets: 1 },
      { width: "10%", targets: 2 },
      { width: "10%", targets: 3 },
      { width: "10%", targets: 4 },
      { width: "10%", targets: 5 },
      // add more targets and widths as needed
    ]
  });

  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapPurchase = $('.bootstrap-select .purchaseFilter');

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
    let selectedOption = $(this).find('option').eq(clickedIndex);
    let { name } = selectedOption.data();
    let customer = $(this).val();
    $('.customerName').html(name);

    bootstrapPurchase.empty();

    APICaller(ORDER_API_ROUTE, { 'customer': customer }, function (response) {
      let orders = response.data;
      if (orders.length > 0) {
        $.each(orders, function ($key, order) {
          bootstrapPurchase.append(`<option 
            value="${order.id}"
            data-id="${order.id}"
            data-name="${order.product.name}"
            data-single-sold-price="${order.single_sold_price}"
            data-total-sold-price="${order.total_sold_price}"
            data-quantity="${order.sold_quantity}"
            data-invoice="${order.invoice_number}"
            value="${order.id}"
          > 
          ${order.product.name} </option>`)
        })
      }
      bootstrapPurchase.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  });

  let sum = 0;
  let counter = 0;

  bootstrapPurchase.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let selectedOption = $(this).find('option').eq(clickedIndex);
    let { id, invoice, quantity, totalSoldPrice, singleSoldPrice, name } = selectedOption.data();

    let duplicate = false;
    
    table.DataTable().rows().data().each(function (row) {
      if (row[2] == invoice) {
        duplicate = true;
        return false; // break out of the loop
      }
    });

    // Create a new tbody with the data
    let tbody = $('<tbody>');

    if (!duplicate) {
      counter++;

      sum += parseFloat(totalSoldPrice);
      let formattedSum = formatSum(sum,counter);
      $('.packagePrice').html(formattedSum.sum);
      $('.ordersCount').html(formattedSum.orders_count);

      let newRow = $('<tr>').append(
        $('<td>').append($("<button onclick='removeRow(this)' class='btn'><i class='fa-solid fa-trash text-danger'></i></button>")),
        $('<td>').text(id).append($("<input>").attr("type", "hidden").attr('name','order_id[]').val(id)),
        $('<td>').text(invoice),
        $('<td>').text(name),
        $('<td>').text(singleSoldPrice),
        $('<td>').text(totalSoldPrice),
        $('<td>').text(quantity)
      );
      tbody.append(newRow);

      table.DataTable().row.add(newRow).draw();
    };

  });

  function formatSum(sum,ordersCount) {
    sum = parseFloat(sum.toFixed(2));
    return {
      "sum":sum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
      'orders_count':ordersCount
    } 
  }
  
  window.removeRow = function(button) {
    let row = $(button).closest('tr');
    let table = $('.productOrderTable').DataTable();
    table.row(row).remove().draw();
  
    // update sum and orders count
    let sum = calculateSum();
    let ordersCount = calculateOrdersCount();
    let formattedSum = formatSum(sum, ordersCount);
    $('.packagePrice').html(formattedSum.sum);
    $('.ordersCount').html(formattedSum.orders_count);
  };

  function calculateSum() {
    let sum = 0;
    table.DataTable().rows().data().each(function (row) {
      sum += parseFloat(row[5]);
    });
    return sum;
  }

  function calculateOrdersCount() {
    let ordersCount = 0;
    table.DataTable().rows().data().each(function (row) {
      ordersCount++;
    });
    return ordersCount;
  }

})