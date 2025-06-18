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

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Default filter: status = pending jika tidak dipilih apa-apa
        $queryParams = $request->all();

        // Jika tidak ada status, isi default pending dan redirect agar tampil di URL
        if (!$request->has('status')) {
            $queryParams['status'] = 'pending';
            return redirect()->route('unit.transaksi.index', $queryParams);
        }

        $query = Transaksi::with(['vendor', 'kategori', 'user'])
            ->where('user_id', Auth::id());

        // Filter berdasarkan nama vendor, kategori, atau item
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('vendor', function ($sub) use ($request) {
                    $sub->where('nama_vendor', 'like', '%' . $request->search . '%');
                })
                    ->orWhereHas('kategori', function ($sub) use ($request) {
                        $sub->where('nama_kategori', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('items', 'like', '%' . $request->search . '%');
            });
        }

        // Filter kategori jika ada
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->latest()->get();
        $kategoris = Kategori::all();

        return view('unit.transaksi.index', compact('transaksis', 'kategoris'));
    }

    public function reorder($id)
    {
        $old = Transaksi::findOrFail($id);

        // Simpan transaksi baru
        Transaksi::create([
            'user_id' => Auth::id(),
            'vendor_id' => $old->vendor_id,
            'kategori_id' => $old->kategori_id,
            'items' => $old->items,
            'status' => 'pending',
        ]);

        Pemesanan::create([
            'user_id' => Auth::id(),
            'vendor_id' => $old->vendor_id,
            'items' => $old->items,
        ]);

        return back()->with('success', 'Transaksi berhasil diulang!');
    }

}