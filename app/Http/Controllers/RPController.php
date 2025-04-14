<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RealisasiProyek;
use App\Models\Proyek;
use App\Models\Perusahaan;
use App\Models\Notification;

class RPController extends Controller
{
    public function index(Request $request)
    {
    
        $rps = RealisasiProyek::orderBy('created_at', 'desc')->paginate(10); 
        return view('realisasi_proyeks.index', compact('rps'));
    }
    
    public function create(Request $request)
{
    $proyekID = $request->query('id'); // Ambil ID dari URL
    $proyek = Proyek::find($proyekID); // Cari proyek berdasarkan ID
    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();

    if (!$proyek) {
        return redirect()->route('realisasi_proyeks.index')->with('error', 'Proyek tidak ditemukan.');
    }

    return view('realisasi_proyeks.create', compact('perusahaan', 'proyek'));
}
    

public function store(Request $request)
{
    $request->validate([
        'ProyekID' => 'required|exists:proyeks,ProyekID',
        'TglMulai' => 'required|date',
        'TglSelesai' => 'required|date',
        'Catatan' => 'required|string',
        
    ]);
    

    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();

    if (!$perusahaan) {
        return redirect()->route('realisasi_proyeks.index')->with('error', 'Perusahaan tidak ditemukan.');
    }

    $proyek = Proyek::find($request->ProyekID);

    if (!$proyek) {
        return redirect()->route('realisasi_proyeks.index')->with('error', 'Proyek tidak ditemukan.');
    }

    


    // Simpan realisasi proyek
    $realisasi = RealisasiProyek::create([
        'ProyekID' => $proyek->ProyekID,
        'PerusahaanID' => $perusahaan->PerusahaanID,
        'TglMulai' => $request->TglMulai,
        'TglSelesai' => $request->TglSelesai,
        'Status' => 'Dalam Proses',
        'Catatan' => $request->Catatan,
         
    ]);

    $proyek->update(['Status' => 'Dikerjakan']);

    // Ambil mitra dari proyek (pastikan proyek memiliki relasi dengan mitra)
    $mitra = $proyek->mitra ?? null; // Sesuaikan relasi jika berbeda

    // Kirim notifikasi ke user terkait
    if ($mitra && $mitra->user_id) {
        Notification::create([
            'type' => 'perusahaan_to_mitra',
            'from_id' => $perusahaan->user_id,
            'to_id' => $mitra->user_id,
            'proyek_id' => $proyek->ProyekID,
            'judul' => 'Realisasi Proyek Baru',
            'pesan' => "Realisasi proyek {$proyek->Judul} telah dibuat dan sedang dalam proses.",
            'dibaca' => false,
        ]);
    }

    return redirect()->route('realisasi_proyeks.index')->with('success', 'Realisasi proyek berhasil ditambahkan!');
}
    

    public function edit($id)
    {

    $rp = realisasiProyek::findOrFail($id);
    $proyeks = Proyek::all();
    $perusahaan = Perusahaan::where('user_id', auth()->id())->first(); // Pastikan ini sudah benar

    return view('realisasi_proyeks.edit', compact('rp', 'proyeks', 'perusahaan'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'TglSelesai' => 'required|date',
            'Status' => 'required|in:dalam proses,selesai',
            'Catatan' => 'required|string|max:255',
            'Dokumentasi' => 'required|file|mimes:doc,docx,pdf,xls,xlsx|max:10240',
        ];
        
        $this->validate($request, $rules);
        
        $rp = RealisasiProyek::findOrFail($id);
        
        // Ambil data perusahaan sesuai user login
        $perusahaan = Perusahaan::where('user_id', auth()->id())->first();
        
        if (!$perusahaan) {
            return redirect()->route('realisasi_proyeks.index')->with('error', 'Perusahaan tidak ditemukan.');
        }
        
        // Ambil data proyek
        $proyek = Proyek::find($rp->ProyekID);
        
        if (!$proyek) {
            return redirect()->route('realisasi_proyeks.index')->with('error', 'Proyek tidak ditemukan.');
        }
        
        // Simpan status lama untuk perbandingan
        $statusLama = $rp->Status;
        
        // Handle file upload
        if ($request->hasFile('Dokumentasi')) {
            // Delete old file if exists
            if ($rp->Dokumentasi && Storage::disk('public')->exists($rp->Dokumentasi)) {
                Storage::disk('public')->delete($rp->Dokumentasi);
            }
            
            $file = $request->file('Dokumentasi');
            $fileName = time() . '_Dokumentasi_' . $file->getClientOriginalName();
            
            // Store the file in the dokumentasi folder
            $filePath = $file->storeAs('dokumentasi', $fileName, 'public');
            
            // Update realisasi proyek
            $rp->update([
                'PerusahaanID' => $perusahaan->PerusahaanID,
                'TglMulai' => $rp->TglMulai, // Menggunakan nilai yang sudah ada
                'TglSelesai' => $request->TglSelesai,
                'Status' => $request->Status,
                'Catatan' => $request->Catatan,
                'Dokumentasi' => $filePath,
            ]);
        } else {
            // If no new file, update other fields only
            $rp->update([
                'PerusahaanID' => $perusahaan->PerusahaanID,
                'TglMulai' => $rp->TglMulai,
                'TglSelesai' => $request->TglSelesai,
                'Status' => $request->Status,
                'Catatan' => $request->Catatan,
            ]);
        }
        
        // Jika status diubah menjadi selesai, perbarui status proyek dan kirim notifikasi
        if ($request->Status == 'selesai' && $statusLama != 'selesai') {
            // Update status proyek
            $proyek->update([
                'Status' => 'selesai'
            ]);
            
            // Ambil mitra dari proyek
            $mitra = $proyek->mitra ?? null;
            
            // Kirim notifikasi ke mitra bahwa proyek telah selesai
            if ($mitra && $mitra->user_id) {
                Notification::create([
                    'type' => 'perusahaan_to_mitra',
                    'from_id' => $perusahaan->user_id,
                    'to_id' => $mitra->user_id,
                    'proyek_id' => $proyek->ProyekID,
                    'judul' => 'Proyek Selesai',
                    'pesan' => "Proyek {$proyek->Judul} telah selesai dikerjakan.",
                    'dibaca' => false,
                ]);
            }
        }
        
        return redirect()->route('realisasi_proyeks.index')->with('success', 'Data realisasi proyek berhasil diubah');
    }


    public function destroy($id)
    {
        $rp = RealisasiProyek::findOrFail($id);
        $rp->delete();

        return redirect()->route('realisasi_proyeks.index')->with('success', 'Data realisas proyek berhasil dihapus');
    }
}
