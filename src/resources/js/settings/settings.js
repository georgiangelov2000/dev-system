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

    $('#print').on('click', function () {
        const doc = new jspdf.jsPDF();
        doc.setFontSize(18);
        doc.setTextColor('#1A237E');
        doc.text('Company Information', 20, 20);

        doc.setFontSize(12);
        doc.setTextColor('#000000');

        // Fetch all input values
        const email = $('#email').val();
        const country = $('#country_id option:selected').text();
        const state = $('#state_id option:selected').text();
        const companyName = $('#comapany-name').val();
        const phoneNumber = $('#phone_number').val();
        const taxNumber = $('#tax_number').val();
        const ownerName = $('#owner_name').val();
        const website = $('#website').val();
        const businessType = $('#bussines_type').val();
        const registrationDate = $('#registration_date').val();
        const address = $('#address').val();

        // Add an image to the PDF
        const imageUrls = [
            '/storage/images/static/pin.png',
            '/storage/images/static/countries.png',
            '/storage/images/static/real-state.png',
            '/storage/images/static/pin.png',
            '/storage/images/static/telephone.png',
            '/storage/images/static/tax.png',
            '/storage/images/static/boss.png',
            '/storage/images/static/internet.png',
            '/storage/images/static/adoption.png',
            '/storage/images/static/mobile-application.png',
            '/storage/images/static/pin.png',
        ];

        // Define the margin between images (in pixels)
        const imageMargin = 1;

        // Loop through the array and add each image to the PDF with margin
        imageUrls.forEach((url, index) => {
            const xPosition = 20; // Adjust the X position as needed
            const yPosition = 30 + index * (imageMargin + 15); // Calculate Y position with margin
            const imageSize = 8; // Adjust the image size as needed
            doc.addImage(url, 'PNG', xPosition, yPosition, imageSize, imageSize);
        });

        // Add input values to the PDF
        doc.text(`Email: ${email}`, 40, 35);
        doc.text(`Country: ${country}`, 40, 51);
        doc.text(`State: ${state}`, 40, 68);
        doc.text(`Company Name: ${companyName}`, 40, 83);
        doc.text(`Phone Number: ${phoneNumber}`, 40, 98);
        doc.text(`Tax Number: ${taxNumber}`, 40, 115);
        doc.text(`Owner: ${ownerName}`, 40, 131);
        doc.text(`Website: ${website}`, 40, 146);
        doc.text(`Business Type: ${businessType}`, 40, 163);
        doc.text(`Registration Date: ${registrationDate}`, 40, 180);
        doc.text(`Address: ${address}`, 40, 195);

        // Add an image to the PDF
        const imageUrl = 'http://localhost/storage/images/company/9Bc2hiKY7f.jpg';
        const imageWidth = 30; // Adjust the image width as needed
        const imageHeight = 30; // Adjust the image height as needed
        const imageX = 170; // Adjust the X position of the image as needed
        const imageY = 5; // Adjust the Y position of the image as needed
        doc.addImage(imageUrl, 'JPEG', imageX, imageY, imageWidth, imageHeight);


        // Save the PDF
        doc.save('company_information.pdf');
    })

})