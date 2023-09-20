@extends('layouts.app')
@section('content')
    <div class="container-fluid" id="forms">
        <h2 class="text-center">Crea qui il tuo appartamento
        </h2>
        <div class="row justify-content-center container" id="content-form">
            <div class="col-12 col-lg-9 col-xl-7">
                <form class="mt-4 create" method="POST" action="{{ route('store') }}" enctype="multipart/form-data"
                    class="bg-light p-4 rounded shadow">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="mb-3 col-12">
                            <label class="form-label" for="name"> <strong>Nome Appartamento</strong></label>
                            <input class="form-control" type="text" id="name" name="name" autocomplete="off"
                                required minlength="3" maxlength="50">
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label class="form-label" for="description"> <strong>Descrizione</strong></label>
                            <textarea class="form-control" name="description" id="description" required maxlength="255">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label class="form-label" for="room"> <strong>Stanze</strong></label>
                            <input class="form-control" type="number" id="room" name="room" required min="1"
                                max="20">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('room')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label class="form-label" for="bathroom"> <strong>Bagni</strong></label>
                            <input class="form-control" type="number" id="bathroom" name="bathroom" min="0"
                                required max="4">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('bathroom')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12 col-md-3">
                            <label class="form-label" for="mq"> <strong>Metri quadrati</strong></label>
                            <input class="form-control" type="number" id="mq" name="mq" required
                                min="8">
                            <div class="invalid-feedback">Non puoi inserire un numero negativo!</div>
                            @error('mq')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label for="address" class="form-label">Indirizzo</label>
                            <input type="text" class="form-control" name="address" id="address" required>
                            @error('address')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div id="dropdown" class="mb-3 col-12 col-md-3"></div>

                        <div class="mb-3 col-12">
                            <label class="d-block" for="visibility">
                                <strong>Vuoi rendere visibile il tuo appartamento?</strong>
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visible" id="visible-yes"
                                    value="1">
                                <label class="form-check-label" for="visible-yes">
                                    Si
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visible" id="visible-no"
                                    value="0">
                                <label class="form-check-label" for="visible-no">
                                    No
                                </label>
                            </div>
                            @error('visible')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label for="cover" class="form-label"> <strong>Immagine principale
                                    dell'appartamento</strong></label>
                            <input type="file" class="form-control" id="cover" name="cover" required
                                aria-label="file example" required>
                                <input type="file" name="images[]" id="images" multiple>
                                <div id="image-previews"></div>
                            @error('cover')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 col-12">
                            <label class="form-label">Services</label>
                            <div class="row ps-2">
                                @foreach ($services as $service)
                                    <div class="form-check col-12 col-md-6 ps-0">
                                        <div class="d-flex">
                                            <input class="form-check-input ps-0 ms-0" type="checkbox"
                                                value="{{ $service->id }}" name="services[]"
                                                id="service-{{ $service->id }}">
                                            <label class="form-check-label" for="service-{{ $service->id }}">
                                                {{ $service->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button id="submit" class="btn btn-success my-3 w-25 col-12 m-auto"
                            type="submit">Salva</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const addressInput = document.getElementById('address');
            addressInput.addEventListener('input', function() {
                let query = addressInput.value;
                if (query.length > 2) {
                    fetch(`/search-address?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            displayDropdown(data);
                        })
                        .catch(error => console.error('Error fetching search data:', error));
                }
            });
        });

        function displayDropdown(data) {
            const dropdown = document.getElementById('dropdown');

            if (data.results && data.results.length) {
                dropdown.style.display = 'flex'; // Mostra il dropdown
            } else {
                dropdown.style.display = 'none'; // Nasconde il dropdown se non ci sono risultati
                return;
            }

            let ul = document.createElement('ul');
            ul.classList.add('list-group'); // Classe di Bootstrap per liste

            data.results.forEach(item => {
                let li = document.createElement('li');
                li.classList.add('list-group-item'); // Classe di Bootstrap per elementi di lista

                let option = document.createElement('a');
                option.textContent = item.address.freeformAddress;
                option.href = "#";
                option.addEventListener('click', function(event) {
                    event.preventDefault(); // Previene il comportamento predefinito del link
                    document.getElementById('address').value = item.address.freeformAddress;
                    dropdown.style.display = 'none'; // Nasconde il dropdown dopo la selezione
                });

                li.appendChild(option);
                ul.appendChild(li);
            });

            // Prima di aggiungere ul all'elemento dropdown, assicurati che dropdown sia vuoto
            dropdown.innerHTML = '';
            dropdown.appendChild(ul);
        }
    </script>

    <style>
        #forms {
            background: #648be9;
            background: linear-gradient(140deg, rgb(124, 136, 198) 0%, rgb(142, 155, 206) 2%, rgb(176, 181, 221) 10%, rgb(207, 213, 235) 31%, rgba(255, 255, 255, 1) 100%);
            padding: 4rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;

        }

        #submit {
            background-color: #7c8bc6;
            color: white;
            border: 3px solid transparent;
        }

        #submit :hover {
            border: 3px solid #ffffff;
            color: #ffffff;
        }

        #submit :active {
            border: 3px solid #ffffff !important;
            color: #ffff !important;
        }

        #content-form {
            padding: 1rem 0;
            background-color: rgba($color: #ffffff, $alpha: 0.2);
            border-radius: 15px;
        }
    </style>

<script>
    document.getElementById('images').addEventListener('change', function() {
        const previewsContainer = document.getElementById('image-previews');
        // Pulisci il contenitore di anteprime esistente prima di aggiungere nuove anteprime
        previewsContainer.innerHTML = '';

        // Per ciascun file selezionato, crea un'anteprima
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(this.src);
            }

            img.height = 100;  // puoi modificare l'altezza come preferisci

            previewsContainer.appendChild(img);
        }
    });
</script>
@endsection
