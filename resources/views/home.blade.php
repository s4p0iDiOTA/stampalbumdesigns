<x-layout>
    <article class="hero component grid">
        <div class="hook">
            <center>
                <h2>Welcome to<br /> Stamp Album designs</h2>
                <p>
                    we manage album pages for every stamp ever issued. <br> There are album pages for over 300
                    countries; over 60,000 pages in total. Each album contains spaces for ever major Scott-listed stamp.
                </p>
 
            </center>

        </div>
        <div class="hook">

            <video video width="440px" autoplay="autoplay" muted preload="auto">
                <source src="{{ url('/stamps_video.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>

        </div>


    </article>

    <hr>

    <div class="grid">
        <!-- Product Card -->
        <article>

            <p>

                <b>Exclusive US Distributor for Printed Stamp Album Pages                    </b><br>


                We offer printed Stamp Album pages on heavyweight 8 1/2 x 11, 3-hole punched paper, or matching Scott
                and Minkus sizes.

                You can order pages for entire countries, specific years, or air mail and semi-postal issues, as
                well as custom "blank" pages with borders and country names.
            </p>

        </article>

        <article>
         <h3>   Pricing is as follows:</h3>
<p>
<b>8 1/2 x 11:</b> $0.20 / page <br>
<b>International/Minkus size: </b> $0.30 / page   <br>
<b>Specialized size: </b> $0.35 / page  <br>
Check the listings for available countries and place your order using <a href="{{url('order')}}"> the Order Form! </a></p>
        </article>

        {{-- <article>
            <img src="https://via.placeholder.com/300" alt="Stamp Album">
            <h2>Classic Stamp Album</h2>
            <p>A beautiful album to store your precious stamps.</p>
            <footer>
                <p>$29.99</p>
                <a href="#" role="button">Buy Now</a>
            </footer>
        </article> --}}
    </div>





    <fieldset>
        <label for="terms">
            <input type="checkbox" role="switch" id="terms" name="terms" />
            I agree to the
            <a href="#" onclick="event.preventDefault()">Privacy Policy</a>
        </label>
    </fieldset>
</x-layout>
