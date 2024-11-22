<x-layout>

    <h1>Your Cart</h1>

    @if (session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <div class="grid">
        <div>
            @if (empty($cart))
                <p>Your cart is empty.</p>
            @else
                @foreach ($cart as $id => $item)
                    {{ $item['search_value'] }}
                    

                    <form action="{{ route('cart.remove') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" value="{{ $id }}">
                        <button class="contrast delete_item" type="submit">❌</button>
                    </form>
                    
                    Price: ${{ $item['p1'] }} <br>
                    Period: {{ $item['hidden_period_value'] }}
                    Quantity:
                    <form action="{{ route('cart.update') }}" method="POST">
                        @csrf
                        <fieldset role="group">
                            @method('PATCH')
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input class="small" type="number" name="quantity" value="{{ $item['quantity'] }}"
                                min="1">
                            <button type="submit">🔄</button>

                    </form>
                </fieldset>
                    
            
                  

<hr>

                   
                @endforeach


                <a href="{{ route('cart.clear') }}">Clear Cart</a>
            @endif
        </div>

        <div class="right">total</div>
    </div>
</x-layout>
