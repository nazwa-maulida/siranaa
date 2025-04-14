@extends('layouts.template')

@section('title', 'Data Survey') {{-- Menambahkan title di tab browser --}}

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center"> 
            <div class="col-lg-12 grid-margin stretch-card"> {{-- Pastikan kolom cukup lebar --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Data Survey</h4>
                       
                        
                        @if(session('success'))
                        <div class="alert alert-success" role="alert" id="success-alert">
                            {{ session('success') }}
                        </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul Proyek</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Tgl Survey</th>
                                        <th>Catatan</th>
                                        <th>Keputusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @if ($surveys->isEmpty())
                                    <tr>
                                        <td class="text-center" colspan="7">Tidak ada data survey</td>
                                    </tr>
                                    @else
                                    
                                    @foreach ($surveys as $survey)
                                    <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $survey->proyek->Judul }}</td>
                                    <td>{{ $survey->perusahaan->NamaPerusahaan ?? 'Perusahaan Tidak Ditemukan' }}</td>
                                    <td>{{ $survey->TglSurvey }}</td>
                                    <td>{{ $survey->Catatan }}</td>
                                    <td>{{ $survey->Keputusan }}</td>
                                    <td>
                                        <a href="{{ route('surveys.edit', $survey->SurveyID) }}" class="btn btn-inverse-info btn-fw">Edit</a>
                                        <form method="POST" action="{{ route('surveys.destroy', $survey->SurveyID) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-inverse-danger btn-fw" onclick="return confirm('Apakah anda yakin ingin menghapusnya?')" title="Hapus">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection
