import { paymentStatuses } from "../helpers/statuses";

$(function () {
    $(
        'select[name="payment_method"], select[name="payment_status"], select[name="is_it_delivered"]'
    ).selectpicker();

    // Handle print button click event
    $("#print").on("click", function () {
        window.print();
    });

    // Get the delivery status select element
    const deliveryStatusSelect = $(
        '.bootstrap-select select[name="is_it_delivered"]'
    );

    // Handle delivery status change event
    deliveryStatusSelect.on(
        "changed.bs.select",
        function (e, clickedIndex, newValue, oldValue) {
            if (clickedIndex === 0) {
                // Clear the template if the index is zero
                $("#deliveryWrapper").html("");
            } else {
                // Update the dateOfPaymentContainer based on the index
                getPaymentDateHtml();

                // Initialize datepicker
                $(".datepicker, .").datepicker({ format: "yyyy-mm-dd" });

                // Handle datepicker change event
                $(".datepicker").on("change", function () {
                    let selectedDate = new Date($(this).val());
                    // Update payment status based on the selected date
                    updatePaymentStatus(selectedDate, expected);
                });

                updatePaymentStatus(null, null, clickedIndex);
            }
        }
    );

    function getPaymentDateHtml() {
        let template = `
        <div class="form-group col-12 dateOfPaymentContainer">
                <label for="date_of_payment">Date of payment</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control datepicker" name="date_of_payment"
                        value="">
                </div>
        </div>
        <div class="form-group col-12 deliveryDateContainer">
                <label for="delivery_date">Delivery date</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control datepicker" name="delivery_date"
                        value="">
                </div>
        </div>
        `;
        $("#deliveryWrapper").html(template);
    }

    function updatePaymentStatus(
        selectedDate = null,
        expectedDate = null,
        index = null
    ) {
        let statusLabel;

        if (selectedDate >= expectedDate) {
            statusLabel = paymentStatuses[4].label;
        } else if (selectedDate <= expectedDate) {
            statusLabel = paymentStatuses[1].label;
        }

        index === 0 ? (statusLabel = paymentStatuses[2].label) : statusLabel;
        $('input[name="payment_status"]').val(statusLabel);
    }

    function initializeDatepicker(elementSelector) {
        // Check if the element exists
        if ($(elementSelector).length) {
            // Initialize datepicker
            $(elementSelector).datepicker({ format: "yyyy-mm-dd" });
        }
    }

    // Call the function for date_of_payment and delivery_date elements
    initializeDatepicker('[name="date_of_payment"]');
    initializeDatepicker('[name="delivery_date"]');
});
