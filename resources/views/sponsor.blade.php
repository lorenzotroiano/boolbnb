@extends('layouts.app')

@section('content')
<div id="dashboard-container">
    <div class="container">
        <h2 class="mb-5 pt-5 text-center">Applica la sponsorizzazione sull'appartamento: <span>{{ $apartment->name }}</span></h2>

        <form class="p-1" action="{{ route('apply-sponsorship', $apartment->id) }}" method="POST">
            @csrf
            <div class="form-group d-flex justify-content-center grid gap-3 mb-4" style="height: 200px">
                @foreach($sponsors as $index => $sponsor)
                <div class="card text-center" data-card="{{ $index + 1 }}" style="width: 18rem;" onclick="updateSelectedCard({{ $index + 1 }}, {{ $sponsor->id }})">
                    <div class="card-body">
                        <h5 class="card-title border-bottom p-2">{{ $sponsor->name }}</h5>
                        <div class="card-details" style="display: none;">
                            <p class="card-text price">Prezzo: €{{ $sponsor->price }}</p>
                            <p class="card-text duration">{{ $sponsor->duration }} ore di sponsorship</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <input type="hidden" id="selectedSponsorId" name="sponsor_id" value="">

            <div class="d-flex">
                <div style="flex-grow: 1;">
                    <div id="dropin-container"></div>
                    <div id="purchase-summary" class="mb-4">
                    </div>
                    <div class="button-center">
                        <button id="submit-button" class="button button--small button--green">Acquista</button>
                        <div id="success-notification" style="display: none; background-color: #4CAF50; color: white; padding: 10px; border-radius: 5px; text-align: center; margin-top: 20px;">
                            Pagamento avvenuto con successo
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://js.braintreegateway.com/web/dropin/1.40.2/js/dropin.js"></script>
            <script>
                const button = document.querySelector('#submit-button');

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

        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Successo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Pagamento avvenuto con successo
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        function updateSelectedCard(cardNumber, sponsorId) {
            // Deseleziona tutte le card e nasconde i dettagli
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.classList.remove('selected-card');
                const cardDetails = card.querySelector('.card-details');
                cardDetails.style.display = 'none';
            });

            // Seleziona la card corrispondente
            const selectedCard = document.querySelector(`[data-card="${cardNumber}"]`);
            
            // Estrai i dati dalla card selezionata
            const sponsorName = selectedCard.querySelector('.card-title').textContent;
            const sponsorPrice = selectedCard.querySelector('.price').textContent;
            const sponsorDuration = selectedCard.querySelector('.duration').textContent;

            // Mostra i dettagli e aggiungi la classe 'selected-card'
            const cardDetails = selectedCard.querySelector('.card-details');
            cardDetails.style.display = 'block';
            selectedCard.classList.add('selected-card');
            
            // Aggiorna il campo nascosto con l'ID dello sponsor selezionato
            document.getElementById('selectedSponsorId').value = sponsorId;
                                    
            // Calcola la data di fine sponsorizzazione
            const endDate = new Date();
            endDate.setHours(endDate.getHours() + parseInt(sponsorDuration));

            // Formatta la data per mostrarla nel formato giorno/mese/anno ore:minuti
            const formattedEndDate = `${endDate.getDate().toString().padStart(2, '0')}/${(endDate.getMonth()+1).toString().padStart(2, '0')}/${endDate.getFullYear()} ${endDate.getHours().toString().padStart(2, '0')}:${endDate.getMinutes().toString().padStart(2, '0')}`;
            
            // Aggiungi la data di fine sponsorizzazione alla card
            const endDateElement = selectedCard.querySelector('.end-date');
            if (!endDateElement) {
                const endDateDiv = document.createElement('p');
                endDateDiv.className = 'card-text end-date';
                endDateDiv.textContent = `Fine: ${formattedEndDate}`;
                cardDetails.appendChild(endDateDiv);
            } else {
                endDateElement.textContent = `Fine: ${formattedEndDate}`;
            }
        }


        document.addEventListener("DOMContentLoaded", function(event) { 
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        });
    </script>

    <style>
        #dashboard-container {
            background: #648be9;
            background: linear-gradient(140deg, rgb(124, 136, 198) 0%, rgb(142, 155, 206) 2%, rgb(176, 181, 221) 10%, rgb(207, 213, 235) 31%, rgba(255, 255, 255, 1) 100%);
        }
        .card {
            transition: max-height 0.3s ease, padding 0.3s ease; /* Aggiungi un'animazione per rendere fluido l'effetto */
            max-height: 80px; /* altezza iniziale */
            overflow: hidden;
        }

        .selected-card {
            max-height: 200px; /* Altezza quando la card è selezionata. Regola a seconda delle tue necessità */
            border: 2px solid rgb(16, 143, 216);
        }

        .braintree-dropin-wrapper .braintree-form .braintree-form__error {
            display: none !important;
        }

        .number .invalid {
            display: none !important;
        }
        .expirationDate .invalid {
            display: none !important;
        }
        .sponsor-card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
        }
        .braintree-dropin-wrapper .braintree-form .braintree-form__error,
        .braintree-dropin-wrapper .field.is-invalid .braintree-form__error {
            display: none !important;
        }

        /* .braintree-form__field-error {
            display: none !important;
        } */

        /* .braintree-hosted-fields-invalid {
            display: none !important;
        } */
    </style>
@endsection


