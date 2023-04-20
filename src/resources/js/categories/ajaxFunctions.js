export function APICaller(url,data,callback){
    $.ajax({
        method: 'GET',
        url: url,
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

export function APIPOSTCALLLER(url, data, successCallback, errorCallback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function (response)
        {
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        },
        error: function (error) {
            if (typeof errorCallback === 'function') {
                errorCallback(error);
            }
        }
    });
}