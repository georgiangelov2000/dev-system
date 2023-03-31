import { APICaller, APICallerWithoutData } from './ajaxFunctions';

$(document).ready(function () {
    let table = $('#customersTable');

    $('.selectAction').selectpicker();
    $('.selectCountry').selectpicker();
    $('.selectState').selectpicker();

    let selectCountry = $('.bootstrap-select .selectCountry');
    let selectState = $('.bootstrap-select .selectState');

    let dataT = table.DataTable({
        ajax: {
            url: CUSTOMER_API_ROUTE
        },
        columns: [
            {
                orderable: false,
                width: "1%",
                render: function (data, type, row) {
                    let checkbox = '<div div class="form-check">\n\
                       <input name="checkbox" class="form-check-input" onclick="selectCustomer(this)" data-id=' + row.id + ' data-name= ' + row.name + ' type="checkbox"> \n\
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
                name: "image",
                render: function (data, type, row) {
                    if (row.image) {
                        return "<img class='img-thumbnail rounded mx-auto d-block w-100' src=" + row.image.path + row.image.name + " />"
                    } else {
                        return "<img class='img-thumbnail rounded mx-auto d-block w-100' src='https://leaveitwithme.com.au/wp-content/uploads/2013/11/dummy-image-square.jpg'/>";
                    }
                }
            },
            {
                width: '10%',
                orderable: false,
                name: "name",
                data: "name"
            },
            {
                width: '10%',
                orderable: false,
                name: "email",
                data: "email",
            },
            {
                width: '10%',
                orderable: false,
                name: "phone",
                data: "phone"
            },
            {
                width: '10%',
                orderable: false,
                name: "address",
                data: "address"
            },
            {
                width: '7%',
                orderable: false,
                name: "website",
                data: "website"
            },
            {
                width: '6%',
                orderable: false,
                name: "zip",
                data: "zip"
            },
            {
                width: '5%',
                orderable: false,
                name: "country",
                render: function (data, type, row) {
                    return row.country ? row.country.name : "";
                }
            },
            {
                width: '5%',
                orderable: false,
                name: 'state',
                render: function (data, type, row) {
                    return row.state ? row.state.name : "";
                }
            },
            {
                width: '15%',
                orderable: false,
                name: "notes",
                render: function (data, type, row) {
                    return "<div class='notes'>" + row.notes + "</div>"
                }
            },
            {
                orderable: false,
                width: '20%',
                render: function (data, type, row) {
                    let deleteButton = '<a data-id=' + row.id + ' onclick="deleteCurrentCustomer(this)" data-name=' + row.name + ' class="btn p-1" title="Delete"><i class="fa-solid fa-trash-can text-danger"></i></a>';
                    let editButton = '<a data-id=' + row.id + ' href="" class="btn p-1" title="Edit"><i class="fa-solid fa-pen text-warning"></i></a>';
                    return `${deleteButton} ${editButton}`;
                }
            }

        ],
        lengthMenu: [[10], [10]],
        pageLength: 10,
        order: [[1, 'asc']]
    });

    //ACTIONS

    selectCountry.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let countryId = $(this).val();
        let selectState = $('.bootstrap-select .selectState');
        selectState.empty();

        $('.selectCategory').val('').selectpicker('refresh');

        APICaller(CUSTOMER_API_ROUTE, { "country": countryId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();

                APICaller(STATE_ROUTE.replace(":id", countryId), function (response) {
                    if (response.length !== 0) {
                        selectState.append('<option value="">All</option>');
                        $.each(response, function (key, value) {
                            selectState.append('<option value=' + value.id + '>' + value.name + '</option>');
                        });
                    } else {
                        selectState.append('<option value="" disabled>Nothing selected</option>');
                    }
                    selectState.selectpicker('refresh');
                }, function (error) {
                    console.log(error);
                });
            };
        }, function (error) {
            console.log(error);
        });
    });


    selectState.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let stateId = $(this).val();
        $('.selectCategory').val('').selectpicker('refresh');

        let countryId = $('select.selectCountry')
            .find('option:checked')
            .val();

        APICaller(CUSTOMER_API_ROUTE, { "country": countryId, 'state': stateId }, function (response) {
            if (response && response.data) {
                table.DataTable().rows().remove();
                table.DataTable().rows.add(response.data).draw();
            }
        }, function (error) {
            console.log(error);
        });
    });

    window.selectCustomer = function (e) {
        if ($('tbody input[type="checkbox"]:checked').length === 0) {
            $('.actions').addClass('d-none');
        } else {
            $('.actions').removeClass('d-none');
        }
    };

    window.deleteCurrentCustomer = function (e) {
        let id = $(e).attr('data-id');
        let name = $(e).attr('data-name');
        let url = CUSTOMER_DELETE_ROUTE.replace(':id', id);

        let template = swalText(name);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            APICallerWithoutData(url, function (response) {
                toastr['success'](response.message);
                table.DataTable().ajax.reload();
            }, function (error) {
                toastr['error']('Customer has not been deleted');
            });
        });
    };

    window.deleteMultipleCustomers = function (e) {

        let searchedIds = [];
        let searchedNames = [];

        $('tbody input[type="checkbox"]:checked').map(function () {
            searchedIds.push($(this).attr('data-id'));
            searchedNames.push($(this).attr('data-name'));
        });

        let template = swalText(searchedNames);

        confirmAction('Selected items!', template, 'Yes, delete it!', 'Cancel', function () {
            searchedIds.forEach(function(id,index){
                APICallerWithoutData(CUSTOMER_DELETE_ROUTE.replace(':id', id), function (response) {
                    toastr['success'](response.message);
                    table.DataTable().ajax.reload();
                }, function (error) {
                    console.log(error);
                    toastr['error']('Customer has not been deleted');
                });
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

     $('.bootstrap-select .selectAction').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        switch ($(this).val()) {
            case 'delete':
                deleteMultipleCustomers();
                break;
            default:
        }
    });

    let swalText = function (params) {
        let text = '<div class="col-12 d-flex flex-wrap justify-content-center">';

        if (Array.isArray(params)) {
            params.forEach(function (name, index) {
                text += `<p class="font-weight-bold m-0">${index !== params.length - 1 ? name + ', ' : name }</p>`;
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

});