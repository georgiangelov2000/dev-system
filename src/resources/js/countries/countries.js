import { APICaller } from "../ajax/methods";

$(function () {
    let table = $("table#countries");

    let dataTable = table.DataTable({
        serverSide: true,
        ajax: {
            url: API_COUNTRY_ROUTE,
            data: function (d) {
                return $.extend({}, d, {
                    search: d.search.value,
                    order_dir: d.order[0].dir,
                });
            },
        },
        columns: [
            {
                orderable: true,
                name: "id",
                render: function (data, type, row) {
                    return `${row.id}`;
                },
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return `<i class="flag-icon flag-icon-${row.short_name.toLowerCase()}"> </i>`;
                },
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return `${row.name}`;
                },
            },
            {
                orderable: true,
                render: function (data, type, row) {
                    return `${row.customers_count}`;
                },
            },
            {
                orderable: true,
                render: function (data, type, row) {
                    return `${row.suppliers_count}`;
                },
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return `<a data-toggle="collapse" data-target="cities_${row.id}" type="button" title="Towns" class="btn p-1" onclick=showCities(this) value="${row.id}">
                        <i class="fa-light fa-city text-primary"></i>
                    </a>`;
                },
            },
        ],
    });

    var cachedData = {};

    window.showCities = function (button) {
        var rowId = $(button).attr("value");
        var tr = $(button).closest("tr");
        var row = dataTable.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass("shown");
        } else {
            if (cachedData[rowId]) {
                // Use cached data to populate the subtable
                row.child(subTable(cachedData[rowId])).show();
                tr.addClass("shown");
            } else {
                // Make API call if data is not cached
                APICaller(
                    API_STATES,
                    { country_id: rowId },
                    function (response) {
                        // Store API response data in the cache variable
                        cachedData[rowId] = response;
                        // Populate subtable with API response data
                        row.child(subTable(response)).show();
                        tr.addClass("shown");
                    },
                    function (error) {
                        // Handle API error if needed
                        console.error(error);
                    }
                );
            }
        }
    };

    function subTable(data) {
        let tableRows = "";

        if (data.length) {
            data.forEach(function (city) {
                tableRows += "<tr>" + "<td>" + city.name + "</td>" + "</tr>";
            });
            return (
                '<table class="subTable cities w-100" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                "<thead>" +
                "<tr>" +
                "<th>Name</th>" +
                "</tr>" +
                "</thead>" +
                "<tbody>" +
                tableRows +
                "</tbody>" +
                "</table>"
            );
        } else {
            return false;
        }
    }
});
