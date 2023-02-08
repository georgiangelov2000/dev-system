$(document).ready(function () {
    $('#submitForm').click(function (e) {
        e.preventDefault();

        var form = $('#categoryForm');
        var actionUrl = form.attr('action');

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(),
            dataType: 'json',
            success: function (response)
            {
                toastr['success'](response.message);

                setTimeout(function () {
                    location.reload(true);
                }, 5000);
            },
            error: function (error) {
                console.log(error)
            }
        });
    })
});