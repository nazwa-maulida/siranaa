<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Rekomendasi;
use App\Models\Proyek;
use App\Models\Perusahaan;
use App\Models\Mitra;
use App\Models\Notification;

class RekomendasiController extends Controller
{
    public function index(Request $request)
    {
        // Tentukan user role (perusahaan atau mitra)
        $user = Auth::user();
        $isPerusahaan = $user->hasRole('perusahaan');
        $isMitra = $user->hasRole('mitra');
        
        if ($isPerusahaan) {
            $perusahaan = Perusahaan::where('user_id', $user->id)->first();
            if (!$perusahaan) {
                return redirect()->route('dashboard')->with('error', 'Profil perusahaan tidak ditemukan.');
            }
            
            // Perusahaan hanya melihat rekomendasinya sendiri
            $rekomens = Rekomendasi::where('PerusahaanID', $perusahaan->PerusahaanID)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        }
        elseif ($isMitra) {
            $mitra = Mitra::where('user_id', $user->id)->first();
            if (!$mitra) {
                return redirect()->route('dashboard')->with('error', 'Profil mitra tidak ditemukan.');
            }
            
            // Mitra hanya melihat rekomendasi untuk proyeknya
            $rekomens = Rekomendasi::whereHas('proyek', function($query) use ($mitra) {
                        $query->where('MitraID', $mitra->MitraID);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        }
        else {
            // Admin melihat semua
            $rekomens = Rekomendasi::orderBy('created_at', 'desc')->paginate(10);
        }
        
        return view('rekomendasis.index', compact('rekomens', 'isPerusahaan', 'isMitra'));
    }

    public function create()
{
    $user = Auth::user();
    if (!$user->hasRole('perusahaan')) {
        return redirect()->route('rekomendasis.index')->with('error', 'Hanya perusahaan yang dapat membuat rekomendasi.');
    }
    
    $perusahaan = Perusahaan::where('user_id', $user->id)->first();
    if (!$perusahaan) {
        return redirect()->route('rekomendasis.index')->with('error', 'Profil perusahaan tidak ditemukan.');
    }
    
    // Ambil daftar proyek yang sudah diambil oleh perusahaan dari mitra
    $proyeks = Proyek::where('PerusahaanID', $perusahaan->PerusahaanID)
               ->whereNotNull('MitraID')
               ->get();
    
    if ($proyeks->isEmpty()) {
        return redirect()->route('rekomendasis.index')->with('error', 'Anda belum memiliki proyek yang terkait dengan mitra.');
    }
            
    return view('rekomendasis.create', compact('perusahaan', 'proyeks'));
}

public function store(Request $request)
{
    $user = Auth::user();
    
    // Cek apakah pengguna memiliki peran 'perusahaan'
    if (!$user->hasRole('perusahaan')) {
        return redirect()->route('rekomendasis.index')->with('error', 'Hanya perusahaan yang dapat membuat rekomendasi.');
    }
    
    // Validasi input
    $request->validate([
        'ProyekID' => 'required|exists:proyeks,ProyekID',
        'Catatan' => 'required|string',
        'FileAnggaran' => 'required|file|mimes:doc,docx,pdf,xls,xlsx|max:10240',
    ]);
    
    // Ambil data perusahaan berdasarkan user yang login
    $perusahaan = Perusahaan::where('user_id', $user->id)->first();
    if (!$perusahaan) {
        return redirect()->route('rekomendasis.index')->with('error', 'Perusahaan tidak ditemukan.');
    }
    
    // Ambil proyek berdasarkan ProyekID
    $proyek = Proyek::find($request->ProyekID);
    if (!$proyek) {
        return redirect()->route('rekomendasis.index')->with('error', 'Proyek tidak ditemukan.');
    }
    
    // Verifikasi proyek milik perusahaan ini
    if ($proyek->PerusahaanID != $perusahaan->PerusahaanID) {
        return redirect()->route('rekomendasis.index')->with('error', 'Proyek ini bukan milik perusahaan Anda.');
    }
    
    // Verifikasi proyek terkait dengan mitra
    if (!$proyek->MitraID) {
        return redirect()->route('rekomendasis.index')->with('error', 'Proyek ini belum terkait dengan mitra.');
    }
    
    // Simpan file anggaran
    $fileAnggaranPath = null;
    if ($request->hasFile('FileAnggaran')) {
        $fileAnggaran = $request->file('FileAnggaran');
        $fileAnggaranName = time() . '_anggaran_' . $fileAnggaran->getClientOriginalName();
        $fileAnggaranPath = $fileAnggaran->storeAs('anggaran', $fileAnggaranName, 'public');
    }
    
   // Buat rekomendasi baru dengan status otomatis 'menunggu persetujuan'
$rekomendasi = Rekomendasi::create([
    'ProyekID' => $proyek->ProyekID,
    'PerusahaanID' => $perusahaan->PerusahaanID,
    'MitraID' => $proyek->MitraID,
    'Catatan' => $request->Catatan,
    'FileAnggaran' => $fileAnggaranPath,
    'FileSPK' => null, // Atur sesuai kebutuhan
    'Status' => 'menunggu persetujuan', // Status diatur otomatis
]);

// Buat notifikasi untuk mitra
$mitra = Mitra::find($proyek->MitraID);
// After creating Rekomendasi, create the notification
if ($mitra && $mitra->user_id) {
    Notification::create([
        'type' => 'perusahaan_to_mitra', // Specify the type
        'from_id' => $perusahaan->PerusahaanID, // Company ID
        'to_id' => $mitra->MitraID, // Partner ID
        'proyek_id' => $proyek->ProyekID, // Project ID
        'rekomendasi_id' => $rekomendasi->RekomendasiID, // Pass the created RekomendasiID
        'judul' => 'Anggaran Baru', // Title of the notification
        'pesan' => "Perusahaan {$perusahaan->NamaPerusahaan} telah mengajukan anggaran untuk proyek {$proyek->Judul}", // Message
        'dibaca' => false // Set as unread
    ]);

}
    
    return redirect()->route('rekomendasis.index')->with('success', 'Rekomendasi berhasil ditambahkan! Menunggu persetujuan dari mitra.');
}

public function edit($id)
{
    $rekomendasi = Rekomendasi::findOrFail($id);
    $user = Auth::user();
    $perusahaan = null; // Inisialisasi variabel agar tidak undefined
    $mitra = null; // Inisialisasi variabel agar tidak undefined

    // Cek hak akses
    if ($user->hasRole('perusahaan')) {
        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
        if (!$perusahaan || $rekomendasi->PerusahaanID != $perusahaan->PerusahaanID) {
            return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke rekomendasi ini.');
        }
    } elseif ($user->hasRole('mitra')) {
        $mitra = Mitra::where('user_id', $user->id)->first();
        if (!$mitra || $rekomendasi->MitraID != $mitra->MitraID) {
            return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke rekomendasi ini.');
        }
    }

    return view('rekomendasis.edit', compact('rekomendasi', 'perusahaan', 'mitra'));
}


    public function show($id)
    {
        $rekomen = Rekomendasi::findOrFail($id);
        $user = Auth::user();
        $isPerusahaan = $user->hasRole('perusahaan');
        $isMitra = $user->hasRole('mitra');
        
        // Periksa hak akses
        if ($isPerusahaan) {
            $perusahaan = Perusahaan::where('user_id', $user->id)->first();
            if (!$perusahaan || $rekomen->PerusahaanID != $perusahaan->PerusahaanID) {
                return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke rekomendasi ini.');
            }
        } elseif ($isMitra) {
            $mitra = Mitra::where('user_id', $user->id)->first();
            if (!$mitra || $rekomen->MitraID != $mitra->MitraID) {
                return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke rekomendasi ini.');
            }
        }
        
        return view('rekomendasis.show', compact('rekomen', 'isPerusahaan', 'isMitra'));
    }

    public function update(Request $request, $id) {
        $rekomen = Rekomendasi::findOrFail($id);
        $user = Auth::user();
        
        // Cek apakah user adalah mitra
        if ($user->hasRole('mitra')) {
            $mitra = Mitra::where('user_id', $user->id)->first();
            if (!$mitra || $rekomen->MitraID != $mitra->MitraID) {
                return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke rekomendasi ini.');
            }
            
            $request->validate([
                'Status' => 'required',
                'FileSPK' => 'required_if:Status,spk terbit|nullable|file|mimes:doc,docx,pdf|max:10240',
                'CatatanMitra' => 'nullable|string',
            ]);
            
            if ($request->Status == 'spk terbit' && $request->hasFile('FileSPK')) {
                $fileSPK = $request->file('FileSPK');
                $fileSPKName = time() . '_spk_' . $fileSPK->getClientOriginalName();
                $fileSPKPath = $fileSPK->storeAs('spk', $fileSPKName, 'public');
                $rekomen->FileSPK = $fileSPKPath;
            }
            
            $rekomen->Status = $request->Status;
            $rekomen->CatatanMitra = $request->CatatanMitra ?? $rekomen->CatatanMitra;
            $rekomen->save();
            
            // Ambil data perusahaan dan proyek
            $perusahaan = Perusahaan::find($rekomen->PerusahaanID);
            $proyek = Proyek::find($rekomen->ProyekID);
            
            // Pastikan perusahaan dan user_id dari perusahaan ada sebelum membuat notifikasi
            if ($perusahaan && $perusahaan->user_id) {
                $pesanNotifikasi = ($rekomen->Status === 'spk terbit')
                    ? 'Anggaran Anda telah disetujui dan SPK berhasil diterbitkan oleh mitra ' . $mitra->NamaMitra . '.'
                    : 'Anggaran Anda telah ditolak oleh mitra ' . $mitra->NamaMitra . '. Silakan unggah ulang anggaran.';
                
                Notification::create([
                    'type' => 'mitra_to_perusahaan',
                    'from_id' => $mitra->MitraID,
                    'to_id' => $perusahaan->user_id,
                    'proyek_id' => $proyek->ProyekID,
                    'judul' => 'Tanggapan Anggaran',
                    'pesan' => $pesanNotifikasi,
                    'dibaca' => false
                ]);
            }
            
            return redirect()->route('rekomendasis.index')->with('success', 'Status rekomendasi berhasil diperbarui!');
        }
        
        if ($user->hasRole('perusahaan')) {
            if ($rekomen->Status !== 'anggaran ditolak') {
                return redirect()->route('rekomendasis.index')->with('error', 'Anda hanya bisa mengunggah ulang anggaran jika anggaran ditolak.');
            }
            
            $request->validate([
                'FileAnggaran' => 'required|file|mimes:xlsx,xls,pdf|max:10240',
            ]);
            
            $fileAnggaran = $request->file('FileAnggaran');
            $fileAnggaranName = time() . '_anggaran_' . $fileAnggaran->getClientOriginalName();
            $fileAnggaranPath = $fileAnggaran->storeAs('anggaran', $fileAnggaranName, 'public');
            $rekomen->FileAnggaran = $fileAnggaranPath;
            $rekomen->Status = 'anggaran diperbarui';
            $rekomen->save();
            
            $perusahaan = Perusahaan::where('user_id', $user->id)->first();
            $mitra = Mitra::find($rekomen->MitraID);
            $proyek = Proyek::find($rekomen->ProyekID);
            
            // Fix: Hapus mitra duplikat dan pastikan ada user_id sebelum membuat notifikasi
            if ($mitra && $mitra->user_id) {
                Notification::create([
                    'type' => 'perusahaan_to_mitra',
                    'from_id' => $perusahaan->user_id,
                    'to_id' => $mitra->user_id,
                    'proyek_id' => $rekomen->ProyekID,
                    'judul' => 'Anggaran Diperbarui',
                    'pesan' => 'Perusahaan ' . $perusahaan->NamaPerusahaan . ' telah mengunggah ulang anggaran untuk proyek ' . $proyek->Judul . '.',
                    'dibaca' => false
                ]);
            }
            
            return redirect()->route('rekomendasis.index')->with('success', 'Anggaran berhasil diunggah ulang!');
        }
        
        return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki hak untuk mengupdate rekomendasi ini.');
    }

    

    public function destroy($id)
    {
        $rekomen = Rekomendasi::findOrFail($id);
        $user = Auth::user();
        
        // Hanya admin atau perusahaan pembuat yang dapat menghapus rekomendasi
        if ($user->hasRole('admin') || 
            ($user->hasRole('perusahaan') && 
             $user->perusahaan && 
             $user->perusahaan->PerusahaanID == $rekomen->PerusahaanID)) {
            
            // Hapus file terkait jika ada
            if ($rekomen->FileAnggaran && Storage::disk('public')->exists($rekomen->FileAnggaran)) {
                Storage::disk('public')->delete($rekomen->FileAnggaran);
            }
            
            if ($rekomen->FileSPK && Storage::disk('public')->exists($rekomen->FileSPK)) {
                Storage::disk('public')->delete($rekomen->FileSPK);
            }
            
            $rekomen->delete();
            return redirect()->route('rekomendasis.index')->with('success', 'Rekomendasi berhasil dihapus');
        }
        
        return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki hak untuk menghapus rekomendasi ini.');
    }

    // Download file anggaran
    public function downloadAnggaran($id)
    {
        $rekomen = Rekomendasi::findOrFail($id);
        $user = Auth::user();
        
        // Cek apakah user memiliki akses
        $hasAccess = false;
        
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        } elseif ($user->hasRole('perusahaan')) {
            $perusahaan = Perusahaan::where('user_id', $user->id)->first();
            if ($perusahaan && $rekomen->PerusahaanID == $perusahaan->PerusahaanID) {
                $hasAccess = true;
            }
        } elseif ($user->hasRole('mitra')) {
            $mitra = Mitra::where('user_id', $user->id)->first();
            if ($mitra && $rekomen->MitraID == $mitra->MitraID) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke file ini.');
        }
        
        if (!$rekomen->FileAnggaran || !Storage::disk('public')->exists($rekomen->FileAnggaran)) {
            return back()->with('error', 'File anggaran tidak ditemukan.');
        }
        
        return Storage::disk('public')->download($rekomen->FileAnggaran);
    }
    
    // Download file SPK
    public function downloadSPK($id)
    {
        $rekomen = Rekomendasi::findOrFail($id);
        $user = Auth::user();
        
        // Cek apakah user memiliki akses
        $hasAccess = false;
        
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        } elseif ($user->hasRole('perusahaan')) {
            $perusahaan = Perusahaan::where('user_id', $user->id)->first();
            if ($perusahaan && $rekomen->PerusahaanID == $perusahaan->PerusahaanID) {
                $hasAccess = true;
            }
        } elseif ($user->hasRole('mitra')) {
            $mitra = Mitra::where('user_id', $user->id)->first();
            if ($mitra && $rekomen->MitraID == $mitra->MitraID) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->route('rekomendasis.index')->with('error', 'Anda tidak memiliki akses ke file ini.');
        }
        
        if (!$rekomen->FileSPK || !Storage::disk('public')->exists($rekomen->FileSPK)) {
            return back()->with('error', 'File SPK tidak ditemukan.');
        }
        
        return Storage::disk('public')->download($rekomen->FileSPK);
    }
}