import {APICaller} from '../ajax/methods';

$(function(){
    $('select[name="customer_id"],select[name="package_id"]').selectpicker()

    let bootstrapCustomer = $('.bootstrap-select select[name="customer_id"]');
    let bootstrapPackage = $('.bootstrap-select select[name="package_id"]');
    let btnSearch = $('#search');
    let table = $('#orders');

    let dataTable = table.DataTable({
        ordering: false,
    });
    
    bootstrapCustomer.on('changed.bs.select',function(e, clickedIndex, isSelected, previousValue){
        let customer = $(this).val();

        APICaller(PACKAGE_API_ROUTE,{
            'customer':customer,
            'select_json':true,
            'no_paid_orders':true 
        },function(response){
            let packages = response;
            if(packages.length > 0) {
                bootstrapPackage.append('<option value="" style="display:none;"></option>');

                $.each(packages, function ($key, pack) {
                    bootstrapPackage.append(`<option value="${pack.id}"> ${pack.package_name} </option>`)
                })
            }
            bootstrapPackage.selectpicker('refresh');
        },function(error){
            console.log(error);
        })
    })


    btnSearch.on('click',function(){
        let packId = bootstrapPackage.val();

        if(packId) {
            APICaller(ORDER_API_ROUTE,{
                'package':packId,
                'is_paid': 0,
                'select_json':true
            },function(response){
                loadDataTable(response);
            },function(error){
                console.log(error);
            })
        } else {
            toastr['error']('Please select package');
        }
        
    })

    function loadDataTable(data) {
        dataTable.clear().draw();
      
        if (data.length > 0) {
          let rows = data.map(function(item) {
            return [
              item.id,
              item.product.name,
              item.invoice_number,
              item.tracking_number,
              item.single_sold_price,
              `<input type="text" name="total_sold_price[]" class="form-control form-control-sm" value="${item.total_sold_price}">`,
              item.original_sold_price,
              `<input type="text" name="sold_quantity[]" class="form-control form-control-sm" value="${item.sold_quantity}">`,
              item.status,
              `
                <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" name="date_of_payment[]" class="form-control form-control-sm">
              `,
              item.date_of_sale
            ];
          });
      
          dataTable.rows.add(rows).draw();

          $('input[name="date_of_payment[]"]').datepicker({
            format: 'yyyy-mm-dd'
          });

        }
      }
      

})