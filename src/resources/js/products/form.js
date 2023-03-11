$(document).ready(function () {
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());

    $('.selectSupplier, .selectCategory, .selectSubCategory, .selectBrands').selectpicker();

    $('.bootstrap-select .selectSupplier').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        console.log($(this).val());
    });

});
    