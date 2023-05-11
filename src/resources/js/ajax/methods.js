export function APIPOSTCALLER(url, data, successCallback, errorCallback) {
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

export function APIDELETECALLER(url, callback) {
    $.ajax({
        url: url,
        method: "POST",
        dataType: 'json',
        data: {
            _method: 'DELETE'
        },
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
