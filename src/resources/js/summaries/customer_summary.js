import { APIPOSTCALLER } from '../ajax/methods';

$(function () {

    $('.selectCustomer, .orderFilter').selectpicker('refresh').val('').trigger('change')

    $('input[name="datetimes"]').daterangepicker({
        timePicker: false,
        startDate: moment().subtract(1, 'year'),
        endDate: moment().startOf('hour'),
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy'
    }).datepicker('setDate', new Date());

    let disabledOption = $('.disabledDateRange');
    let dateRangePicker = $('input[name="datetimes"]');
    let dateRangeCol = $('.dateRange');
    let bootstrapSelectCustomer = $('.bootstrap-select .selectCustomer');
    let form = $('#filterForm');

    disabledOption.on('click', function () {
        if ($(this).is(':checked')) {
            dateRangeCol.addClass('d-none');
            dateRangePicker.addClass('d-none').prop('disabled', true).val(null);
        } else {
            dateRangeCol.removeClass('d-none');
            dateRangePicker.removeClass('d-none').prop('disabled', false);
            dateRangePicker.data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
            dateRangePicker.data('daterangepicker').setEndDate(moment().startOf('hour'));
        }
    });

    form.on('submit', function (e) {
        e.preventDefault();
        let customer = bootstrapSelectCustomer.selectpicker('val');
        let date = dateRangePicker.val();

        let formData = {
            'type': 'customer',
            'user': customer,
            'date': date,
        };


        $('#loader').show();

        $.ajax({
            type: "POST",
            url: SUMMARY,
            data: formData,
            success: function (response, xhr) {
                // Call the summaryTemplate function with the response data
                summaryTemplate(response);
                $('#loader').hide();
            },
            error: function (error) {
                console.log(error);
            }
        });

    })


    const summaryTemplate = (data) => {
        const summaryContainer = $('#summary-container');
        let template = '';
        if (data !== null) {
            template += `
            ${headTemplate(data)}`;
        }

        summaryContainer.html(template);

    };

    const headTemplate = function (data) {
        let html = '';
        let groupedTemplate = '';
        let groupedSummary = data.summary;
        let iconWrapper = ""


        if (groupedSummary) {
            for (const key in groupedSummary) {
                let nestedObject = groupedSummary[key];

                if(key == 'paid') {
                    iconWrapper = `
                    <span class="info-box-icon bg-success">
                    <i class="fal fa-check-circle"></i>
                    </span>
                    `
                }
                else if(key == 'overdue') {
                    iconWrapper = `
                    <span class="info-box-icon bg-warning">
                        <i class="fal fa-exclamation-circle"></i>
                    </span>
                    `
                }
                else if(key === 'pending') {
                    iconWrapper = `
                    <span class="info-box-icon bg-primary">
                        <i class="fal fa-hourglass-half"></i>
                    </span>`;
                }
                else if(key === 'ordered') {
                    iconWrapper = `
                    <span class="info-box-icon bg-secondary">
                        <i class="fal fa-shopping-cart"></i>
                    </span>`;
                }

                groupedTemplate += `
                    <div class="col-md-2 col-sm-6 col-12">
                        <div class="info-box">
                            ${iconWrapper}
                            <div class="info-box-content">
                                <span class="info-box-text">
                                  <span class="font-weight-bold">Status:</span><span>${key.toUpperCase()}</span>
                                </span>
                                <div class="d-flex align-items-center">
                                    <span>Price:</span>
                                    <span class="info-box-number mt-0">${nestedObject['price']}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span>Count:</span>
                                    <span class="info-box-number mt-0">${nestedObject['counts']}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
            }
        }

        html += `
            <div class="row justify-content-between align-items-end">
                <div class="col-md-6 col-sm-6 col-lg-6 col-xl-6 d-flex flex-wrap align-items-center justify-content-between">
                    <h4 data-target="name" class="mb-0">${data.name}</h4>
                    <h4 data-target="date" class="float-right"></h4>
                </div>
                <div class="col-md-3 col-sm-3 col-lg-3 col-xl-3 d-flex flex-wrap align-items-center justify-content-end">
                    <img class="img-fluid w-25 rounded" id="customerImage" src="${data.image_path}">
                </div>
            </div>
            <hr class="w-100 m-1">
            <div class="row invoice-info mb-3">
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fa-light fa-info"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">
                                <span class="font-weight-bold">Status:</span><span>ALL</span>
                            </span>
                            <div class="d-flex align-items-center">
                                <span>Price:</span>
                                <span class="info-box-number mt-0">${data.total_price}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span>Count:</span>
                                <span class="info-box-number mt-0">${data.counts}</span>
                            </div>
                        </div>
                    </div>
                </div>
                ${groupedTemplate}
            </div>
        `;

        return html;
    }

});