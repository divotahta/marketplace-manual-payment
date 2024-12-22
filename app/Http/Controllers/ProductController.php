<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Halaman index (untuk publik)
    public function index(Request $request)
    {
        $query = Product::query();
        $categories = Category::all();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan kondisi
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Pengurutan
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(9);

        return view('products.index', compact('products', 'categories'));
    }

    // Method untuk admin (perlu login)
    public function indexAdmin()
    {
        $products = Product::with('category')
                          ->latest()
                          ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:500000',
            'condition' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024'
        ], [
            'price.min' => 'Harga minimal adalah Rp. 500.000',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 1MB'
        ]);

        try {
            $data = $request->all();
            
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/products'), $filename);
                $data['image'] = 'products/' . $filename;
            } else {
                $data['image'] = 'products/default.jpg';
            }

            Product::create($data);

            return redirect()->route('admin.dashboard')
                           ->with('success', 'Produk berhasil ditambahkan');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function edit(Product $product)
    {
        // Ambil semua kategori untuk dropdown
        $categories = Category::all();

        // Cek apakah produk sedang dalam transaksi aktif
        $hasActiveTransaction = $product->transaksis()
            ->whereIn('status', ['menunggu', 'diproses'])
            ->exists();

        if ($hasActiveTransaction) {
            return back()->with('error', 'Produk tidak dapat diedit karena sedang dalam proses transaksi');
        }

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:500000',
            'condition' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024'
        ], [
            'price.min' => 'Harga minimal adalah Rp. 500.000',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 1MB'
        ]);

        // Cek apakah produk sedang dalam transaksi aktif
        $hasActiveTransaction = $product->transaksis()
            ->whereIn('status', ['menunggu', 'diproses'])
            ->exists();

        if ($hasActiveTransaction) {
            return back()->with('error', 'Produk tidak dapat diedit karena sedang dalam proses transaksi');
        }

        DB::beginTransaction();
        try {
            $data = $request->except('image');
            
            // Handle upload gambar baru jika ada
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Hapus gambar lama
                if ($product->image && $product->image != 'products/default.jpg') {
                    Storage::disk('public')->delete($product->image);
                }
                
                // Upload gambar baru
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/products'), $filename);
                $data['image'] = 'products/' . $filename;
            }

            // Update data produk
            $product->update($data);

            DB::commit();
            return redirect()->route('admin.products.index')
                           ->with('success', 'Produk berhasil diperbarui');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image && $product->image != 'products/default.jpg') {
                if (file_exists(public_path('storage/' . $product->image))) {
                    unlink(public_path('storage/' . $product->image));
                }
            }
            
            $product->delete();
            
            return redirect()->route('admin.dashboard')
                           ->with('success', 'Produk berhasil dihapus');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function report()
    {
        $kategoriHP = Category::withCount('products')
                        ->orderBy('products_count', 'desc')
                        ->get();

        return view('admin.report', compact('kategoriHP'));
    }

    public function search(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan kondisi
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Pengurutan
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(9);

        return view('products.partials.product-list', compact('products'))->render();
    }

    public function searchAdmin(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $products = $query->latest()->paginate(10);

        return view('admin.products.partials.product-table', compact('products'))->render();
    }
}
