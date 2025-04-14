<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Notification;
use App\Models\NotificationHistory;
use App\Models\User;
use App\Models\Mitra;
use App\Models\Perusahaan;
use App\Models\Proyek;
use App\Models\Rekomendasi;
use App\Models\Survey;



class AuthentificationController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'mitra' => redirect()->route('mitra.dashboard'),
                'perusahaan' => redirect()->route('perusahaan.dashboard'),
                default => redirect()->route('dashboard'), // Default jika role tidak dikenali
            };
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => null, // Peran ditentukan setelah registrasi
        ]);
    
        return redirect()->route('auth.role-selection', ['id' => $user->id]);
    }
    
    public function showRoleSelectionForm($id)
    {
        $user = User::findOrFail($id);
        return view('auth.role-selection', compact('user'));
    }
    
    public function setRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:mitra,perusahaan'
        ]);
    
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
    
        // Redirect sesuai peran
        return $request->role === 'mitra'
            ? redirect()->route('mitra.form', ['id' => $user->id])
            : redirect()->route('perusahaan.form', ['id' => $user->id]);
    }
    
    public function showMitraForm($id)
    {
        $user = User::findOrFail($id);
        return view('auth.mitra-form', compact('user'));
    }
    
    public function submitMitra(Request $request, $id)
    {
        $request->validate([
            'NamaMitra' => 'required|string|max:150',
            'PIC' => 'required|string|max:100',
            'NoTelp' => 'required|string|max:15',
            'Alamat' => 'required|string|max:150',
        ]);
    
        Mitra::create([
            'user_id' => $id,
            'NamaMitra' => $request->NamaMitra,
            'PIC' => $request->PIC,
            'NoTelp' => $request->NoTelp,
            'Alamat' => $request->Alamat,
        ]);
    
        return redirect()->route('login')->with('success', 'Pendaftaran mitra berhasil!');
    }
    

    public function showPerusahaanForm($id)
{
    $user = User::findOrFail($id); // Cari user berdasarkan ID

    return view('auth.perusahaan-form', compact('user'));
}


    public function submitPerusahaan(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user || $user->role !== 'perusahaan') {
            return redirect()->route('login')->with('error', 'Akses tidak valid.');
        }

        $request->validate([
            'NamaPerusahaan' => 'required|string|max:255',
            'PIC' => 'required|string|max:100',
            'NoTelp' => 'required|string|max:15',
            'Alamat' => 'required|string|max:255',
        ]);

        Perusahaan::create([
            'user_id' => $user->id,
            'NamaPerusahaan' => $request->NamaPerusahaan,
            'PIC' => $request->PIC,
            'NoTelp' => $request->NoTelp,
            'Alamat' => $request->Alamat,
        ]);

        Auth::logout();
        return redirect()->route('login')->with('success', 'Pendaftaran Perusahaan berhasil, silakan login.');
    }

    public function dashboard()
{
    // Ambil user yang sedang login
    $user = auth()->user();
    
    // Pastikan user tidak null
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }
    
    // Ambil data perusahaan jika user memiliki peran 'perusahaan'
    $perusahaan = null;
    if ($user->role == 'perusahaan') {
        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
    }
    
    // Query dasar untuk proyek
    $proyekQuery = Proyek::with('mitra');
    
    // Filter berbeda berdasarkan peran user
    if ($user->role == 'perusahaan' && $perusahaan) {
        // Untuk perusahaan, kita perlu mengecualikan proyek yang telah ditolak oleh perusahaan ini dan diajukan ulang
        $proyekQuery->where(function($query) use ($perusahaan) {
            // Proyek dengan status 'Diajukan'
            $query->where(function($q) use ($perusahaan) {
                $q->where('Status', 'Diajukan')
                  ->where(function($subq) use ($perusahaan) {
                      // Tampilkan proyek yang bukan resubmitted, atau resubmitted tapi bukan ditolak oleh perusahaan ini
                      $subq->where('IsResubmitted', false)
                           ->orWhereNull('IsResubmitted')
                           ->orWhere(function($innerq) use ($perusahaan) {
                               $innerq->where('IsResubmitted', true)
                                     ->where('PerusahaanID', '!=', $perusahaan->PerusahaanID);
                           });
                  });
            });
            
            // Atau proyek yang diambil oleh perusahaan ini
            $query->orWhere(function($q) use ($perusahaan) {
                $q->where('Status', 'Diambil')
                  ->where('PerusahaanID', $perusahaan->PerusahaanID);
            });
            
            // Atau proyek lain yang sedang diproses oleh perusahaan ini
            $query->orWhere(function($q) use ($perusahaan) {
                $q->where('PerusahaanID', $perusahaan->PerusahaanID)
                  ->whereIn('Status', ['Disurvey', 'Disetujui', 'Ditolak', 'Dikerjakan', 'Selesai']);
            });
        });
    } else {
        // Untuk role lain, tampilkan semua proyek dengan status tersebut
        $proyekQuery->where(function($query) {
            $query->where('Status', 'Diajukan')
                  ->orWhere('Status', 'Diambil')
                  ->orWhere('Status', 'Disurvey')
                  ->orWhere('Status', 'Disetujui')
                  ->orWhere('Status', 'Ditolak')
                  ->orWhere('Status', 'Dikerjakan')
                  ->orWhere('Status', 'Selesai');
        });
    }
    
    // Ambil hasil query
    $proyeks = $proyekQuery->get();
    
    // Ambil notifikasi dari user yang sedang login
    $allNotifications = $user->notifications ?? collect();
    $unreadNotifications = $user->unreadNotifications ?? collect();
    
    // Ambil rekomendasi yang terkait dengan proyek
    $rekomens = Rekomendasi::whereIn('ProyekID', $proyeks->pluck('ProyekID'))->get();
    
    // Pass semua variabel yang diperlukan ke view
    return view('dashboard.perusahaan', compact('proyeks', 'allNotifications', 'unreadNotifications', 'rekomens', 'perusahaan'));
}

