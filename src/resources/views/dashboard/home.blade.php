@extends('app')

@section('content')

    @php
        $order_data_this_month = $dashboard_data['orders']['this_month'];
        $order_data_previous_month = $dashboard_data['orders']['previous_month'];
        
        $packages_data_this_month = $dashboard_data['packages']['this_month'];
        $packages_data_previous_month = $dashboard_data['packages']['previous_month'];
        
        $products_data_this_month = $dashboard_data['products']['this_month'];
        $products_data_previous_month = $dashboard_data['products']['previous_month'];
        
        $top_five_customers = $dashboard_data['top_five_customers'];

        $orders_sum_by_status = $dashboard_data['grouped_orders_sum'];
        $products_sum_by_status = $dashboard_data['grouped_products_sum'];
    @endphp

    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Dashboard</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="dashboardBox col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Packages</h5>
                            </div>
                            <div class="col-6 d-flex flex-column statisticsColumn align-self-center">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total:</strong>
                                            {{ $packages_data_this_month['counts'] ?? 0 }}
                                        </div>
                                        <div>
                                            <strong>Air:</strong>
                                            <span>
                                                {{ $packages_data_this_month['by_status']['Air'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ground:</strong>
                                            <span>
                                                {{ $packages_data_this_month['by_status']['Ground'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Sea:</strong>
                                            <span>
                                                {{ $packages_data_this_month['by_status']['Sea'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <h6 class="mb-0">Previous month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total:</strong>
                                            <span>
                                                {{ $packages_data_previous_month['counts'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Air:</strong>
                                            <span>
                                                {{ $packages_data_previous_month['by_status']['Air'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ground:</strong>
                                            <span>
                                                {{ $packages_data_previous_month['by_status']['Ground'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Sea:</strong>
                                            <span>
                                                {{ $packages_data_previous_month['by_status']['Sea'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <img class="staticDashboardImages" src="/storage/images/static/720.jpg" title="Packages"
                                    alt="Packages">
                            </div>
                            <div class="col-12 text-center bg-primary p-2">
                                <a class="d-block" href="{{route('package.index')}}" class="font-weight-bold" href="">More info</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="dashboardBox col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Orders</h5>
                            </div>
                            <div class="col-6 d-flex flex-column statisticsColumn align-self-center">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                {{ $order_data_this_month['counts'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Received: </strong>
                                            <span>
                                                {{ $order_data_this_month['by_status']['Received']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ordered: </strong>
                                            <span>
                                                {{ $order_data_this_month['by_status']['Ordered']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Pending: </strong>
                                            <span>
                                                {{ $order_data_this_month['by_status']['Pending']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <h6 class="mb-0">Previous month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                {{ $order_data_previous_month['counts'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Received: </strong>
                                            <span>
                                                {{ $order_data_previous_month['by_status']['Received']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ordered: </strong>
                                            <span>
                                                {{ $order_data_previous_month['by_status']['Ordered']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Pending: </strong>
                                            <span>
                                                {{ $order_data_previous_month['by_status']['Pending']['count'] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <img class="staticDashboardImages" src="/storage/images/static/29126.jpg" title="Orders"
                                    alt="Orders">
                            </div>
                            <div class="col-12 text-center bg-primary p-2">
                                <a class="font-weight-bold d-block" href="{{route('order.index')}}">More info</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="dashboardBox col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Purchases</h5>
                            </div>
                            <div class="col-6 d-flex flex-column statisticsColumn">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                {{ $products_data_this_month['counts'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">Previous month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                {{ $products_data_previous_month['counts'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <img class="staticDashboardImages" src="/storage/images/static/20943859.jpg"
                                    title="Puchases" alt="Puchases">
                            </div>
                            <div class="col-12 text-center bg-primary p-2">
                                <a class="font-weight-bold d-block" href="{{route('purchase.index')}}">More info</a>
                            </div>
                        </div>
                    </div>
                </div>

                @php 
                   $paidProducts = $products_sum_by_status['paid'] ?? 0;
                   $notPaidProducts = $products_sum_by_status['not_paid'] ?? 0;
                   $productSumByStatus = $products_sum_by_status['total'] ?? 0; 

                   $paidOrders = $orders_sum_by_status['Received'] ?? 0;
                   $orderedOrders = $orders_sum_by_status['Ordered'] ?? 0;
                   $pendingOrders = $orders_sum_by_status['Pending'] ?? 0;
                @endphp

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="cardTemplate rounded mb-0">
                            <div class="card-footer rounded bg-white">
                                <div class="row">
                                    <div class="col-sm-4 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header">
                                                 @if(is_numeric($paidProducts) && is_numeric($paidOrders))
                                                    @if($paidOrders > $paidProducts )
                                                        @php
                                                            $diff = ($paidOrders - $paidProducts);
                                                            $formattedDiff = number_format($diff, 2, '.', '.');
                                                        @endphp
                                                        <span class="text-success">
                                                           + €{{$formattedDiff}}
                                                        </span>
                                                    @elseif($paidOrders < $paidProducts)
                                                        @php
                                                            $diff = ($paidProducts - $paidOrders);
                                                            $formattedDiff = number_format($diff, 2, '.', ',');
                                                        @endphp
                                                        {{$formattedDiff}}
                                                    @elseif($paidProducts === $paidOrders)
                                                        0
                                                    @endif
                                                 @endif
                                            </h5>
                                            <span class="description-text">TOTAL BALANCE</span>
                                        </div>

                                    </div>

                                    <div class="col-sm-4 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header">
                                                €{{$paidProducts ? number_format($paidProducts, 2, '.', '.') : 0}}
                                            </h5>
                                            <span class="description-text">PAID PURCHASES</span>
                                        </div>

                                    </div>

                                    <div class="col-sm-4 col-6">
                                        <div class="description-block border-right">
                                            <h5 class="description-header">
                                                €{{$paidOrders ? number_format($paidOrders, 2, '.', '.') : 0}}
                                            </h5>
                                            <span class="description-text">PAID ORDERS</span>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-6">
                        <div class="cardTemplate rounded">
                            <div class="card-header">
                                <h3 class="card-title">Top 5 Customers</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Orders</th>
                                            <th>Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($top_five_customers))
                                            @foreach ($top_five_customers as $key => $data)
                                                <tr>
                                                    <td>
                                                        {{ $data['customer_id'] }}
                                                    </td>
                                                    <td>
                                                        {{ $key }}
                                                    </td>
                                                    <td>
                                                        {{ $data['customer_email'] }}
                                                    </td>
                                                    <td>
                                                        {{ $data['customer_phone'] }}
                                                    </td>
                                                    <td>{{ $data['orders_count'] }}</td>
                                                    <td>
                                                        €{{ number_format($data['total_price'], 2, '.', '.') }}
                                                    </td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    Data are not available
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="cardTemplate rounded">
                            <div class="card-header border-0">
                                <h3 class="card-title">Online Store Overview</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                                    <p class="text-lg">
                                        <i title="Orders review" class="fa-light fa-bookmark"></i>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i>€{{$paidOrders ? number_format($paidOrders, 2, '.', '.') : 0}}
                                        </span>
                                        <span class="text-muted">Received</span>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i>€{{$orderedOrders ? number_format($orderedOrders, 2, '.', '.') : 0}}
                                        </span>
                                        <span class="text-muted">Ordered</span>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-up text-success"></i> €{{$pendingOrders ? number_format($pendingOrders, 2, '.', '.') : 0}} 
                                        </span>
                                        <span class="text-muted">Pending</span>
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="text-lg">
                                        <i title="Purchase reviews" class="fa-light fa-cart-shopping"></i>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-down text-danger"></i> €{{$productSumByStatus ? number_format($productSumByStatus, 2, '.', '.') : 0}}
                                        </span>
                                        <span class="text-muted">Total</span>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-down text-danger"></i> €{{$paidProducts ? number_format($paidProducts, 2, '.', '.') : 0}}
                                        </span>
                                        <span class="text-muted">Paid</span>
                                    </p>
                                    <p class="d-flex flex-column text-right">
                                        <span class="font-weight-bold">
                                            <i class="ion ion-android-arrow-down text-danger"></i> €{{$notPaidProducts ? number_format($notPaidProducts, 2, '.', '.') : 0}}
                                        </span>
                                        <span class="text-muted">Not paid</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
