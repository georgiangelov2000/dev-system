import { initializeMap, setMapView } from '../ajax/leaflet';
import { APICaller } from '../ajax/methods';


$(document).ready(function () {

    $('.selectMultiple, .selectCountry, .selectState, .selectCategory')
        .selectpicker();

    const bootstrapCountry = $('.bootstrap-select .selectCountry');
    const bootstrapState = $('.bootstrap-select .selectState');
    const searchAddress = $('#searchAddress');
    const addresses = $('.addresses');
    $('#image').on('change', function () {
        previewImage(this);
    })

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.imagePreview').removeClass('d-none');
                $('#preview-image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    bootstrapCountry.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let countryId = $(this).val();
        bootstrapState.empty();

        APICaller(LOCATION_API_ROUTE, { "country_id": countryId }, function (response) {
            let data = response;

            if (data.length) {
                $.each(data, function (key, value) {
                    bootstrapState.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                bootstrapState.append('<option value="0">Nothing selected</option>');
            }
            bootstrapState.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
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
                        template += '<li title="Apply" onclick="applyAddress(this)" class="list-unstyled" data-latitude="' + currentElement.lat + '" data-longitude="' + currentElement.lon + '"><a class="text-primary" type="button">' + currentElement.display_name + '<a/></li>';
                    });
                    template += '</ul>';
                    addresses.html(template);
                } else {
                    addresses.html('<p class="text-danger pl-3"> No results found. </p>');
                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    })

    window.applyAddress = function (e) {
        $('input[name="address"]').val($(e).text());
        var latitude = parseFloat($(e).data('latitude'));
        var longitude = parseFloat($(e).data('longitude'));
        setMapView([latitude, longitude], 15);
    }

    initializeMap()

});
