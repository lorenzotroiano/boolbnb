@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1 class="text-center mb-5">Create new Apartment</h1>

        <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data" class="bg-light p-4 rounded shadow">
            @csrf
            @method('POST')

            <div class="mb-3">
                <label for="cover" class="form-label text-center">Main picture</label>
                <input type="file" class="form-control" name="cover" id="cover">
                @error('cover')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" required minlength="3"
                    maxlength="50">
                @error('name')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" id="description" required maxlength="255">
                @error('description')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="room" class="form-label">Room</label>
                <input type="number" class="form-control" name="room" id="room" required min="1"
                    max="20">
                @error('room')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="bathroom" class="form-label">Bathroom</label>
                <input type="number" class="form-control" name="bathroom" id="bathroom" required max="4">
                @error('bathroom')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="mq" class="form-label">mq</label>
                <input type="number" class="form-control" name="mq" id="mq" required min="8">
                @error('mq')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" id="address" required>
                @error('address')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div id="dropdown" class="mb-3"></div>

            <div class="mb-3">
                <label class="form-label">Visible</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="visible" id="visible-yes" value="1">
                        <label class="form-check-label" for="visible-yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="visible" id="visible-no" value="0">
                        <label class="form-check-label" for="visible-no">No</label>
                    </div>
                </div>
                @error('visible')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Services</label>
                @foreach ($services as $service)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $service->id }}" name="services[]"
                            id="service-{{ $service->id }}">
                        <label class="form-check-label" for="service-{{ $service->id }}">
                            {{ $service->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">CREATE</button>
        </form>
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
        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1.5rem
        }

        #dropdown {
            max-height: 150px;
            overflow-y: auto;
        }

        #dropdown a {
            text-decoration: none
        }
    </style>
@endsection
