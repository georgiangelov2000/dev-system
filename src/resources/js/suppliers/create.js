import { getStates } from './ajaxFunctions';

$(document).ready(function () {
    $('select#country').on("change", function () {
        let countryId = $(this).val();
        let countryName = $(this).find('option:selected').attr('data-country');
        let url = STATE_ROUTE.replace(":id", countryId);
        
        getStates(url, function (response) {
            toastr['success']("Succesfully fetched states for " + countryName);
            $('#state').empty();
            $('#state').append('<option value="">Select a state</option>');
            $.each(response, function (key, value) {
                $('#state').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        }, function (error) {
            toastr['error']("Unsuccesfully fetched states");
        });
    });
});
