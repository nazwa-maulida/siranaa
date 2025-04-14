<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey;
use App\Models\Proyek;
use App\Models\Perusahaan;
use App\Models\Mitra;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;



class SurveyController extends Controller
{
    
    public function index(Request $request)
{
    // Ambil perusahaan yang sedang login
    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();

    // Jika perusahaan ditemukan, ambil survei yang dibuat oleh perusahaan tersebut
    if ($perusahaan) {
        // Ambil proyek yang dimiliki oleh perusahaan
        $proyeks = Proyek::where('PerusahaanID', $perusahaan->PerusahaanID)->get();

        // Ambil survei yang terkait dengan proyek-proyek tersebut
        $surveys = Survey::whereIn('ProyekID', $proyeks->pluck('ProyekID'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('surveys.index', compact('proyeks', 'surveys', 'perusahaan'));
    }

    // Jika perusahaan tidak ditemukan, ambil mitra yang sedang login
    $mitra = Mitra::where('user_id', auth()->id())->first();

    if (!$mitra) {
        return redirect()->route('home')->with('error', 'Mitra tidak ditemukan.');
    }

    // Ambil proyek yang dimiliki oleh mitra yang sedang login
    $proyeks = Proyek::where('MitraID', $mitra->MitraID)->get();

    // Ambil survei yang terkait dengan proyek-proyek tersebut
    $surveys = Survey::whereIn('ProyekID', $proyeks->pluck('ProyekID'))
        ->orderBy('created_at', 'desc')
        ->get();

    return view('surveys.index', compact('proyeks', 'surveys', 'mitra'));
}
    public function create(Request $request, $ProyekID)
    {
        // Ambil ID proyek yang diambil
        $proyeks = Proyek::findOrFail($ProyekID);
        // Log untuk melihat data proyek dan perusahaan yang sedang login
        Log::info('Proyek ID: ' . $ProyekID . ', Perusahaan ID: ' . auth()->user()->perusahaan->PerusahaanID);
        // Validasi bahwa proyek tersebut ada dan milik perusahaan yang sedang login
        if ($proyeks->PerusahaanID !== auth()->user()->perusahaan->PerusahaanID) {
            return redirect()->route('proyeks.index')->with('error', 'Anda tidak berhak membuat survey untuk proyek ini.');
        }
        // Melanjutkan untuk membuat survey...
        return view('surveys.create', compact('proyeks'));
    }
    
    public function store(Request $request)
{
    $request->validate([
        'ProyekID' => 'required|exists:proyeks,ProyekID',
        'TglSurvey' => 'required|date',
        'Catatan' => 'nullable|string',
    ]);

    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();
    
    if (!$perusahaan) {
        return redirect()->route('surveys.index')->with('error', 'Perusahaan tidak ditemukan.');
    }

    $proyek = Proyek::find($request->ProyekID);
    
    if (!$proyek) {
        return redirect()->route('surveys.index')->with('error', 'Proyek tidak ditemukan.');
    }
    
    // Simpan survey
    $survey = Survey::create([
        'ProyekID' => $request->ProyekID,
        'PerusahaanID' => $perusahaan->PerusahaanID,
        'TglSurvey' => $request->TglSurvey,
        'Catatan' => $request->Catatan,
        'Keputusan' => 'Pending',
    ]);
    
    // Update status proyek menjadi "Disurvey"
    if ($proyek->Status == 'Diambil') {
        $proyek->Status = 'Disurvey';
        $proyek->save();
    
        // Buat notifikasi untuk mitra
        Notification::create([
            'type' => 'perusahaan_to_mitra',
            'from_id' => $perusahaan->PerusahaanID,
            'to_id' => $proyek->MitraID,
            'proyek_id' => $proyek->ProyekID,
            'judul' => 'Jadwal Survey Ditetapkan',
            'pesan' => 'Jadwal survey untuk proyek "' . $proyek->Judul . '" telah ditetapkan pada tanggal ' . $request->TglSurvey . ' oleh perusahaan ' . $perusahaan->NamaPerusahaan . '.',
            'dibaca' => false
        ]);
    }

    return redirect()->route('surveys.index')->with('success', 'Survey berhasil ditambahkan!');
}
    
public function edit($id)
{
    $survey = Survey::findOrFail($id);
    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();
    
    if (!$perusahaan) {
        return redirect()->route('surveys.index')->with('error', 'Perusahaan tidak ditemukan.');
    }

    // Ambil semua proyek yang terkait dengan perusahaan
    $proyeks = Proyek::where('PerusahaanID', $perusahaan->PerusahaanID)->get();
    
    return view('surveys.edit', compact('survey', 'proyeks', 'perusahaan'));
}
    
public function update(Request $request, $id)
{
    $rules = [
        'ProyekID' => 'nullable|exists:proyeks,ProyekID',
        'PerusahaanID' => 'nullable|exists:perusahaans,PerusahaanID', // Tidak wajib, tetapi jika ada harus valid
        'Catatan' => 'required|string|max:255',
        'Keputusan' => 'required|in:Lanjut,Tidak Lanjut', // Pastikan hanya menerima nilai yang valid
    ];
    $this->validate($request, $rules);

    $survey = Survey::findOrFail($id);
    // Ambil data perusahaan sesuai user login
    $perusahaan = Perusahaan::where('user_id', auth()->id())->first();

    // Update data survey
    $survey->update([
        'PerusahaanID' => $perusahaan ? $perusahaan->PerusahaanID : $survey->PerusahaanID,
        'Catatan' => $request->Catatan,
        'Keputusan' => $request->Keputusan,
    ]);

    // Update status proyek berdasarkan keputusan
    $proyek = Proyek::findOrFail($survey->ProyekID);
    if ($request->Keputusan === 'Lanjut') {
        $proyek->Status = 'Disetujui';
        $pesanNotifikasi = 'Proyek "' . $proyek->Judul . '" telah disetujui oleh perusahaan ' . $perusahaan->NamaPerusahaan . '.';
    } elseif ($request->Keputusan === 'Tidak Lanjut') {
        $proyek->Status = 'Ditolak';
        $pesanNotifikasi = 'Proyek "' . $proyek->Judul . '" telah ditolak oleh perusahaan ' . $perusahaan->NamaPerusahaan . '.';
    }
    $proyek->save();

    // Kirim notifikasi ke mitra
    Notification::create([
        'type' => 'perusahaan_to_mitra',
        'from_id' => $perusahaan->PerusahaanID,
        'to_id' => $proyek->MitraID,
        'proyek_id' => $proyek->ProyekID,
        'judul' => 'Keputusan Proyek',
        'pesan' => $pesanNotifikasi,
        'dibaca' => false
    ]);

    return redirect()->route('surveys.index')->with('success', 'Data survey berhasil diubah dan status proyek telah diperbarui.');
}
    
    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return redirect()->route('surveys.index')->with('success', 'Data survey berhasil dihapus');
    }
    
   
}
