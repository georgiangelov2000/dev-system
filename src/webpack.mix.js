const mix = require('laravel-mix');

mix.js('resources/js/ajax/methods.js', 'public/js/ajax'),
mix.js('resources/js/ajax/leaflet.js','public/js/ajax');

mix.js('resources/js/users/users.js', 'public/js/users'),
mix.js('resources/js/users/form.js', 'public/js/users'),

mix.js('resources/js/categories/categories.js', 'public/js/categories'),
mix.js('resources/js/subcategories/subcategories.js', 'public/js/subcategories'),

mix.js('resources/js/brands/brands.js', 'public/js/brands'),

mix.js('resources/js/helpers/action_helpers.js', 'public/helpers/js'),
mix.js('resources/js/helpers/render_helpers.js', 'public/js/helpers'),

mix.js('resources/js/settings/settings.js', 'public/js/settings'),
mix.js('resources/js/settings/e-mail.js', 'public/js/settings'),

mix.js('resources/js/suppliers/form.js', 'public/js/suppliers'),
mix.js('resources/js/suppliers/suppliers.js', 'public/js/suppliers'),
mix.js('resources/js/suppliers/mass_edit_purchases.js', 'public/js/suppliers'),

mix.js('resources/js/purchases/form.js', 'public/js/purchases'),
mix.js('resources/js/purchases/purchases.js', 'public/js/purchases'),
mix.js('resources/js/purchases/payments.js', 'public/js/purchases'),

mix.js('resources/js/customers/form.js', 'public/js/customers'),
mix.js('resources/js/customers/customers.js', 'public/js/customers'),

mix.js('resources/js/orders/form.js', 'public/js/orders'),
mix.js('resources/js/orders/orders.js', 'public/js/orders'),
mix.js('resources/js/orders/payments.js', 'public/js/orders'),

mix.js('resources/js/packages/form.js', 'public/js/packages'),
mix.js('resources/js/packages/packages.js', 'public/js/packages'),

mix.js('resources/js/summaries/customer_summary.js', 'public/js/summaries'),
mix.js('resources/js/summaries/supplier_summary.js', 'public/js/summaries'),

mix.js('resources/js/payments/customer_payments.js', 'public/js/payments'),
mix.js('resources/js/payments/supplier_payments.js', 'public/js/payments'),

mix.js('resources/js/packages/customer_package_payment.js', 'public/js/packages'),

mix.js('resources/js/reports/reports.js', 'public/js/reports');

// Javascript libraries
mix.babel('resources/js/adminlte.min.js', 'public/js/adminlte.min.js'),
mix.babel('resources/js/app.js', 'public/js/app.js'),
mix.babel('resources/js/bootstrap.js', 'public/js/bootstrap.js'),
mix.babel('resources/js/jquery.min.js', 'public/js/jquery.min.js'),
mix.babel('resources/js/jqueryui.min.js', 'public/js/jqueryui.min.js'),
mix.babel('resources/js/toastr.min.js', 'public/js/toastr.min.js'),
mix.babel('resources/js/bootstrap.bundle.min.js', 'public/js/bootstrap.bundle.min.js'),
mix.babel('resources/js/sweetalert2.min.js', 'public/js/sweetalert2.min.js'),
mix.babel('resources/js/datatables.min.js', 'public/js/datatables.min.js'),
mix.babel('resources/js/bootstrap-select-min.js', 'public/js/bootstrap-select-min.js'),
mix.babel('resources/js/datepicker.min.js', 'public/js/datepicker.min.js'),
mix.babel('resources/js/moment.min.js', 'public/js/moment.min.js'),
mix.babel('resources/js/daterangepicker.min.js', 'public/js/daterangepicker.min.js'),
mix.babel('resources/js/jspdf.min.js', 'public/js/jspdf.min.js'),
mix.babel('resources/js/dataTables.buttons.min.js', 'public/js/dataTables.buttons.min.js'),
mix.babel('resources/js/buttons.print.min.js', 'public/js/buttons.print.min.js'),
mix.babel('resources/js/jszip.min.js', 'public/js/jszip.min.js'),
mix.babel('resources/js/pdfmake.min.js', 'public/js/pdfmake.min.js'),
mix.babel('resources/js/leaflet.js', 'public/js/leaflet.js'),
mix.babel('resources/js/lodash.min.js', 'public/js/lodash.min.js')
mix.babel('resources/js/buttons.html5.min.js', 'public/js/buttons.html5.min.js')
mix.babel('resources/js/buttons.print.min.js', 'public/js/buttons.print.min.js')
mix.babel('resources/js/jquery.fileDownload.min.js', 'public/js/jquery.fileDownload.min.js')
.version();

mix.sass('resources/sass/_variables.scss', 'public/css')
    .sass('resources/sass/all.min.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/template.scss', 'public/css')
    .sass('resources/sass/toastr.min.scss', 'public/css')
    .sass('resources/sass/sweetalert2.scss', 'public/css')
    .sass('resources/sass/datatables.min.scss', 'public/css')
    .sass('resources/sass/bootstrap-select.min.scss', 'public/css')
    .sass('resources/sass/datepicker.min.scss', 'public/css')
    .sass('resources/sass/daterangepicker.min.scss', 'public/css')
    .sass('resources/sass/icheck-bootstrap.min.scss', 'public/css')
    .version();
