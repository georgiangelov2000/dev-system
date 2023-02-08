$(document).ready(function () {
    let inc = 0;

    $('#addModalForm').click(function () {

        $('.modal-content')
                .find('.modal-form:first')
                .clone(true)
                .appendTo('#categoryForm');

        incrementing($(this).attr('id'));

        if (inc != 0) {
            $('.modal-content').find('#removeModalForm').removeClass('d-none');
        } else {
            $('.modal-content').find('#removeModalForm').addClass('d-none');
        }

    });

    $('#removeModalForm').click(function () {
        $('.modal-content').find('.modal-form').last().remove();

        incrementing($(this).attr('id'));

        if (inc != 0) {
            $('.modal-content').find('#removeModalForm').removeClass('d-none');
        } else {
            $('.modal-content').find('#removeModalForm').addClass('d-none');
        }
    });

    function incrementing(param) {

        if (param == 'addModalForm') {
            inc++
        } else if (param == 'removeModalForm') {
            inc--
        }
        return inc;
    }
    
});