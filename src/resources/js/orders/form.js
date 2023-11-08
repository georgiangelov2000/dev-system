import { APICaller } from "../ajax/methods";
import { handleErrors } from "../helpers/action_helpers";
import { numericFormat } from "../helpers/functions";

$(function () {
    $(".selectCustomer,.selectUser,.selectType,.productFilter,.selectPackage").selectpicker();

    $(".datepicker").datepicker({
        format: "mm/dd/yyyy",
    });

    let bootstrapCustomer = $(".bootstrap-select .selectCustomer");
    let bootstrapProduct = $(".bootstrap-select .productFilter");
    let bootstrapSelectUser = $(".bootstrap-select .selectUser");
    let bootstrapSelectPackage = $(".bootstrap-select .selectPackage");

    let table = $(".productOrderTable");

    let totalPriceProducts = 0;
    let availableQuantity = 0;
    let counter = 0;

    table.DataTable({
        ordering: false,
        columns: [
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "2%" },
            { class: "text-center", width: "1%" },
            { class: "text-center", width: "7%" },
            { class: "text-center", width: "7%" },
            { class: "text-center", width: "7%" },
            { class: "text-center", width: "7%" },
            { class: "text-center", width: "5%" },
            { class: "text-center", width: "5%" },
        ],
    });

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
            },
            function (response) {
                let purchases = response;

                if (purchases.length > 0) {
                    bootstrapProduct.append(
                        '<option value="" style="display:none;"></option>'
                    );
                    $.each(purchases, function ($key, purchase) {
                        bootstrapProduct.append(
                            `<option value="${purchase.id}"> ${purchase.name} </option>`
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

    if (typeof ORDER === 'undefined') {
        
    } else {
        APICaller(ORDER_API_ROUTE,{'id':ORDER,'select_json':1},function(response){
            let data = response;
            console.log(data);
            renderData(data);
        })
    }
    
    function calculateOrderPrice(row) {
        const quantity =
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

        let discountPrice = 0;

        let totalPrice = quantity * price;

        if (!isNaN(discount)) {
            discountPrice = totalPrice - (totalPrice * discount) / 100;
        }

        discountPrice = parseFloat(discountPrice.toFixed(2));
        totalPrice = parseFloat(totalPrice.toFixed(2));

        let formattedDiscountPrice = discountPrice.toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        let formattedTotalPrice = totalPrice.toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });

        row.find('span[name="original_price"]').html(formattedDiscountPrice);
        row.find('span[name="regular_price"]').html(formattedTotalPrice);
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

    function renderData(data) {
        let image = "";

        if(data.order) {
            image = `<img src="${data.order.purchase.image_path}" />`;
        } else {
            image = `<img src="${data.image_path}" />`;
        }
        
        let price = data.order !== undefined
        ? numericFormat(data.order.purchase.price) 
        : numericFormat(data.price);

        let quantity = data.order !== undefined
        ? data.order.purchase.quantity 
        : data.quantity;

        let name = data.order !== undefined
        ? data.order.purchase.name 
        : data.name;

        let orderQuantity = data.order !== undefined
        ? data.order.sold_quantity 
        : 0;

        let orderUnitPrice = data.order!== undefined
        ?  data.order.single_sold_price 
        : 0;

        let orderDiscountPrice = data.order !== undefined
        ?  data.order.discount_percent 
        : 0;

        let orderTrackingNumber = data.order !== undefined
        ?  data.order.tracking_number
        : '';

        let orderFinalPrice = data.order !== undefined
        ?  numericFormat(data.order.total_sold_price)
        : numericFormat(0);


        let orderRegularPrice = data.order !== undefined 
        ?  numericFormat(data.order.original_sold_price)
        : numericFormat(0);

        let currentId = data.order !==undefined
        ? data.order.purchase.id
        : data.id;

        let template = `
        <tr data-id="${currentId}">
            <input type="hidden" value='${currentId}' name="purchase_id[]" />
            <td>
                <button class="text-danger btn p-0" onclick="removeRow(this)" type="button">
                    <i class="fa-light fa-trash text-danger"></i>
                </button>
            </td>
            <td>${image}</td>
            <td>
                <a href="${PURCHASE_ROUTE.replace(":id", currentId)}">${name}</a>
            </td>
            <td>${price}</td>
            <td>${quantity}</td>
            <td>
                <div class="form-group col-12">
                    <input 
                        name="sold_quantity[]" 
                        type='number'
                        data-manipulation-name="sold_quantity"
                        max='${quantity}'
                        value ="${orderQuantity}"
                        class='form-control form-control-sm' 
                        onkeyup="handleOrderQuantity(this)" 
                        placeholder="Integer value (e.g., 1, 2)"
                    />
                    <span name="sold_quantity.${
                        counter - 1
                    }" class="text-danger"></span>
                </div>
            </td>
            <td>
                <div class="form-group col-12">
                    <input 
                        type='text'
                        name="single_sold_price[]"
                        data-manipulation-name="single_sold_price"
                        class='form-control form-control-sm' 
                        min="0"
                        value="${orderUnitPrice}"
                        onkeyup="handleSinglePrice(this)" 
                        placeholder="Numeric value (e.g., 1.00)"
                    />
                    <span name="single_sold_price.${
                        counter - 1
                    }" class="text-danger"></span>
                </div>
            </td>
            <td> 
                <div class="form-group col-12">
                    <input 
                        type='number' 
                        min="0"
                        class='form-control form-control-sm' 
                        data-manipulation-name="discount_percent"
                        name="discount_percent[]"
                        value="${orderDiscountPrice}"
                        onkeyup="handleDiscountChange(this)"
                        placeholder="Integer value (e.g., 1, 2)"
                    />
                    <span name="discount_percent.${
                        counter - 1
                    }" class="text-danger"></span>
                </div>
            </td>
            <td>
                <div class="form-group col-12">
                    <input 
                        type="text" 
                        class="form-control form-control-sm" 
                        name="tracking_number[]"
                        value="${orderTrackingNumber}"
                        placeholder="Max length 20"
                    />
                    <span name="tracking_number.${
                        counter - 1
                    }" class="text-danger"></span>
                </div>
            </td>
            <td>
                <span name="original_price">${orderFinalPrice}</span>
            </td>
            <td>
                <span name="regular_price">${orderRegularPrice}</span>
            </td>
        </tr>`;

        table.DataTable().row.add($(template)).draw();
    }

    //Send HTTP POST
    $('#orderForm').submit(function (event) {
        event.preventDefault();

        let form = $(this);
        let url = form.attr("action");
        let method = form.attr("method");

        let formData = $(form)
            .serializeArray()
            .reduce((acc, obj) => {
                acc[obj.name] = obj.value;
                return acc;
            }, {});

        $.ajax({
            type: method,
            url: url,
            data: formData,
            success: function (response) {
                toastr["success"](response.message);
                table.DataTable().clear().draw();
            },
            error: function (xhr, status, error) {
                console.log(error);
                if (xhr.status === 422) {
                    toastr["error"](xhr.responseJSON.message);
                    var errors = xhr.responseJSON.errors;
                    handleErrors(errors);
                }
            },
        });
    });
});
