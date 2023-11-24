import { APICaller } from "../ajax/methods";
import { handleErrors } from "../helpers/action_helpers";
import { numericFormat } from "../helpers/functions";

$(function () {
    if (typeof ORDER !== "undefined") {
        APICaller(ORDER_API_ROUTE, { id: ORDER, select_json: 1 }, (response) =>
            renderData(response, true)
        );
    }

    $(
        ".selectCustomer,.selectUser,.selectType,.productFilter,.selectPackage"
    ).selectpicker();

    $(".datepicker").datepicker({
        format: "mm/dd/yyyy",
    });

    let bootstrapCustomer = $(".bootstrap-select .selectCustomer");
    let bootstrapProduct = $(".bootstrap-select .productFilter");
    let bootstrapSelectUser = $(".bootstrap-select .selectUser");
    let bootstrapSelectPackage = $(".bootstrap-select .selectPackage");
    let filterPurchaseStatuses = [1,4];

    let table = $(".productOrderTable");

    let totalPriceProducts = 0;
    let availableQuantity = 0;
    let counter = -1;

    const tableConfig = {};
    tableConfig["ordering"] = false;

    if (typeof IS_EDITABLE !== "undefined" && IS_EDITABLE == false) {
        tableConfig["columns"] = [
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
        ];
    } else {
        tableConfig["columns"] = [
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "4%" },
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "8%" },
            { class: "text-center", width: "8%" },
            { class: "text-center", width: "8%" },
            { class: "text-center", width: "8%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "8%" },
        ];
    }

    table.DataTable(tableConfig);

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
                        bootstrapCustomer.append(
                            `<option value="${customer.id}"> ${customer.name} </option>`
                        );
                    });
                }
                bootstrapCustomer.selectpicker("refresh");
            },
            function (error) {
                console.log(error);
            }
        );
    });

    $('.selectPackage input[type="text"]').on("keyup", function () {
        let text = $(this).val();
        bootstrapSelectPackage.empty();

        if (text === "") {
            bootstrapSelectPackage.selectpicker("refresh");
            return;
        }

        APICaller(
            PACKAGE_API_ROUTE,
            {
                select_json: 1,
                is_it_delivered: 0,
            },
            function (response) {
                let packages = response;

                if (packages.length > 0) {
                    bootstrapSelectPackage.append(
                        '<option value="" style="display:none;"></option>'
                    );
                    $.each(packages, function ($key, purchase) {
                        bootstrapSelectPackage.append(
                            `<option value="${purchase.id}"> ${purchase.package_name} </option>`
                        );
                    });
                }
                bootstrapSelectPackage.selectpicker("refresh");
            }
        );
    });

    $('.productFilter input[type="text"]').on("keyup", function () {
        let text = $(this).val();
        bootstrapProduct.empty();

        if (text === "") {
            bootstrapProduct.selectpicker("refresh");
            return;
        }

        APICaller(
            PRODUCT_API_ROUTE,
            {
                search: text,
                out_of_stock: 0,
                select_json: 1,
                status: filterPurchaseStatuses

            },
            function (response) {
                let purchases = response;

                if (purchases.length > 0) {
                    bootstrapProduct.append(
                        '<option value="" style="display:none;"></option>'
                    );
                    $.each(purchases, function ($key, purchase) {
                        bootstrapProduct.append(
                            `<option value="${purchase.id}"> ${purchase.name} - Remaining amount <b>${purchase.quantity}</b> </option>`
                        );
                    });
                }
                bootstrapProduct.selectpicker("refresh");
            },
            function (error) {
                console.log(error);
            }
        );
    });

    $('.selectUser input[type="text"]').on("keyup", function () {
        let text = $(this).val();
        bootstrapSelectUser.empty();

        if (text === "") {
            bootstrapSelectUser.selectpicker("refresh");
            return;
        }

        APICaller(
            USER_API_ROUTE,
            {
                search: text,
                role_id: 2,
                no_datatable_draw: 1,
            },
            function (response) {
                let users = response;
                if (users.length > 0) {
                    $.each(users, function ($key, user) {
                        bootstrapSelectUser.append(
                            `<option value="${user.id}"> ${user.username} </option>`
                        );
                    });
                }
                bootstrapSelectUser.selectpicker("refresh");
            },
            function (error) {
                console.log(error);
            }
        );
    });

    bootstrapProduct.on(
        "changed.bs.select",
        function (e, clickedIndex, isSelected, previousValue) {
            let id = $(this).val();
            bootstrapProduct.empty().selectpicker("refresh");

            APICaller(
                PRODUCT_API_ROUTE,
                {
                    id: id,
                    select_json: 1,
                },
                function (response) {
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
                    $(this).empty();
                    $(this).selectpicker("refresh");
                }
            );
        }
    );

    function calculateOrderPrice(row) {
        let remainingAmount = 0;
        let updatedAmount = 0;
        let finalAmount = 0;
        let discountPrice = 0;
        let totalPrice = 0;

        const amount =
            parseInt(
                row.find('input[data-manipulation-name="sold_quantity"]').val()
            ) || 0;
        const price =
            parseFloat(
                row
                    .find('input[data-manipulation-name="single_sold_price"]')
                    .val()
            ) || 0;
        const discount =
            parseFloat(
                row
                    .find('input[data-manipulation-name="discount_percent"]')
                    .val()
            ) || 0;
        const purchaseInitAmount =
            parseInt(
                row.find('td[name="purchase_init_amount"]').attr("value")
            ) || 0;
        const purchaseCurrentAmount =
            parseInt(row.find('td[name="purchase_amount"]').attr("value")) || 0;

        if (typeof ORDER !== "undefined") {
            remainingAmount =
                parseInt(ORDER_AMOUNT) - parseInt(ORIGINAL_AMOUNT);
            updatedAmount = remainingAmount + amount;
            finalAmount = purchaseInitAmount - updatedAmount;
        } else {
            if (amount > purchaseCurrentAmount) {
                finalAmount = -1 * (amount - purchaseCurrentAmount);
            } else {
                finalAmount = Math.abs(amount - purchaseCurrentAmount);
            }
        }

        totalPrice = parseFloat(amount * price).toFixed(2);

        if (!isNaN(discount)) {
            discountPrice = parseFloat(
                totalPrice - (totalPrice * discount) / 100
            );
        }

        if (finalAmount < 0) {
            finalAmount = `<span class="text-danger">${finalAmount}</span>`;
        } else {
            finalAmount = `<span class="text-dark">${Math.abs(
                finalAmount
            )}</span>`;
        }

        row.find('span[name="original_price"]').html(
            numericFormat(discountPrice)
        );
        row.find('span[name="regular_price"]').html(numericFormat(totalPrice));
        row.find('td[name="purchase_amount"]').html(finalAmount);
    }

    window.removeRow = function (button) {
        let tr = $(button).closest("tr");

        let rowTotalPrice = tr.find(".totalPrice").text();
        let rowQuantity = tr.find(".purchaseQuantity").text();

        totalPriceProducts -= parseFloat(rowTotalPrice);
        availableQuantity -= parseInt(rowQuantity);
        table.DataTable().row(tr).remove().draw();
    };

    window.handleSinglePrice = function (e) {
        const row = $(e).closest("tr");
        calculateOrderPrice(row);
    };

    window.handleOrderQuantity = function (e) {
        const row = $(e).closest("tr");
        calculateOrderPrice(row);
    };

    window.handleDiscountChange = function (e) {
        const row = $(e).closest("tr");
        calculateOrderPrice(row);
    };

    function renderData(data, isEdit = false) {
        let template = ``;

        if (isEdit) {
            if (typeof IS_EDITABLE !== "undefined" && IS_EDITABLE == false) {
                template += `<tr data-id=${data.order.id}>
                    <td>
                        <b>${data.order.id}</b>
                    </td>
                    <td>
                        <img src = "${data.order.purchase.image_path}" />
                    </td>
                    <td>
                        <a href="${PURCHASE_ROUTE.replace(
                            ":id",
                            data.order.purchase.id
                        )}">
                            ${data.order.purchase.name}
                        </a>
                    </td>
                    <td>
                        ${data.order.sold_quantity}
                    </td>
                    <td>
                        ${numericFormat(data.order.single_sold_price)}
                    </td>
                    <td>
                        ${numericFormat(data.order.discount_single_sold_price)}
                    </td>
                    <td>
                        ${numericFormat(data.order.total_sold_price)}
                    </td>
                    <td>
                        ${numericFormat(data.order.original_sold_price)}
                    </td>
                    <td>
                        ${data.order.discount_percent}
                    </td>
                    <td>
                        ${data.order.tracking_number}
                    </td>
                    <td>
                        ${moment(data.order.created_at).format("MMMM Do YYYY")}
                    </td>
                    <td>
                        ${moment(data.order.updated_at).format("MMMM Do YYYY")}
                    </td>
                </tr>`;
            } else {
                template += `<tr data-id=${data.order.id}>
                    <td>
                        <b>${data.order.id}</b>
                    </td>
                    <td>
                        <img src = "${data.order.purchase.image_path}" />
                    </td>
                    <td>
                        <a href="${PURCHASE_ROUTE.replace(":id",data.order.purchase.id)}">
                            ${data.order.purchase.name}
                        </a>
                    </td>
                    <td>
                        ${numericFormat(data.order.purchase.price)}
                    </td>
                    <td value="${data.order.purchase.quantity}" name="purchase_amount">
                        ${data.order.purchase.quantity}
                    </td>
                    <td value="${data.order.purchase.initial_quantity}" name="purchase_init_amount">
                        ${data.order.purchase.initial_quantity}
                    </td>
                    <td>
                        <div class="form-group col-12">
                            <input
                                name="sold_quantity" 
                                type='number'
                                value ="${data.order.sold_quantity}"
                                data-manipulation-name="sold_quantity"
                                class='form-control form-control-sm'
                                placeholder="Integer value (e.g., 1, 2)"
                                onkeyup="handleOrderQuantity(this)"
                            />
                            <span name="sold_quantity" class="text-danger"></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group col-12">
                            <input
                                name="single_sold_price" 
                                type='text'
                                value ="${data.order.single_sold_price}"
                                data-manipulation-name="single_sold_price"
                                class='form-control form-control-sm'
                                placeholder="Integer value (e.g., 1, 2)"
                                onkeyup="handleOrderQuantity(this)"
                            />
                            <span name="single_sold_price" class="text-danger"></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group col-12">
                            <input
                                name="discount_percent" 
                                type='number'
                                value ="${data.order.discount_percent}"
                                data-manipulation-name="discount_percent"
                                class='form-control form-control-sm'
                                placeholder="Integer value (e.g., 1, 2)"
                                onkeyup="handleOrderQuantity(this)"
                            />
                            <span name="discount_percent" class="text-danger"></span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group col-12">
                            <input
                                name="tracking_number" 
                                type='text'
                                value ="${data.order.tracking_number}"
                                data-manipulation-name="tracking_number"
                                class='form-control form-control-sm'
                                onkeyup="handleOrderQuantity(this)"
                            />
                            <span name="tracking_number" class="text-danger"></span>
                        </div>
                    </td>
                    <td>
                        <span name="original_price">${numericFormat(data.order.total_sold_price)}</span>
                    </td>
                    <td>
                        <span name="regular_price">${numericFormat(data.order.original_sold_price)}</span>
                    </td>
                </tr>`;
            }
        }
        table.DataTable().row.add($(template)).draw();
    }

    //Send HTTP POST
    $("#orderForm").submit((event) => {
        event.preventDefault();

        const form = $(event.currentTarget);
        const url = form.attr("action");
        const method = form.attr("method");

        const formData = $(form)
            .serializeArray()
            .filter((item) => item.name !== "DataTables_Table_0_length")
            .reduce((acc, obj) => {
                acc[obj.name] = obj.value;
                return acc;
            }, {});

        // console.log(formData);

        $.ajax({
            type: method,
            url,
            data: formData,
            success: (response) => {
                toastr.success(response.message);

                if (ORDER !== undefined) {
                    window.location.href = ORDER_INDEX_ROUTE;
                }

                table.DataTable().clear().draw();
            },
            error: (xhr, status, error) => {
                toastr.error(xhr.responseJSON.message);
                handleErrors(xhr.responseJSON.errors);
            },
        });
    });
});
