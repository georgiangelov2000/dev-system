$(function () {
    let exportButtons = $('button[data-export]');


    exportButtons.on('click', function (e) {
        e.preventDefault();

        let attributes = {};

        let data = $(this).closest('form').serialize();
        let url = $(this).closest('form').attr('action');

        data.split('&').forEach(function (val, index) {
            let pair = val.split('=');
            let key = decodeURIComponent(pair[0]);
            let value = decodeURIComponent(pair[1]);

            if (key === 'month') {
                attributes[key] = value;
            } else {
                if (attributes[key]) {
                    if (Array.isArray(attributes[key])) {
                        attributes[key].push(value);
                    } else {
                        attributes[key] = [value];
                    }
                } else {
                    attributes[key] = [value];
                }
            }

        });

        attributes['type_export'] = $(this).attr('data-type_export');
        attributes['data_export'] = $(this).attr('data-export');
        
        console.log(attributes);

        if (attributes.options.length) {
            let options = attributes.options;
            options.forEach(element => {
                console.log(element);
                $.ajax({
                    method: "POST",
                    url: url,
                    data: {
                        'type_export':attributes.type_export,
                        'data_export':attributes.data_export,
                        'options':element,
                        'month':attributes.month
                    },
                    success: function (response) {
                        if (response.download_url) {
                            $.fileDownload(response.download_url)
                                .done(function () {
                                    console.log('Download successful');
                                })
                                .fail(function () {
                                    console.log('Download failed');
                                });
                        } else {
                            console.error('Download URL not found in the response.');
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                })
            });
        }

    })

})