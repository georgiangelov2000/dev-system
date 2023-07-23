@extends('app')

@section('content')
    <div class="card card-default cardTemplate">
        <div class="row p-3">
            <div class="col-12 col-sm-6">
                <h3 class="d-inline-block d-sm-none">LOWA Men’s Renegade GTX Mid Hiking Boots Review</h3>
                <div class="col-6 m-auto">
                    <img src="{{ $purchase->images[0]->path . '/' . $purchase->images[0]->name }}" class="product-image"
                        alt="Product Image">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <button id="print" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
                <h5 class="my-3 text-primary">Purchase</h5>
                <p>{{ $purchase->notes }}</p>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                <span>Purchase:</span>
                                <b> {{ $purchase->name }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Supplier:</span>
                                <b>{{ $purchase->supplier->name }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Initial quantity:</span>
                                <b>{{ $purchase->initial_quantity }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Single price:</span>
                                <b>€{{ $purchase->price }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Total price:</span>
                                <b>€{{ $purchase->total_price }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Code:</span>
                                <b>{{ $purchase->code }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Created at:</span>
                                <b>{{ $purchase->created_at }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Updated at:</span>
                                <b>{{ $purchase->updated_at }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="text-primary">Payment</h5>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>
                                <span>Quantity:</span>
                                <b>{{ $purchase->payment->quantity }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Price</span>
                                <b>€{{ $purchase->payment->price }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Payment method:</span>
                                <b>
                                    {{ isset(config('statuses.payment_methods_statuses')[$purchase->payment->payment_method]) ? config('statuses.payment_methods_statuses')[$purchase->payment->payment_method] : '' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Payment reference:</span>
                                <b>{{ $purchase->payment->payment_reference }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Payment status:</span>
                                <b> {{ isset(config('statuses.payment_statuses')[$purchase->payment->payment_status]) ? config('statuses.payment_statuses')[$purchase->payment->payment_status] : '' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Date of payment:</span>
                                <b>{{ $purchase->payment->date_of_payment }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- @if ($purchase->images && count($purchase->images) > 1)
                <div id="carouselExampleControls" class="col-12 carousel slide" data-ride="carousel">
                    <div class="carousel-inner rounded">
                        @foreach ($purchase->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img class="img-fluid cardWidgetImage d-block card card-widget w-100"
                                    src="{{ $image->path . '/' . $image->name }}" alt="Slide {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            @elseif ($purchase->images && count($purchase->images) === 1)
                <div class="col-12 mb-3">
                    <img class="cardWidgetImage w-100 m-0"
                        src="{{ $purchase->images[0]->path . '/' . $purchase->images[0]->name }}" />
                </div>
            @else
                <div class="col-12 mb-3">
                    <img class="cardWidgetImage w-100 m-0"
                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png" />
                </div>
            @endif --}}
    </div>
    @push('scripts')
        <script type="text/javascript">
            $(function(){
                $('#print').on('click',function(){
                    window.print();
                })
            })
        </script>
    @endpush
@endsection
