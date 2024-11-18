<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProdukController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function ViewProduk()
    {
    // $produk = Produk::all(); //mengambil semua data di tabel produk

    $isAdmin = Auth::user()->role == 'admin';

    // jika user adalah admin, maka tampilkan semua data, jika bukan admin, maka tampilkan data dengan user_id yang sama dengan user yang Login
    $produk = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get();

    return view('produk',['produk'=> $produk ]);
}
    /**
     * Store a newly created product in the database.
     */
    public function CreateProduk(Request $request)
    {
        // Validasi input sebelum menyimpan data
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'jumlah_produk' => 'required|integer',
        ]);
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/images', $imageName); // Store the image in the 'storage/app/public/images' directory
        }
        // Menyimpan data produk ke database
        Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'jumlah_produk' => $request->jumlah_produk,
            'image' => $imageName,
            'user_id' => Auth::user()->id

        ]);

        return redirect(Auth::user()->role.'/produk')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Show the form to create a new product.
     */
    public function ViewAddProduk()
    {
        return view('addproduk'); // Menampilkan view addproduk.blade.php
    }

    /**
     * Delete the specified product from the database.
     */
    public function DeleteProduk($id)
    {
        // Memastikan bahwa produk dengan id ada
        $produk = Produk::where('id', $id)->first();

        if ($produk) {
            $produk->delete();
            return redirect(Auth::user()->role.'/produk')->with('success', 'Produk berhasil dihapus');
        } else {
            return redirect(Auth::user()->role.'/produk')->with('error', 'Produk tidak ditemukan');
        }
    }

    /**
     * Show the form to edit the specified product.
     */
    public function ViewEditProduk($id)
    {
        $ubahproduk = Produk::where('id', $id)->first();

        if ($ubahproduk) {
            return view('editproduk', compact('ubahproduk'));
        } else {
            return redirect(Auth::user()->role.'/produk')->with('error', 'Produk tidak ditemukan');
        }
    }

    /**
     * Update the specified product in the database.
     */
    public function UpdateProduk(Request $request, $id)
    {
        // Validasi input sebelum mengupdate data
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric',
            'jumlah_produk' => 'required|integer',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
        $imageFile = $request->file('image');
        $imageName = time() . '_' . $imageFile->getClientOriginalName();
        $imageFile->storeAs('public/images', $imageName); // Store the image in the 'storage/app/public/images' directory
}

        // Memastikan bahwa produk dengan id ada
        $produk = Produk::find($id);

        if ($produk) {
            // Mengupdate data produk di database
            $produk->update([
                'nama_produk' => $request->nama_produk,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'jumlah_produk' => $request->jumlah_produk,
                'image' => $imageName

            ]);

            return redirect(Auth::user()->role.'/produk')->with('success', 'Produk berhasil diperbarui');
        } else {
            return redirect(Auth::user()->role.'/produk')->with('error', 'Produk tidak ditemukan');
        }

    }

    public function ViewLaporan()
    {
        $products = Produk::all();
        return view('laporan', ['products'=> $products]);
    }

    public function print()
    {
    // Mengambil semua data produk
    $products = Produk::all();

    // Load view untuk PDF dengan data produk
    $pdf = Pdf::loadView('report', compact('products'));

    // Menampilkan PDF langsung di browser
    return $pdf->stream('laporan-produk.pdf');

    }

}
