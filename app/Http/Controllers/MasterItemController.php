<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Http\Requests\StoreMasterItemRequest;
use Illuminate\Http\Request;

class MasterItemController extends Controller
{
    // Tampilkan daftar barang
    public function index()
    {
        // Ambil semua barang, urutkan dari yang terbaru
        $items = MasterItem::latest()->get();
        return view('dashboard.super_admin.items.index', compact('items'));
    }

    // Simpan barang baru
    public function store(StoreMasterItemRequest $request)
    {
        // Validasi sudah otomatis jalan di StoreMasterItemRequest
        // Jika lolos, kode di bawah baru dieksekusi
        
        MasterItem::create($request->validated());

        return back()->with('success', 'Barang berhasil ditambahkan ke sistem.');
    }

   // Update barang (Ganti harga/nama)
    public function update(Request $request, MasterItem $master_item)
    {
        // 1. CUCI DATA DULU (Hapus titik Rupiah)
        // Kita paksa ubah input 'price' yang tadinya "10.000" jadi "10000"
        if ($request->has('price')) {
            $cleanPrice = str_replace(['.', 'Rp', ' '], '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        // 2. Baru Validasi (Sekarang aman karena sudah jadi angka murni)
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'unit'      => 'required|string|max:50',
            'price'     => 'required|numeric|min:0', // Sekarang pasti lolos
            'is_active' => 'boolean'
        ]);

        // 3. Update Database
        // Perhatikan parameter di function update(...) harus sama dengan nama variabel binding
        // Kalau di route: master-items/{master_item}, maka di sini $master_item
        $master_item->update($data);

        return back()->with('success', 'Data barang berhasil diperbarui.');
    }
    
    // Hapus (atau Non-aktifkan)
    public function destroy(MasterItem $item)
    {
        $item->delete();
        return back()->with('success', 'Barang dihapus.');
    }
}