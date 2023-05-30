<nav class="main-header navbar navbar-expand header-navigation mb-3 navbar-white">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link burgerLink" data-widget="pushmenu" href="#" role="button"><i
                    class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <button title="Calculator" class="btn btn-dark">
                <i class="fa-light fa-calculator-simple"></i>
            </button>
        </li>
        <li class="nav-item ml-2">
            <button title="Change style" class="btn btn-dark">
                <i class="fa-light fa-frame"></i>
            </button>
        </li>
        <li class="nav-item ml-2">
            <button title="Language" class="btn btn-dark">
                <i class="fa-light fa-globe"></i>
            </button>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-sm-inline-block">
            <span href="#" class="nav-link bg-danger rounded">
                <i title="User" class="fa-light fa-user"></i> {{ $user !== false ? $user : '' }}
            </span>
        </li>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <li class="nav-item">
                <button type="submit" title="Logout" class="nav-link btn btn-link pt-0 pb-0">
                    <i class="fa fa-light fa-sign-in-alt"></i>
                </button>
            </li>
        </form>
    </ul>
</nav>
<aside class="main-sidebar elevation-4 navbar-white">


    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li  class="nav-item">
                        <img 
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png" 
                            alt="Company logo"
                            id="company-logo"
                            >
                    </li>
                    <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-house"></i>
                        <p>
                            General
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-box"></i>
                        <p>
                            Product widgets
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('category.index') }}" class="nav-link">
                                <p>Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('subcategory.index') }}" class="nav-link">
                                <p>Subcategories</p>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('brand.index') }}" class="nav-link">
                                <p>Brands</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-truck"></i>
                        <p>
                            Supplier Managment
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('supplier.index') }}" class="nav-link">
                                <p>Suppliers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('supplier.create') }}" class="nav-link">
                                <p>Create supplier</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-user-plus" aria-hidden="true"></i>
                        <p>
                            Customer Managment
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('customer.index') }}" class="nav-link">
                                <p>Customers</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('customer.create') }}" class="nav-link">
                                <p>Create customer</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-cart-shopping"></i>
                        <p>
                            Purchase Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('purchase.index') }}" class="nav-link">
                                <p>Purchases</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('purchase.create') }}" class="nav-link">
                                <p>Create purchase</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-boxes-packing"></i>
                        <p>
                            Package Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('package.index') }}" class="nav-link">
                                <p>Packages</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('package.create') }}" class="nav-link">
                                <p>Create package</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-bookmark"></i>
                        <p>
                            Order Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}" class="nav-link">
                                <p>Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('order.create') }}" class="nav-link">
                                <p>Create order</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-bar-chart" aria-hidden="true"></i>
                        <p>
                            Summaries
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('summary.customer') }}" class="nav-link">
                                <p>Customer summary</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('summary.supplier') }}" class="nav-link">
                                <p>Supplier summary</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-light fa-credit-card"></i>
                        <p>
                            Payments
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('payment.customer') }}" class="nav-link">
                                <p>Customer payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('order.create') }}" class="nav-link">
                                <p>Company payments</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fal fa-analytics"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('order.index') }}" class="nav-link">
                                <p>Company reports</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa fa-light fa-cog" aria-hidden="true"></i>
                        <p>
                            Settings
                             <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('settings.company') }}" class="nav-link">
                                <p>Company info</p>
                            </a>
                            <a href="{{ route('settings.email') }}" class="nav-link">
                                <p>Send E-mails</p>
                            </a>
                            <a href="{{ route('settings.server') }}" class="nav-link">
                                <p>Server information</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
