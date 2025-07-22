<nav>
    
    <ul>      
        <li>
            <a href="/" style="display: flex; align-items: center;">
                <img src="{{ asset('logo-original.png') }}" alt="Stamp Album Designs Logo" style="height: 42px; width: auto; margin-right: 0.5rem;">
            </a>
        </li>
        <!-- <li class="logotype"><strong>Stamp Album Designs</strong></li> -->
    </ul>
    <ul>
        <li>
            @if (request()->is('/'))
           <button href="/">Home</button>
           @else
           <a class="conoutlinetrast" href="{{ url('/') }}">Home</a>
           @endif
        </li>
        <li>
            @if (request()->is('order'))
            <button href="/order">Order</button>
            @else
            <a class="conoutlinetrast" href="{{ url('order') }}">Order</a>
            @endif
        </li>
        <li>
            @if (request()->is('contact'))
            <button href="/contact">Contact</button>
            @else
            <a class="conoutlinetrast" href="{{ url('contact') }}">Contact</a>
            @endif
        </li>
    </ul>
</nav>