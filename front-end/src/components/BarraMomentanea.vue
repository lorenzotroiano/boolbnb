   <!-- SEARCH -->
<div class="row justify-content-center flex-column align-content-center">

    <!-- Ricerca -->
    <div class="col-8 mb-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Cerca appartamento..." aria-label="Cerca..."
                aria-describedby="button-addon2" v-model="search" @input="getSuggestions"
                @keyup.enter="searchApartments">
            <div class="input-group-append">
                <button type="button" class="btn btn-primary" @click="searchApartments">Search</button>
                
            </div>
        </div>
    </div>

    <!-- SUGGERIMENTI -->
<div class="col-2" id="suggest">
        <div v-if="suggestions.length" class="suggestions-list d-flex justify-content-center">
            <ul>
                <li v-for="suggestion in suggestions" :key="suggestion" @click="selectSuggestion(suggestion)">
                    {{ suggestion }}
                </li>
            </ul>
        </div>
    </div>

</div>