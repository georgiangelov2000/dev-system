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
                                    @if (array_key_exists('company', $dashboard))
                                        <img class="img-fluid w-50" src="{{ $dashboard['company']['image_path'] }}"
                                            alt="">
                                    @else
                                    @endif
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Name: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['name'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Email: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['email'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Address: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['address'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Country:</span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['country'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>State: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['state'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Type: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['bussines_type'] : '' }}</span>
                                </div>
                                <div class="info-box-content flex-row align-items-center p-0">
                                    <span>Phone: </span>
                                    <span
                                        class="info-box-number mt-0">{{ array_key_exists('company', $dashboard) ? $dashboard['company']['phone_number'] : '' }}</span>
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
                                    @if ($dashboard['server_information']['os'] === 'Linux')
                                        <img class="img-fluid w-25" src="storage/images/static/linux.png"
                                            alt="Linux image" />
                                    @elseif($dashboard['server_information']['os'] === 'Windows')
                                        <img class="img-fluid w-25" src="storage/images/static/windows.png"
                                            alt="Window image" />
                                    @endif
                                </div>
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
                                        <span class="info-box-number">{{ $dashboard['customers'] }}</span>
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
                                        <span class="info-box-number">{{ $dashboard['drivers'] }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('user.index') }}"
                                    class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-yellow fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Orders</span>
                                    <span class="info-box-number">{{ $dashboard['orders'] }}</span>
                                </div>
                                <a href="{{ route('order.index') }}"
                                    class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-red fa-money-bill-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Purchases</span>
                                    <span class="info-box-number">{{ $dashboard['purchases'] }}</span>
                                </div>
                                <a href="{{ route('purchase.index') }}"
                                    class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-purple fa-box"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Packages</span>
                                    <span class="info-box-number">{{ $dashboard['packages'] }}</span>
                                </div>
                                <a href="{{ route('package.index') }}"
                                    class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box flex-wrap pl-0 pr-0 pb-0">
                                <span class="info-box-icon"><i class="fas text-orange fa-industry"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Suppliers</span>
                                    <span class="info-box-number">{{ $dashboard['suppliers'] }}</span>
                                </div>
                                <a href="{{ route('supplier.index') }}"
                                    class="small-box-footer w-100 bg-primary p-2 mt-2 text-center">More info
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
                                <h6 class="mb-3">
                                    Top Selling Drivers
                                </h6>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        @if (array_key_exists('top_selling_drivers', $dashboard))
                                            @if (count($dashboard['top_selling_drivers']))
                                                @foreach ($dashboard['top_selling_drivers'] as $item)
                                                    <li class="item">
                                                        <div class="product-img">
                                                            <img src="{{ $item['image'] }}" alt="User Image"
                                                                class="img-size-50">
                                                        </div>
                                                        <div class="product-info">
                                                            <a href="javascript:void(0)"
                                                                class="product-title">{{ $item['username'] }}
                                                                <span
                                                                    class="badge badge-warning float-right">${{ $item['total_price'] }}</span>
                                                                <br>
                                                                
                                                                <span class="badge badge-warning float-right">Amount:
                                                                    {{ $item['total_quantity'] }}</span>
                                                                    <br>
                                                                <span class="badge badge-warning float-right">Count:
                                                                    {{ $item['orders_count'] }}</span>
                                                            </a>
                                                            <span class="product-description">
                                                                {{ $item['phone'] }}
                                                            </span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        @else
                                        @endif
                                    </ul>
                                </div>

                                <div class="card-footer text-center">
                                    <a  href="{{ route('user.index') }}" class="uppercase">View All Users</a>
                                </div>

                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <h6 class="mb-3">
                                    Top Selling Purchases
                                </h6>
                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        @if (array_key_exists('top_selling_products', $dashboard))
                                            @if (count($dashboard['top_selling_products']))
                                                @foreach ($dashboard['top_selling_products'] as $item)
                                                    <li class="item">
                                                        <div class="product-img">
                                                            <img src="{{ $item['first_image'] }}" alt="Product Image"
                                                                class="img-size-50">
                                                        </div>
                                                        <div class="product-info">
                                                            <a href="javascript:void(0)"
                                                                class="product-title">{{ $item['name'] }}
                                                                <span
                                                                    class="badge badge-warning float-right">${{ $item['total_price'] }}</span>
                                                                <br>
                                                                <span class="badge badge-warning float-right">Amount:
                                                                    {{ $item['total_quantity'] }}</span>
                                                                <br>
                                                                <span class="badge badge-warning float-right">Count:
                                                                    {{ $item['orders_count'] }}</span>
                                                            </a>
                                                            <span class="product-description">
                                                                {{ $item['code'] }}
                                                            </span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        @else
                                        @endif
                                    </ul>
                                </div>

                                <div class="card-footer text-center">
                                    <a href="{{ route('purchase.index') }}" class="uppercase">View All Products</a>
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
                                                <th>Counts</th>
                                                <th class="text-center">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (array_key_exists('top_categories', $dashboard))
                                                @if (count($dashboard['top_categories']))
                                                    @foreach ($dashboard['top_categories'] as $item)
                                                        <tr>
                                                            <td class="pt-1 pb-1">
                                                                @if ($item['image_path'])
                                                                    <img src="{{ $item['image_path'] }}"
                                                                        alt="Category Image" class="img-size-50" />
                                                                @else
                                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                                        alt="Category Image" class="img-size-50" />
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a
                                                                    class="product-title font-weight-bold">{{ $item['name'] }}</a>
                                                            </td>
                                                            <td>
                                                                <b>{{ $item['products_count'] }}</b>
                                                            </td>
                                                            <td class="text-center">
                                                                <b>{{ $item['products_sum_total_price'] }}</b> USD
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
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
                                            @if (array_key_exists('top_suppliers', $dashboard))
                                                @if (count($dashboard['top_suppliers']))
                                                    @foreach ($dashboard['top_suppliers'] as $item)
                                                        <tr>
                                                            <td class="pt-1 pb-1">
                                                                @if ($item['image_path'])
                                                                    <img src="{{ $item['image_path'] }}"
                                                                        alt="Supplier image" class="img-size-50" />
                                                                @else
                                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                                        alt="Supplier image" class="img-size-50" />
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a
                                                                    class="product-title font-weight-bold">{{ $item['name'] }}</a>
                                                            </td>
                                                            <td>
                                                                <b>{{ $item['total_quantity'] }}</b>
                                                            </td>
                                                            <td class="text-center">
                                                                <b>{{ $item['total_price'] }}</b> USD
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
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
                                            @if (array_key_exists('top_customers', $dashboard))
                                                @if (count($dashboard['top_customers']))
                                                    @foreach ($dashboard['top_customers'] as $item)
                                                        <tr>
                                                            <td class="pt-1 pb-1">
                                                                @if ($item['image_path'])
                                                                    <img src="{{ $item['image_path'] }}"
                                                                        alt="Supplier image" class="img-size-50" />
                                                                @else
                                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                                        alt="Supplier image" class="img-size-50" />
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a
                                                                    class="product-title font-weight-bold">{{ $item['name'] }}</a>
                                                            </td>
                                                            <td>
                                                                <b>{{ $item['total_quantity'] }}</b>
                                                            </td>
                                                            <td class="text-center">
                                                                <b>{{ $item['total_price'] }}</b> USD
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endif
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
        @endpush
    @endsection
