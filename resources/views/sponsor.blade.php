@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Apply Sponsorship for Apartment: {{ $apartment->name }}</h2>

        <form action="{{ route('apply-sponsorship', $apartment->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="sponsor">Choose a Sponsorship Level</label>
                <select class="form-control" id="sponsor" name="sponsor_id">
                    @foreach ($sponsors as $sponsor)
                        <option value="{{ $sponsor->id }}">
                            {{ $sponsor->name }} - €{{ $sponsor->price }} for {{ $sponsor->duration }} hours
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="dropin-container"></div>
            <button id="submit-button" class="button button--small button--green">Purchase</button>


            <script src="https://js.braintreegateway.com/web/dropin/1.40.2/js/dropin.js"></script>
            <script>
                button = document.querySelector('#submit-button');

                braintree.dropin.create({
                    authorization: 'sandbox_g42y39zw_348pk9cgf3bgyw2b',
                    selector: '#dropin-container'
                }, function(err, instance) {
                    button.addEventListener('click', function() {
                        instance.requestPaymentMethod(function(err, payload) {
                            // Submit payload.nonce to your server
                        });
                    })
                });
            </script>
        </form>
    </div>
@endsection
