import { paymentStatuses,deliveryStatuses } from "../helpers/statuses";

$(function () {
    $('select[name="payment_method"], select[name="payment_status"], select[name="is_it_delivered"]').selectpicker();

    // Get the delivery status select element
    const deliveryStatusSelect = $(
        '.bootstrap-select select[name="is_it_delivered"]'
    );

    getPaymentDateHtml(deliveryStatusSelect.val());

    // Handle delivery status change event
    deliveryStatusSelect.on("changed.bs.select",function (e, clickedIndex, newValue, oldValue) {
        getPaymentDateHtml(clickedIndex);
    });

    function getPaymentDateHtml(index) {
        let template = ``;

        if (index == 1 && paymentStatus != 5) {
            template = `
                <div class="form-group col-3">
                        <label for="date_of_payment">Date of payment</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" onChange="datepickerUpdate(this)" class="form-control datepicker" name="date_of_payment" 
                            value="${
                                typeof dateOfPayment !== "undefined"
                                    ? dateOfPayment
                                    : ""
                            }" />
                        </div>
                </div>
                <div class="form-group col-3">
                        <label for="delivery_date">Delivery date</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control datepicker" onChange="datepickerUpdate(this)" name="delivery_date"
                            value="${
                                typeof deliveryDate !== "undefined"
                                    ? deliveryDate
                                    : ""
                            }" />
                        </div>
                </div>
            `;
        }

        console.log(dateOfPayment);
        $("#deliveryWrapper").html(template);

        initializeDatepicker(".datepicker");
    }

    function initializeDatepicker(elementSelector) {
        // Check if the element exists
        if ($(elementSelector).length) {
            // Initialize datepicker
            $(elementSelector).datepicker({ format: "yyyy-mm-dd" });
        }
    }

    // Handle datepicker change event
    window.datepickerUpdate = (e) => {
            const selectedDate = new Date($(e).val());
            updatePaymentStatus(e, selectedDate);
    };

    function updatePaymentStatus(e, selectedDate) {
        let changedStatus;
        let statusLabel;
        let expected;
        let modifiedStatuses;

        // Assuming expectedDateOfPayment and expectedDeliveryDate are defined elsewhere
        // or you need to define them here.



        if ($(e).attr("name") === "date_of_payment") {
            changedStatus = $('input[name="payment_status"]');
            modifiedStatuses = paymentStatuses;
            expected = new Date(expectedDateOfPayment); // Convert to Date
        } else if ($(e).attr("name") === "delivery_date") {
            changedStatus = $('input[name="delivery_status"]');
            modifiedStatuses = deliveryStatuses
            expected = new Date(expectedDeliveryDate); // Convert to Date
        }

        if (selectedDate > expected) {
            statusLabel = modifiedStatuses[4].label;
        } else {
            statusLabel = modifiedStatuses[1].label;
        }
        console.log(changedStatus);

        changedStatus.val(statusLabel);
    }

    // Handle print button click event
    $("#print").on("click", function () {
        window.print();
    });
});
