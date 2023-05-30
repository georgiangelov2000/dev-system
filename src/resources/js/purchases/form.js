import { APICaller } from '../ajax/methods';

$(function () {

    $('.selectSupplier, .selectCategory, .selectSubCategory, .selectBrands').selectpicker();

    const selectSupplier = $('.bootstrap-select .selectSupplier')
    const selectCategory = $('.bootstrap-select .selectCategory')
    const selectSubCategory = $('.bootstrap-select .selectSubCategory')
    const deleteImage = $('#deletePurchaseImage');

    selectSupplier.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

        let supplier = $(this).val();
        selectCategory.empty();
        selectSubCategory.empty();

        APICaller(CATEGORY_ROUTE, { "supplier": supplier }, function (response) {
            if (response.data.length > 0) {
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


    deleteImage.on('click', function (e) {
        e.preventDefault();

        var form = $(this).closest('form');
        var imageId = form.data('image-id');
        var url = form.attr('action');
    
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            data: {
                _method: 'DELETE',
                image_id: JSON.stringify(imageId)
            },
            success: function(response) {
                form.closest('.productImage').remove();
                toastr['success'](response.message);    
            },
            error: function(error) {
                toastr['error'](error.message);
            }
        });
    })

});
