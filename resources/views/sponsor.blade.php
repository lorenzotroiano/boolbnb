@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-5 mt-4">Applica la sponsorizzazione sull'appartamento: {{ $apartment->name }}</h2>

        <form class="p-1" action="{{ route('apply-sponsorship', $apartment->id) }}" method="POST">
            @csrf
            <div class="form-group d-flex justify-content-center grid gap-3 mb-4">
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title border-bottom p-2 text-center">{{ $sponsors[0]->name }}</h5>
                        <p class="card-text text-center">€{{ $sponsors[0]->price }} for {{ $sponsors[0]->duration }} ore</p>
                        <input type="radio" id="sponsor1" name="sponsor_id" value="{{ $sponsors[0]->id }}">
                        <label for="sponsor1">Seleziona</label>

                    </div>
                </div>

                <!-- Card 2 - Livello 2 -->
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title border-bottom p-2 text-center">{{ $sponsors[1]->name }}</h5>
                        <p class="card-text text-center">€{{ $sponsors[1]->price }} for {{ $sponsors[1]->duration }} ore</p>
                        <input type="radio" id="sponsor2" name="sponsor_id" value="{{ $sponsors[1]->id }}">
                        <label for="sponsor2">Seleziona</label>

                    </div>
                </div>

                <!-- Card 3 - Livello 3 -->
                <div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title border-bottom p-2 text-center">{{ $sponsors[2]->name }}</h5>
                        <p class="card-text text-center">€{{ $sponsors[2]->price }} for {{ $sponsors[2]->duration }} ore
                        </p>
                        <input type="radio" id="sponsor3" name="sponsor_id" value="{{ $sponsors[2]->id }}">
                        <label for="sponsor3">Seleziona</label>

                    </div>
                </div>
            </div>
            <div id="dropin-container"></div>
            <button id="submit-button" class="button button--small button--green">Acquista</button>


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
