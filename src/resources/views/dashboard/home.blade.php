@extends('app')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Dashboard</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Packages</h5>
                            </div>
                            <div class="col-6 d-flex flex-column align-self-center">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Air:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ground: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Sea: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <h6 class="mb-0">Last month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Air:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ground: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Sea: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
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
                                <a class="font-weight-bold" href="">More info</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Orders</h5>
                            </div>
                            <div class="col-6 d-flex flex-column align-self-center">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                <small>150</small> / <small>15.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ordered: </strong>
                                            <span>
                                                <small>120</small> / <small>12.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Received: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <h6 class="mb-0">Last month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Ordered:</strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                        <div>
                                            <strong>Received: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
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
                                <a class="font-weight-bold" href="">More info</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="col-12 d-flex flex-wrap rounded border border-primary p-0">
                            <div class="col-12 p-2 border-bottom border-primary">
                                <h5 class="text-primary mb-0">Purchases</h5>
                            </div>
                            <div class="col-6 d-flex flex-column">
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">This month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 mt-2">
                                    <h6 class="mb-0">Last month</h6>
                                    <div class="ml-2 mt-2">
                                        <div>
                                            <strong>Total: </strong>
                                            <span>
                                                <small>100</small> / <small>10.05%</small>
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
                                <a class="font-weight-bold" href="">More info</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Top 5 customers</h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-tool btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Price</th>
                                            <th>Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Some Product
                                            </td>
                                            <td>$13 USD</td>
                                            <td>
                                                <small class="text-success mr-1">
                                                    <i class="fas fa-arrow-up"></i>
                                                    12%
                                                </small>
                                                12,000 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Another Product
                                            </td>
                                            <td>$29 USD</td>
                                            <td>
                                                <small class="text-warning mr-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                    0.5%
                                                </small>
                                                123,234 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Amazing Product
                                            </td>
                                            <td>$1,230 USD</td>
                                            <td>
                                                <small class="text-danger mr-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                    3%
                                                </small>
                                                198 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Perfect Item
                                                <span class="badge bg-danger">NEW</span>
                                            </td>
                                            <td>$199 USD</td>
                                            <td>
                                                <small class="text-success mr-1">
                                                    <i class="fas fa-arrow-up"></i>
                                                    63%
                                                </small>
                                                87 Sold
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Top 5 Suppliers</h3>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-tool btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Some Product
                                            </td>
                                            <td>$13 USD</td>
                                            <td>
                                                <small class="text-success mr-1">
                                                    <i class="fas fa-arrow-up"></i>
                                                    12%
                                                </small>
                                                12,000 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Another Product
                                            </td>
                                            <td>$29 USD</td>
                                            <td>
                                                <small class="text-warning mr-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                    0.5%
                                                </small>
                                                123,234 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Amazing Product
                                            </td>
                                            <td>$1,230 USD</td>
                                            <td>
                                                <small class="text-danger mr-1">
                                                    <i class="fas fa-arrow-down"></i>
                                                    3%
                                                </small>
                                                198 Sold
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="dist/img/default-150x150.png" alt="Product 1"
                                                    class="img-circle img-size-32 mr-2">
                                                Perfect Item
                                                <span class="badge bg-danger">NEW</span>
                                            </td>
                                            <td>$199 USD</td>
                                            <td>
                                                <small class="text-success mr-1">
                                                    <i class="fas fa-arrow-up"></i>
                                                    63%
                                                </small>
                                                87 Sold
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

@endsection
