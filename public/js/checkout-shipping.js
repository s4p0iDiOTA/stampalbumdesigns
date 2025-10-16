// Enhanced Checkout JavaScript for Dynamic Shipping Rates
document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutWithShipping', () => ({
        // Shipping state
        shippingRates: [],
        loadingRates: false,
        ratesLoaded: false,
        selectedShippingMethod: null,
        shippingBreakdown: null,

        // Address state
        shippingZip: '',
        shippingState: '',
        shippingCity: '',

        // UI state
        showShippingDetails: false,

        init() {
            // Watch for zip code changes to fetch rates
            this.$watch('shippingZip', (value) => {
                if (value.length >= 5) {
                    this.fetchShippingRates();
                }
            });

            // Load initial shipping breakdown
            this.loadShippingBreakdown();
        },

        async fetchShippingRates() {
            if (!this.shippingZip || this.shippingZip.length < 5) {
                return;
            }

            this.loadingRates = true;

            try {
                const response = await fetch('/api/shipping/rates', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        zip: this.shippingZip,
                        state: this.shippingState,
                        city: this.shippingCity,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.shippingRates = data.rates;
                    this.shippingBreakdown = data.breakdown;
                    this.ratesLoaded = true;

                    // Auto-select cheapest option
                    if (this.shippingRates.length > 0 && !this.selectedShippingMethod) {
                        this.selectedShippingMethod = this.shippingRates[0].service_code;
                    }
                } else {
                    console.error('Failed to load shipping rates:', data.message);
                    // Use fallback rates if provided
                    if (data.rates) {
                        this.shippingRates = data.rates;
                        this.ratesLoaded = true;
                    }
                }
            } catch (error) {
                console.error('Error fetching shipping rates:', error);
            } finally {
                this.loadingRates = false;
            }
        },

        async loadShippingBreakdown() {
            try {
                const response = await fetch('/api/shipping/breakdown');
                const data = await response.json();

                if (data.success) {
                    this.shippingBreakdown = data.breakdown;
                }
            } catch (error) {
                console.error('Error loading shipping breakdown:', error);
            }
        },

        getSelectedRate() {
            if (!this.selectedShippingMethod || !this.shippingRates.length) {
                return null;
            }
            return this.shippingRates.find(r => r.service_code === this.selectedShippingMethod);
        },

        calculateTotal(cartTotal) {
            const selectedRate = this.getSelectedRate();
            const shippingCost = selectedRate ? selectedRate.cost : 0;
            return (parseFloat(cartTotal) + parseFloat(shippingCost)).toFixed(2);
        },

        formatWeight(weightOz) {
            if (!weightOz) return '0 oz';

            const lbs = Math.floor(weightOz / 16);
            const oz = (weightOz % 16).toFixed(1);

            if (lbs > 0) {
                return oz > 0 ? `${lbs} lb ${oz} oz` : `${lbs} lb`;
            }
            return `${oz} oz`;
        },

        toggleShippingDetails() {
            this.showShippingDetails = !this.showShippingDetails;
        }
    }));
});
