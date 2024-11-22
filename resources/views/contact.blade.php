<x-layout>


    @if (session('success'))
        <article class="message success">
            <p>{{ session('success') }}</p>
        </article>
    @endif

    <h1>Contact Us</h1>

    <div class="grid">
        <div>
            <h5>Online Inquiry</h5>

            <form action="{{ route('contact.submit') }}" method="POST">
                @csrf

                <label for="name">
                    Name
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                </label>

                @error('name')
                    <small class="error">{{ $message }}</small>
                @enderror

                <label for="email">
                    Email
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                </label>

                @error('email')
                    <small class="error">{{ $message }}</small>
                @enderror

                <label for="message">
                    Message
                    <textarea name="message" id="message" rows="5" required>{{ old('message') }}</textarea>
                </label>

                @error('message')
                    <small class="error">{{ $message }}</small>
                @enderror

                <button type="submit">Send Message</button>
            </form>

        </div>

        <div class="">
            <h5>Contact details</h5>
         
            📱 786-768-1022 <br> 📧 stampalbumpages@gmail.com
            <br>
            📨 Carlos Galguera <br>     
            5745 SW 75th Street #326
             Gainesville, FL 32608

        </div>

    </div>






</x-layout>
