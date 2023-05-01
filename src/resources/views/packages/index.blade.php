@extends('app')
@section('title', 'Packages')

@section('content')
    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">Purchases</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p class="bg-dark p-2 font-weight-bold filters">
                            <i class="fa-solid fa-filter"></i> Filters
                        </p>
                    </div>
                    <div class="col-3 actions d-none">
                        <div class="form-group">
                            <label>Actions</label>
                            <select class="form-control selectAction">
                                <option value="0">Select Option</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="delievery_type">Package</label>
                            <select class="form-control selectPackageType" name="" id="delievery_type">
                                <option value="">All</option>
                                @foreach (config('statuses.package_types') as $key => $package)
                                    <option value="{{$key}}">
                                        {{$package}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="delievery_method">Delievery</label>
                            <select class="form-control selectDelieveryMethod" name="" id="delievery_method">
                                <option value="">All</option>
                                @foreach (config('statuses.delievery_methods') as $key => $delievery)
                                    <option value="{{$key}}">
                                        {{$delievery}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="delievery_method">Customers</label>
                            <select class="form-control selectCustomer" name="" id="select_customer">
                                <option value="">All</option>
                                @foreach ($customers as $key => $customer)
                                    <option value="{{$customer->id}}">
                                        {{$customer->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Legend:</h6>
                    </div>
                    <div class="col-12">
                            <i title="Air" class="fa-light fa-plane"></i>
                            <span>-</span>
                            <span>The chosen method for delivering the package is air.</span>
                    </div>
                    <div class="col-12">
                        <i title="Ground" class="fa-light fa-truck"></i>
                        <span>-</span>
                        <span>The chosen method for delivering the package is ground.</span>
                    </div>
                    <div class="col-12">
                        <i title="Sea" class="fa-light fa-water"></i>
                        <span>-</span>
                        <span>The chosen method for delivering the package is sea.</span>
                    </div>
                </div>
                <table id="packagesTable" class="table table-hover table-sm dataTable no-footer">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input selectAll" type="checkbox">
                                    <label class="form-check-label" for="flexCheckDefault"></label>
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Package name</th>
                            <th>Tracking number</th>
                            <th>Package type</th>
                            <th>Delievery method</th>
                            <th>Package price</th>
                            <th>Delievery Date</th>
                            <th>Orders</th>
                            <th>Customer notes</th>
                            <th>Package notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>  

    @push('scripts')
        <script type="text/javascript" src="{{mix('js/packages/packages.js')}}"></script>
        <script type="text/javascript">
            let PACKAGE_API_ROUTE = "{{route('api.packages')}}"
            let PACKAGE_UPDATE_STATUS_ROUTE = "{{route('package.status',':id')}}"
            let PACKAGE_DELETE_ROUTE = "{{route('package.delete',':id')}}"
            let PACKAGE_EDIT_ROUTE = "{{route('package.edit',':id')}}"
            console.log(PACKAGE_UPDATE_STATUS_ROUTE);
        </script>
    @endpush

@endsection
