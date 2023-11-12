import { APICallerWithoutData, APICaller } from '../ajax/methods';

$(function () {
    $('select[name="country_id"], select[name="state_id"]').selectpicker()

    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    const selectCountry = $('.bootstrap-select .selectCountry');
    const selectState = $('.bootstrap-select .selectState');
    const searchAddress = $('#searchAddress');
    const addresses = $('.addresses');

    selectCountry.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let countryId = $(this).val();
        let url = LOCATION_API_ROUTE.replace("country_id", countryId);

        APICallerWithoutData(url, function (response) {
            let options = "";

            if (response.length > 0) {
                $.each(response, function (key, value) {
                    options += '<option value=' + value.id + '>' + value.name + '</option>';
                });
            } else {
                options += '<option value="0">Nothing selected</option>';
            }

            selectState.html(options);
            selectState.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        });
    });


    searchAddress.on('click', function () {
        var url = 'https://nominatim.openstreetmap.org/search';
        var query = $('input[name="address"]').val();
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                q: query,
                format: 'json',
                addressdetails: 5,
                limit: 5
            },
            success: function (response) {
                console.log(response);
                if (response.length > 0) {
                    var template = '<ul class="pl-3">';
                    response.forEach(function (currentElement) {
                        template += '<li title="Apply" onclick="applyAddress(this)" class="list-unstyled"><a class="text-primary" type="button">' + currentElement.display_name + '<a/></li>';
                    });
                    template += '</ul>';
                    addresses.html(template);
                } else {
                    addresses.html('<p class="text-danger pl-3"> No results found. </p>');
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    })

    window.applyAddress = function (e) {
        $('input[name="address"]').val($(e).text());
    }

})