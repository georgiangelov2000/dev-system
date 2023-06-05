const mix = require('laravel-mix');

mix.js('resources/js/ajax/methods.js', 'public/js/ajax/'),
mix.js('resources/js/ajax/leaflet.js','public/js/ajax');

mix.js('resources/js/categories/categories.js', 'public/js'),
mix.js('resources/js/subcategories/subcategories.js', 'public/js'),
mix.js('resources/js/brands/brands.js', 'public/js'),

mix.js('resources/js/helpers/action_helpers.js', 'public/helpers/js'),
mix.js('resources/js/helpers/render_helpers.js', 'public/js/helpers'),

mix.js('resources/js/settings/settings.js', 'public/js/settings'),
mix.js('resources/js/settings/e-mail.js', 'public/js/settings'),

mix.js('resources/js/suppliers/form.js', 'public/js/suppliers'),
mix.js('resources/js/suppliers/suppliers.js', 'public/js/suppliers'),

mix.js('resources/js/purchases/form.js', 'public/js/purchases'),
mix.js('resources/js/purchases/purchases.js', 'public/js/purchases'),

mix.js('resources/js/customers/form.js', 'public/js/customers'),
mix.js('resources/js/customers/customers.js', 'public/js/customers'),
mix.js('resources/js/customers/payments.js', 'public/js/customers'),

mix.js('resources/js/orders/form.js', 'public/js/orders'),
mix.js('resources/js/orders/orders.js', 'public/js/orders'),

mix.js('resources/js/packages/form.js', 'public/js/packages'),
mix.js('resources/js/packages/packages.js', 'public/js/packages'),

mix.js('resources/js/summaries/customer_summary.js', 'public/js/summaries'),
mix.js('resources/js/summaries/supplier_summary.js', 'public/js/summaries'),

mix.js('resources/js/payments/customer_payments.js', 'public/js/payments'),
mix.js('resources/js/payments/supplier_payments.js', 'public/js/payments'),

// Javascript libraries
mix.js('resources/js/adminlte.min.js', 'public/js'),
mix.js('resources/js/app.js', 'public/js'),
mix.js('resources/js/bootstrap.js', 'public/js'),
mix.js('resources/js/jquery.min.js', 'public/js'),
mix.js('resources/js/jqueryui.min.js', 'public/js'),
mix.js('resources/js/toastr.min.js', 'public/js'),
mix.js('resources/js/bootstrap.bundle.min.js', 'public/js'),
mix.js('resources/js/sweetalert2.min.js', 'public/js'),
mix.js('resources/js/datatables.min.js', 'public/js'),
mix.js('resources/js/bootstrap-select-min.js', 'public/js'),
mix.js('resources/js/datepicker.min.js', 'public/js'),
mix.js('resources/js/moment.min.js', 'public/js'),
mix.js('resources/js/daterangepicker.min.js', 'public/js')
.version();

mix.sass('resources/sass/_variables.scss', 'public/css'),
mix.sass('resources/sass/all.min.scss', 'public/css'),
mix.sass('resources/sass/app.scss', 'public/css'),
mix.sass('resources/sass/template.scss', 'public/css'),
mix.sass('resources/sass/toastr.min.scss', 'public/css'),
mix.sass('resources/sass/sweetalert2.scss', 'public/css'),
mix.sass('resources/sass/datatables.min.scss', 'public/css'),
mix.sass('resources/sass/bootstrap-select.min.scss', 'public/css'),
mix.sass('resources/sass/datepicker.min.scss', 'public/css'),
mix.sass('resources/sass/daterangepicker.min.scss', 'public/css'),
mix.sass('resources/sass/icheck-bootstrap.min.scss', 'public/css')
.version();