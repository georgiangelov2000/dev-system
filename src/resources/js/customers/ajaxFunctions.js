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
        contentType: 'application/json',
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