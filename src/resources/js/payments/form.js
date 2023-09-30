$(function () {
    $('select[name="payment_method"],select[name="payment_status"]').selectpicker();

    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
    });

    $('.bootstrap-select select[name="payment_status"]').on('changed.bs.select', function (e, clickedIndex) {
        $('#partiallyPaidPriceInput').toggleClass('d-none', clickedIndex !== 3);
    });

    $("#print").on("click", function () {
        window.print();
    });
});
