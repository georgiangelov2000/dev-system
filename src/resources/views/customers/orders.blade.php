@extends('app')
@section('title', 'Customer orders')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">
                        Orders {{$orders['customer_name']}}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                @if (isset($orders['data']) && count($orders['data']) )
                    <form method="POST" action="{{route('customer.update.orders')}}">
                        @csrf
                        @method('PUT')
                            <table id="customerOrders" class="table table-hover table-sm dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tracking number</th>
                                        <th>Name</th>
                                        <th>Single sold price</th>
                                        <th>Sold quantity</th>
                                        <th>Total sold price</th>
                                        <th>Total markup</th>
                                        <th>Single markup</th>
                                        <th>Regular price</th>
                                        <th>Product price</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach ($orders['data'] as $order)
                                            <tr>
                                                <td>
                                                    {{ $order['id'] }}
                                                    <input 
                                                        type="hidden" 
                                                        name="order_ids[]" 
                                                        value="{{$order['id']}}"
                                                    />
                                                </td>
                                                <td>
                                                    <span>
                                                        {{$order['tracking_number']}}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-sm" 
                                                        name="name[]"
                                                        value="{{ $order['name'] }}">
                                                </td>
                                                <td>
                                                    <input 
                                                        type="text" 
                                                        class="form-control form-control-sm" 
                                                        name="single_sold_price[]"
                                                        value="{{ $order['single_sold_price'] }}">
                                                        @error('single_sold_price.' . $loop->index)
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                        class="form-control form-control-sm" 
                                                        name="sold_quantity[]"
                                                        value="{{ $order['sold_quantity'] }}">
                                                    @error('sold_quantity.' . $loop->index)
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                                <td>
                                                    @if($order['total_sold_price'])
                                                        <span class="text-success">
                                                            + {{$order['total_sold_price']}}
                                                        </span>
                                                    @elseif(!$order['total_sold_price'] || $order['total_sold_price'] === $order['product_price'])
                                                        <span>
                                                            {{$order['total_sold_price']}}
                                                        </span>
                                                    @endif
                                                    <span>
                                                        €
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success">
                                                        + {{$order['total_markup']}} 
                                                    </span>
                                                    <span>
                                                        €
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success">
                                                        + {{$order['single_markup']}} 
                                                    </span>
                                                    <span>
                                                        €
                                                    </span>
                                                </td>
                                                <td>
                                                    <span>
                                                        {{$order['regular_price']}} €
                                                    </span>
                                                </td>
                                                <td>
                                                    <span>
                                                        {{$order['product_price']}} €
                                                    </span>
                                                </td>
                                                <td>
                                                    <span>
                                                        {{$order['discount']}}%
                                                    </span>
                                                    <input 
                                                        type="hidden" 
                                                        name="discount[]" 
                                                        value="{{$order['discount']}}"
                                                    />
                                                </td>
                                                <td>
                                                    <span>
                                                        @if($order['status'] === 'Received')
                                                            <i title="Reveived" class="fa-light fa-check"></i>
                                                        @elseif(($order['status'] === 'Pending'))
                                                            <i title="Pending" class="fa-light fa-loader"></i>
                                                        @elseif($order['status'] === 'Ordered')
                                                            <i title="Ordered" class="fa-light fa-truck"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <th>
                                                    @if($order['is_paid'])
                                                        <span class="text-success">Yes</span>
                                                    @else
                                                        <span class="text-danger">No</span>
                                                    @endif
                                                </th>
                                            </tr>
                                        @endforeach
                                </tbody>
                            </table>
                            <button class="btn btn-primary" type="submit">
                                Save changes
                            </button>
                    </form>
                @else
                    <span class="text-danger m-0">Orders are not available!</span>
                @endif
            </div>
        </div>
    </div>



    @push('scripts')
        <script type="text/javascript"></script>
    @endpush

@endsection
