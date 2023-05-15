import { APICaller, APIPOSTCALLER, APIDELETECALLER } from '../ajax/methods';

$(function(){
    $('.selectAction, .selectType, .selectCustomer').selectpicker();

    let bootstrapCustomer = $('.bootstrap-select .selectCustomer');
    let bootstrapOrderStatus = $('.bootstrap-select .selectType');
    let bootstrapSelectAction = $('.bootstrap-select .selectAction');
    let modal = $('#transaction_modal');
    let payForm = $('#payOrderForm');
    let payButton = $('#savePayOrder');

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

    let table = $('#ordersTable');
    let applyBtn = $('.applyBtn');

    let dataT = table.DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
              extend: 'copy',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
              }
            },
            {
              extend: 'csv',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
              }
            },
            {
              extend: 'excel',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9] 
              }
            },
            {
              extend: 'pdf',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
              }
            },
            {
              extend: 'print',
              class: 'btn btn-outline-secondary',
              exportOptions: {
                columns: [1,2,3,4,5,6,7,8,9]
              }
            }
          ],
        ajax: {
            url: ORDER_API_ROUTE
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectOrder(this)" data-id=' + row.id + ' data-name= ' + row.invoice_number + ' type="checkbox"> \n\
                    </div>';

                    return `${checkbox}`;
                }
            },
            {
                width: '3%',
                name: "id",
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.id + '</span>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "invoice_number",
                render: function (data, type, row) {
                    return '<span class="font-weight-bold">' + row.invoice_number + '</span>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "customer",
                render: function (data, type, row) {
                    return '<a target="_blank" href="'+CUSTOMER_EDIT_ROUTE.replace(':id',row.customer.id)+'" >' + row.customer.name + '</a>';
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "product",
                render: function (data, type, row) {
                    return '<a target="_blank" href="'+EDIT_PRODUCT_ROUTE.replace(':id',row.product.id)+'">' + row.product.name + '</a>';
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "sold_quantity",
                data: "sold_quantity"
            },
            {
                width: '10%',
                orderable: false,
                name: "single_sold_price",
                render: function(data,type,row) {
                    return `<span>${row.single_sold_price} €</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "total_sold_price",
                render: function(data,type,row) {
                    return `<span>${row.total_sold_price} €</span>`
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "discount_percent",
                render: function (data,type,row){
                    return `<span>${row.discount_percent}%</span>`;
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "date_of_sale",
                data: "date_of_sale"
            },
            {
                width: '5%',
                orderable: false,
                name: "status",
                render: function (data, type, row) {
                    if (row.status === 'Received') {
                        return '<i title="Reveived" class="fa-light fa-check"></i>';
                    }
                    else if (row.status === 'Pending') {
                        return '<i title="Pending" class="fa-light fa-loader"></i>'
                    }
                    else if (row.status === 'Ordered') {
                        return '<i title="Ordered" class="fa-light fa-truck"></i>'
                    }
                }
            },
            {
                width: '5%',
                orderable: false,
                name: "is_paid",
                render: function (data, type, row) {
                    console.log(row.is_paid);
                    if(row.is_paid) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                render: function (data, type, row) {
                    console.log(row.is_paid);
                    let deleteFormTemplate = "\
                    <form style='display:inline-block;' id='delete-form' action=" + ORDER_DELETE_ROUTE.replace(':id', row.id) + " method='POST' data-name=" + row.invoice_number + ">\
                        <input type='hidden' name='_method' value='DELETE'>\
                        <input type='hidden' name='id' value='" + row.id + "'>\
                        <button type='submit' class='btn p-1' title='Delete' onclick='event.preventDefault(); deleteCurrentOrder(this);'><i class='fa-light fa-trash text-danger'></i></button>\
                    <form/>\
                    ";

                    let editButton = '<a href='+ORDER_EDIT_ROUTE.replace(':id',row.id)+' data-id=' + row.id + 'class="btn p-1" title="Edit"><i class="fa-light fa-pen text-warning"></i></a>';

                    let previewButton = '<a title="Review" class="btn p-1"><i class="text-primary fa-sharp fa-thin fa-magnifying-glass"></i></a>'

                    let payButton = "";
                    
                    if(row.status === "Received" && !row.is_paid) {
                        payButton = `<a onclick="payment(this)" order-price="${row.total_sold_price}" customer-id=${row.customer.id} order-id=${row.id} class='btn p-0' title="Payment"><i class="fa-thin fa-cash-register"></i></a>`;
                    }

                    let dropdown = `
                    <div class="dropdown d-inline">
                        <button class="btn text-info p-0" title="Change status" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-light fa-rotate-right"></i>
                        </button>
                        <div id="changeStatus" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button type="button" order-id=${row.id} value="1" onclick="changeStatus(this)" class="dropdown-item">Received</button>
                            <button type="button" order-id=${row.id} value="3" onclick="changeStatus(this)" class="dropdown-item">Pending</button>
                            <button type="button" order-id=${row.id} value="4" onclick="changeStatus(this)" class="dropdown-item">Ordered</button>
                        </div>
                    </div>
                    `;

                    return `${deleteFormTemplate} ${editButton} ${dropdown} ${payButton} ${previewButton}`;
                }
            },
        ],
        lengthMenu: [[10], [10]],
        pageLength: 10,
        order: [[1, 'asc']]

    });

    window.changeStatus = function (e) {
        let status = $(e).attr('value');
        let order = $(e).attr('order-id');

        APIPOSTCALLER(ORDER_UPDATE_STATUS.replace(':id', order), { 'status': status }, function (response) {
            toastr['success'](response.message);
            table.DataTable().ajax.reload();
        }, function (error) {
            toastr['error']('Order has not been updated');
        })
    }

    $('.selectCustomer input[type="text"]').on('keyup', function () {
        let text = $(this).val();
        bootstrapCustomer.empty();

        APICaller(CUSTOMER_API_ROUTE, { 'search': text }, function (response) {
            let customers = response.data;
            if (customers.length > 0) {
                $.each(customers, function ($key, customer) {
                    bootstrapCustomer.append(`<option value="${customer.id}"> ${customer.name} </option>`)
                })
            }
            bootstrapCustomer.selectpicker('refresh');
        }, function (error) {
            console.log(error);
        })
    })

    bootstrapCustomer.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let customerId = $(this).val();
        APICaller(ORDER_API_ROUTE, { "customer": customerId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });

    bootstrapOrderStatus.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let status = $(this).val();
        APICaller(ORDER_API_ROUTE, { "status": status }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    });


    applyBtn.on('click', function () {
        console.log('yes');
        let date = $('input[name="datetimes"]').val();
        let dateParts = date.split(" - ");
        let startDate = dateParts[0];
        let endDate = dateParts[1];
        APICaller(ORDER_API_ROUTE, { "start_date": startDate, 'end_date': endDate }, function (response) {
            console.log(response.data);
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        })
    })

    window.deleteCurrentOrder = function (e) {
        let form = $(e).closest('form');

        let name = form.attr('data-name');
        let url = form.attr('action');

        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            APIDELETECALLER(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error']('Product has not been deleted');
            });
        });
    };

    $(document).on('change', ".selectAll", function () {
        if (this.checked) {
            $('.actions').removeClass('d-none');
            $(':checkbox').each(function () {
                this.checked = true;
            });
        } else {
            $('.actions').addClass('d-none');

            $(':checkbox').each(function () {
                this.checked = false;
            });
        }
    });

    window.deleteMultipleOrders = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function (id, index) {
                APIDELETECALLER(ORDER_DELETE_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    toastr['error']('Order has not been deleted');
                });
            });
        });

    };

    bootstrapSelectAction.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleOrders();
                break;
            default:
        }
    });

    window.selectOrder = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    let swalText = function (params) {
        let text = '<div class="col-12 d-flex flex-wrap justify-content-center">';

        if (Array.isArray(params)) {
            params.forEach(function (name, index) {
                text += `<p class="font-weight-bold m-0">${index !== params.length - 1 ? name + ', ' : name}</p>`;
            });
        } else {
            text += `<p class="font-weight-bold m-0">${params}</p>`;
        }

        text += '</div>';

        return text;
    };

    let confirmAction = function (title, message, confirmButtonText, cancelButtonText, callback) {
        Swal.fire({
            title: title,
            html: message,
            icon: 'warning',
            background: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    };

    window.payment = function(e) {
        modal.modal('show');
        let order =$(e).attr('order-id');
        let price = $(e).attr('order-price');
        let customer = $(e).attr('customer-id');

        modal.find('form input[name="price"]').val(price);
        modal.find('form input[name="order_id"]').val(order);
        modal.find('form input[name="customer_id"]').val(customer);
    }

    $('.modalCloseBtn').on('click', function () {
        modal.modal('hide');
    });

    payButton.on('click',function(e){
        e.preventDefault();
        
        let dateInput = payForm.find('input[name="date_of_payment"]');
        let priceInput = payForm.find('input[name="price"]');
        let orderInput = payForm.find('input[name="order_id"]');

        let date = moment(dateInput.val()).format('YYYY-MM-DD');
        let price = parseFloat(priceInput.val()).toLocaleString('en-US');
        let orderId = parseInt(orderInput.val());

        let url = ORDER_PAY_ROUTE.replace(':id', orderId);

        APIPOSTCALLER(url,{'price':price,'date':date},function(response){
            toastr['success'](response.message);
            table.DataTable().ajax.reload();
            modal.modal('hide');
        },function(error){
            toastr['error'](error.message);
        })

    })

});