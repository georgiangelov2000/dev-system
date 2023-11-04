<div class="card col-12 p-0">
    <div class="card-header">
        <h3 class="card-title bg-muted">Summary statistics</h3>
        <div class="card-tools">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <ul class="col-6 todo-list pr-1">
                <h6 class="alert alert-dark mb-2">
                    <i class="fa-light fa-bookmark"></i>
                    Purchase payments
                </h6>
                @if (count($result['purchase_payments']))
                    @php
                        $payments = $result['purchase_payments'];
                    @endphp
                    @foreach ($payments as $key => $item)
                        <li class="border-bottom p-0 d-flex align-items-center p-1">
                            <span class="text">
                                {{ $item['status'] }}
                            </span>
                            <span class="ml-1 mr-1">:</span>
                            <span class="text text-primary m-0">
                                {{ $item['total_price'] }} $
                            </span>
                        </li>
                    @endforeach
                @endif
            </ul>
            <ul class="col-6 todo-list pr-1">
                <h6 class="alert alert-dark mb-2">
                    <i class="fa-light fa-cart-shopping"></i>
                    Order payments
                </h6>
                @if (count($result['order_payments']))
                    @php
                        $payments = $result['order_payments'];
                    @endphp
                    @foreach ($payments as $key => $item)
                        <li class="border-bottom p-0 d-flex align-items-center p-1">
                            <span class="text">{{ $item['status'] }}</span>
                            <span class="ml-1 mr-1">:</span>
                            <span class="text text-primary m-0">
                                {{ $item['total_price'] }} $
                            </span>
                        </li>
                    @endforeach
                @else
                    <li class="border-bottom p-0 d-flex align-items-center p-1">
                        <span class="text font-weight-normal">Data not found:</span>
                    </li>
                @endif
            </ul>
        </div>
        <div class="row">
            <ul class="col-6 todo-list pr-1">
                <h6 class="alert alert-dark mb-2">
                    <i class="fa-light fa-truck"></i>
                    Suppliers
                </h6>
                @if (count($result['suppliers']))
                    @php
                        $suppliers = $result['suppliers'];
                    @endphp
                    @foreach ($suppliers as $item)
                        <li class="border-bottom p-0 d-flex align-items-center p-1">
                            <span class="text font-weight-normal">Name:</span>
                            <span class="text mr-3">
                                {{ $item['name'] }}
                            </span>
                            <span class="text text-primary m-0">
                                <span class="text font-weight-normal">Price:</span>
                                {{ $item['total_price'] }} $
                            </span>
                        </li>
                    @endforeach
                @else
                    <li class="border-bottom p-0 d-flex align-items-center p-1">
                        <span class="text font-weight-normal">Data not found:</span>
                    </li>
                @endif
            </ul>
            <ul class="col-6 todo-list">
                <h6 class="alert alert-dark mb-2">
                    <i class="fa-light fa-user-plus" aria-hidden="true"></i>
                    Customers
                </h6>
                @if (count($result['customers']))
                    @php
                        $customers = $result['customers'];
                    @endphp
                    @foreach ($customers as $item)
                        <li class="border-bottom p-0 d-flex align-items-center p-1">
                            <span class="text font-weight-normal">Name:</span>
                            <span class="text mr-3">
                                {{ $item['name'] }}
                            </span>
                            <span class="text text-primary m-0">
                                <span class="text font-weight-normal">Price:</span>
                                {{ $item['total_price'] }} $
                            </span>
                        </li>
                    @endforeach
                @else
                    <li class="border-bottom p-0 d-flex align-items-center p-1">
                        <span class="text font-weight-normal">Data not found:</span>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
