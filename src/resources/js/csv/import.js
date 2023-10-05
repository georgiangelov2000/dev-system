$(function(){
    $('#fileInput').on('change', function(event) {
        const fileName = event.target.files[0].name;
        $(this).next('.custom-file-label').html(fileName);

        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const contents = e.target.result;
                const rows = contents.split('\n').map(row => row.split(','));

                const tableContainer = $('#tableContainer');
                const tableHTML = generateTableHTML(rows);

                if ($.fn.DataTable.isDataTable('#fileData')) {
                    $('#previewTable').DataTable().destroy();
                }

                tableContainer.html(tableHTML);

                $('#fileData').DataTable({
                    ordering:false
                });
            };

            reader.readAsText(file);
        }
    });

    function generateTableHTML(data) {
        if (!data || data.length === 0) {
            return '<p>No data available.</p>';
        }

        data.pop();
        let tableHTML = '<h5>Preview</h5><table id="fileData" class="table table-striped table-hover table-sm">';
        tableHTML += '<thead><tr>';

        data[0].forEach(header => {
            tableHTML += `<th>${header}</th>`;
        });

        tableHTML += '</tr></thead><tbody>';

        for (let i = 1; i < data.length; i++) {
            tableHTML += '<tr>';
            data[i].forEach(value => {
                tableHTML += `<td>${value}</td>`;
            });
            tableHTML += '</tr>';
        }
        tableHTML += '</tbody></table>';

        return tableHTML;
    }
});
