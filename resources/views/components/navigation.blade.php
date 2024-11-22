<nav>
    
    <ul>      
        <li><a href="/"><x-application-logo class="home_logo" style="height: 42px;width:42px;color: #2d2d2d"/></a></li>
        <li class="logotype"><strong>Stamp Album Designs</strong></li>
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