<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Kategori;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with('kategori');

        // Filter berdasarkan kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Search berdasarkan nama_vendor, email, atau whatsapp
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_vendor', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('kontak_whatsapp', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nama_asc':
                    $query->orderBy('nama_vendor', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama_vendor', 'desc');
                    break;
                default:
                    break; // jangan taruh latest() di sini
            }
        } else {
            $query->latest();
        }

        $vendors = $query->get();
        $kategoris = \App\Models\Kategori::all();

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