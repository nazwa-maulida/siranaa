@extends('layouts.template')

@section('title', 'Edit Jadwal Survey') {{-- Menambahkan title di tab browser --}}

@section('content')

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-wrapper">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Update Hasil Survey</h4>

                            <form class="forms-sample" method="POST" action="{{ route('surveys.update', $survey->SurveyID) }}">
                            @csrf
                            @method('PUT')

                                {{-- Input Hidden untuk PerusahaanID (otomatis dari user yang login) --}}
                                <input type="hidden" name="PerusahaanID" value="{{ auth()->user()->perusahaan->id }}">

                                <div class="form-group">
                                    <label for="Keputusan">Keputusan</label><br>
                                    <input type="radio" id="lanjut" name="Keputusan" value="Lanjut" {{ $survey->Keputusan == 'Lanjut' ? 'checked' : '' }} onclick="toggleCatatan(false)">
                                    <label for="lanjut">Lanjut</label><br>
                                    <input type="radio" id="tidak_lanjut" name="Keputusan" value="Tidak Lanjut" {{ $survey->Keputusan == 'Tidak Lanjut' ? 'checked' : '' }} onclick="toggleCatatan(true)">
                                    <label for="tidak_lanjut">Tidak Lanjut</label>
                                </div>

                                <div class="form-group" id="catatanField" style="display: {{ $survey->Keputusan == 'Tidak Lanjut' ? 'block' : 'none' }};">
                                    <label for="Catatan">Catatan</label>
                                    <textarea class="form-control" name="Catatan" rows="4">{{ old('Catatan', $survey->Catatan) }}</textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <a href="{{ route('surveys.index') }}" class="btn btn-light">Cancel</a>
                            
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>

<script>
    function toggleCatatan(show) {
        document.getElementById('catatanField').style.display = show ? 'block' : 'none';
    }
</script>
@endsection