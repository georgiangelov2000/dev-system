import { APICaller} from './ajaxFunctions.js';

$(document).ready(function () {

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());

    let selectedSupplier = $('.selectSupplier').find('option:selected').val();

    
    $('.selectSupplier, .selectCategory, .selectSubCategory, .selectBrands').selectpicker();

    const selectSupplier = $('.bootstrap-select .selectSupplier')
    const selectCategory = $('.bootstrap-select .selectCategory')
    const selectSubCategory = $('.bootstrap-select .selectSubCategory')

    if(selectedSupplier !== null) {
        APICaller(CATEGORY_ROUTE, {"supplier": selectedSupplier}, function (response) {
            const categories = response.data;
            if (categories.length > 0) {
                $.each(categories, function (key, category) {
                    const selected = category.id == SELECTED_CATEGORY ? 'selected' : '';
                    selectCategory.append(`<option ${selected} value="${category.id}">${category.name}</option>`);
                });
            }
            selectCategory.selectpicker('refresh');

            const selectedCategory = categories.find(category => category.id == SELECTED_CATEGORY);

            if (selectedCategory !== undefined) { 
                APICaller(CATEGORY_ROUTE, {"category": SELECTED_CATEGORY}, function (response) {
                    let subCategories = response.data;
                    if (subCategories.length > 0) {
                        selectSubCategory.append('<option value="0">All</option>');
                        $.each(subCategories, function (key, subCategory) {
                            const selected = SELECTED_SUBCATEGORIES.includes(subCategory.id) ? 'selected' : "";
                            selectSubCategory.append(`<option ${selected} value="${subCategory.id}">${subCategory.name}</option>`);
                        });
                    } else {
                        selectSubCategory.append('<option value="0">Nothing selected</option>');
                    }
                    selectSubCategory.selectpicker('refresh');
                }, function (error) {
                    console.log(error);
                });
            }

        },function(error){
            console.log(error);
        });
    }

    selectSupplier.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {

        let supplier = $(this).val();
        selectCategory.empty();

        selectSubCategory.empty();

        APICaller(CATEGORY_ROUTE, {"supplier": supplier}, function (response) {
            if (response.data.length > 0) {
                selectCategory.append('<option value="">Nothing selected</option>');
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

        APICaller(CATEGORY_ROUTE, {"category": category}, function (response) {
            if (response.data.length > 0) {
                selectSubCategory.append('<option value="0">All</option>');
                $.each(response.data, function (key, value) {
                    selectSubCategory.append('<option value=' + value.id + '>' + value.name + '</option>');
                });
            } else {
                selectSubCategory.append('<option value="0">Nothing selected</option>');
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

});
    