@extends('app')
@section('title', 'Customer orders')

@section('content')

    <div class="row">
        <div class="card col-12 cardTemplate">
            <div class="card-header">
                <div class="col-12">
                    <h3 class="card-title">
                        Orders {{ $result->customer_name }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                @if (isset($result->orders) && count($result->orders))
                    <form method="POST" action="{{ route('customer.update.orders') }}">
                        @csrf
                        @method('PUT')
                        <table id="customerOrders" class="table table-hover table-sm dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice number</th>
                                    <th>Tracking number</th>
                                    <th>Name</th>
                                    <th>Single price</th>
                                    <th>Sold quantity</th>
                                    <th>Discount</th>
                                    <th>Total price</th>
                                    <th>Single price</th>
                                    <th>Status</th>
                                    <th>Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result->orders as $order)
                                    <tr>
                                        <td>
                                            {{ $order['id'] }}
                                            <input type="hidden" name="order_ids[]" value="{{ $order['id'] }}" />
                                        </td>
                                        <td>
                                            <span>
                                                {{ $order['invoice_number'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                {{ $order['tracking_number'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                {{ $order['product']['name'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="single_sold_price[]" value="{{ $order['single_sold_price'] }}">
                                            @error('single_sold_price.' . $loop->index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                name="sold_quantity[]" value="{{ $order['sold_quantity'] }}">
                                            @error('sold_quantity.' . $loop->index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm"
                                                name="discount_percent[]" value="{{ $order['discount_percent'] }}">
                                            @error('discount_percent.' . $loop->index)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <span class="text-success">
                                                + {{ $order['total_sold_price'] }}
                                            </span>
                                            <span>
                                                €
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-success">
                                                + {{ $order['single_mark_up'] }}
                                            </span>
                                            <span>
                                                €
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                @if ($order['status'] === 'Received')
                                                    <i title="Reveived" class="fa-light fa-check"></i>
                                                @elseif($order['status'] === 'Pending')
                                                    <i title="Pending" class="fa-light fa-loader"></i>
                                                @elseif($order['status'] === 'Ordered')
                                                    <i title="Ordered" class="fa-light fa-truck"></i>
                                                @endif
                                            </span>
                                        </td>
                                        <th>
                                            @if ($order['is_paid'])
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
