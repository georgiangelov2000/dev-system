export function apiGetCategoriesForSupplier(url,data,callback){
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

export function deleteProduct(url, callback) {
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