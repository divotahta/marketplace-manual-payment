<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-6">Daftar Transaksi</h2>

                    <div class="transaksi-list-content">
                        <!-- Konten tabel transaksi -->
                    </div>

                    <div class="mt-4">
                        {{ $transaksis->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 