@extends('app')

@section('content')
    <div id="dashboard" class="row">
        <div class="card col-12">
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 d-flex align-items-center justify-content-between">
                                <h5>Company information</h5>
                                <i class="fas fa-lg fa-briefcase text-primary"></i>
                            </div>
                            <hr class="w-100 mt-1 mb-2">
                        </div>
                        <div class="col-md-12 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Web server: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['web_server'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Http User Agent: </span>
                                    <span
                                        class="info-box-number mt-0">{{ Str::limit($dashboard['server_information']['http_user_agent'], 20) }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Protocol: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['server_protocol'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">PHP Version: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['php_version'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">OS: </span>
                                    <span class="info-box-number mt-0">{{ $dashboard['server_information']['os'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Architecture: </span>
                                    <span class="info-box-number mt-0">{{ $dashboard['server_information']['ar'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 d-flex align-items-center justify-content-between">
                                <h5>Server information</h5>
                                <i class="fas fa-lg fa-server text-primary"></i>
                            </div>
                            <hr class="w-100 mt-1 mb-2">
                        </div>
                        <div class="col-md-12 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Web server: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['web_server'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Http User Agent: </span>
                                    <span
                                        class="info-box-number mt-0">{{ Str::limit($dashboard['server_information']['http_user_agent'], 20) }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Protocol: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['server_protocol'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">PHP Version: </span>
                                    <span
                                        class="info-box-number mt-0">{{ $dashboard['server_information']['php_version'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">OS: </span>
                                    <span class="info-box-number mt-0">{{ $dashboard['server_information']['os'] }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span class="info-box-text">Architecture: </span>
                                    <span class="info-box-number mt-0">{{ $dashboard['server_information']['ar'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 d-flex align-items-center justify-content-between">
                                <h5>Dashboard</h5>
                                <i class="fas fa-lig fa-chart-line text-primary"></i>
                            </div>
                            <hr class="w-100 mt-1 mb-2">
                        </div>

                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <div class="d-flex">
                                    <span class="info-box-icon bg-aqua"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Customers</span>
                                        <span class="info-box-number">250</span>
                                    </div>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <div class="d-flex">
                                    <span class="info-box-icon"><i class="fas text-green fa-truck"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Drivers</span>
                                        <span class="info-box-number">50</span>
                                    </div>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-yellow fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Orders</span>
                                    <span class="info-box-number">1000</span>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-red fa-money-bill-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Purchases</span>
                                    <span class="info-box-number">750</span>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-purple fa-box"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Packages</span>
                                    <span class="info-box-number">350</span>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-orange fa-industry"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Suppliers</span>
                                    <span class="info-box-number">25</span>
                                </div>
                                <a href="#" class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12 d-flex align-items-center justify-content-between">
                                <h5>Statistics</h5>
                                <i class="fas fa-lg fa-chart-bar text-primary"></i>
                            </div>
                            <hr class="w-100 mt-1 mb-2">
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 mb-5 d-flex">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="border-0 pt-0 pb-0">
                                    <h6 class="mb-0">Sales</h6>
                                </div>
                                <canvas id="myChart"></canvas>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 p-0">
                                <h6 class="mb-3">
                                    Top Selling Products
                                </h6>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        <li class="item">
                                            <div class="product-img">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                    alt="Product Image" class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">Samsung TV
                                                    <span class="badge badge-warning float-right">$1800</span></a>
                                                <span class="product-description">
                                                    Samsung 32" 1080p 60Hz LED Smart HDTV.
                                                </span>
                                            </div>
                                        </li>
                                        <li class="item">
                                            <div class="product-img">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                    alt="Product Image" class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">Samsung TV
                                                    <span class="badge badge-warning float-right">$1800</span></a>
                                                <span class="product-description">
                                                    Samsung 32" 1080p 60Hz LED Smart HDTV.
                                                </span>
                                            </div>
                                        </li>
                                        <li class="item">
                                            <div class="product-img">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                    alt="Product Image" class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">Bicycle
                                                    <span class="badge badge-info float-right">$700</span></a>
                                                <span class="product-description">
                                                    26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                                                </span>
                                            </div>
                                        </li>

                                        <li class="item">
                                            <div class="product-img">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                    alt="Product Image" class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    Xbox One <span class="badge badge-danger float-right">
                                                        $350
                                                    </span>
                                                </a>
                                                <span class="product-description">
                                                    Xbox One Console Bundle with Halo Master Chief Collection.
                                                </span>
                                            </div>
                                        </li>

                                        <li class="item">
                                            <div class="product-img">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                    alt="Product Image" class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">PlayStation 4
                                                    <span class="badge badge-success float-right">$399</span></a>
                                                <span class="product-description">
                                                    PlayStation 4 500GB Console (PS4)
                                                </span>
                                            </div>
                                        </li>

                                    </ul>
                                </div>

                                <div class="card-footer text-center">
                                    <a href="javascript:void(0)" class="uppercase">View All Products</a>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0">Top categories</h6>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-sm table-hover table-valign-middle">
                                        <thead>
                                            <tr>
                                                <th>Icon</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th class="text-center">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>
                                                    $13
                                                </td>
                                                <td class="text-center">
                                                    <b>12,000</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$29</td>
                                                <td class="text-center">
                                                    <b>123,234</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$1,230</td>
                                                <td class="text-center">
                                                    <b>198</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$199</td>
                                                <td class="text-center">
                                                    <b>87</b> Sold
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0">Top Suppliers</h6>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-sm table-hover table-valign-middle">
                                        <thead>
                                            <tr>
                                                <th>Icon</th>
                                                <th>Supplier</th>
                                                <th>Amount</th>
                                                <th class="text-center">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$13</td>
                                                <td class="text-center">
                                                    <b>12,000</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$29</td>
                                                <td class="text-center">
                                                    <b>123,234</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$1,230</td>
                                                <td class="text-center">
                                                    <b>198</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$199</td>
                                                <td class="text-center">
                                                    <b>87</b> Sold
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0">Top Customers</h6>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-sm table-hover table-valign-middle">
                                        <thead>
                                            <tr>
                                                <th>Icon</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th class="text-center">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$13</td>
                                                <td class="text-center">
                                                    <b>12,000</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$29</td>
                                                <td class="text-center">
                                                    <b>123,234</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$1,230</td>
                                                <td class="text-center">
                                                    <b>198</b> Sold
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="pt-1 pb-1">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        alt="Product Image" class="img-size-50">
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="product-title font-weight-bold">Samsung TV</a>
                                                </td>
                                                <td>$199</td>
                                                <td class="text-center">
                                                    <b>87</b> Sold
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
            <script type="text/javascript">
                const ctx = document.getElementById('myChart');

                // Data for each month (example data)
                const monthlyData = [12, 19, 3, 5, 2, 3, 8, 15, 9, 7, 20, 10]; // Replace with your data

                const monthNames = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthNames,
                        datasets: [{
                            label: '# of Votes',
                            data: monthlyData,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        @endpush
    @endsection
