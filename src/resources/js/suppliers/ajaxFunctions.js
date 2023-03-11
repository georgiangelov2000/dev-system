export function getStates(url, callback) {
    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
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

export function deleteSupplier(url, callback) {
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

export function editSupplier(url, callback) {
    $.ajax({
        method: "GET",
        url: url,
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

export function deleteMultipleSuppliers(url, callback) {
    $.ajax({
        method: "GET",
        url: url,
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

export function detachSupplierCategory  (url, callback) {
    $.ajax({
        url: url,
        type: "GET",
        success: function (response)
        {
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

export function apiSuppliers(url,data,callback){
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