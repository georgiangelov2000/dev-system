import { APICaller, APIDELETECALLER } from "../ajax/methods";
import {
    handleErrors,
    swalText,
    showConfirmationDialog,
} from "../helpers/action_helpers";
import { numericFormat } from "../helpers/functions";
import { statusPaymentsWithIcons, paymentMethods } from "../helpers/statuses";

$(function () {
    $(".selectSupplier").selectpicker("refresh").val("").trigger("change");

    $('input[name="datetimes"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: "Clear",
        },
    });

    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
    });

    let disabledOption = $(".disabledDateRange");
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $(".dateRange");
    let bootstrapSelectSupplier = $(".bootstrap-select .selectSupplier");
    let btnFilter = $(".filter");
    let modalInvoice = $("#modalInvoice");
    let submitForm = $("#submitForm");

    let dataTable;

    dateRangePicker.on("apply.daterangepicker", function (ev, picker) {
        $(this).val(
            picker.startDate.format("YYYY-MM-DD") +
                " - " +
                picker.endDate.format("YYYY-MM-DD")
        );
    });

    disabledOption.on("click", function () {
        const isChecked = $(this).is(":checked");
        dateRangeCol.toggleClass("d-none", isChecked);
        dateRangePicker
            .toggleClass("d-none")
            .prop("disabled", isChecked)
            .val(null);

        if (!isChecked) {
            const picker = dateRangePicker.data("daterangepicker");
            picker.setStartDate(moment().subtract(1, "year"));
            picker.setEndDate(moment().startOf("hour"));
        }
    });

    btnFilter.bind("click", function (e) {
        loadDataTable();
    });

    function updateUI(response) {
        const supplier = response.supplier;
        const sum = response.sum;
        const date = response.date || "";
        const amountDue = date ? `Amount Due: ${date}` : "";

        $('h4[data-target="name"]').text(supplier.name);
        $('h4[data-target="date"]').text(date);
        $('p[data-target="lead-date"]').text(amountDue);
        $('span[data-target="address"]').text(supplier.address);
        $('span[data-target="phone"]').text(supplier.phone);
        $('span[data-target="email"]').text(supplier.email);
        $('span[data-target="country"]').text(supplier.country.name);
        $('span[data-target="city"]').text(supplier.state.name);
        $('span[data-target="zip"]').text(supplier.zip);
        $('#amountDueTable td[data-td-target="sum"]').text(numericFormat(sum));
        $('#amountDueTable td[data-td-target="records"]').text(response.recordsFiltered);
        $('img[id="supplierImage"]').attr("src", supplier.image_path);
    }

    function loadDataTable() {
        let table = `
            <div class="p-3 mb-3">

                <div class="row">
                    <div class="col-12 d-flex flex-wrap align-items-center justify-content-between">
                        <h4 data-target="name"></h4>
                        <h4 data-target="date" class="float-right"></h4>
                    </div>
                </div>
    
                <div class="row invoice-info mb-3">
                    <div class="col-sm-4 invoice-col">
                        <span class="font-weight-bold">Address:</span> <span data-target="address"></span> <br>
                        <span class="font-weight-bold">Phone:</span> <span data-target="phone"></span> <br>
                        <span class="font-weight-bold">Email:</span> <span data-target="email"></span>
                    </div>
    
                    <div class="col-sm-4 invoice-col">
                        <span class="font-weight-bold">Country:</span> <span data-target="country"></span> <br>
                        <span class="font-weight-bold">City:</span> <span data-target="city"></span> <br>
                        <span class="font-weight-bold">Zip code:</span> <span data-target="zip"></span> <br>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="paymentsTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Tracking</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Reference</th>
                                    <th>Exp delivery date</th>
                                    <th>Delivery date</th>
                                    <th>Date of payment</th>
                                    <th>Exp date of payment</th>
                                    <th>Delay</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
    
            <div class="row justify-content-end">
                <div class="col-6">
                    <img class="img-fluid w-25 rounded" id="supplierImage" src=""  />
                </div>
                <div class="col-6">
                    <p class="lead" data-target="lead-date"></p>
                    <div class="table-responsive">
                        <table id="amountDueTable" class="table">
                            <tbody>
                                <tr>
                                    <th style="width:50%">Final price:</th>
                                    <td data-td-target="sum"></td>
                                </tr>
                                <tr>
                                    <th style="width:50%">Records:</th>
                                    <td data-td-target="records"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row no-print">
                <div class="col-12">
                    <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                        <i class="fas fa-download"></i> Generate PDF
                    </button>
                </div>
            </div>
    </div>`;

        $("#paymentTemplate").removeClass("d-none").html(table);

        dataTable = $("#paymentsTable").DataTable({
            serverSide: true,
            ajax: {
                url: SUPPLIER_PAYMENTS_API,
                data: function (data) {
                    data.user = bootstrapSelectSupplier.val();
                    data.date = dateRangePicker.val();
                    data.type = TYPE;
                    data.custom_length = data.length; // Corrected the property assignment
                    data.search.value = data.search.value; // Corrected the property assignment
                },
                dataSrc: function (response) {
                    updateUI(response);
                    return response.data;
                },
            },
            columns: [
                { width: "1%", orderable: true, data: "id" },
                {
                    width: "1%",
                    orderable: false,
                    name: "image",
                    render: function (data, type, row) {
                        return row.purchase.image_path
                            ? `<img id="preview-image" alt="Preview" class="img-fluid card-widget widget-user w-100 m-0" src="${row.purchase.image_path}">`
                            : `<img class="rounded mx-auto w-100" src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png">`;
                    },                    
                },
                {
                    width: "15%",
                    name: "name",
                    orderable: false,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `<a href="${PURCHASE_EDIT_ROUTE.replace(":id",row.purchase.id)}">${row.purchase.name}</a>`;
                    },
                },
                {
                    width: "5%",
                    name: "code",
                    orderable: false,
                    class: "text-center",
                    render: function (data, type, row) {
                        return `<p class="text-break">${row.purchase.code}</p>`
                    },
                },
                {
                    width: "1%",
                    class: "text-center",
                    name: "price",
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>${numericFormat(row.price)}</span>`;
                    },
                },
                {
                    width: "1%",
                    class: "text-center",
                    name: "quantity",
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>${row.quantity}</span>`;
                    },
                },
                {
                    width: "1%",
                    class: "text-center",
                    name: "discount_percent",
                    orderable: false,
                    render: function (data, type, row) {
                        return `<span>${row.purchase.discount_percent}%</span>`;
                    },
                },
                {
                    width: "1%",
                    class: "text-center",
                    name: "payment_method",
                    orderable: false,
                    render: function (data, type, row) {
                        const status = paymentMethods[row.payment_method] || "";
                        return `<span>${status}</span>`;
                    },
                },
                {
                    width: "1%",
                    class: "text-center",
                    name: "payment_status",
                    orderable: false,
                    render: function (data, type, row) {
                        const statusData = statusPaymentsWithIcons[row.payment_status] || { label: "", iconClass: "" };
                        return `
                            <div title="${statusData.label}" class="status">
                                <span class="icon"><i class="${statusData.iconClass}"></i></span>
                            </div>`;
                    },
                },
                {
                    width: "10%",
                    class: "text-center",
                    name: "payment.purchase.category",
                    orderable: false,
                    render: function (data, type, row) {
                        return row.purchase.categories.length > 0
                        ? row.purchase.categories.map(category => `<span> ${category.name} </span>`).join(', ')
                        : '';
                    },
                },
                {
                    width: "3%",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        return `<p class="text-break">${row.payment_reference}</p>`
                    }
                },
                {
                    width: "13%",
                    name: "expected_delivery_date",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        const formattedDate = row.purchase.expected_delivery_date ? moment(row.purchase.expected_delivery_date).format('MMM DD, YYYY') : '';
                        return `<span>${formattedDate}</span>`;
                    },
                },
                {
                    width: "11%",
                    name: "expected_date_of_payment",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        const formattedExpectedDate = row.purchase.delivery_date ? moment(row.purchase.delivery_date).format('MMM DD, YYYY') : '';
                        return `<span>${formattedExpectedDate}</span>`;
                    },
                },    
                {
                    width: "11%",
                    name: "date_of_payment",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        const formattedDate = row.date_of_payment ? moment(row.date_of_payment).format('MMM DD, YYYY') : '';
                        return `<span>${formattedDate}</span>`;
                    },
                },
                {
                    width: "13%",
                    name: "expected_date_of_payment",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        const formattedExpectedDate = row.expected_date_of_payment ? moment(row.expected_date_of_payment).format('MMM DD, YYYY') : '';
                        return `<span>${formattedExpectedDate}</span>`;
                    },
                },                
                {
                    width: "8%",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        let status = row.payment_status;
                        if (status === 1 || status === 4) {
                            let dateOfPayment = moment(row.date_of_payment); // Assuming you're using Moment.js
                            let expectedDateOfPayment = moment(
                                row.expected_date_of_payment
                            );

                            let daysDifference = dateOfPayment.diff(
                                expectedDateOfPayment,
                                "days"
                            );

                            if (daysDifference > 0) {
                                return `<span class="text-danger">${daysDifference} days delay in payment</span>`;
                            } else {
                                return `<span class="text-success">Payment made on time</span>`;
                            }
                        } else {
                            return "";
                        }
                    },
                },
                {
                    width: "5%",
                    class: "text-center",
                    orderable: false,
                    render: function (data, type, row) {
                        let edit = `<a title="Edit" class="dropdown-item" href="${PURCHASE_PAYMENT_EDIT.replace(":payment", row.id).replace(":type", "purchase")}">
                                        <i class="fa-light fa-pen text-primary"></i> Edit
                                    </a>`;
                
                        let invoice = `<a title="Invoice" class="dropdown-item" data-id="${row.invoice.id}" onclick="editInvoice(this)">
                                          <i class="fa-light fa-file-invoice text-primary"></i> Invoice
                                       </a>`;
                
                        let deleteForm = `<form data-name=${row.purchase.name} style='display:inline-block;' id='delete-form' action="${PURCHASE_PAYMENT_DELETE_ROUTE.replace(":payment", row.id).replace(":type", "purchase")}" method='POST'>
                                                <input type='hidden' name='_method' value='DELETE'>
                                                <input type='hidden' name='id' value='${row.id}'>
                                                <button type='submit' class='dropdown-item' title='Delete' onclick='event.preventDefault(); deletePayment(this);'>
                                                    <i class='fa-light fa-trash text-primary'></i> Delete
                                                </button>
                                            </form>`;
                
                        return `<div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-light fa-list" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        ${edit}
                                        ${invoice}
                                        ${deleteForm}
                                    </div>
                                </div>`;
                    },
                },                                
            ],
        });
    }

    // Window actions
    window.editInvoice = function (e) {
        let id = $(e).attr("data-id");
        APICaller(INVOICE_API_ROUTE,{ invoice: id, select_json:1, type:TYPE },
            function (response) {
                let invoice = response;
                modalInvoice.modal("show");

                modalInvoice
                    .find("form")
                    .attr("action",INVOICE_API_ROUTE.replace(":type",TYPE).replace(":id", id));

                modalInvoice.find("form input").each(function () {
                    let inputName = $(this).attr("name");

                    if (inputName && invoice.hasOwnProperty(inputName)) {
                        $(this).val(invoice[inputName]);
                    }
                });
            },
            function (error) {
                toastr["error"](error.message);
            }
        );
    };

    // Window actions
    window.deletePayment = function (e) {
        let form = $(e).closest("form");

        let name = form.attr("data-name");
        let url = form.attr("action");

        const template = swalText(name);

        showConfirmationDialog("Selected items!", template, function () {
            APIDELETECALLER(
                url,
                function (response) {
                    toastr["success"](response.message);
                    dataTable.ajax.reload(null, false);
                },
                function (error) {
                    toastr["error"]("Order payment has not been deleted");
                }
            );
        });
    };

    submitForm.on("click", function (e) {
        e.preventDefault();

        let actionUrl = modalInvoice.find("form").attr("action");
        let method = modalInvoice.find("form").attr("method");
        let data = modalInvoice.find("form").serialize();

        $.ajax({
            url: actionUrl,
            method: method,
            data: data,
            success: function (response) {
                console.log(response.message);
                toastr["success"](response.message);
                modalInvoice.find("form").trigger("reset");
                modalInvoice.modal("toggle");
                dataTable.ajax.reload(null, false);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    toastr["error"](xhr.responseJSON.message);
                    var errors = xhr.responseJSON.errors;
                    handleErrors(errors);
                }
            },
        });
    });
});
