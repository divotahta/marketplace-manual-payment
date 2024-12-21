<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Detail Transaksi #{{ $transaksi->id }}</h2>
                        <a href="{{ route('admin.transaksi.index') }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Kembali
                        </a>
                    </div>

                    <!-- Status dan Form Update Status -->
                    <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex gap-4">
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold
                {{ $transaksi->status === 'selesai'
                    ? 'bg-green-100 text-green-800'
                    : ($transaksi->status === 'menunggu'
                        ? 'bg-yellow-100 text-yellow-800'
                        : ($transaksi->status === 'diproses'
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-red-100 text-red-800')) }}">
                                    Status: {{ ucfirst($transaksi->status) }}
                                </span>
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold
                {{ $transaksi->payment_status === 'selesai'
                    ? 'bg-green-100 text-green-800'
                    : ($transaksi->payment_status === 'menunggu'
                        ? 'bg-yellow-100 text-yellow-800'
                        : ($transaksi->payment_status === 'menunggu konfirmasi'
                            ? 'bg-blue-100 text-blue-800'
                            : 'bg-red-100 text-red-800')) }}">
                                    Pembayaran: {{ ucfirst($transaksi->payment_status) }}
                                </span>
                            </div>

                            <!-- Form Update Status -->
                            <form action="{{ route('admin.transaksi.update-status', $transaksi) }}" method="POST"
                                class="flex gap-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <select name="status"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="diproses"
                                            {{ $transaksi->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai"
                                            {{ $transaksi->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan"
                                            {{ $transaksi->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <select name="payment_status"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="menunggu konfirmasi"
                                            {{ $transaksi->payment_status === 'menunggu konfirmasi' ? 'selected' : '' }}>
                                            Menunggu Konfirmasi</option>
                                        <option value="selesai"
                                            {{ $transaksi->payment_status === 'selesai' ? 'selected' : '' }}>Selesai
                                        </option>
                                        <option value="gagal"
                                            {{ $transaksi->payment_status === 'gagal' ? 'selected' : '' }}>Gagal
                                        </option>
                                    </select>
                                </div>

                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Update Status
                                </button>
                            </form>
                        </div>

                        <!-- Notifikasi Pembatalan -->
                        @if($transaksi->status === 'dibatalkan' && $transaksi->payment_status !== 'gagal')
                            <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Pelanggan meminta pembatalan pesanan. Silahkan konfirmasi dengan mengubah status pembayaran menjadi 'Gagal'.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informasi Produk -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Informasi Produk</h3>
                            <div class="flex gap-4">
                                <img src="{{ Storage::url($transaksi->product->image) }}"
                                    alt="{{ $transaksi->product->name }}" class="w-24 h-24 object-cover rounded-lg">
                                <div>
                                    <h4 class="font-medium">{{ $transaksi->product->name }}</h4>
                                    <p class="text-gray-600">{{ $transaksi->product->category->name }}</p>
                                    <p class="text-lg font-bold text-blue-600 mt-2">
                                        Rp {{ number_format($transaksi->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembeli -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Informasi Pembeli</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Nama:</span> {{ $transaksi->name }}</p>
                                <p><span class="font-medium">Telepon:</span> {{ $transaksi->phone }}</p>
                                <p><span class="font-medium">Alamat:</span> {{ $transaksi->shipping_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    @if ($transaksi->bukti_pembayaran)
                        <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Bukti Pembayaran</h3>
                            <img src="{{ Storage::url($transaksi->bukti_pembayaran) }}" alt="Bukti Pembayaran"
                                class="max-w-md rounded-lg">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
