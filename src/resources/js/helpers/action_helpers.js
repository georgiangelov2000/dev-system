/* global Swal, CATEGORY_ROUTE */
import {
    APIPOSTCALLER,
} from '../ajax/methods';

export function openModal(modal, url, data = null) {
    const form = modal.find('form');
    form.attr('action', url);
    
    if (data) {
      const inputName = form.find('input[name="name"]');
      inputName.val(data);
    }
    modal.modal('show');
  }

export function closeModal(modal) {
    modal.modal('hide');
}

export function swalText(params) {
    let text = '<div class="col-12 d-flex flex-wrap justify-content-center">';

    if (Array.isArray(params)) {
        params.forEach(function (name, index) {
            text += `<p class="font-weight-bold m-0">${index !== params.length - 1 ? name + ', ' : name}</p>`;
        });
    } else {
        text += `<p class="font-weight-bold m-0">${params}</p>`;
    }

    text += '</div>';

    return text;
};

export function submit(e, modal, table) {
    e.preventDefault();

    const form = modal.find('form');
    const url = form.attr('action');
    const data = form.serialize();

    APIPOSTCALLER(url, data, function (response,xhr) {
        let status = xhr;
        
        if (status == 'success') {
            toastr['success'](response.message);
            form.trigger('reset');
            modal.modal('toggle');
            table.DataTable().ajax.reload();
        } else {
            toastr['error'](response.responseJSON.message);
            ajaxResponse(response.responseJSON, modal);
        }
    },function(error) {
        console.log(error)
    });
};

export function ajaxResponse(obj, modal) {
    let form = modal.find('form');

    for (const [key, value] of Object.entries(obj.errors)) {
        console.log(key,value);
        form.find('span[id*=' + key + ']').removeClass('d-none').html(value);
        form.find(':input[name*=' + key + ']').addClass('is-invalid');

        setTimeout(function () {
            form.find('span[id*=' + key + ']').addClass('d-none').html('');
            form.find(':input[name*=' + key + ']').removeClass('is-invalid');
        }, 2000);
    }
};

export function showConfirmationDialog(title, template,callback) {
    Swal.fire({
      title: title,
      html: template,
      icon: 'warning',
      background: '#fff',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if(result.isConfirmed) {
            callback()
        }
    });
};

export function handleErrors(errors){
    $.each(errors, function(field, fieldErrors) {
        var errorSpan = $('span[name="' + field + '"]');
        errorSpan.text(fieldErrors[0]);
    });
}