import { APICaller } from '../ajax/methods';
import { numericFormat } from '../helpers//functions';
$(function () {

    $('.selectSupplier, .selectCategory, .selectSubCategory, .selectBrands').selectpicker();

    const selectSupplier = $('.bootstrap-select .selectSupplier')
    const selectCategory = $('.bootstrap-select .selectCategory')
    const selectSubCategory = $('.bootstrap-select .selectSubCategory')
    const selectBrands = $('.bootstrap-select .selectBrands')

    const quantityInput = $('input[name="quantity"]');
    const priceInput = $('input[name="price"]');
    const discountInput = $('input[name="discount_percent"]');
    const resultTable = $('table#result');
    const categoryId = selectCategory.val();

    if(categoryId && categoryId !== '0') {
        APICaller(SUB_CATEGORY_ROUTE, { "category": categoryId, 'select_json':true }, response => {
            const subCategories = response;
            subCategories.forEach(subCategory => {
                selectSubCategory.append(`<option value=${subCategory.id}>${subCategory.name}</option>`);
            });
            selectSubCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }

    $('.datepicker').datepicker({format: 'mm/dd/yyyy'})

    selectSupplier.on('changed.bs.select', function () {
        const supplier = $(this).val();
        selectCategory.empty();
        selectSubCategory.empty();

        APICaller(CATEGORY_ROUTE, { "supplier": supplier, 'select_json': true }, response => {
            const categories = response;

            if (categories.length) {
                selectCategory.append('<option>Select category</option>');
                categories.forEach(category => {
                    selectCategory.append(`<option value=${category.id}>${category.name}</option>`);
                });
            } else {
                selectCategory.append('<option value="">Nothing selected</option>');
            }
            selectCategory.selectpicker('refresh');
            selectSubCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    });

    selectCategory.on('changed.bs.select', function () {
        const category = $(this).val();
        selectSubCategory.empty();

        APICaller(SUB_CATEGORY_ROUTE, { "category": category, 'select_json':true }, response => {
            const subCategories = response;
            subCategories.forEach(subCategory => {
                selectSubCategory.append(`<option value=${subCategory.id}>${subCategory.name}</option>`);
            });
            selectSubCategory.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    });

    $('.selectSupplier input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
        
        APICaller(SUPPLIER_API_ROUTE, { "search": text }, function (response) {
            const suppliers = response.data;
            if (suppliers.length) {
                selectSupplier.empty().append('<option value="" class="d-none"></option>');
                suppliers.forEach(supplier => {
                    selectSupplier.append(`<option value="${supplier.id}"> ${supplier.name} </option>`);
                });
            }
            selectSupplier.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));

    $('.selectBrands input[type="text"]').on('keyup', _.debounce(function () {
        const text = $(this).val();
    
        APICaller(BRAND_API_ROUTE, { "search": text }, response => {
            const brands = response.data;
            if (brands.length) {
                selectBrands.append('<option value="" class="d-none"></option>');
                brands.forEach(brand => {
                    selectBrands.append(`<option value="${brand.id}"> ${brand.name} </option>`);
                });
            }
            selectBrands.selectpicker('refresh');
        }, error => {
            console.log(error);
        });
    }, 300));

    $('.generateCode').on('click', function () {
        const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const length = 20;
        let randomString = '';
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            randomString += charset[randomIndex];
        }
        $('input[name="code"]').val(randomString);
    });

    $('#image').on('change', function () {
        previewImage(this);
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('img[name="cardWidgetImage"]').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('input[name="image"]').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $('#fileLabel').text(fileName || 'Choose file');
    });
    
    function updateResults(updatedQuantity) {
        const quantity = parseInt(quantityInput.val()) || 0;
        const price = parseFloat(priceInput.val()) || 0;
        const discountPercent = parseInt(discountInput.val()) || 0;
    
        let initAmount = initialQuantity !== undefined ? initialQuantity : parseInt(quantity);    
        let orderAmount = purchaseOrderAmount !== undefined ? purchaseOrderAmount : 0;

        if (updatedQuantity !== undefined) {
            initAmount = (parseInt(updatedQuantity) + parseInt(orderAmount)) || 0;
        }
        
        const originalPrice = price * initAmount;
        const finalPrice = originalPrice - (originalPrice * discountPercent) / 100;
        const unitDiscountPrice = price - (price * discountPercent) / 100;
    
        const numericFormatResult = (value) => numericFormat(value);
    
        resultTable.find('td[name="initial_amount"]').text(initAmount);
        resultTable.find('td[name="current_amount"]').text(quantity);
        resultTable.find('td[name="final_price"]').text(numericFormatResult(finalPrice));
        resultTable.find('td[name="regular_price"]').text(numericFormatResult(originalPrice));
        resultTable.find('td[name="unit_discount_price"]').text(numericFormatResult(unitDiscountPrice));
        resultTable.find('td[name="unit_price"]').text(numericFormatResult(price));
        resultTable.find('td[name="order_amount"]').text(orderAmount);
    }

    quantityInput.add(priceInput).add(discountInput).on('keyup', function () {
        updateResults(quantityInput.val());
    });

    updateResults();

});
