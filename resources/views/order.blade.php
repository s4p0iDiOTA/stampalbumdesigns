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

        /* Pico CSS Range styling is handled automatically */

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
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">Build Your Order</h2>

                <!-- Step 1: Paper Configuration -->
                <div class="panel-section">
                    <div class="panel-title">1. Configure Paper</div>

                    <!-- Step 1a: Select Paper Size -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Paper Size</label>
                        <select
                            x-model="paperConfig.size"
                            @change="loadPaperOptions()"
                            style="font-size: 0.95rem; width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px;"
                        >
                            <option value="">Select size...</option>
                            <template x-for="size in availableSizes" :key="size.id">
                                <option :value="size.id" x-text="`${size.name} - $${size.base_price.toFixed(2)}/page base`"></option>
                            </template>
                        </select>
                        <div x-show="selectedSizeInfo" style="margin-top: 0.5rem; padding: 0.75rem; background: #f0f9ff; border-left: 3px solid #3b82f6; border-radius: 4px;">
                            <div style="font-size: 0.875rem; color: #1e40af;">
                                <strong x-text="selectedSizeInfo?.name"></strong>
                                <p x-text="selectedSizeInfo?.description" style="margin: 0.25rem 0;"></p>
                                <p x-show="selectedSizeInfo?.badge" style="margin: 0.25rem 0;">
                                    <span style="background: #10b981; color: white; padding: 0.125rem 0.5rem; border-radius: 3px; font-size: 0.75rem; font-weight: 600;" x-text="selectedSizeInfo?.badge"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Paper Options (shown when size is selected) -->
                    <div x-show="paperConfig.size && paperOptions" style="display: none;">

                        <!-- Paper Weight -->
                        <div x-show="paperOptions?.paper_weights?.length > 1" style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Paper Weight</label>
                            <select
                                x-model="paperConfig.options.paper_weight"
                                @change="calculateCurrentPrice()"
                                style="font-size: 0.95rem; width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px;"
                            >
                                <template x-for="weight in paperOptions.paper_weights" :key="weight.id">
                                    <option :value="weight.id" x-text="`${weight.name} ${formatPriceModifier(weight.price_modifier)}`"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Color -->
                        <div x-show="paperOptions?.colors?.length > 1" style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Paper Color</label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem;">
                                <template x-for="color in paperOptions.colors" :key="color.id">
                                    <label
                                        style="cursor: pointer; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px; transition: all 0.2s;"
                                        :style="paperConfig.options.color === color.id ? 'border-color: #3b82f6; background: #eff6ff;' : ''"
                                    >
                                        <input
                                            type="radio"
                                            :value="color.id"
                                            x-model="paperConfig.options.color"
                                            @change="calculateCurrentPrice()"
                                            style="display: none;"
                                        />
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="width: 24px; height: 24px; border-radius: 4px; border: 1px solid #d1d5db;" :style="`background: ${color.hex};`"></div>
                                            <div style="flex: 1;">
                                                <div style="font-size: 0.875rem; font-weight: 600;" x-text="color.name"></div>
                                                <div style="font-size: 0.75rem; color: #6b7280;" x-text="formatPriceModifier(color.price_modifier)"></div>
                                            </div>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Punches -->
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Hole Punch</label>
                            <select
                                x-model="paperConfig.options.punches"
                                @change="calculateCurrentPrice()"
                                style="font-size: 0.95rem; width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px;"
                            >
                                <template x-for="punch in paperOptions.punches" :key="punch.id">
                                    <option :value="punch.id" x-text="`${punch.name} ${formatPriceModifier(punch.price_modifier)}`"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Corners -->
                        <div x-show="paperOptions?.corners?.length > 1" style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Corner Style</label>
                            <select
                                x-model="paperConfig.options.corners"
                                @change="calculateCurrentPrice()"
                                style="font-size: 0.95rem; width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px;"
                            >
                                <template x-for="corner in paperOptions.corners" :key="corner.id">
                                    <option :value="corner.id" x-text="`${corner.name} ${formatPriceModifier(corner.price_modifier)}`"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Protection/Mounts -->
                        <div x-show="paperOptions?.protection?.length > 1" style="margin-bottom: 1rem;">
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151;">Mount Type</label>
                            <select
                                x-model="paperConfig.options.protection"
                                @change="calculateCurrentPrice()"
                                style="font-size: 0.95rem; width: 100%; padding: 0.75rem; border: 2px solid #d1d5db; border-radius: 6px;"
                            >
                                <template x-for="protection in paperOptions.protection" :key="protection.id">
                                    <option :value="protection.id" x-text="`${protection.name} ${formatPriceModifier(protection.price_modifier)}`"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Current Price Display -->
                        <div style="padding: 1rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.875rem; margin-bottom: 0.25rem;">Current Price Per Page</div>
                            <div style="font-size: 2rem; font-weight: 700;">
                                $<span x-text="currentPricePerPage.toFixed(2)"></span>
                            </div>
                            <div style="font-size: 0.75rem; margin-top: 0.25rem; opacity: 0.9;" x-text="currentConfigSku"></div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Country Selection Section -->
                <div class="panel-section" x-data="{ dropdownOpen: false }" @click.outside="filteredCountries = []">
                    <div class="panel-title">2. Select Country</div>
                    <div style="position: relative;">
                        <input
                            type="text"
                            id="country"
                            x-model="countryQuery"
                            placeholder="Search for a country..."
                            @input="filterCountries"
                            @focus="showAllCountries"
                            @click="showAllCountries"
                            @keydown="handleKeydown($event)"
                            autocomplete="off"
                            style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.95rem; background-image: none;"
                        />
                        <button
                            x-show="countryQuery.length > 0"
                            @click="clearCountrySearch()"
                            style="position: absolute; right: 0.5rem; top: 27px; transform: translateY(-50%); background: #ef4444; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 1.2rem; padding: 0; display: flex; align-items: center; justify-content: center; line-height: 1; transition: all 0.2s;"
                            onmouseover="this.style.background='#dc2626'; this.style.transform='translateY(-50%) scale(1.1)'"
                            onmouseout="this.style.background='#ef4444'; this.style.transform='translateY(-50%) scale(1)'"
                            title="Clear search"
                        >
                            ×
                        </button>
                    </div>

                    <!-- Country Suggestions -->
                    <div x-show="filteredCountries.length > 0"
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

                <!-- Step 3: Year Range Selection Section -->
                <div x-show="selectedCountry && availablePeriods.length > 0 && availableYears.length > 0" x-cloak class="panel-section">
                <div class="panel-title">3. Select Year Range</div>

                    <div class="input-group">
                        <div class="input-field">
                            <label class="input-label">From Year</label>
                            <input
                                type="range"
                                :min="minAvailableYear"
                                :max="maxAvailableYear"
                                x-model="startYear"
                                @input="handleStartYearChange"
                            />
                            <select x-model="startYear" @change="handleStartYearChange" style="margin-top: 0.5rem; font-size: 0.875rem;">
                                <template x-for="year in availableYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                        <div class="input-field">
                            <label class="input-label">To Year</label>
                            <input
                                type="range"
                                :min="minAvailableYear"
                                :max="maxAvailableYear"
                                x-model="endYear"
                                @input="handleEndYearChange"
                            />
                            <select x-model="endYear" @change="handleEndYearChange" style="margin-top: 0.5rem; font-size: 0.875rem;">
                                <template x-for="year in availableYears" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Files Selection Section -->
                <div x-show="selectedCountry && availablePeriods.length > 0" x-cloak class="panel-section" style="padding:0;">
                    <div class="panel-title" style="padding: 1.5rem 1.5rem 0 1.5rem;">4. Select Files</div>

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
                                    <td style="width: 35%;" x-text="period.description"></td>
                                    <td style="width: 35%;" x-text="period.pagesInRange || period.pages"></td>
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
                <!-- Persistent Cart (Session-based) -->
                @if(!empty($cart))
                    <article style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #10b981;">
                        <header style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; margin: -1rem -1rem 1rem -1rem; padding: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h5 style="margin: 0; color: white;">🛒 Cart ({{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'items' }})</h5>
                                <a href="{{ route('checkout.index') }}" style="background: white; color: #059669; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;">
                                    Proceed to Checkout →
                                </a>
                            </div>
                        </header>

                        @php
                            $cartTotal = 0;
                            $cartPages = 0;
                            foreach($cart as $item) {
                                // Safety check for new cart structure
                                if(isset($item['total']) && isset($item['quantity'])) {
                                    $cartTotal += floatval($item['total']) * intval($item['quantity']);
                                    if(isset($item['order_groups'])) {
                                        foreach($item['order_groups'] as $group) {
                                            $cartPages += intval($group['totalPages']) * intval($item['quantity']);
                                        }
                                    }
                                }
                            }
                        @endphp

                        <div style="font-size: 0.9rem;">
                            @foreach($cart as $itemId => $item)
                                @if(isset($item['total']) && isset($item['quantity']))
                                    <div style="background: white; border: 1px solid #d1fae5; border-radius: 6px; padding: 0.75rem; margin-bottom: 0.75rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                            <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                                Order #{{ substr($itemId, -8) }}
                                            </div>
                                            <div style="font-weight: 700; color: #059669;">
                                                ${{ number_format($item['total'], 2) }}
                                            </div>
                                        </div>
                                        @if(isset($item['order_groups']))
                                            @foreach($item['order_groups'] as $group)
                                                <div style="font-size: 0.8rem; color: #64748b; padding: 0.25rem 0;">
                                                    {{ $group['country'] }} ({{ $group['actualYearRange'] }}) - {{ $group['totalPages'] }} pages
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            <div style="background: white; border: 2px solid #10b981; border-radius: 6px; padding: 0.75rem; margin-top: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 500;">Total Pages:</span>
                                    <span style="font-weight: 600; color: #3b82f6;">{{ $cartPages }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 1px solid #d1fae5;">
                                    <span style="font-weight: 700;">Cart Total:</span>
                                    <span style="font-weight: 700; color: #059669; font-size: 1.1rem;">${{ number_format($cartTotal, 2) }}</span>
                                </div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                                <a href="{{ route('checkout.index') }}" style="flex: 1; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 0.75rem; border-radius: 6px; text-decoration: none; font-weight: 600; text-align: center; font-size: 0.9rem;">
                                    Checkout
                                </a>
                                <a href="{{ route('checkout.clear') }}" style="background: #ef4444; color: white; padding: 0.75rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem;" onclick="return confirm('Clear all items from cart?')">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </article>
                @endif

                <article>
                    <header>
                        <h5>Your Order Summary</h5>
                    </header>

                    <div x-show="orderGroups.length === 0" x-cloak style="text-align: center; padding: 3rem 0;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <p style="color: #64748b;">Your order will appear here</p>
                        <p style="font-size: 0.85rem; color: #94a3b8;">Start by selecting a paper type</p>
                    </div>

                    <div x-show="orderGroups.length > 0" x-cloak>
                        <!-- Group by Paper Type -->
                        <template x-for="[paperType, groups] in Object.entries(groupedByPaperType())" :key="paperType">
                            <div style="margin-bottom: 1.5rem;">
                                <!-- Paper Type Header -->
                                <div style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; padding: 0.875rem 1.25rem; border-radius: 8px 8px 0 0; font-weight: 700; font-size: 1rem; display: flex; justify-content: space-between; align-items: center;">
                                    <span x-text="getPaperTypeName(paperType)"></span>
                                    <span style="background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem;">
                                        $<span x-text="paperType"></span>/page
                                    </span>
                                </div>

                                <!-- Countries under this paper type -->
                                <div style="border: 2px solid #e2e8f0; border-top: none; border-radius: 0 0 8px 8px; overflow: hidden;">
                                    <template x-for="group in groups" :key="group.id">
                                        <div style="border-bottom: 1px solid #e2e8f0; last:border-bottom-none;">
                                            <div style="background: #f8fafc; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                                                <div style="flex: 1;">
                                                    <div style="font-size: 1.05rem; font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;" x-text="group.country"></div>
                                                    <div style="font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="opacity: 0.7;">
                                                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                                            </svg>
                                                            <span x-text="group.actualYearRange"></span>
                                                        </span>
                                                        <span>•</span>
                                                        <span style="display: flex; align-items: center; gap: 0.25rem;">
                                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="opacity: 0.7;">
                                                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                                                            </svg>
                                                            <span x-text="group.totalPages + ' pages'"></span>
                                                        </span>
                                                        <span>•</span>
                                                        <span style="font-weight: 600; color: #059669;">
                                                            $<span x-text="(group.totalPages * parseFloat(group.paperType)).toFixed(2)"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <button
                                                    @click="removeGroup(group.id)"
                                                    style="background: #ef4444; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.3rem; flex-shrink: 0; margin-left: 1rem;"
                                                    onmouseover="this.style.background='#dc2626'; this.style.transform='scale(1.05)'"
                                                    onmouseout="this.style.background='#ef4444'; this.style.transform='scale(1)'"
                                                    title="Remove this country"
                                                >
                                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Order Summary using Pico CSS -->
                        <section style="margin-top: 1rem;" x-cloak>
                            <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                <h3 style="margin-top: 0; margin-bottom: 0.75rem; font-size: 1.1rem;">Order Summary</h3>
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                                    <span style="font-weight: 500;">Total Pages:</span>
                                    <span style="font-weight: 600; color: #3b82f6;" x-text="totalPages"></span>
                                </div>
                                <div x-show="total > 0" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 1.2rem; margin-top: 0.5rem;">
                                    <span style="font-weight: 700;">Total:</span>
                                    <span style="font-weight: 700; color: #059669;">$<span x-text="total.toFixed(2)"></span></span>
                                </div>
                                <div x-show="total === 0 && orderGroups.length > 0" style="padding: 0.5rem 0; color: #ef4444; font-size: 0.85rem; text-align: center;">
                                    Please select paper type for each country
                                </div>
                            </div>

                            <!-- Add to Cart Button -->
                            <form x-show="total > 0 && orderGroups.length > 0" x-cloak action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="order_groups" :value="JSON.stringify(orderGroups)">
                                <input type="hidden" name="quantity" :value="quantity">
                                <input type="hidden" name="total" :value="total.toFixed(2)">

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
                paperConfig: {
                    size: '',
                    options: {
                        paper_weight: '',
                        color: '',
                        punches: '',
                        corners: '',
                        protection: ''
                    }
                },
                availableSizes: [],
                paperOptions: null,
                selectedSizeInfo: null,
                currentPricePerPage: 0,
                currentConfigSku: '',

                selectedPaperType: 0,  // Deprecated, kept for compatibility
                quantity: 1,
                total: 0,
                totalPages: 0,

                // JSON data
                countryYearPageDict: {},
                pageCountPerFile: {},

                // Initialize component
                init() {
                    // Load paper sizes
                    this.loadPaperSizes();

                    // Load country year page dictionary
                    fetch("{{ url('/country_year_page_dict.json') }}")
                        .then(response => response.json())
                        .then(data => {
                            this.countryYearPageDict = data;
                            // Extract country names
                            this.countries = Object.keys(data).map(name => ({ name: name }));
                        })
                        .catch(error => console.error('Error loading country year page dict:', error));

                    // Load page count per file
                    fetch("{{ url('/page_count_per_file_per_country.json') }}")
                        .then(response => response.json())
                        .then(data => {
                            this.pageCountPerFile = data;
                        })
                        .catch(error => console.error('Error loading page count per file:', error));
                },

                // Paper Configuration Methods
                async loadPaperSizes() {
                    try {
                        const response = await fetch('/api/paper-sizes');
                        const result = await response.json();
                        if (result.success) {
                            this.availableSizes = result.data;
                            // Auto-select default size
                            const defaultSize = this.availableSizes.find(s => s.is_default);
                            if (defaultSize) {
                                this.paperConfig.size = defaultSize.id;
                                await this.loadPaperOptions();
                            }
                        }
                    } catch (error) {
                        console.error('Error loading paper sizes:', error);
                    }
                },

                async loadPaperOptions() {
                    if (!this.paperConfig.size) {
                        this.paperOptions = null;
                        this.selectedSizeInfo = null;
                        return;
                    }

                    try {
                        // Get size info
                        this.selectedSizeInfo = this.availableSizes.find(s => s.id === this.paperConfig.size);

                        // Get available options for this size
                        const response = await fetch(`/api/paper-sizes/${this.paperConfig.size}/options`);
                        const result = await response.json();

                        if (result.success) {
                            this.paperOptions = result.data;

                            // Set defaults from size
                            const defaults = this.selectedSizeInfo.default_options;
                            this.paperConfig.options.paper_weight = defaults.paper_weight;
                            this.paperConfig.options.color = defaults.color;
                            this.paperConfig.options.punches = defaults.punches;
                            this.paperConfig.options.corners = defaults.corners;
                            this.paperConfig.options.protection = defaults.protection;

                            // Calculate initial price
                            await this.calculateCurrentPrice();
                        }
                    } catch (error) {
                        console.error('Error loading paper options:', error);
                    }
                },

                async calculateCurrentPrice() {
                    if (!this.paperConfig.size) {
                        this.currentPricePerPage = 0;
                        this.currentConfigSku = '';
                        return;
                    }

                    try {
                        const response = await fetch('/api/paper-configurations/calculate', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                size: this.paperConfig.size,
                                options: this.paperConfig.options,
                                pages: 1
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.currentPricePerPage = result.data.price_per_page;
                            this.currentConfigSku = result.data.sku;
                        }
                    } catch (error) {
                        console.error('Error calculating price:', error);
                    }
                },

                formatPriceModifier(modifier) {
                    if (!modifier || modifier === 0) return '';
                    return modifier > 0 ? `(+$${modifier.toFixed(2)})` : `(-$${Math.abs(modifier).toFixed(2)})`;
                },

                // Show all countries when input is focused
                showAllCountries() {
                    if (this.countryQuery.length === 0) {
                        this.filteredCountries = [...this.countries];
                    } else {
                        this.filterCountries();
                    }
                },

                // Filter countries based on search query
                filterCountries() {
                    if (this.countryQuery.length > 0) {
                        this.filteredCountries = this.countries.filter(country =>
                            country.name.toLowerCase().includes(this.countryQuery.toLowerCase())
                        );
                    } else {
                        this.filteredCountries = [...this.countries];
                    }
                    // Reset highlighted index when filtering
                    this.highlightedIndex = -1;
                },

                // Clear country search
                clearCountrySearch() {
                    this.countryQuery = '';
                    this.filteredCountries = [];
                    this.highlightedIndex = -1;
                    this.selectedCountry = '';
                    this.availablePeriods = [];
                    this.filteredPeriods = [];
                    this.selectedPeriods = [];
                    this.availableYears = [];
                    this.selectAll = false;
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

                    // Build periods from JSON data
                    if (!this.countryYearPageDict[countryName]) {
                        console.error('Country not found in data:', countryName);
                        return;
                    }

                    const countryData = this.countryYearPageDict[countryName];
                    const fileDescriptions = this.pageCountPerFile[countryName] || {};

                    // Group by file description to create periods
                    const periodsMap = new Map();
                    let periodId = 1;

                    Object.keys(countryData).forEach(year => {
                        Object.keys(countryData[year]).forEach(fileDesc => {
                            if (!periodsMap.has(fileDesc)) {
                                periodsMap.set(fileDesc, {
                                    id: periodId++,
                                    description: fileDesc,
                                    pages: fileDescriptions[fileDesc] || 0,
                                    years: new Set(),
                                    yearPageMap: {}
                                });
                            }
                            periodsMap.get(fileDesc).years.add(parseInt(year));
                            periodsMap.get(fileDesc).yearPageMap[year] = countryData[year][fileDesc];
                        });
                    });

                    // Convert map to array
                    this.availablePeriods = Array.from(periodsMap.values()).map(period => ({
                        ...period,
                        years: Array.from(period.years).sort((a, b) => a - b)
                    }));

                    this.filteredPeriods = this.availablePeriods;
                    this.generateAvailableYears();

                    // Auto-select all periods by default
                    this.selectedPeriods = this.availablePeriods.map(period => period.id.toString());
                    this.selectAll = true;
                },

                // Generate available years from periods
                generateAvailableYears() {
                    const years = new Set();
                    this.availablePeriods.forEach(period => {
                        // Use actual years from period data
                        if (period.years && period.years.length > 0) {
                            period.years.forEach(year => years.add(year));
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
                        const start = this.startYear ? parseInt(this.startYear) : 0;
                        const end = this.endYear ? parseInt(this.endYear) : 9999;

                        filtered = this.availablePeriods.map(period => {
                            // Filter years within the range
                            const filteredYears = period.years.filter(year => year >= start && year <= end);

                            if (filteredYears.length === 0) return null;

                            // Calculate actual page count for filtered years
                            const pagesInRange = this.calculatePagesForYearRange(period, filteredYears);

                            return {
                                ...period,
                                filteredYears: filteredYears,
                                pagesInRange: pagesInRange
                            };
                        }).filter(p => p !== null);
                    } else {
                        // No filter, use all periods
                        filtered = this.availablePeriods.map(period => ({
                            ...period,
                            filteredYears: period.years,
                            pagesInRange: period.pages
                        }));
                    }

                    this.filteredPeriods = filtered;
                    // Auto-select all filtered periods
                    this.selectedPeriods = this.filteredPeriods.map(period => period.id.toString());
                    this.selectAll = true;
                },

                // Calculate pages for a specific year range
                calculatePagesForYearRange(period, years) {
                    const allPages = new Set();

                    years.forEach(year => {
                        const yearStr = year.toString();
                        if (period.yearPageMap[yearStr]) {
                            period.yearPageMap[yearStr].forEach(page => allPages.add(page));
                        }
                    });

                    return allPages.size;
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
                        if (!period) return total;
                        // Use pagesInRange if available (filtered), otherwise use pages
                        const pageCount = period.pagesInRange !== undefined ? period.pagesInRange : period.pages;
                        return total + parseInt(pageCount);
                    }, 0);
                },

                // Calculate total price from all groups
                async calculateTotal() {
                    let totalPrice = 0;

                    for (const group of this.orderGroups) {
                        if (group.paper_size && group.paper_options) {
                            try {
                                const response = await fetch('/api/paper-configurations/calculate', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        size: group.paper_size,
                                        options: group.paper_options,
                                        pages: group.totalPages
                                    })
                                });

                                const result = await response.json();
                                if (result.success) {
                                    totalPrice += result.data.total_price;
                                }
                            } catch (error) {
                                console.error('Error calculating group price:', error);
                            }
                        }
                    }

                    this.total = totalPrice;
                },

                // Group order groups by paper type
                groupedByPaperType() {
                    const grouped = {};
                    this.orderGroups.forEach(group => {
                        const paperType = group.paperType || 0;
                        if (!grouped[paperType]) {
                            grouped[paperType] = [];
                        }
                        grouped[paperType].push(group);
                    });
                    return grouped;
                },

                // Get paper type display name from price
                getPaperTypeName(price) {
                    const names = {
                        '0.20': 'Heavyweight, 3-hole',
                        '0.30': 'Scott International / Minkus 2-hole',
                        '0.35': 'Scott Specialized'
                    };
                    return names[price] || 'Custom Paper Type';
                },

                // Group order groups by paper configuration (New method)
                groupedByPaperConfig() {
                    const grouped = {};
                    this.orderGroups.forEach(group => {
                        const configKey = `${group.paper_size || 'none'}-${JSON.stringify(group.paper_options || {})}`;
                        if (!grouped[configKey]) {
                            grouped[configKey] = {
                                config: { size: group.paper_size, options: group.paper_options },
                                groups: []
                            };
                        }
                        grouped[configKey].groups.push(group);
                    });
                    return grouped;
                },

                // Get period object by ID
                getPeriodById(periodId) {
                    // First try to find in filtered periods (has pagesInRange)
                    const filteredPeriod = this.filteredPeriods.find(period => period.id == periodId);
                    if (filteredPeriod) return filteredPeriod;
                    // Fallback to available periods
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
                    if (!this.paperConfig.size) {
                        alert('Please select a paper size first');
                        return;
                    }

                    const periodsToAdd = this.selectedPeriods.map(id => this.getPeriodById(id));
                    const yearRange = this.getYearRange();
                    const actualYearRange = this.calculateActualYearRange(periodsToAdd);
                    const groupId = this.groupIdCounter++;

                    // Calculate total pages using pagesInRange if available
                    const totalPages = periodsToAdd.reduce((total, period) => {
                        const pageCount = period.pagesInRange !== undefined ? period.pagesInRange : period.pages;
                        return total + parseInt(pageCount);
                    }, 0);

                    // Create new order group with paper configuration
                    const newGroup = {
                        id: groupId,
                        country: this.selectedCountry,
                        yearRange: yearRange,
                        actualYearRange: actualYearRange,
                        periods: periodsToAdd,
                        totalFiles: periodsToAdd.length,
                        totalPages: totalPages,
                        paper_size: this.paperConfig.size,
                        paper_options: { ...this.paperConfig.options },  // Store current paper configuration
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
                        // Use pagesInRange if available
                        group.totalPages = group.periods.reduce((total, period) => {
                            const pageCount = period.pagesInRange !== undefined ? period.pagesInRange : period.pages;
                            return total + parseInt(pageCount);
                        }, 0);

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

                    const allYears = new Set();
                    periods.forEach(period => {
                        // Use filteredYears if available, otherwise use all years
                        const yearsToUse = period.filteredYears || period.years || [];
                        yearsToUse.forEach(year => allYears.add(year));
                    });

                    const years = Array.from(allYears).sort((a, b) => a - b);

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
