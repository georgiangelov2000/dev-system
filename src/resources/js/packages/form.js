import { APICaller } from '../ajax/methods';

$(function () {
  $('.purchaseFilter, .selectCustomer, .packageType, .delieveryMethod').selectpicker();

  if (typeof PACKAGE_DATE !== 'undefined' && PACKAGE_DATE) {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date(PACKAGE_DATE));
  } else {
    $('.datepicker').datepicker({
      format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());
  }

  if (typeof CUSTOMER !== "undefined" && CUSTOMER !== null) {
    APICaller(ORDER_API_ROUTE, { 'customer': CUSTOMER }, function (response) {
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
  }

  var table = $('.productOrderTable').DataTable({
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

  function overview() {
    let total = 0;
    let packageRowsCount = table.rows().nodes().to$().filter('tr[name="package"]').length;

    table.cells('tbody tr[name="package"] td[name="total-sold-price"]').every(function () {
      total += parseFloat($(this.node()).text());
    });

    total = parseFloat(total.toFixed(2));

    $('.packagePrice').html(total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    $('.ordersCount').html(packageRowsCount);
  }

  overview();

  window.removeRow = function (button) {
    console.log('yes');
    let row = $(button).closest('tr');
    table.row(row).remove().draw();
    overview();
  };

  bootstrapPurchase.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let selectedOption = $(this).find('option').eq(clickedIndex);
    let { id, invoice, quantity, totalSoldPrice, singleSoldPrice, name } = selectedOption.data();

    let duplicate = false;

    table.rows().data().each(function (row) {
      if (row[2] == invoice) {
        duplicate = true;
        return false; // break out of the loop
      }
    }); 

    // Create a new tbody with the data
    let tbody = $('<tbody>');

    if (!duplicate) {

      let newRow = $('<tr name="package">').append(
        $('<td>').append($("<button type='button' onclick='removeRow(this)' class='btn p-0'><i class='fa-light fa-trash text-danger'></i></button>")),
        $('<td>').text(id).append($("<input>").attr("type", "hidden").attr('name', 'order_id[]').val(id)),
        $('<td>').text(invoice),
        $('<td>').text(name),
        $('<td name="single-sold-price">').text(singleSoldPrice),
        $('<td name="total-sold-price">').text(totalSoldPrice).append($("<input>").attr("type", "hidden").attr('name', 'total_order_price[]').val(totalSoldPrice)),
        $('<td>').text(quantity)
      );
      tbody.append(newRow);

      table.row.add(newRow).draw();

      overview();

    };

  });

})