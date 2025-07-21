<x-layout>
    <style>
        /* Hide elements initially to prevent FOUC */
        [x-cloak] { display: none !important; }
        
        /* Enhanced country suggestions styling */
        .country-suggestions {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
        
        .country-suggestions::-webkit-scrollbar {
            width: 6px;
        }
        
        .country-suggestions::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 3px;
        }
        
        .country-suggestions::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        .country-suggestions::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .last-item {
            border-bottom: none !important;
        }
        
        /* Enhanced input focus styling */
        #country:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            outline: none;
        }
        
        /* Consistent Left Panel Styling */
        .panel-section {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .panel-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1rem;
            display: block;
        }
        
        .input-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .input-field {
            display: flex;
            flex-direction: column;
        }
        
        .input-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .range-input {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            cursor: pointer;
        }
        
        .range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        
        .range-input::-webkit-slider-thumb:hover {
            background: #1d4ed8;
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(59, 130, 246, 0.4);
        }
        
        .range-input::-moz-range-thumb {
            width: 18px;
            height: 18px;
            background: #3b82f6;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        
        .range-input::-moz-range-thumb:hover {
            background: #1d4ed8;
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(59, 130, 246, 0.4);
        }
        
        .range-value-display {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #3b82f6;
            text-align: center;
            min-width: 60px;
        }
        
        .add-button {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }
        
        .add-button:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
    </style>
    <main class="container" x-data="orderPageData()">
        <!-- Two Column Grid using Pico CSS -->
        <div class="grid">
            
            <!-- Left Column - Select Items (50%) -->
            <div style="grid-column: span 6;">
                <!-- <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 2rem;">Select Items</h2> -->
                
                <!-- Country Selection Section -->
                <div class="panel-section">
                    <div class="panel-title">Country</div>
                    <input 
                        type="text" 
                        id="country"
                        x-model="countryQuery" 
                        placeholder="Search for a country..."
                        @input="filterCountries"
                        @keydown="handleKeydown($event)"
                        autocomplete="off"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem; background-image: none;"
                    />
                    
                    <!-- Country Suggestions -->
                    <div x-show="filteredCountries.length > 0 && countryQuery.length > 1" 
                         class="country-suggestions"
                         style="border: 2px solid #e2e8f0; border-radius: 8px; max-height: 240px; overflow-y: auto; background: white; margin-top: 0.5rem; z-index: 1000; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                        <template x-for="(country, index) in filteredCountries" :key="country.name">
                            <div style="padding: 0.875rem 1rem; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: all 0.2s ease; font-size: 0.95rem;" 
                                 :style="highlightedIndex === index ? 
                                    'background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; border-left: 4px solid #1e40af; transform: translateX(2px); box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1);' : 
                                    'background: white; color: #374151; border-left: 4px solid transparent;'"
                                 @click="selectCountry(country.name)"
                                 @mouseenter="highlightedIndex = index"
                                 @mouseleave="highlightedIndex = -1"
                                 :class="index === filteredCountries.length - 1 ? 'last-item' : ''">
                                <span x-html="highlightMatch(country.name)" 
                                      :style="highlightedIndex === index ? 'font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.1);' : 'font-weight: 500;'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Year Range Selection Section -->
                <div x-show="selectedCountry && availablePeriods.length > 0" x-cloak class="panel-section">
                <div class="panel-title">Year Range</div>
                    
                    <div class="input-group">
                        <div class="input-field">
                            <label class="input-label">From Year</label>
                            <input 
                                type="range" 
                                class="range-input"
                                :min="minAvailableYear"
                                :max="maxAvailableYear"
                                x-model="startYear"
                                @input="handleStartYearChange"
                            />
                            <div class="range-value-display" x-text="startYear || minAvailableYear"></div>
                        </div>
                        <div class="input-field">
                            <label class="input-label">To Year</label>
                            <input 
                                type="range" 
                                class="range-input"
                                :min="minAvailableYear"
                                :max="maxAvailableYear"
                                x-model="endYear"
                                @input="handleEndYearChange"
                            />
                            <div class="range-value-display" x-text="endYear || maxAvailableYear"></div>
                        </div>
                    </div>
                </div>

                <!-- Periods Selection Section -->
                <div x-show="selectedCountry && availablePeriods.length > 0" x-cloak class="panel-section" style="padding:0;">
                    <!-- <div class="panel-title">Available Periods</div> -->
                    
                    <!-- Periods Table using Pico CSS -->
                    <table x-show="filteredPeriods.length > 0" x-cloak role="grid" class="striped" style="table-layout: fixed; width: 100%; margin: 0;">
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
                    
                    <!-- Add to Order Button -->
                    <div x-show="selectedPeriods.length > 0" x-cloak style="margin-top: 1.5rem; text-align: center;">
                        <button @click="addToOrder()" class="add-button">
                         + Add to Order
                        </button>
                    </div>
                </div>
            </div>

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
                highlightedIndex: -1,
                
                // Period/Year selection
                availablePeriods: [],
                filteredPeriods: [],
                selectedPeriods: [],
                availableYears: [],
                startYear: '',
                endYear: '',
                minAvailableYear: 1900,
                maxAvailableYear: 2024,
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
                    // Reset highlighted index when filtering
                    this.highlightedIndex = -1;
                },

                // Highlight matching text in country names
                highlightMatch(name) {
                    if (!this.countryQuery) return name;
                    const regex = new RegExp(`(${this.countryQuery})`, 'gi');
                    return name.replace(regex, "<b><ins>$1</ins></b>");
                },

                // Handle keyboard navigation
                handleKeydown(event) {
                    if (this.filteredCountries.length === 0) return;

                    switch (event.key) {
                        case 'ArrowDown':
                            event.preventDefault();
                            this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredCountries.length - 1);
                            break;
                        case 'ArrowUp':
                            event.preventDefault();
                            this.highlightedIndex = Math.max(this.highlightedIndex - 1, -1);
                            break;
                        case 'Enter':
                            event.preventDefault();
                            if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredCountries.length) {
                                this.selectCountry(this.filteredCountries[this.highlightedIndex].name);
                            }
                            break;
                        case 'Escape':
                            this.filteredCountries = [];
                            this.highlightedIndex = -1;
                            break;
                    }
                },

                // Select a country and load its periods
                selectCountry(countryName) {
                    this.selectedCountry = countryName;
                    this.countryQuery = countryName;
                    this.filteredCountries = [];
                    this.highlightedIndex = -1;
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
                    
                    // Set min/max available years
                    if (this.availableYears.length > 0) {
                        this.minAvailableYear = Math.min(...this.availableYears);
                        this.maxAvailableYear = Math.max(...this.availableYears);
                        // Set default range to full range
                        this.startYear = this.minAvailableYear;
                        this.endYear = this.maxAvailableYear;
                    }
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
                },

                // Handle start year slider change
                handleStartYearChange() {
                    // Ensure start year doesn't exceed end year
                    if (parseInt(this.startYear) > parseInt(this.endYear)) {
                        this.endYear = this.startYear;
                    }
                    this.filterPeriodsByYear();
                },

                // Handle end year slider change
                handleEndYearChange() {
                    // Ensure end year doesn't go below start year
                    if (parseInt(this.endYear) < parseInt(this.startYear)) {
                        this.startYear = this.endYear;
                    }
                    this.filterPeriodsByYear();
                },

                // Get the style for the range fill
                getRangeFillStyle() {
                    const start = parseInt(this.startYear) || this.minAvailableYear;
                    const end = parseInt(this.endYear) || this.maxAvailableYear;
                    const min = this.minAvailableYear;
                    const max = this.maxAvailableYear;
                    
                    const startPercent = ((start - min) / (max - min)) * 100;
                    const endPercent = ((end - min) / (max - min)) * 100;
                    
                    return `left: ${startPercent}%; width: ${endPercent - startPercent}%;`;
                }
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>


</x-layout>
