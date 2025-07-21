<x-layout>
    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 0;
        }
        
        .hero-content h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-content p {
            font-size: clamp(1.1rem, 2.5vw, 1.3rem);
            margin-bottom: 2.5rem;
            color: white;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .hero-cta {
            display: inline-flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .cta-primary {
            background: white;
            color: #1e40af;
            border: none;
            padding: 1.2rem 2.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .cta-primary:hover {
            background: #f1f5f9;
            color: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .cta-secondary {
            background: transparent;
            color: white;
            border: 3px solid white;
            padding: 1.2rem 2.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .cta-secondary:hover {
            background: white;
            color: #1e40af;
        }
        
        /* Features Section */
        .features-section {
            padding: 5rem 0;
            background: #f8fafc;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: clamp(2.2rem, 4vw, 2.8rem);
            color: #1e293b;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        .section-title p {
            font-size: 1.3rem;
            color: #475569;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            display: block;
        }
        
        .feature-card h3 {
            color: #1e293b;
            margin-bottom: 1.2rem;
            font-size: 1.6rem;
            font-weight: 600;
        }
        
        .feature-card p {
            color: #334155;
            line-height: 1.7;
            font-size: 1.1rem;
        }
        
        /* Pricing Section */
        .pricing-section {
            padding: 5rem 0;
            background: white;
        }
        
        .pricing-card {
            background: white;
            border: 2px solid #e2e8f0;
            padding: 2.5rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .pricing-card:hover {
            border-color: #2563eb;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
        }
        
        .pricing-card.featured {
            border-color: #2563eb;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            transform: scale(1.05);
        }
        
        .pricing-card.featured .feature-icon {
            color: white;
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }
        
        .price-unit {
            font-size: 1rem;
            font-weight: normal;
            opacity: 0.8;
        }
        
        /* CTA Section */
        .final-cta {
            padding: 5rem 0;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            text-align: center;
        }
        
        .final-cta h2 {
            font-size: clamp(2.2rem, 4vw, 2.8rem);
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: white;
        }
        
        .final-cta p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            color: #e2e8f0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .hero-section {
                padding: 3rem 0;
            }
            
            .features-section,
            .pricing-section,
            .final-cta {
                padding: 3rem 0;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-primary,
            .cta-secondary {
                width: 200px;
                text-align: center;
            }
            
            .pricing-card.featured {
                transform: none;
                margin: 1rem 0;
            }
        }
        
        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Professional Stamp Album Pages</h1>
                <p>Premium printed album pages for over 300 countries. Every major Scott-listed stamp has a dedicated space in our comprehensive collection of 60,000+ pages.</p>
                <div class="hero-cta">
                    <a href="{{ url('/order') }}" class="cta-primary" role="button">Start Your Order</a>
                    <a href="{{ url('/contact') }}" class="cta-secondary" role="button">Get in Touch</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Our Album Pages?</h2>
                <p>We're the exclusive US distributor for high-quality printed stamp album pages</p>
            </div>
            
            <div class="grid">
                <article class="feature-card">
                    <span class="feature-icon">🌍</span>
                    <h3>Complete Coverage</h3>
                    <p>Album pages for over 300 countries with spaces for every major Scott-listed stamp. From classics to modern issues.</p>
                </article>
                
                <article class="feature-card">
                    <span class="feature-icon">📄</span>
                    <h3>Premium Quality</h3>
                    <p>Heavyweight 8½×11 paper, 3-hole punched, or matching Scott and Minkus sizes. Professional printing on acid-free paper.</p>
                </article>
                
                <article class="feature-card">
                    <span class="feature-icon">🎯</span>
                    <h3>Flexible Orders</h3>
                    <p>Order entire countries, specific years, airmail, semi-postal issues, or custom blank pages with borders and country names.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section">
        <div class="container">
            <div class="section-title">
                <h2>Simple, Transparent Pricing</h2>
                <p>Choose the format that works best for your collection</p>
            </div>
            
            <div class="grid">
                <article class="pricing-card">
                    <span class="feature-icon">📋</span>
                    <h3>Standard Size</h3>
                    <div class="price">$0.20<span class="price-unit"> / page</span></div>
                    <p>Heavyweight 8½×11, 3-hole punched paper. Perfect for most collectors and easy to store in standard binders.</p>
                </article>
                
                <article class="pricing-card featured">
                    <span class="feature-icon">📖</span>
                    <h3>International/Minkus</h3>
                    <div class="price">$0.30<span class="price-unit"> / page</span></div>
                    <p>International and Minkus matching sizes. Premium format preferred by serious collectors worldwide.</p>
                </article>
                
                <article class="pricing-card">
                    <span class="feature-icon">📑</span>
                    <h3>Specialized Size</h3>
                    <div class="price">$0.35<span class="price-unit"> / page</span></div>
                    <p>Scott Specialized format with precise dimensions. Ideal for specialized collections and exhibitions.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta">
        <div class="container">
            <h2>Ready to Organize Your Collection?</h2>
            <p>Join thousands of collectors who trust us with their stamp album needs</p>
            <a href="{{ url('/order') }}" class="cta-primary" role="button">Browse Countries & Order Now</a>
        </div>
    </section>
</x-layout>