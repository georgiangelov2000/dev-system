import { APICaller } from "../ajax/methods";
import { numericFormat } from "../helpers/functions";
import { statusPaymentsWithIcons } from "../helpers/statuses";

$(function () {
    $(
        ".purchaseFilter, .selectCustomer, .packageType, .deliveryMethod, .selectSupplier"
    ).selectpicker();

    let bootstrapCustomer = $(".bootstrap-select .selectCustomer");
    let bootstrapOrder = $(".bootstrap-select .purchaseFilter");
    let generateCodeBtn = $("#generateCode");

    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
    });

    let table = $(".productOrderTable");

    let dataTable = table.DataTable({
        ordering: false,
        columnDefs: [
            { width: "1%", targets: 0, class: "text-center" },
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
        ],
    });

    if (typeof PACKAGE_ID !== "undefined") {
        APICaller(
            ORDER_API_ROUTE,
            {
                select_json: 1,
                collection: 1,
                package: PACKAGE_ID,
            },
            function (response) {
                let orders = response.orders;
                let ordersLength = orders.length;
                if (ordersLength) {
                    $.each(orders, function ($key, order) {
                        renderData(order);
                    });
                }
                overview();
            },
            function (error) {
                console.log(error);
            }
        );
    }

    // Utility function to generate a random tracking number
    function generateRandomTrackingNumber(length) {
        let charset =
            "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let randomString = "";
        for (let i = 0; i < length; i++) {
            let randomIndex = Math.floor(Math.random() * charset.length);
            randomString += charset[randomIndex];
        }
        return randomString;
    }

    // Event handler for generating tracking number
    generateCodeBtn.on("click", function () {
        let randomTrackingNumber = generateRandomTrackingNumber(20);
        $('input[name="tracking_number"]').val(randomTrackingNumber);
    });

    // Action filters
    $('.selectCustomer input[type="text"]').on("keyup", function () {
        let text = $(this).val();
        bootstrapCustomer.empty();

        if (text === "") {
            bootstrapCustomer.selectpicker("refresh");
            return;
        }

        APICaller(
            CUSTOMER_API_ROUTE,
            { search: text },
            function (response) {
                let customers = response.data;

                if (customers.length > 0) {
                    bootstrapCustomer.append(
                        '<option value="" style="display:none;"></option>'
                    );
                    $.each(customers, function ($key, customer) {
                        bootstrapCustomer.append(`
          <option 
            value="${customer.id}"
            data-name="${customer.name}"
          > 
            ${customer.name}
          </option>`);
                    });
                }
                bootstrapCustomer.selectpicker("refresh");
            },
            function (error) {
                console.log(error);
            }
        );
    });

    // Select picker handlers
    bootstrapCustomer.on(
        "changed.bs.select",
        function (e, clickedIndex, isSelected, previousValue) {
            bootstrapOrder.empty();

            APICaller(
                ORDER_API_ROUTE,
                {
                    customer: bootstrapCustomer.val(),
                    select_json: 1,
                    without_package: 1,
                    status: [2],
                    collection: 1,
                },
                function (response) {
                    let orders = response.orders;
                    let ordersLength = orders.length;

                    if (!ordersLength) {
                        toastr["error"](
                            "No orders found. Please try again later."
                        );
                    } else {
                        toastr["success"](
                            `${ordersLength} ${
                                ordersLength > 1 ? "orders" : "order"
                            } was fetched.`
                        );
                        bootstrapOrder.append(
                            '<option value="" style="display:none;"></option>'
                        );
                        $.each(orders, function ($key, order) {
                            bootstrapOrder.append(
                                `<option value="${order.id}"> ${order.purchase.name} - ${order.tracking_number} </option>`
                            );
                        });
                    }

                    bootstrapOrder.selectpicker("refresh");
                },
                function (error) {
                    console.log(error);
                }
            );
        }
    );

    bootstrapOrder.on(
        "changed.bs.select",
        function (e, clickedIndex, isSelected, previousValue) {
            let id = $(this).val();
            bootstrapOrder.selectpicker("refresh");

            APICaller(
                ORDER_API_ROUTE,
                { id: id, select_json: 1, collection: 0 },
                function (response) {
                    let order = response.order;
                    let row = table.find(`tr[data-id="${order.id}"]`);

                    if (row.length) {
                        toastr["error"](
                            "This order is already in the table. Please select a different order."
                        );
                        return false;
                    }

                    renderData(order);

                    overview();
                }
            );
        }
    );

    function renderData(data) {
        const statusInfo = statusPaymentsWithIcons[
            data.payment.payment_status
        ] || { text: "Unknown", iconClass: "fal fa-question" };

        let deleteBTN = '';

        if(!data.is_it_delivered) {
            deleteBTN = 
            `<button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
                <i class="fa-light fa-trash text-danger"></i>
            </button>`
        } 

        let template = `
      <tr data-id="${data.id}">
        <td>
          ${deleteBTN}
        </td>
        <td>
          ${data.id}
          <input type="hidden" value='${data.id}' name="order_id[]" />
        </td>
        <td>${data.tracking_number}</td>
        <td>
          <a href='${PURCHASE_EDIT_ROUTE.replace(":id", data.purchase.id)}'>${
            data.purchase.name
        }</a>
        </td>
        <td>${numericFormat(data.discount_single_sold_price)}</td>
        <td>${numericFormat(data.single_sold_price)}</td>
        <td>
          <input 
            type="hidden" 
            name="total_sold_price" 
            value="${data.total_sold_price}" 
          />
          ${numericFormat(data.total_sold_price)}
        </td>
        <td>${numericFormat(data.original_sold_price)}</td>
        <td>${data.discount_percent}</td>
        <td>${data.sold_quantity}</td>
        <td><i title="${statusInfo.label}" class="${
            statusInfo.iconClass
        }"></i></td>
      </tr>
    `;

        table.DataTable().row.add($(template)).draw();
    }

    function overview() {
        let tbody = table.find("tbody tr[data-id]");
        let totalSum = 0;

        tbody.each(function () {
            let row = $(this);
            let totalSoldPriceCell = row
                .find('input[name="total_sold_price"]')
                .val();

            // Parse the value as a floating-point number
            let priceValue = parseFloat(totalSoldPriceCell);

            // Check if the value is a valid number and then accumulate the total
            if (!isNaN(priceValue)) {
                totalSum += priceValue;
            }
        });

        $(".ordersCount").html(tbody.length);
        $(".packagePrice").html(
            totalSum.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    }

    overview();

    window.removeRow = function (button) {
        let row = $(button).closest("tr");
        dataTable.row(row).remove().draw();
        overview();
    };
});
