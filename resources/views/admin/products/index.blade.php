<x-admin-layout>
    <div class="flex">
        <div class="flex-1 p-8">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold">Daftar Produk</h2>
                            <div class="flex items-center gap-4">
                                <!-- Search Input -->
                                <div class="relative">
                                    <input type="text" id="searchInput" placeholder="Cari produk..."
                                        class="w-64 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <a href="{{ route('admin.products.create') }}"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-150">
                                    Tambah Produk
                                </a>
                            </div>
                        </div>

                        <!-- Tabel Produk -->
                        <div class="overflow-x-auto" id="productsTable">
                            @include('admin.products.partials.product-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let typingTimer;
            const doneTypingInterval = 500;

            function searchProducts() {
                const searchInput = document.getElementById('searchInput');
                const productsTable = document.getElementById('productsTable');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.products.search') }}', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        productsTable.innerHTML = xhr.responseText;
                    }
                };

                xhr.send('search=' + encodeURIComponent(searchInput.value));
            }

            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(searchProducts, doneTypingInterval);
            });
        </script>
    @endpush
</x-admin-layout>
