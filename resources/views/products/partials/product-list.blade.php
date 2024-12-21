@forelse($products as $product)
    <div class="relative">
        @if($product->isSold())
        <div class="absolute top-0 right-0 bg-red-500 text-white px-3 py-1 rounded-bl-lg z-10">
            Terjual
        </div>
        @endif
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden {{ $product->isSold() ? 'opacity-75' : '' }}">
            <a href="{{ route('products.show', $product) }}">
                <img src="{{ Storage::url($product->image) }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-48 object-cover hover:opacity-90 transition">
            </a>
            <div class="p-4">
                <a href="{{ route('products.show', $product) }}"
                    class="text-xl font-semibold text-gray-900 hover:text-blue-600 mb-2 block">
                    {{ $product->name }}
                </a>
                <p class="text-gray-600 mb-2">{{ Str::limit($product->description, 100) }}</p>
                <p class="text-lg font-bold text-gray-900 mb-2">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>
                <div class="mb-4">
                    <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-600">
                        {{ $product->category->name }}
                    </span>
                    <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-600">
                        {{ $product->condition }}
                    </span>
                </div>
                @if(!$product->isSold())
                <a href="{{ route('pelanggan.transaksi.create', $product) }}" 
                   class="block w-full text-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-shopping-cart mr-2"></i>Beli Sekarang
                </a>
                @else
                <button disabled 
                        class="block w-full text-center bg-gray-400 text-white py-2 cursor-not-allowed">
                    Sudah Terjual
                </button>
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-12">
        <p class="text-gray-500">Tidak ada produk yang ditemukan</p>
    </div>
@endforelse