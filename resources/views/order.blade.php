<x-layout>
    <main class="container" x-data="orderPageData()">
        <!-- Two Column Grid using Pico CSS -->
        <div class="grid">
            
            <!-- Left Column - Select Items (70%) -->
            <article style="grid-column: span 8;">
                <header>
                    <h5>Select Items</h5>
                </header>
                
                <!-- Country Selection -->
                <fieldset>
                    <label for="country">Country:</label>
                    <input 
                        type="search" 
                        id="country"
                        x-model="countryQuery" 
                        placeholder="Search for a country..."
                        @input="filterCountries"
                    />
                    
                    <!-- Country Suggestions -->
                    <div x-show="filteredCountries.length > 0 && countryQuery.length > 1" 
                         style="border: 1px solid var(--muted-border-color); border-radius: var(--border-radius); max-height: 200px; overflow-y: auto; background: var(--card-background-color); margin-top: 0.5rem;">
                        <template x-for="country in filteredCountries" :key="country.name">
                            <div style="padding: 0.75rem; border-bottom: 1px solid var(--muted-border-color); cursor: pointer;" 
                                 @click="selectCountry(country.name)"
                                 onmouseover="this.style.backgroundColor='var(--muted-color)'"
                                 onmouseout="this.style.backgroundColor='transparent'">
                                <span x-html="highlightMatch(country.name)"></span>
                            </div>
                        </template>
                    </div>
                </fieldset>

                <!-- Year Selection (appears after country selection) -->
                <fieldset x-show="selectedCountry && availablePeriods.length > 0">
                    <div class="grid">
                        <div>
                            <label for="startYear">Start Year:</label>
                            <select 
                                id="startYear"
                                x-model="startYear"
                                @change="filterPeriodsByYear"
                            >
                                <option value="">Select start year</option>
                                <template x-for="year in availableYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label for="endYear">End Year:</label>
                            <select 
                                id="endYear"
                                x-model="endYear"
                                @change="filterPeriodsByYear"
                            >
                                <option value="">Select end year</option>
                                <template x-for="year in availableYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Periods Table using Pico CSS -->
                    <figure x-show="filteredPeriods.length > 0">
                        <table role="grid" class="striped" style="table-layout: fixed; width: 100%; pico-background-slate-600">
                            <thead data-theme="dark" style="--pico-background-color:rgb(144, 158, 190); --pico-color:white;--pico-form-element-background-color: rgb(197 199 203)">
                                <tr>
                                    <th scope="col" style="width: 30%;">
                                        <label>
                                            <input 
                                                type="checkbox" 
                                                x-model="selectAll"
                                                @change="toggleSelectAll"
                                            />
                                            Select All
                                        </label>
                                    </th>
                                    <th scope="col" style="width: 35%;">File Name</th>
                                    <th scope="col" style="width: 35%;">Pages in Range</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="period in filteredPeriods" :key="period.id">
                                    <tr>
                                        <td style="width: 30%;">
                                            <label>
                                                <input 
                                                    type="checkbox" 
                                                    :value="period.id"
                                                    x-model="selectedPeriods"
                                                    @change="updateOrderSummary"
                                                />
                                            </label>
                                        </td>
                                        <td style="width: 35%;" x-text="extractYear(period.description) || period.description"></td>
                                        <td style="width: 35%;" x-text="period.pages"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </figure>
                </fieldset>
            </article>

            <!-- Right Column - Your Order (30%) -->
            <aside style="grid-column: span 4;">
                <article>
                    <header>
                        <h5>Your Order</h5>
                    </header>
                
                    <div x-show="selectedPeriods.length === 0" style="text-align: center; padding: 3rem 0;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <p>Select items to build your order</p>
                    </div>
                
                    <div x-show="selectedPeriods.length > 0">
                        <!-- Order Table using Pico CSS -->
                        <figure>
                            <table role="grid" style="table-layout: fixed; width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 35%;">Country & Range</th>
                                        <th scope="col" style="width: 20%;">Files</th>
                                        <th scope="col" style="width: 20%;">Pages</th>
                                        <th scope="col" style="width: 25%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="periodId in selectedPeriods" :key="periodId">
                                        <tr>
                                            <td style="width: 35%; word-wrap: break-word;">
                                                <strong x-text="selectedCountry"></strong>
                                                <br>
                                                <small x-text="getYearRange()"></small>
                                            </td>
                                            <td style="width: 20%;" x-text="extractYear(getPeriodById(periodId)?.description) || '1'"></td>
                                            <td style="width: 20%;" x-text="getPeriodById(periodId)?.pages"></td>
                                            <td style="width: 25%;">
                                                <button 
                                                    @click="removePeriod(periodId)"
                                                    class="secondary outline"
                                                    style="font-size: 0.7rem; padding: 0.25rem 0.5rem;"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </figure>
                        
                        <!-- Order Summary using Pico CSS -->
                        <section style="margin-top: 1rem;">
                            <h3>Total Pages: <span x-text="totalPages"></span></h3>
                            
                            <!-- Paper Type Selection -->
                            <label for="paperType">Paper Type:</label>
                            <select 
                                id="paperType"
                                x-model="selectedPrice"
                                @change="calculateTotal"
                                style="font-size: 0.9rem;"
                            >
                                <option value="0">Select paper type</option>
                                <option value="0.20">Heavyweight, 3-hole ($0.20/page)</option>
                                <option value="0.30">Scott International ($0.30/page)</option>
                                <option value="0.35">Scott Specialized 2-hole ($0.35/page)</option>
                                <option value="0.35">Scott Specialized 3-hole ($0.35/page)</option>
                                <option value="0.30">Minkus 2-hole ($0.30/page)</option>
                            </select>
                            
                            <!-- Quantity -->
                            <label for="quantity">Quantity:</label>
                            <input 
                                type="number" 
                                id="quantity"
                                x-model="quantity" 
                                min="1" 
                                @input="calculateTotal"
                                style="max-width: 100px;"
                            />
                            
                            <!-- Total Price -->
                            <div x-show="total > 0" style="background: var(--primary-focus); color: white; padding: 0.75rem; border-radius: var(--border-radius); text-align: center; margin: 1rem 0; font-size: 0.9rem;">
                                <strong>Total: $<span x-text="total"></span></strong>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <form x-show="total > 0" action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="country" :value="selectedCountry">
                                <input type="hidden" name="periods" :value="JSON.stringify(selectedPeriods)">
                                <input type="hidden" name="paper_type" :value="selectedPrice">
                                <input type="hidden" name="quantity" :value="quantity">
                                <input type="hidden" name="total" :value="total">
                                
                                <button type="submit" style="width: 100%; font-size: 0.9rem;">
                                    🛒 Add to Cart
                                </button>
                            </form>
                        </section>
                    </div>
                </article>
            </aside>
        </div>
    </main>


    <script>
        function orderPageData() {
            return {
                // Country selection
                countryQuery: '',
                countries: [],
                filteredCountries: [],
                selectedCountry: '',
                
                // Period/Year selection
                availablePeriods: [],
                filteredPeriods: [],
                selectedPeriods: [],
                availableYears: [],
                startYear: '',
                endYear: '',
                selectAll: false,
                
                // Paper and pricing
                selectedPrice: 0,
                quantity: 1,
                total: 0,
                totalPages: 0,

                // Initialize component
                init() {
                    // Load all countries from the server
                    fetch("{{ url('/countries') }}")
                        .then(response => response.json())
                        .then(data => {
                            this.countries = data;
                        })
                        .catch(error => console.error('Error loading countries:', error));
                },

                // Filter countries based on search query
                filterCountries() {
                    if (this.countryQuery.length > 1) {
                        this.filteredCountries = this.countries.filter(country =>
                            country.name.toLowerCase().includes(this.countryQuery.toLowerCase())
                        );
                    } else {
                        this.filteredCountries = [];
                    }
                },

                // Highlight matching text in country names
                highlightMatch(name) {
                    if (!this.countryQuery) return name;
                    const regex = new RegExp(`(${this.countryQuery})`, 'gi');
                    return name.replace(regex, "<b><ins>$1</ins></b>");
                },

                // Select a country and load its periods
                selectCountry(countryName) {
                    this.selectedCountry = countryName;
                    this.countryQuery = countryName;
                    this.filteredCountries = [];
                    this.selectedPeriods = [];
                    this.availablePeriods = [];
                    this.filteredPeriods = [];
                    this.selectAll = false;
                    
                    // Fetch periods for the selected country
                    fetch(`{{ url('/search-country?name=') }}${countryName}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.country && data.periods) {
                                this.availablePeriods = data.periods;
                                this.filteredPeriods = data.periods;
                                this.generateAvailableYears();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading periods:', error);
                        });
                },

                // Generate available years from periods
                generateAvailableYears() {
                    const years = new Set();
                    this.availablePeriods.forEach(period => {
                        const yearMatch = period.description.match(/\d{4}/g);
                        if (yearMatch) {
                            yearMatch.forEach(year => years.add(parseInt(year)));
                        }
                    });
                    this.availableYears = Array.from(years).sort((a, b) => a - b);
                },

                // Filter periods by year range
                filterPeriodsByYear() {
                    let filtered = this.availablePeriods;
                    
                    if (this.startYear || this.endYear) {
                        filtered = this.availablePeriods.filter(period => {
                            const yearMatch = period.description.match(/\d{4}/);
                            if (yearMatch) {
                                const periodYear = parseInt(yearMatch[0]);
                                const start = this.startYear ? parseInt(this.startYear) : 0;
                                const end = this.endYear ? parseInt(this.endYear) : 9999;
                                return periodYear >= start && periodYear <= end;
                            }
                            return true;
                        });
                    }
                    
                    this.filteredPeriods = filtered;
                    // Reset select all when filtering
                    this.selectAll = false;
                },

                // Toggle select all periods
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedPeriods = this.filteredPeriods.map(period => period.id.toString());
                    } else {
                        this.selectedPeriods = [];
                    }
                    this.updateOrderSummary();
                },

                // Update order summary when periods are selected
                updateOrderSummary() {
                    this.calculateTotalPages();
                    this.calculateTotal();
                    // Update select all state
                    this.selectAll = this.selectedPeriods.length === this.filteredPeriods.length && this.filteredPeriods.length > 0;
                },

                // Calculate total pages from selected periods
                calculateTotalPages() {
                    this.totalPages = this.selectedPeriods.reduce((total, periodId) => {
                        const period = this.getPeriodById(periodId);
                        return total + (period ? parseInt(period.pages) : 0);
                    }, 0);
                },

                // Calculate total price
                calculateTotal() {
                    if (this.selectedPrice > 0 && this.totalPages > 0 && this.quantity > 0) {
                        this.total = parseFloat(this.totalPages * this.selectedPrice * this.quantity).toFixed(2);
                    } else {
                        this.total = 0;
                    }
                },

                // Get period object by ID
                getPeriodById(periodId) {
                    return this.availablePeriods.find(period => period.id == periodId);
                },

                // Extract year from period description
                extractYear(description) {
                    const yearMatch = description?.match(/\d{4}/);
                    return yearMatch ? yearMatch[0] : null;
                },

                // Get year range for display
                getYearRange() {
                    if (this.startYear && this.endYear) {
                        return `${this.startYear} - ${this.endYear}`;
                    } else if (this.startYear) {
                        return `${this.startYear}+`;
                    } else if (this.endYear) {
                        return `Up to ${this.endYear}`;
                    }
                    return 'All years';
                },

                // Remove a period from selection
                removePeriod(periodId) {
                    this.selectedPeriods = this.selectedPeriods.filter(id => id != periodId);
                    this.updateOrderSummary();
                }
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>


</x-layout>
