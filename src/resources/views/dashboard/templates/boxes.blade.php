<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['orders_count'] }}</h3>
            <p>Orders</p>
        </div>
        <div class="icon">
            <i class="fa-light fa-bookmark"></i>
        </div>
        <a href="{{ route('orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['purchase_count'] }}</h3>
            <p>Purchases</p>
        </div>
        <div class="icon">
            <i class="fa-light fa-cart-shopping"></i>
        </div>
        <a href="{{ route('purchases.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['suppliers_count'] }}</h3>
            <p>Suppliers</p>
        </div>
        <div class="icon">
            <i class="fa-light fa-truck"></i>
        </div>
        <a href="{{ route('supplier.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['customers_count'] }}</h3>
            <p>Customers</p>
        </div>
        <div class="icon">
            <i class="fa-light fa-user-plus" aria-hidden="true"></i>
        </div>
        <a href="{{ route('customer.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['packages_count'] }}</h3>
            <p>Packages </p>
        </div>
        <div class="icon">
            <i class="fa-light fa-boxes-packing" aria-hidden="true"></i>
        </div>
        <a href="{{ route('packages.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['categories_count'] }}</h3>
            <p>Categories </p>
        </div>
        <div class="icon">
            <i class="fa-light fa-list" aria-hidden="true"></i>
        </div>
        <a href="{{ route('category.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['purchase_payments_count'] }}</h3>
            <p>Purchase payments </p>
        </div>
        <div class="icon">
            <i class="fa-light fa-credit-card"></i>
        </div>
        <a href="{{ route('purchases.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>

<div class="col-lg-3 col-6">
    <div class="small-box bg-white">
        <div class="inner">
            <h3>{{ $stats['order_payments_count'] }}</h3>
            <p>Order payments </p>
        </div>
        <div class="icon">
            <i class="fa-light fa-credit-card"></i>
        </div>
        <a href="{{ route('purchases.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
</div>