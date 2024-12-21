<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Daftar Produk</h2>

                    <!-- Search and Filter Form -->
                    <div class="mb-8">
                        <form id="searchForm" action="{{ route('products.index') }}" method="GET" 
                            class="space-y-4 md:space-y-0 md:flex md:gap-4">
                            <div class="flex-1">
                                <input type="text" 
                                       name="search" 
                                       id="searchInput"
                                       value="{{ request('search') }}"
                                       placeholder="Cari produk..."
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="md:w-48">
                                <select name="category" 
                                        id="categoryFilter"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:w-48">
                                <select name="condition" 
                                        id="conditionFilter"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Kondisi</option>
                                    <option value="Bekas - Seperti Baru" {{ request('condition') == 'Bekas - Seperti Baru' ? 'selected' : '' }}>
                                        Bekas - Seperti Baru
                                    </option>
                                    <option value="Bekas - Mulus" {{ request('condition') == 'Bekas - Mulus' ? 'selected' : '' }}>
                                        Bekas - Mulus
                                    </option>
                                </select>
                            </div>

                            <div class="md:w-48">
                                <select name="sort" 
                                        id="sortFilter"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Urutkan</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    <!-- Results Info -->
                    @if (request('search') || request('condition') || request('category') || request('sort'))
                        <div class="mb-4 text-gray-600">
                            Menampilkan hasil pencarian {{ $products->total() }} produk
                            @if (request('search'))
                                untuk "{{ request('search') }}"
                            @endif
                            @if (request('category'))
                                dalam kategori "{{ $categories->find(request('category'))->name }}"
                            @endif
                            @if (request('condition'))
                                dengan kondisi {{ request('condition') }}
                            @endif
                        </div>
                    @endif

                    <!-- Products Grid Container -->
                    <div id="productsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @include('products.partials.product-list')
                    </div>

                    <div class="mt-6">
                        {{ $products->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Section -->
    @include('components.footer')

    @push('scripts')
    <script>
    let typingTimer;
    const doneTypingInterval = 500; // Waktu tunggu setelah user selesai mengetik (dalam milidetik)

    // Fungsi untuk melakukan pencarian
    function searchProducts() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const conditionFilter = document.getElementById('conditionFilter');
        const sortFilter = document.getElementById('sortFilter');
        const productsContainer = document.getElementById('productsContainer');

        // Buat objek FormData
        const formData = new FormData();
        formData.append('search', searchInput.value);
        formData.append('category', categoryFilter.value);
        formData.append('condition', conditionFilter.value);
        formData.append('sort', sortFilter.value);

        // Buat XMLHttpRequest
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route("products.search") }}', true);
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

        xhr.onload = function() {
            if (xhr.status === 200) {
                productsContainer.innerHTML = xhr.responseText;
            }
        };

        xhr.send(formData);
    }

    // Event listener untuk input pencarian
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(searchProducts, doneTypingInterval);
    });

    // Event listener untuk filter
    document.getElementById('categoryFilter').addEventListener('change', searchProducts);
    document.getElementById('conditionFilter').addEventListener('change', searchProducts);
    document.getElementById('sortFilter').addEventListener('change', searchProducts);
    </script>
    @endpush
</x-app-layout>
