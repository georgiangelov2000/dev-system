export function APIPOSTCALLER(url, data, callback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Let the browser set the content type
        success: function (response,xhr) {
            if (typeof callback === 'function') {
                callback(response,xhr);
            }
        },
        error: function (error) {
            if (typeof callback === 'function') {
                callback(error);
            }
        }
    });
}

export function APICaller(url,data,callback){
    if (typeof data === 'function') {
      callback = data;
      data = undefined;
    }    
    $.ajax({
        url: url,
        method: 'GET',
        data:data,
        success: function (response) {
            if (typeof callback === 'function') {
                callback(response);
            }
        },
        error: function (error) {
            if (typeof callback === 'function') {
                callback(error);
            }
        }
    })
}

export function APICallerWithoutData(url, callback) {
    $.ajax({
        url: url,
        method: "GET",
        success: function (response) {
            if (typeof callback === 'function') {
                callback(response);
            }
        },
        error: function (error) {
            if (typeof callback === 'function') {
                callback(error);
            }
        }
    });
}

export function APIDELETECALLER(url, data, callback) {
    if (typeof data === 'function') {
        callback = data;
        data = undefined;
    }

    if (data === undefined || data === null) {
        sendRequest();
    } else {
        if (!Array.isArray(data)) {
            data = [data];
        }

        var successCount = 0;
        var errorCount = 0;

        data.forEach(function (item) {
            var requestData = {
                _method: 'DELETE'
            };

            // Check if the item has a custom key name
            if (typeof item === 'object' && item.hasOwnProperty('keyName') && item.hasOwnProperty('value')) {
                requestData[item.keyName] = item.value;
            } else {
                requestData.id = item;
            }

            $.ajax({
                url: url,
                method: "POST",
                dataType: 'json',
                data: requestData,
                success: function (response) {
                    successCount++;
                    if (typeof callback === 'function') {
                        callback(response);
                    }
                    checkCompletion();
                },
                error: function (error) {
                    errorCount++;
                    if (typeof callback === 'function') {
                        callback(error);
                    }
                    checkCompletion();
                }
            });
        });

        function checkCompletion() {
            if (successCount + errorCount === data.length) {
                // All requests have been processed
                // Perform any necessary actions
                console.log('All requests completed');
            }
        }
    }

    function sendRequest() {
        var requestData = {
            _method: 'DELETE'
        };

        $.ajax({
            url: url,
            method: "POST",
            dataType: 'json',
            data: requestData,
            success: function (response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function (error) {
                if (typeof callback === 'function') {
                    callback(error);
                }
            }
        });
    }
}
