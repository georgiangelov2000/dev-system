import { APICaller } from '../ajax/methods';

$(function () {
  $('.purchaseFilter, .selectCustomer, .packageType, .deliveryMethod, .selectSupplier').selectpicker();

  let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
  let bootstrapOrders = $('.bootstrap-select .purchaseFilter');

  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });

  var table = $('.productOrderTable').DataTable({
    ordering: false,
    columnDefs: [
      { width: "1%", targets: 0 },
      { width: "5%", targets: 1, class: "text-center" },
      { width: "5%", targets: 2, class: "text-center" },
      { width: "5%", targets: 3, class: "text-center" },
      { width: "5%", targets: 4, class: "text-center" },
      { width: "5%", targets: 5, class: "text-center" },
      { width: "5%", targets: 6, class: "text-center" },
      { width: "5%", targets: 7, class: "text-center" },
      { width: "5%", targets: 8, class: "text-center" },
      { width: "5%", targets: 9, class: "text-center" }
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
    bootstrapOrders.empty();

    APICaller(ORDER_API_ROUTE, {
      'customer': bootstrapCustomer.val(),
      'select_json': true,
      'withoutPackage': true,
      'is_paid': 0
    }, function (response) {
      let orders = response;

      if (orders.length > 0) {
        bootstrapOrders.append('<option value="" style="display:none;"></option>');
        $.each(orders, function ($key, order) {
          bootstrapOrders.append(`<option 
            value="${order.id}"
            data-id="${order.id}"
            data-name="${order.purchase.name}"
            data-single-sold-price="${order.single_sold_price}"
            data-total-sold-price="${order.total_sold_price}"
            data-tracking_number = "${order.tracking_number}"
            data-quantity="${order.sold_quantity}"
            data-date_of_sale="${order.date_of_sale}"
            data-original_sold_price="${order.original_sold_price}"
            data-discount="${order.sold_quantity}"
          > 
          ${order.purchase.name} </option>`)
        })
      }
      bootstrapOrders.selectpicker('refresh');
    }, function (error) {
      console.log(error);
    })
  });

  function overview() {
    let total = 0;
    let packageRowsCount = table.rows().nodes().to$().filter('tr[name="package"]').length;

    table.cells('tbody tr[name="package"] td[name="total-sold-price"]').every(function () {
      let text = $(this.node()).text();
      let number = parseFloat(text.match(/\d+(\.\d+)?/)[0]);
      total += number;
    });

    console.log(total);

    total = parseFloat(total.toFixed(2));

    $('.packagePrice').html(total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

    $('.ordersCount').html(packageRowsCount);
  }

  overview();

  window.removeRow = function (button) {
    let row = $(button).closest('tr');
    table.row(row).remove().draw();
    overview();
  };

  bootstrapOrders.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    let selectedOption = $(this).find('option').eq(clickedIndex);

    let {
      id,
      name,
      singleSoldPrice,
      totalSoldPrice,
      tracking_number,
      quantity,
      date_of_sale,
      original_sold_price,
      discount
    } = selectedOption.data();

    let duplicate = false;

    table.rows().data().each(function (row) {
      const tempElement = $(`<div>${row[1]}</div>`);
      const rowProductId = parseInt(tempElement.find('input').val(), 10);

      if (rowProductId == id) {
        duplicate = true;
        toastr['error']('You have already the current order in the table');
        return false;
      }
    });

    // Create a new tbody with the data
    let tbody = $('<tbody>');

    if (!duplicate) {
      // Create elements
      const deleteButton = $("<button>", {
        type: 'button',
        class: 'btn p-0',
        onclick: 'removeRow(this)',
      }).append($('<i>', {
        class: 'fa-light fa-trash text-danger',
      }));

      const productIdInput = $("<input>", {
        type: 'hidden',
        name: 'order_id[]',
        value: id,
      });

      const productNameLink = $("<a>", {
        href: ORDER_EDIT_ROUTE.replace(':id', id),
        html: name,
      });

      const singleSoldPriceTd = $('<td>', {
        name: 'single-sold-price',
        text: `€${singleSoldPrice}`,
      });

      const totalSoldPriceInput = $("<input>", {
        type: 'hidden',
        name: 'total_order_price[]',
        value: totalSoldPrice,
      });

      const totalSoldPriceTd = $('<td>', {
        name: 'total-sold-price',
        text: `€${totalSoldPrice}`,
      }).append(totalSoldPriceInput);

      // Create the new row and append elements
      let newRow = $('<tr>', {
        name: 'package',
      }).append(
        $('<td>').append(deleteButton),
        $('<td>').text(id).append(productIdInput),
        $('<td>').text(tracking_number),
        $('<td>').append(productNameLink),
        $('<td>').text(date_of_sale),
        singleSoldPriceTd,
        totalSoldPriceTd,
        $('<td>').text(original_sold_price),
        $('<td>').text(`${discount}%`),
        $('<td>').text(`${quantity}`)
      );

      tbody.append(newRow);

      table.row.add(newRow).draw();

      overview();
    };


  });

})