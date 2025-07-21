<x-layout>
    <style>
        /* Hide elements initially to prevent FOUC */
        [x-cloak] { display: none !important; }
        .periods-table { display: none; }
        .add-button { display: none; }
    </style>
    <main class="container" x-data="orderPageData()">
        <!-- Two Column Grid using Pico CSS -->
        <div class="grid">
            
            <!-- Left Column - Select Items (50%) -->
            <article style="grid-column: span 6;">
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
                <fieldset x-show="selectedCountry && availablePeriods.length > 0" x-cloak>
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
                    <figure x-show="filteredPeriods.length > 0" x-cloak>
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
                                            All
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
                    
                    <!-- Add to Order Button -->
                    <div x-show="selectedPeriods.length > 0" x-cloak style="margin-top: 1rem; text-align: center;">
                        <button @click="addToOrder()" style="contrast">
                         + Add to Order
                        </button>
                    </div>
                </fieldset>
            </article>

            <!-- Right Column - Your Order (50%) -->
            <aside style="grid-column: span 6;">
                <article>
                    <header>
                        <h5>Your Order</h5>
                    </header>
                
                    <div x-show="orderGroups.length === 0" x-cloak style="text-align: center; padding: 3rem 0;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <p>Add items to build your order</p>
                    </div>
                
                    <div x-show="orderGroups.length > 0" x-cloak>
                        <!-- Country Groups Accordion Style -->
                        <template x-for="group in orderGroups" :key="group.id">
                            <details style="margin-bottom: 0.75rem; border: 2px solid var(--muted-border-color); border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <summary style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1.25rem; cursor: pointer; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--muted-border-color);">
                                    <div style="flex: 1;">
                                        <div style="font-size: 1.2rem; color: #1e293b; margin-bottom: 0.25rem;" x-text="group.country"></div>
                                        <div style="font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="background: #3b82f6; color: white; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500;" x-text="group.actualYearRange"></span>
                                            <span>•</span>
                                            <span style="font-weight: 500;" x-text="group.totalPages + ' pages'"></span>
                                        </div>
                                    </div>
                                    <button 
                                        @click.stop="removeGroup(group.id)"
                                        style="background: #ef4444; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.8rem; font-weight: 500; cursor: pointer; transition: background 0.2s;"
                                        onmouseover="this.style.background='#dc2626'"
                                        onmouseout="this.style.background='#ef4444'"
                                    >
                                        🗑️ Remove
                                    </button>
                                </summary>
                                
                                <!-- Expanded content showing individual years -->
                                <div style="padding: 1.25rem; background: #fefefe;">
                                    <div style="display: flex; align-items: center; margin-bottom: 0.75rem;">
                                        <h6 style="margin: 0; color: #475569; font-size: 0.9rem; font-weight: 600;">Individual Years:</h6>
                                        <span style="margin-left: 0.5rem; background: #f1f5f9; color: #64748b; padding: 0.1rem 0.4rem; border-radius: 8px; font-size: 0.7rem;" x-text="group.periods.length + ' selected'"></span>
                                    </div>
                                    <div style="display: grid; gap: 0.5rem;">
                                        <template x-for="period in group.periods" :key="period.id">
                                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;">
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <span style="font-weight: 600; color: #1e293b;" x-text="extractYear(period.description) || period.description"></span>
                                                    <span style="color: #64748b; font-size: 0.8rem;" x-text="period.pages + ' pages'"></span>
                                                </div>
                                                <button 
                                                    @click="removePeriodFromGroup(group.id, period.id)"
                                                    style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.7rem; cursor: pointer; transition: all 0.2s;"
                                                    onmouseover="this.style.background='#e2e8f0'; this.style.color='#475569'"
                                                    onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b'"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </details>
                        </template>
                        
                        <!-- Order Summary using Pico CSS -->
                        <section style="margin-top: 1rem;" x-cloak>
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
                            
                            
                            <!-- Total Price -->
                            <div x-show="total > 0" x-cloak style="background: var(--primary-focus); color: white; padding: 0.75rem; border-radius: var(--border-radius); text-align: center; margin: 1rem 0; font-size: 0.9rem;">
                                <strong>Total: $<span x-text="total"></span></strong>
                            </div>
                            
                            <!-- Add to Cart Button -->
                            <form x-show="total > 0 && orderGroups.length > 0" x-cloak action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_groups" :value="JSON.stringify(orderGroups)">
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
                
                // Order management
                orderGroups: [],
                groupIdCounter: 1,
                
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
                                // Auto-select all periods by default
                                this.selectedPeriods = data.periods.map(period => period.id.toString());
                                this.selectAll = true;
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
                    // Auto-select all filtered periods
                    this.selectedPeriods = this.filteredPeriods.map(period => period.id.toString());
                    this.selectAll = true;
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
                    if (this.selectedPrice > 0 && this.totalPages > 0) {
                        this.total = parseFloat(this.totalPages * this.selectedPrice).toFixed(2);
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
                },

                // Add selected periods to order
                addToOrder() {
                    if (this.selectedPeriods.length === 0) return;
                    
                    const periodsToAdd = this.selectedPeriods.map(id => this.getPeriodById(id));
                    const yearRange = this.getYearRange();
                    const actualYearRange = this.calculateActualYearRange(periodsToAdd);
                    const groupId = this.groupIdCounter++;
                    
                    // Create new order group
                    const newGroup = {
                        id: groupId,
                        country: this.selectedCountry,
                        yearRange: yearRange,
                        actualYearRange: actualYearRange,
                        periods: periodsToAdd,
                        totalFiles: periodsToAdd.length,
                        totalPages: periodsToAdd.reduce((total, period) => total + parseInt(period.pages), 0),
                        expanded: false
                    };
                    
                    this.orderGroups.push(newGroup);
                    
                    // Clear selections
                    this.selectedPeriods = [];
                    this.selectAll = false;
                    
                    // Recalculate totals
                    this.calculateOrderTotals();
                },

                // Calculate total pages across all order groups
                calculateOrderTotals() {
                    this.totalPages = this.orderGroups.reduce((total, group) => total + group.totalPages, 0);
                    this.calculateTotal();
                },

                // Toggle group expansion
                toggleGroup(groupId) {
                    const group = this.orderGroups.find(g => g.id === groupId);
                    if (group) {
                        group.expanded = !group.expanded;
                    }
                },

                // Remove entire group
                removeGroup(groupId) {
                    this.orderGroups = this.orderGroups.filter(g => g.id !== groupId);
                    this.calculateOrderTotals();
                },

                // Remove individual period from group
                removePeriodFromGroup(groupId, periodId) {
                    const group = this.orderGroups.find(g => g.id === groupId);
                    if (group) {
                        group.periods = group.periods.filter(p => p.id != periodId);
                        group.totalFiles = group.periods.length;
                        group.totalPages = group.periods.reduce((total, period) => total + parseInt(period.pages), 0);
                        
                        // Remove group if no periods left
                        if (group.periods.length === 0) {
                            this.removeGroup(groupId);
                        } else {
                            // Update actual year range
                            group.actualYearRange = this.calculateActualYearRange(group.periods);
                            this.calculateOrderTotals();
                        }
                    }
                },

                // Calculate actual year range from selected periods
                calculateActualYearRange(periods) {
                    if (periods.length === 0) return '';
                    
                    const years = periods.map(period => {
                        const yearMatch = period.description.match(/\d{4}/);
                        return yearMatch ? parseInt(yearMatch[0]) : null;
                    }).filter(year => year !== null);
                    
                    if (years.length === 0) return 'Various';
                    if (years.length === 1) return years[0].toString();
                    
                    const minYear = Math.min(...years);
                    const maxYear = Math.max(...years);
                    
                    return minYear === maxYear ? minYear.toString() : `${minYear} - ${maxYear}`;
                }
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>


</x-layout>
