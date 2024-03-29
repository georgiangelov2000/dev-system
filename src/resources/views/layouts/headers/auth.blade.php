<nav class="main-header navbar navbar-expand header-navigation mb-3 navbar-white">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link burgerLink btn" data-widget="pushmenu" href="#" role="button"><i
                    class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-flex align-items-center">
            <div
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; border-radius: 8px; padding: 10px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                @php
                    $user = Auth::user();
                    $email = $user->email;
                    $role = $user->role;
                @endphp
                <span style="color: #555;">
                    <b>User:</b> {{ $email }}
                </span>

                <span style="color: #555;">
                    {{-- <b>Role:</b> {{ $role }} --}}
                </span>
            </div>
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
                <li class="nav-item">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                        alt="Company logo" id="company-logo" />
                </li>

                @can('access-profile')
                    <li class="nav-item">
                        <a href="{{ route('profile.index') }}" class="nav-link">
                            <i class="fa-light fa-user"></i>
                            <p>My profile</p>
                        </a>
                    </li>
                @endcan
                @can('access-country')
                    <li class="nav-item">
                        <a href="{{ route('country.index') }}" class="nav-link">
                            <i class="fal fa-flag"></i>
                            <p>Countries</p>
                        </a>
                    </li>
                @endcan
                @can('access-role-management')
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link">
                            <i class="fa-light fa-lock"></i>
                            <p>Role managment</p>
                        </a>
                    </li>
                @endcan
                @can('access-staff-members')
                    <li class="nav-item">
                        <a href="{{ route('user.index') }}" class="nav-link">
                            <i class="fa fa-light fa-users"></i>
                            <p>
                                Staff
                            </p>
                        </a>
                    </li>
                @endcan
                @can('access-general')
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
                @endcan
                @can('access-product-widgets')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fa-light fa-box"></i>
                            <p>
                                Product Widgets
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
                @endcan
                @can('access-supplier-management')
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
                @endcan
                @can('access-customer-management')
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
                @endcan
                @can('access-purchase-management')
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
                                <a href="{{ route('purchases.index') }}" class="nav-link">
                                    <p>Purchases</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('purchases.create') }}" class="nav-link">
                                    <p>Create purchase</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('access-package-management')
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
                                <a href="{{ route('packages.index') }}" class="nav-link">
                                    <p>Packages</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('packages.create') }}" class="nav-link">
                                    <p>Create package</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('packages.form.operations') }}" class="nav-link">
                                    <p>Operations</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @can('access-orders-management')
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
                                    <a href="{{ route('orders.index') }}" class="nav-link">
                                        <p>Orders</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('orders.create') }}" class="nav-link">
                                        <p>Create orders</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('access-payments-management')
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
                                    <a href="{{ route('payment.index', 'order') }}" class="nav-link">
                                        <p>Order payments</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('payment.index', 'purchase') }}" class="nav-link">
                                        <p>Purchase payments</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('access-invoices-management')
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fal fa-file-invoice"></i>
                                <p>
                                    Invoices
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="{{ route('payment.index', 'order') }}" class="nav-link">
                                        <p>Order invoices</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('payment.index', 'purchase') }}" class="nav-link">
                                        <p>Purchase invoices</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    @can('access-reports-management')
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fa-light fa-flag"></i>
                                <p>
                                    Reports
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="{{ route('settings.get') }}" class="nav-link">
                                        <p></p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                    <li class="nav-item">
                        @can('access-imports-management')
                            <a href="#" class="nav-link">
                                <i class="fa-light fa-file-import"></i>
                                <p>
                                    Imports
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display: none;">
                                <li class="nav-item">
                                    <a href="{{ route('import.index', 'supplier') }}" class="nav-link">
                                        <p>Suppliers</p>
                                    </a>
                                    <a href="{{ route('import.index', 'customer') }}" class="nav-link">
                                        <p>Customers</p>
                                    </a>
                                    <a href="{{ route('import.index', 'purchase') }}" class="nav-link">
                                        <p>Purchases</p>
                                    </a>
                                    <a href="{{ route('import.index', 'category') }}" class="nav-link">
                                        <p>Categories</p>
                                    </a>
                                    </a>
                                    <a href="{{ route('import.index', 'brand') }}" class="nav-link">
                                        <p>Brands</p>
                                    </a>
                                </li>
                            </ul>
                        @endcan
                        @can('access-logs-management')
                        <li class="nav-item">
                            <a href="{{ route('logs.index') }}" class="nav-link">
                                <i class="fa-light fa-clock-rotate-left"></i>
                                <p>Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logs.index') }}" class="nav-link">
                                <i class="fa-light fa-desktop" aria-hidden="true"></i>
                                <p>System logs</p>
                            </a>
                        </li>
                    @endcan
                    @can('access-settings-management')
                        <li class="nav-item">
                            <a href="{{ route('settings.get') }}" class="nav-link">
                                <i class="fa fa-light fa-cog" aria-hidden="true"></i>
                                <p>
                                    Settings
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('access-notifications-management')
                        <li class="nav-item">
                            <a href="{{ route('country.index') }}" class="nav-link">
                                <i class="fa-light fa-bell"></i>
                                <p>Notifications</p>
                            </a>
                        </li>
                    @endcan
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
