@extends('layouts.app')
@section('content')
    <div class="container text-center">
        <h1>Create new Apartment</h1>

        <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data">

            @csrf
            @method('POST')

            <div>
                <label for="cover">Main picture</label>
                <br>
                <input type="file" name="cover" id="cover">
                <br>
                @error('cover')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="name">name</label>
                <br>
                <input type="text" name="name" id="name" required minlength="3" maxlength="50">
                <br>
    
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


            <div>
                <label for="description">description</label>
                <br>
                <input type="text" name="description" id="description" required maxlength="255">
                <br>
                @error('description')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


            <div>
                <label for="room">room</label>
                <br>
                <input type="number" name="room" id="room" required min="1" max="20">
                <br>
                @error('room')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


            <div>
                <label for="bathroom">bathroom</label>
                <br>
                <input type="number" name="bathroom" id="bathroom" required max="4">
                <br>
                @error('bathroom')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>


            <div>
                <label for="mq">mq</label>
                <br>
                <input type="number" name="mq" id="mq" required min="8">
                <br>
                @error('mq')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>



            <div>
                <label for="address">address</label>
                <br>
                <input type="text" name="address" id="address" required>
                <br>
                @error('address')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            {{-- Dropdown autocomplete --}}
            <div id="dropdown">
            </div>

            <div>
                <label for="visible">Visible</label>
                <br>
                <input type="radio" name="visible" id="visible-yes" value="1"> Yes
                <input type="radio" name="visible" id="visible-no" value="0"> No
                <br>
                @error('visible')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div>
                @foreach ($services as $service)
                    <div class="form-check mx-auto user-select-none" style="max-width: 300px">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" value="{{ $service->id }}" name="services[]"
                                id="service-{{ $service->id }}">
                            {{ $service->name }}
                        </label>
                    </div>
                @endforeach
            </div>


            <!-- Bottone di submit per inviare il form -->
            <input class="my-3" type="submit" value="CREATE">

        </form>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const addressInput = document.getElementById('address');
    addressInput.addEventListener('input', function() {
        let query = addressInput.value;
        if(query.length > 2) {
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
            dropdown.innerHTML = ''; // Chiudi il dropdown dopo la selezione
        });

        li.appendChild(option);
        ul.appendChild(li);
    });

    dropdown.appendChild(ul);
}


</script>

<style>
    form{
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 1.5rem
    }

    #dropdown{
        max-height: 150px;
        overflow-y: auto;
    }
    #dropdown a{
        text-decoration: none
    }

</style>



@endsection
