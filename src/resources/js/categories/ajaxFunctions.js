export function createData(url, data, successCallback, errorCallback) {
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


export function updateData(url, data, successCallback, errorCallback) {
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