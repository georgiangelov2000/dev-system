$(function(){
    let table = $('#logsTable');

    var dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: LOG_API_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    "search": d.search.value,
                    'order_dir': d.order[0].dir,
                    'limit': d.custom_length = d.length,
                });
            }
        },
        columns: [
            {
                width: '0%',
                name: "id",
                orderable: true,
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${row.id}</span>`;
                }
            },
            {
                width: '2%',
                name: "action",
                orderable: false,
                class:"text-center",
                render: function (data, type, row) {
                    let action = row.action;
                    let col = getActionColor(action);
                    
                    return `<span class="text-${col}">${row.action}</span>`;
                }
            },
            {
                width: '2%',
                name: "message",
                orderable: false,
                class:"text-center",
                render: function (data, type, row) {
                    let action = row.action;
                    let col = getActionColor(action);
                    
                    return `<span class="text-${col}">${row.message}</span>`;
                }
            },
            {
                width: '2%',
                name: "username",
                orderable: false,
                class:"text-center",
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${row.user.username}</span>`;
                }
            },
            {
                width: '2%',
                name: "created_at",
                orderable: false,
                class:"text-center",
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${moment(row.created_at).format('Y-MM-DD H:mm:ss')}</span>`;
                }
            },
            {
                width: '2%',
                name: "updated_at",
                orderable: false,
                class:"text-center",
                render: function (data, type, row) {
                    return `<span class='font-weight-bold'>${moment(row.updated_at).format('Y-MM-DD H:mm:ss')}</span>`;
                }
            }
        ],
        
        order: [[0, 'asc']]
    });

    function getActionColor(action) {
        const lowercaseAction = action.toLowerCase();
    
        if (lowercaseAction.includes('created') || lowercaseAction.includes('updated')) {
            return 'primary';
        } else if (lowercaseAction.includes('deleted')) {
            return 'danger';
        } else {
            // Return a default color or handle other cases as needed
            return 'secondary';
        }
    }

})