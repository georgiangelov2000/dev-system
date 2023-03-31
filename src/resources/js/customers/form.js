import { APICallerWithoutData } from './ajaxFunctions';

$(document).ready(function () {
    $('.selectCountry').selectpicker();
    $('.selectState').selectpicker();
    $('.selectCategory').selectpicker();

    let bootstrapCountry = $('.bootstrap-select .selectCountry');
    let bootstrapState = $('.bootstrap-select .selectState');

    bootstrapCountry.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let countryId = $(this).val();
        let url = STATE_ROUTE.replace(":id", countryId);
        bootstrapState.empty();
        
        APICallerWithoutData(url, function (response) {
            console.log(response);
            if (response.length > 0) {
                $.each(response, function (key, value) {
                    bootstrapState.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                bootstrapState.append('<option value="0">Nothing selected</option>');
            }
            bootstrapState.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        });

    });
});