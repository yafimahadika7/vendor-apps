<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Kategori;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::with('kategori')->latest()->get();
        $kategoris = \App\Models\Kategori::all(); // untuk dropdown form nanti
        return view('admin.vendor.index', compact('vendors', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'kontak_whatsapp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        Vendor::create([
            'nama_vendor' => $request->nama_vendor,
            'email' => $request->email,
            'kontak_whatsapp' => $request->kontak_whatsapp,
            'alamat' => $request->alamat,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'kontak_whatsapp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'nama_vendor' => $request->nama_vendor,
            'email' => $request->email,
            'kontak_whatsapp' => $request->kontak_whatsapp,
            'alamat' => $request->alamat,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor berhasil dihapus!');
    }

}