$(function () {
  let searchAddress = $('#searchAddress');
  let addresses = $('.addresses');

  $('select[name="gender"],select[name="role_id"]').selectpicker();

  $('.datepicker').datepicker({
    format: 'mm/dd/yyyy'
  });

  $('#image').on('change', function () {
    let fileName = $(this).val().split('\\').pop();
    $('#fileLabel').text(fileName || 'Choose file');
  });

  searchAddress.on('click', function () {
    var url = 'https://nominatim.openstreetmap.org/search';
    var query = $('input[name="address"]').val();
    $.ajax({
      url: url,
      method: 'GET',
      data: {
        q: query,
        format: 'json',
        addressdetails: 5,
        limit: 5
      },
      success: function (response) {
        console.log(response);
        if (response.length > 0) {
          var template = '<ul class="pl-3">';
          response.forEach(function (currentElement) {
            template += '<li title="Apply" onclick="applyAddress(this)" class="list-unstyled" data-latitude="' + currentElement.lat + '" data-longitude="' + currentElement.lon + '"><a class="text-primary" type="button">' + currentElement.display_name + '<a/></li>';
          });
          template += '</ul>';
          addresses.html(template);
        } else {
          addresses.html('<p class="text-danger pl-3"> No results found. </p>');
        }
      },
      error: function (error) {
        console.log(error)
      }
    });
  })

  window.applyAddress = function (e) {
    $('input[name="address"]').val($(e).text());
    var latitude = parseFloat($(e).data('latitude'));
    var longitude = parseFloat($(e).data('longitude'));
    setMapView([latitude, longitude], 15);
  }

  $('#pdf').on('change', function () {
    $('#pdfMockUpImage').remove();
    const file = this.files[0];
    if (file) {
      const fileReader = new FileReader();

      fileReader.onload = function () {
        $('#pdfPreviewContainer').css('display', 'block');
        $('#pdfPreview').attr('data', this.result);
      };

      fileReader.readAsDataURL(file);
    } else {
      $('#pdfPreviewContainer').css('display', 'none');
      $('#pdfPreview').removeAttr('data');
    }
  });

})