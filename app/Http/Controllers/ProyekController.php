<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Proyek;
use App\Models\Mitra;
use App\Models\Perusahaan;
use App\Models\Survey;
use App\Models\Rekomendasi;
use App\Models\RealisasiProyek;
use Illuminate\Support\Facades\Log;


class ProyekController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
{
    $search = $request->input('search');
    $query = Proyek::with('mitra');

    // Mendapatkan MitraID yang sesuai dengan user yang login
    $mitra = Mitra::where('user_id', auth()->id())->first();
    if ($mitra) {
        $query->where('MitraID', $mitra->MitraID); // Menggunakan MitraID dari tabel mitra
    }

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('Judul', 'LIKE', "%$search%")
                ->orWhere('Deskripsi', 'LIKE', "%$search%")
                ->orWhere('Lokasi', 'LIKE', "%$search%")
                ->orWhereHas('mitra', function ($q) use ($search) {
                    $q->where('NamaMitra', 'LIKE', "%$search%");
                });
        });
    }

    $proyeks = $query->latest()->paginate(10);
    return view('proyeks.index', compact('proyeks'));
}

    public function create()
    {
        $mitra = Mitra::where('user_id', auth()->id())->first();
        if (!$mitra) {
            return redirect()->route('proyeks.index')->with('error', 'Akun Anda belum memiliki data mitra.');
        }
        
        return view('proyeks.create', compact('mitra'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'Judul' => 'required|string|max:255',
            'Deskripsi' => 'required|string|max:500',
            'Lokasi' => 'required|string|max:255',
            'TglPengajuan' => 'required|date',
        ]);
        
        $mitra = Mitra::where('user_id', auth()->id())->first();
        if (!$mitra) {
            return redirect()->route('proyeks.index')->with('error', 'Mitra tidak ditemukan.');
        }
        
        Proyek::create([
            'MitraID' => $mitra->MitraID,
            'Judul' => $request->Judul,
            'Deskripsi' => $request->Deskripsi,
            'Lokasi' => $request->Lokasi,
            'Status' => 'Diajukan',
            'TglPengajuan' => $request->TglPengajuan, // Use the date from the form (which is today's date)
        ]);
        
        return redirect()->route('proyeks.index')->with('success', 'Proyek berhasil ditambahkan!');
    }

    public function show($id)
{
    // Find the project
    $proyek = Proyek::findOrFail($id);
    $user = auth()->user();
    $role = $user->role;
    
    // Check if user has access to this project
    if ($role === 'perusahaan') {
        $perusahaan = $user->perusahaan;
        if ($proyek->PerusahaanID != $perusahaan->PerusahaanID && $proyek->Status !== 'Diajukan') {
            return redirect()->route('proyeks.index')->with('error', 'Anda tidak memiliki akses ke proyek ini.');
        }
    } elseif ($role === 'mitra') {
        $mitra = $user->mitra;
        if ($proyek->MitraID != $mitra->MitraID) {
            return redirect()->route('proyeks.index')->with('error', 'Anda tidak memiliki akses ke proyek ini.');
        }
    }
    
    // Initialize related data as null
    $survey = null;
    $rekomendasi = null;
    $realisasi = null;
    
    // Try to find related data for this project
    $survey = Survey::where('ProyekID', $proyek->ProyekID)->first();
    $rekomendasi = Rekomendasi::where('ProyekID', $proyek->ProyekID)->first();
    $realisasi = RealisasiProyek::where('ProyekID', $proyek->ProyekID)->first();
    
    // Add debugging to verify the values
    // Log::debug('Project Status: ' . $proyek->Status . ', Survey: ' . ($survey ? 'exists' : 'null'));
    
    // Return view with data
    return view('proyeks.show', compact('proyek', 'survey', 'rekomendasi', 'realisasi', 'role'));
}
    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        return view('proyeks.edit', compact('proyek'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Status' => 'required|string',
        ]);

        $proyek = Proyek::findOrFail($id);
        
        // Hanya perusahaan yang bisa mengubah status
        if (auth()->user()->role === 'perusahaan') {
            $proyek->update([
                'Status' => $request->Status,
            ]);
            return redirect()->route('proyeks.index')->with('success', 'Status proyek berhasil diperbarui.');
        }

        return redirect()->route('proyeks.index')->with('error', 'Anda tidak memiliki izin untuk mengubah proyek.');
    }

    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->delete();
        return redirect()->route('proyeks.index')->with('success', 'Data proyek berhasil dihapus');
    }

    public function ambilProyek($id)
{
    $proyek = Proyek::findOrFail($id);
    $perusahaan = auth()->user()->perusahaan;
    
    if (!$perusahaan) {
        return redirect()->route('proyeks.index')->with('error', 'Perusahaan tidak ditemukan.');
    }
    
    if ($proyek->Status !== 'Diajukan') {
        return redirect()->route('proyeks.index')->with('error', 'Proyek sudah tidak tersedia.');
    }
    
    // Update status ke "Diambil" dan simpan perubahan ke database
    $proyek->PerusahaanID = $perusahaan->PerusahaanID;
    $proyek->Status = 'Diambil';
    $proyek->save();
    
    // Buat notifikasi
    Notification::create([
        'type' => 'perusahaan_to_mitra',
        'from_id' => $perusahaan->PerusahaanID,
        'to_id' => $proyek->MitraID,
        'proyek_id' => $proyek->ProyekID,
        'judul' => 'Proyek Diambil',
        'pesan' => 'Proyek "' . $proyek->Judul . '" telah diambil oleh perusahaan ' . $perusahaan->NamaPerusahaan . '.',
        'dibaca' => false
    ]);
    
    // Redirect ke halaman detail proyek
    return redirect()->route('proyeks.show', $proyek->ProyekID)
        ->with('success', 'Proyek berhasil diambil. Anda dapat menjadwalkan survey setelah meninjau detail proyek.');
}


    public function lanjutkan($id) {
        $proyek = Proyek::findOrFail($id);
        
        // Ubah status proyek menjadi 'Diajukan'
        $proyek->Status = 'Diajukan';
        
        // Tambahkan flag untuk menandai proyek yang dilanjutkan setelah ditolak
        $proyek->IsResubmitted = true;
        
        $proyek->save();
        
        // Hapus data survey yang terkait dengan proyek ini
        Survey::where('ProyekID', $id)->delete();
        
        return redirect()->back()->with('success', 'Proyek berhasil dilanjutkan dan data survey telah dihapus.');
    }
    
    public function tdkselesai($id) {
        $proyek = Proyek::findOrFail($id);
        
        // Ubah status proyek menjadi 'Selesai'
        $proyek->Status = 'Tidak Selesai';
        $proyek->save();
        
        return redirect()->back()->with('success', 'Proyek Tidak diselesaikan.');
    }
    
   
   

}
