@extends('layouts.template')

@section('title', 'Buat Rekomendasi Baru')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Buat Rekomendasi Anggaran Baru</h4>
                        <p class="card-description">
                            Silakan pilih proyek dan upload file anggaran untuk direview oleh mitra
                        </p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($proyeks->isEmpty())
                            <div class="alert alert-info">
                                Anda belum memiliki proyek yang terkait dengan mitra. Silakan ambil proyek dari mitra terlebih dahulu.
                            </div>
                        @else
                            <form class="forms-sample" method="POST" action="{{ route('rekomendasis.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
    <label>Proyek Terpilih</label>
    <input type="text" class="form-control" value="{{ $proyeks->firstWhere('ProyekID', request('proyekId'))?->Judul }}" readonly>
</div>
<input type="hidden" name="ProyekID" value="{{ request('proyekId') }}">


                                <div class="form-group">
                                    <label for="Catatan">Catatan</label>
                                    <textarea class="form-control @error('Catatan') is-invalid @enderror" 
                                        name="Catatan" id="Catatan" rows="4" 
                                        placeholder="Berikan detail atau catatan terkait anggaran yang diajukan" 
                                        required>{{ old('Catatan') }}</textarea>
                                    @error('Catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="FileAnggaran">Upload File dokumentasi dan anggaran anda</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control file-upload-info @error('FileAnggaran') is-invalid @enderror" 
                                            name="FileAnggaran" id="FileAnggaran" 
                                            accept=".doc,.docx,.pdf,.xls,.xlsx" required>
                                    </div>
                                    <small class="text-muted">Format yang diterima: DOC, DOCX, PDF, XLS, XLSX (Max: 10MB)</small>
                                    @error('FileAnggaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary mr-2">Kirim Anggaran</button>
                                    <a href="{{ route('rekomendasis.index') }}" class="btn btn-light">Batal</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk file upload preview (jika diperlukan)
    $(document).ready(function() {
        $('.file-upload-browse').on('click', function() {
            var fileInput = $(this).parents('.input-group').find('.file-upload-info');
            fileInput.trigger('click');
        });
        
        $('.file-upload-info').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).parent().find('.form-control').val(fileName);
        });
    });
</script>
@endpush