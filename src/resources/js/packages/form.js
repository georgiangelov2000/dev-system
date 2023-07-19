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
      { width: "5%", targets: 1, class:"text-center" },
      { width: "5%", targets: 2, class:"text-center"},
      { width: "5%", targets: 3, class:"text-center" },
      { width: "5%", targets: 4, class:"text-center" },
      { width: "5%", targets: 5, class:"text-center" },
      { width: "5%", targets: 6, class:"text-center" },
      { width: "5%", targets: 7, class:"text-center" },
      { width: "5%", targets: 8, class:"text-center" },
      // add more targets and widths as needed
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
      'select_json':true, 
      'withoutPackage':true,
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
            value="${order.id}"
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
      quantity, 
      totalSoldPrice, 
      singleSoldPrice, 
      tracking_number,
      date_of_sale,
      name 
    } = selectedOption.data();

    let duplicate = false;

    table.rows().data().each(function (row) {
      if (row[1] == id) {
        duplicate = true;
        toastr['error']('You have already the current product in the table');
        return false; // break out of the loop
      }
    }); 

    // Create a new tbody with the data
    let tbody = $('<tbody>');

    if (!duplicate) {

      let newRow = $('<tr name="package">').append(
        $('<td>').append($("<button type='button' onclick='removeRow(this)' class='btn p-0'><i class='fa-light fa-trash text-danger'></i></button>")),
        $('<td>').text(id).append($("<input>").attr("type", "hidden").attr('name', 'order_id[]').val(id)),
        $('<td>').text(tracking_number),
        $('<td>').text(name),
        $('<td>').text(date_of_sale),
        $('<td name="single-sold-price">').text('€' +  singleSoldPrice),
        $('<td name="total-sold-price">').text('€' + totalSoldPrice).append($("<input>").attr("type", "hidden").attr('name', 'total_order_price[]').val(totalSoldPrice)),
        $('<td>').text(quantity)
      );
      tbody.append(newRow);

      table.row.add(newRow).draw();

      overview();
    };

  });

})