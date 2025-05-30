<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;
use App\Models\Kategori;
use App\Models\Pemesanan;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Storage;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_vendor', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('kontak_whatsapp', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nama_asc':
                    $query->orderBy('nama_vendor', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama_vendor', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $vendors = $query->get();
        $kategoris = Kategori::all();

        return view('unit.pemesanan.index', compact('vendors', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items.*.upload_gambar.*' => 'nullable|image|max:5120' // 5MB per file
        ]);

        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        $items = [];

        foreach ($request->input('items', []) as $index => $item) {
            $processedItem = $item;

            // Proses upload gambar jika ada untuk item ini
            if ($request->hasFile("items.$index.upload_gambar")) {
                $uploadedFiles = $request->file("items.$index.upload_gambar");
                $paths = [];

                foreach ($uploadedFiles as $file) {
                    $paths[] = $file->store('uploads', 'public');
                }

                $processedItem['upload_gambar'] = $paths;
            } else {
                $processedItem['upload_gambar'] = [];
            }

            $items[] = $processedItem;
        }

        // Simpan ke pemesanans
        Pemesanan::create([
            'user_id' => Auth::id(),
            'vendor_id' => $request->vendor_id,
            'items' => $items,
        ]);

        // Simpan ke transaksis
        Transaksi::create([
            'user_id' => Auth::id(),
            'vendor_id' => $request->vendor_id,
            'kategori_id' => Vendor::find($request->vendor_id)?->kategori_id,
            'items' => $items,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Transaksi berhasil dikirim!');
    }

}