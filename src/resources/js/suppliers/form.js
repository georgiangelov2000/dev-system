import { APICaller } from '../ajax/methods';
$(document).ready(function () {

    $('.selectCategory, .selectCountry, .selectState, .selectCategory')
        .selectpicker();

    const bootstrapCountry = $('.bootstrap-select .selectCountry');
    const bootstrapState = $('.bootstrap-select .selectState');
    const bootstrapCategory = $('.bootstrap-select .selectCategory');
    const searchAddress = $('#searchAddress');
    const addresses = $('.addresses');

    const categoryIDS = $(bootstrapCategory).val();

    $('.selectCountry input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();

        if(text == '') {
            bootstrapCountry.empty().selectpicker('refresh');
            return;
        }

        APICaller(COUNTRY_API_ROUTE, {"search": text, "select_json":true}, function (response) {
            const countries = response;
            bootstrapCountry.empty().append('<option value="" class="d-none"></option>');
            if (countries.length) {
                countries.forEach(country => {
                    bootstrapCountry.append(`
                        <option value="${country.id}"> 
                            <i class="flag-icon flag-icon-${country.short_name.toLowerCase()}"> </i>
                            ${country.name}
                        </option>
                    `);
                });
            }
            bootstrapCountry.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));

    $('.selectCategory input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
        bootstrapCategory.empty().append('<option value="" class="d-none"></option>');

        if(text == '') {
            bootstrapCategory.append('<option value="0">All</option>').selectpicker('refresh');
            return;
        }

        APICaller(CATEGORY_API_ROUTE, {"search": text, "select_json":true}, function (response) {
            const categories = response;
            if (categories.length) {
                categories.forEach(category => {
                    let categoryID = category.id;
                    const isSelected = categoryIDS.includes(categoryID.toString());
                    const option = `<option ${isSelected ? 'selected' : ''} value="${category.id}">${category.name}</option>`;
                    bootstrapCategory.append(option);
                });
            }
            bootstrapCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));

    $('#image').on('change', function () {
        previewImage(this);
    })

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('img[name="cardWidgetImage"]').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    bootstrapCountry.on('changed.bs.select', function () {
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

    searchAddress.on('click', function() {
        var url = 'https://nominatim.openstreetmap.org/search';
        var query = $('input[name="address"]').val();
    
        $.get(url, {
            q: query,
            format: 'json',
            addressdetails: 5,
            limit: 5
        })
        .done(function(response) {
            if (response.length > 0) {
                var template = '<ul class="todo-list">';
                response.forEach(function(element) {
                    template += `<li title="Apply" onclick="applyAddress(this)" class="border-bottom p-0 d-flex align-items-center p-1" data-latitude="${element.lat}" data-longitude="${element.lon}"><a class="text-primary" type="button">${element.display_name}</a></li>`;
                });
                template += '</ul>';
                addresses.removeClass('d-none').html(template);
            } else {
                addresses.html('<p class="text-danger pl-3">No results found.</p>');
            }
        })
        .fail(function(error) {
            console.error(error);
        });
    });
    
    window.applyAddress = function (e) {
        $('input[name="address"]').val($(e).text());
    }
});
