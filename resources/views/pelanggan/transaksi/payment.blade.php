<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Pembayaran</h2>
                        <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800">
                            &larr; Kembali
                        </a>
                    </div>
                    
                    <!-- Informasi Pembayaran -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4">Detail Pembayaran</h3>
                        <div class="space-y-2">
                            <p>Total Pembayaran: <span class="font-semibold">Rp {{ number_format($transaksi->total_price, 0, ',', '.') }}</span></p>
                            <p>Nomor Rekening: <span class="font-semibold">BCA 1234567890</span></p>
                            <p>Atas Nama: <span class="font-semibold">PT Secondpedia</span></p>
                        </div>
                    </div>

                    <!-- Form Upload Bukti Transfer -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Upload Bukti Transfer</h3>
                        <form action="{{ route('pelanggan.transaksi.upload-payment', $transaksi) }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              class="mt-4">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Bukti Transfer
                                </label>
                                <input type="file" 
                                       name="bukti_pembayaran" 
                                       accept="image/*"
                                       required
                                       class="mt-1 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                                @error('bukti_pembayaran')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" 
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Upload Bukti Transfer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>