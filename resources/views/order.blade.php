<x-layout>
    <?php
    // TODO: fix this
    //dump(url());
    ?>



    {{--     <div
        style="
    margin: 0% auto;
    position: fixed;
    float: left;
    background: white;
    top: 12%;
    width: 100%;
">
        <nav aria-label="breadcrumb" style="--pico-nav-breadcrumb-divider: '⇢' ;">
            <ul>
                <li><a href="#s1">1</a></li>
                <li><a href="#s2">2</a></li>
                <li>3</li>
            </ul>
        </nav>
    </div>

 --}}

    <br>



    <details open class="orders" id="s1">
        <summary><button> 1 </button> </summary>

        <fieldset>
            <legend><strong> Select Country:</strong></legend>
            <div x-data="countryAutocomplete()">
                {{--   <legend><strong> Select country:</strong></legend> --}}

                <!-- Search Input -->

                <form id="searchForm" action="{{ route('cart.add') }}" method="POST">
                    @csrf

                    <input id="searchInput" type="search" x-model="query" placeholder="🌎 Search for a country..."
                        aria-label="Search" @input="filterCountries" name="search_value" />


                    <!-- Suggestions Dropdown -->
                    <ul class="border mt-2 max-h-64 overflow-y-auto"
                        x-show="filteredCountries.length > 0 && query.length > 1">
                        <template x-for="country in filteredCountries" :key="country.name">
                            <li class="p-2 hover:bg-gray-100 cursor-pointer" @click="selectCountry(country.name)">
                                <!-- Display the country name with matching part in bold -->
                                <span x-html="highlightMatch(country.name)"></span>
                            </li>
                        </template>
                    </ul>

                    <!-- Selected Country (Optional) -->
                    {{--       <div x-show="selectedCountry" >
                    <h4>Selected Country: <span x-text="selectedCountry"></span></h4>
                </div> --}}
            </div>

            <div id="results"></div>
        </fieldset>
    </details>


    <hr />
    <details open class="orders" id="s3" x-data="calculator()">
        <summary><button> 2 </button> </summary>


        <fieldset>



            <legend><strong> Printed paper size:</strong></legend>
            <label>
                <input type="radio" id="p1" name="p1" value="0.20" x-model="selectedPrice"
                    @change="calculateTotal">
                Heavyweight, 3-hole, 8½"x11"
            </label>
            <label>
                <input type="radio" id="p2" name="p2" value="0.30" x-model="selectedPrice"
                    @change="calculateTotal" />
                2 hole Scott International
            </label>
            <label>
                <input type="radio" id="p3" name="p3" value="0.35" x-model="selectedPrice"
                    @change="calculateTotal" />
                Scott Specialized
                <span style="font-size:12px">&#128306;</span>
                <span style="font-size:12px">&#128306;</span>
                (2 rectangular holes)
            </label>
            <label>
                <input type="radio" id="p4" name="p4" value="0.350" x-model="selectedPrice"
                    @change="calculateTotal" />
                Scott Specialized
                <span style="font-size:12px">&#128280;</span>
                <span style="font-size:12px">&#128280;</span>
                <span style="font-size:12px">&#128280;</span>
                (3 round holes)
            </label>
            <label>
                <input type="radio" id="p5" name="p5" value="0.300" x-model="selectedPrice"
                    @change="calculateTotal" />
                Minkus 2-hole
            </label>


        </fieldset>
        <legend><strong> Paper Quantity:</strong></legend>
        {{--         <input type="number" name="Quantity" placeholder="Quantity" aria-label="Quantity"> --}}
        <input type="number" name="quantity" id="quantity" x-model="quantity" min="1"
            placeholder="Enter quantity" @input="calculateTotal">
        <h2>Total: $<span x-text="total"></span></h2>
    </details>

    <hr />


    <script>
        function calculator() {
            return {
                quantity: 1, // Default quantity
                selectedPrice: 0, // Default selected price
                total: 0, // Total amount

                // Calculate the total based on the quantity and selected price
                calculateTotal() {
                    this.total = parseFloat(this.quantity * this.selectedPrice).toFixed(2);
                }
            }
        }
    </script>

    <input type="hidden" id="searchHiddenInput" name="hidden_search_value">
    <input type="hidden" id="hidden_period_value" name="hidden_period_value">
    <input type="submit" class="contrast" value=" Add to Cart 🛒">
    </form>


    {{-- <h1>Search for a Country</h1> --}}

    {{-- <form id="searchForm">
        <input type="text" name="name" id="name" placeholder="Enter country name" required>
        <button type="submit">Search</button> --}}
    {{-- </form> --}}



    {{-- TODO remove this
    {{--  <script src="{{ asset('countrylist_datafile.js') }} "></script> --}}
    {{--  <script src="{{ asset('js/helpers.js') }} "></script>

    <script>
        // autocomplete(document.getElementById("myInput"), countries);
    </script> --}}




    ----------------




    {{-- @if (session('msg'))
    <div class="alert alert-success">
        {{ session('msg') }}
    </div>
@endif --}}


    ----------------------

    <script src="//unpkg.com/alpinejs" defer></script>



    <script>
        function countryAutocomplete() {
            return {
                query: '', // The user's search query
                countries: [], // Array of all countries loaded from the server
                filteredCountries: [], // Array to hold the filtered countries
                selectedCountry: '', // The selected country name

                // Initialize by fetching all countries
                init() {
                    //fetch('/countries')
                    fetch("{{ url('/countries') }}")
                        .then(response => response.json())
                        .then(data => {
                            this.countries = data;
                        });
                },

                // Filter countries based on user input
                filterCountries() {
                    // Only filter if the query length is at least x characters
                    if (this.query.length > 1) {
                        // console.log(this.query);
                        this.filteredCountries = this.countries.filter(country =>
                            country.name.toLowerCase().includes(this.query.toLowerCase())

                        );
                    } else {
                        // this.filteredCountries = []; // Clear suggestions if less than x characters
                    }
                },

                // Highlight the matching part of the country name
                highlightMatch(name) {
                    if (!this.query) return name;
                    // window.scrollTo(0,document.body.scrollHeight);
                    window.scrollTo({
                        left: 0,
                        top: 200,
                        behavior: "smooth"
                    });

                    // Create a regular expression to find the matching part (case-insensitive)
                    const regex = new RegExp(`(${this.query})`, 'gi');

                    // Replace the matching part with the same text wrapped in a <strong> tag
                    return name.replace(regex, "<b><ins>$1</ins></b>");
                },

                // Set the selected country when a user clicks on a suggestion
                selectCountry(countryName) {
                    this.selectedCountry = countryName;
                    this.query = countryName;
                    this.filteredCountries = []; // Clear suggestions after selection

                    console.log("selected: " + countryName);
                    fetch(`{{ url('/search-country?name=') }}${countryName}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.country) {
                                console.log(data.country.name);
                                //get the country selected to pass to cart
                                document.getElementById('searchHiddenInput').value = data.country.name;


                                let results =
                                    `<br><legend><strong> Select Period:</strong></legend><select name="select" id="period_select" aria-label="Select" required>`;


                                data.periods.forEach(period => {
                                    results +=
                                        `<option>📅${period.description} -- 📄${period.pages} pages</option>`;
                                });
                                results += '</select>';
                                document.getElementById('results').innerHTML = results;

                                document.getElementById('hidden_period_value').value = document.getElementById(
                                    'period_select').value;

                                // Listen for changes in the search input
                                document.getElementById('period_select').addEventListener('change', function() {
                                    // Update the hidden field in the main form with the value from the search form
                                    document.getElementById('hidden_period_value').value = document
                                        .getElementById('period_select').value;
                                    console.log("input value: " + document.getElementById('period_select')
                                        .value);


                                });

                            } else {
                                document.getElementById('results').innerHTML = 'Country not found.';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('results').innerHTML = 'An error occurred.';
                        });

                }
            }
        }

        // embed the search result
    </script>




    -----


</x-layout>
