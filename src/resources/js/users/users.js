$(function () {

    $('select[name="role_id"]').selectpicker();

    let table = $('table#users');
    let bootstrapRole = $('.bootstrap-select  select[name="role_id"]');

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: API_USER_ROUTE,
            data: function (d) {
                let orderColumnIndex = d.order[0].column; // Get the index of the column being sorted
                let orderColumnName = d.columns[orderColumnIndex].name; // Retrieve the name of the column using the index

                return $.extend({}, d, {
                    "search": d.search.value,
                    'limit': d.custom_length = d.length,
                    'order_column': orderColumnName, // send the column name being sorted
                    'order_dir': d.order[0].dir, // send the sorting direction (asc or desc)
                    'role_id': bootstrapRole.val()
                })
            }
        },
        columns: [
            {
                orderable: false,
                width: "0%",
                class: 'text-center',
                render: function (data, type, row) {
                    return `${row.online ? '<i class="fa-light fa-toggle-on text-success"></i>' : '<i class="fa-light fa-toggle-on text-danger"></i>'}`;
                }
            },
            {
                orderable: true,
                width: "1%",
                name: "id",
                class: 'text-center',
                render: function (data, type, row) {
                    return `<strong>${row.id}</strong>`
                }
            },
            {
                orderable: false,
                width: "2%",
                name: "last_seen",
                class: 'text-center',
                render: function (data, type, row) {
                    return `<strong>${row.last_seen ?? ""}</strong>`
                }
            },
            {
                orderable: false,
                width: '2%',
                name: "image",
                class:'text-center',
                render: function (data, type, row) {

                    if (row.image_path) {
                        return `<img class="rounded mx-auto w-100" src="${row.image_path}" />`
                    } else {
                        return "<img class='rounded mx-auto w-100' src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png'/>";
                    }
                }
            },
            {
                orderable: false,
                width: '1%',
                name: "email",
                render: function (data, type, row) {
                    return `<a href="mailto:${row.email}">${row.email}</a>`;
                }
            },
            {
                orderable: false,
                width: '1%',
                render: function (data, type, row) {
                    return `<b>${row.role ? row.role.name : ""}</b>`;
                }
            },
            {
                orderable: false,
                width: '1%',
                render: function (data, type, row) {
                    return `${row.username}`;
                }
            },
            {
                orderable: false,
                width: '2%',
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>${row.phone ?? ''}</span>`;
                }
            },
            {
                orderable: false,
                width: '5%',
                class:'text-center',
                render: function (data, type, row) {
                    return `<span>${row.address ?? ''}</span>`;
                }
            },
            {
                orderable: false,
                width: '1%',
                class:'text-center',
                render: function (data, type, row) {
                    let pdfLink = row.pdf_file_path ? `<a class='truncated-link' href="${row.pdf_file_path}" target="_blank">Overview</a>` : '';
                    return `${pdfLink}`;
                }
            },
            {
                orderable: false,
                width:'3%',
                class:'text-center',
                render: function(data,type,row) {
                    let editButton = '<a data-id=' + row.id + ' href=' + EDIT_USER_ROUTE.replace(":id", row.id) + ' class="btn p-0" title="Edit"><i class="fa-light fa-pen text-primary"></i></a>';
                    return `${editButton}`;
                }
            }
        ]
    });

    bootstrapRole.bind('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        dataTable.ajax.reload(null, false);
    })

})