public function dashboardMitra()
{
    $user = auth()->user();

    // Pastikan user tidak null
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Ambil data mitra berdasarkan user yang login
    $mitra = Mitra::where('user_id', $user->id)->first();

    if (!$mitra) {
        return redirect()->route('surveys.index')->with('error', 'Mitra tidak ditemukan.');
    }

    // Ambil semua proyek milik mitra beserta relasinya
    $proyeks = Proyek::with(['mitra', 'rekomendasi'])
    ->where('MitraID', $mitra->MitraID)
    ->orderBy('TglPengajuan', 'desc') // Mengurutkan berdasarkan tanggal pengajuan terbaru
    ->get();

    // Ambil ID proyek yang dimiliki mitra
    $proyekIds = $proyeks->pluck('ProyekID');

    // Ambil semua survei yang terkait dengan proyek-proyek tersebut
    $surveys = Survey::with(['proyek'])
        ->whereIn('ProyekID', $proyekIds)
        ->get();

    // Ambil semua notifikasi atau inisialisasi sebagai koleksi kosong
    $allNotifications = $user->notifications ?? collect();
    $unreadNotifications = $user->unreadNotifications ?? collect();

    // Ambil rekomendasi berdasarkan proyek yang dimiliki mitra
    $rekomendasis = Rekomendasi::whereIn('ProyekID', $proyekIds)->get();

    // Kirim variabel $mitra ke view agar bisa diakses untuk menampilkan nama PIC
    return view('dashboard.mitra', compact('proyeks', 'surveys', 'allNotifications', 'unreadNotifications', 'rekomendasis', 'mitra'));
}

public function dashboardAdmin()
{
    // Ambil user yang sedang login
    $user = auth()->user();

    // Pastikan user tidak null
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Ambil data perusahaan jika user memiliki peran 'perusahaan'
    $perusahaan = null;
    if ($user->role == 'perusahaan') {
        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
    }

    // Ambil semua proyek dengan relasi mitra
    $proyeks = Proyek::with('mitra')->get();

    // Ambil notifikasi dari user yang sedang login
    $allNotifications = $user->notifications ?? collect(); 
    $unreadNotifications = $user->unreadNotifications ?? collect();

    // Ambil rekomendasi yang terkait dengan proyek
    $rekomens = Rekomendasi::whereIn('ProyekID', $proyeks->pluck('ProyekID'))->get();

    // Pass semua variabel yang diperlukan ke view
    return view('dashboard.perusahaan', compact('proyeks', 'allNotifications', 'unreadNotifications', 'rekomens', 'perusahaan'));
}

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    

    public function updateStatus($id)
    {
        $proyek = Proyek::findOrFail($id);
    
        // Hanya perusahaan yang dapat mengubah status proyek
        if (auth()->user()->isPerusahaan()) {
            $proyek->update([
                'Status' => 'Diambil', // Ganti dengan status yang sesuai
            ]);
            return redirect()->route('proyeks.index')->with('success', 'Status proyek berhasil diperbarui.');
        }
    
        return redirect()->route('proyekss.index')->with('error', 'Anda tidak memiliki izin untuk mengubah status proyek.');
    }
    
}
