import { APICaller } from '../ajax/methods';

$(function () {

    $('.selectSupplier, .selectCategory, .selectSubCategory, .selectBrands').selectpicker();

    const selectSupplier = $('.bootstrap-select .selectSupplier')
    const selectCategory = $('.bootstrap-select .selectCategory')
    const selectSubCategory = $('.bootstrap-select .selectSubCategory')

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    })

    selectSupplier.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

        let supplier = $(this).val();
        selectCategory.empty();
        selectSubCategory.empty();

        APICaller(CATEGORY_ROUTE, { "supplier": supplier }, function (response) {
            if (response.data.length > 0) {
                selectCategory.append('<option>Select category</option>');
                $.each(response.data, function (key, value) {
                    selectCategory.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                selectCategory.append('<option value="">Nothing selected</option>');
            }
            selectCategory.selectpicker('refresh');
            selectSubCategory.selectpicker('refresh')
        }, function (error) {
            console.log(error);
        });
    });

    selectCategory.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let category = $(this).val();
        selectSubCategory.empty();

        APICaller(CATEGORY_ROUTE, { "category": category }, function (response) {
            let responseData = response.data[0];
            let subCategories = responseData.sub_categories;

            if (subCategories.length > 0) {
                $.each(subCategories, function (key, value) {
                    selectSubCategory.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            }
            selectSubCategory.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        });
    });

    $('.generateCode').on('click', function () {
        // Define the character set that you want to use
        var charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var length = 20;

        var randomString = '';
        for (var i = 0; i < length; i++) {
            var randomIndex = Math.floor(Math.random() * charset.length);
            randomString += charset[randomIndex];
        }

        $('input[name="code"]').val(randomString);

    });

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

    $('input[name="quantity"], input[name="price"], input[name="discount_percent"]').on('keyup', function () {
        calculation(
            parseFloat($('input[name="quantity"]').val()) || 0,
            parseFloat($('input[name="price"]').val()) || 0,
            parseFloat($('input[name="discount_percent"]').val()) || 0
        );
    });

    function calculation(amount, price, discountPercent) {

        if ($('p[name="order_amount"]') && $('p[name="initial_quantity"]')) {
            let orderAmount = parseInt($('p[name="order_amount"]').text() || 0);

            if (amount < orderAmount) {
                let warningTemplate = `<p class='text-danger'>Insufficient purchase quantity. The total order quantity exceeds the available purchase quantity.</p>`;
                $('#warning').removeClass('d-none').html(warningTemplate);
                return false;
            } else {
                $('#warning').addClass('d-none');
            }

        }

        let originalPrice = price * amount;
        let finalPrice = originalPrice - ((originalPrice * discountPercent) / 100);
        let unitDiscountPrice = price - ((price * discountPercent) / 100);

        $('#final_price').text(finalPrice.toFixed(2));
        $('#original_price').text(originalPrice.toFixed(2));
        $('#discount_price').text(unitDiscountPrice.toFixed(2));
        $('#unit_price').text(price.toFixed(2));
        $('#amount').text(amount);

    }

    calculation(
        parseFloat($('p[name="initial_quantity"]').text()) || 0,
        parseFloat($('input[name="price"]').val().replace(',', '.')) || 0,
        parseFloat($('input[name="discount_percent"]').val()) || 0
    );

});
