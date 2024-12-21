<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['product'])
            ->where('user_id', auth('web')->user()->id)
            ->latest()
            ->paginate(10);
            
        return view('pelanggan.transaksi.index', compact('transaksis'));
    }

    public function show(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        return view('pelanggan.transaksi.show', compact('transaksi'));
    }

    public function create(Product $product)
    {
        return view('pelanggan.transaksi.create', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'shipping_address' => 'required|string',
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Cek apakah produk masih tersedia
        if ($product->status === 'sold') {
            return back()->with('error', 'Maaf, produk sudah terjual');
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'user_id' => auth('web')->user()->id,
                'product_id' => $product->id,
                'total_price' => $product->price,
                'status' => 'menunggu',
                'payment_status' => 'menunggu',
                'name' => $request->name,
                'phone' => $request->phone,
                'shipping_address' => $request->shipping_address,
            ]);

            // Set produk menjadi sold saat di-checkout
            $product->update(['status' => 'sold']);

            DB::commit();

            return redirect()->route('pelanggan.transaksi.payment', $transaksi)
                ->with('success', 'Silahkan lakukan pembayaran');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan');
        }
    }

    public function payment(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        return view('pelanggan.transaksi.payment', compact('transaksi'));
    }

    public function uploadBuktiPembayaran(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        try {
            $request->validate([
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:12048'
            ]);

            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                
                // Hapus file lama jika ada
                if ($transaksi->bukti_pembayaran && Storage::disk('public')->exists($transaksi->bukti_pembayaran)) {
                    Storage::disk('public')->delete($transaksi->bukti_pembayaran);
                }

                // Generate nama file yang unik
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Pindahkan file ke folder public
                $file->move(public_path('storage/bukti_pembayarans'), $fileName);
                
                // Update database dengan path file
                $transaksi->update([
                    'bukti_pembayaran' => 'bukti_pembayarans/' . $fileName,
                    'payment_status' => 'menunggu konfirmasi'
                ]);

                return redirect()->route('pelanggan.transaksi.show', $transaksi)
                    ->with('success', 'Bukti pembayaran berhasil diupload');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengupload bukti pembayaran');
        }
    }

    public function cancel(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        if ($transaksi->status !== 'menunggu') {
            return back()->with('error', 'Hanya pesanan dengan status menunggu yang dapat dibatalkan');
        }

        DB::beginTransaction();
        try {
            // Update status transaksi menjadi dibatalkan
            $transaksi->update([
                'status' => 'dibatalkan'
            ]);

            // Set produk menjadi available kembali
            $transaksi->product->update(['status' => 'available']);

            DB::commit();
            return redirect()->route('pelanggan.transaksi.show', $transaksi)
                ->with('success', 'Permintaan pembatalan pesanan sedang diproses admin');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan');
        }
    }
}
