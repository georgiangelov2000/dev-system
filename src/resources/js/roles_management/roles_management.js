$(function(){
    let table = $('#rolesManagеmentTable');

    let dataTable = table.DataTable({ 
        serverSide: true,
        ajax: {
            url: ROLES_MANAGEMENT_API_ROUTE,
            data: function (d) {
                var orderColumnIndex = d.order[0].column; 
                var orderColumnName = d.columns[orderColumnIndex].name; 

                return $.extend({}, d, {
                    "search": d.search.value,
                    'order_column': orderColumnName,
                    'order_dir': d.order[0].dir,
                    'limit': d.custom_length = d.length,
                });
            }
        },
        columns: [
            {
                orderable: false,
                width: '1%',
                render: function (data, type, row) {
                    return `${row.id}`
                }
            },
            {
                orderable: false,
                width: '5%',
                render: function (data, type, row) {
                    return `${row.name}`
                }
            },
            {
                orderable: false,
                width: '5%',
                render: function (data, type, row) {
                    const permissions = (row.roles_access_management || []).map(accessManagement => `<b class="text-primary">${accessManagement.access}</b>`).join(', ');
                    return permissions;                    
                }
            },
            {
                orderable: false,
                width: '5%',
                render: function (data, type, row) {
                    let editButton = `<a href="${ROLES_MANAGEMENT_EDIT_ROUTE.replace(':id',row.id)}"><i class="fa-light fa-pen text-primary"></i></a>`
                    return editButton;
                }
            },
        ],
        order: [[0, 'asc']]
    });
})