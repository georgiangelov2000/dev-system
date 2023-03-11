export function apiGetCategories(url,data,callback){
    $.ajax({
        method: 'POST',
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