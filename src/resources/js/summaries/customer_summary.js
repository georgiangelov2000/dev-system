import { APIPOSTCALLER } from './ajaxFunctions.js';

$(function () {
    
    $('.selectCustomer').selectpicker('refresh').val('').trigger('change')

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

        $('#loader').show();

        APIPOSTCALLER(SUMMARY, { "customer": customer, 'date': date }, function (response) {
            let respData = response;

            if (respData) {
                summaryTemplate(respData);
            }

            $('#loader').hide();
        }, function (error) {
            console.log(error);
        })

    })


    const summaryTemplate = function (data) {
        
        const summaryContainer = document.getElementById('summary-container');
        const tableClass = 'table-summary';
    
        let template = '';
    
        template += '<div class="summary">';
    
        for (const key in data.products) {

            template += `${renderPackageData(data.products[key],key)}`;

            const product = data.products[key];
            
            if (product.status) {           

                const statuses = Object.keys(product.status);

                statuses.forEach((status) => {

                    const statusData = product.status[status];
                    const products = statusData.products;
                    
                    if (products.length > 0) {
                        template+=`
                            ${renderStatusData(statusData,status)}
                            <div style="background-color:rgb(244, 246, 249);" class="p-4 rounded m-2">
                                <table class="${tableClass} table table-sm table-bordered table-without-border table-hover col-6">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Single sold price</th>
                                            <th>Total sold price</th>
                                            <th>Regular price</th>
                                            <th>Sold quantity</th>
                                            <th>Single markup</th>
                                            <th>Total markup</th>
                                            <th>Discount %</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${renderProductTable(products)}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        `;
                    }
                });
            }
        }
    
        template += '</div>';
    
        summaryContainer.innerHTML = template;
    
        const dataTables = document.getElementsByClassName(tableClass);
        
        for (let i = 0; i < dataTables.length; i++) {
            new DataTable(dataTables[i], {
                columns: [
                    { orderable: false },
                    { orderable: false },
                    { orderable: false }, 
                    { orderable: false },
                    { orderable: false },
                    { orderable: false },
                    { orderable: false },
                    { orderable: false },
                    { orderable: false },
                ]
            });
        }
    }
    
    const renderPackageData = (productData,packageKey) => {
        return `
        <h6 title="Package" class="mb-0 mt-2">
                <strong class="badge bg-primary">
                    ${packageKey}
                </strong>
        </h6> 
        <div class="mb-2">
            <div>
                <span>Orders count:</span>
                <strong> ${productData.orders_count}</strong>
            </div>
            <div>
                <span>Total sales: </span>
                <strong class="text-success"> + ${productData.sum}</strong> €
            </div>
            <div>
                <span>Paid sales: </span>
                <strong class="text-success"> + ${productData.paid_sales_total_price}</strong> €
            </div>
        </div>
        `;
    }
    
    const renderStatusData = (statusData,status) => {
        return `
            <div class="col">
                <h6 title="Status" class="mb-0">
                    <strong class="badge bg-secondary">
                        ${status} (${statusData.products.length})
                    </strong>
                </h6> 
                <div>
                    <span>Orders count:</span>
                    <strong> ${statusData.orders_count}</strong>
                </div>
                <div>
                    <span>Total sales:</span>
                    <strong class="text-success"> ${statusData.sum} </strong> €
                </div>
            </div>
        `;
    }
    
    const renderProductTable = (products) => {
        let tableHtml = '';
    
        products.forEach((product) => {
            tableHtml += `
                <tr data_id="${product.id}" data_price="${product.total_sold_price}">
                    <td>${product.name}</td>
                    <td>${product.single_sold_price} €</td>
                    <td>${product.total_sold_price} €</td>
                    <td>${product.regular_price} €</td>
                    <td>${product.sold_quantity}</td>
                    <td>${product.single_markup} €</td>
                    <td>${product.total_markup} €</td>
                    <td>${product.discount}</td>
                    <td>
                        <a title='Preview' href="${PREVIEW_ROUTE.replace(':id', product.main_product_id)}" class='btn p-0'><i class='fa fa-light fa-eye text-info' aria-hidden='true'></i></a>
                    </td>
                </tr>
            `;
        });
    
        return tableHtml;
    }

});