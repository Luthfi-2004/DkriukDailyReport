@extends('layouts.app')

@section('title', 'Edit Profil Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line mr-1 align-middle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Pengaturan Profil</h4>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        {{-- KOLOM KIRI: FOTO --}}
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <label class="d-block mb-2 font-weight-bold">Foto Profil</label>
                                
                                {{-- Preview Gambar Saat Ini --}}
                                <div class="mx-auto mb-3 position-relative" style="width: 150px; height: 150px;">
                                    <img id="avatarPreview" 
                                         src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('assets/images/users/avatar-2.jpg') }}" 
                                         class="rounded-circle img-thumbnail w-100 h-100" 
                                         style="object-fit: cover;"
                                         alt="Profile">
                                    
                                    {{-- Tombol Ganti --}}
                                    <label for="uploadImage" class="btn btn-sm btn-primary position-absolute" style="bottom: 0; right: 0; border-radius: 50%;">
                                        <i class="ri-camera-fill"></i>
                                    </label>
                                </div>
                                <small class="text-muted">Klik ikon kamera untuk mengganti.</small>

                                {{-- Input File Tersembunyi --}}
                                <input type="file" id="uploadImage" class="d-none" accept="image/*">
                                {{-- Input Hidden untuk menampung hasil Crop (Base64) --}}
                                <input type="hidden" name="avatar_cropped" id="avatarCropped">
                            </div>
                        </div>

                        {{-- KOLOM KANAN: DATA DIRI --}}
                        <div class="col-md-8">
                            {{-- Nama (Boleh Edit) --}}
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            {{-- Email (Read Only) --}}
                            <div class="form-group">
                                <label>Email Login</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly>
                                <small class="text-muted">Email tidak dapat diubah. Hubungi Super Admin jika mendesak.</small>
                            </div>

                            {{-- Role (Read Only) --}}
                            <div class="form-group">
                                <label>Jabatan / Role</label>
                                <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="ri-save-line align-middle mr-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CROPPER --}}
<div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sesuaikan Posisi Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="img-container" style="max-height: 500px; display: block; background-color: #000;">
                    <img id="imageToCrop" src="" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="cropAndSave">Potong & Simpan</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
{{-- CSS Cropper.js --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%; /* Agar crop areanya bulat */
    }
</style>
@endpush

@push('scripts')
{{-- JS Cropper.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    $(document).ready(function(){
        var $modal = $('#cropModal');
        var image = document.getElementById('imageToCrop');
        var cropper;
        var $avatarImage = $('#avatarPreview');
        var $inputImage = $('#uploadImage');
        var $hiddenInput = $('#avatarCropped');

        // 1. Saat User Pilih File
        $inputImage.change(function(event){
            var files = event.target.files;
            var done = function(url){
                image.src = url;
                $modal.modal('show');
            };

            if(files && files.length > 0){
                var reader = new FileReader();
                reader.onload = function(event){
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });

        // 2. Saat Modal Muncul, Inisialisasi Cropper
        $modal.on('shown.bs.modal', function() {
            cropper = new Cropper(image, {
                aspectRatio: 1, // Wajib kotak (1:1) biar pas di lingkaran
                viewMode: 1,
                preview: '.preview'
            });
        }).on('hidden.bs.modal', function() {
            // Hancurkan cropper saat tutup modal biar gak berat
            cropper.destroy();
            cropper = null;
            $inputImage.val(''); // Reset input file
        });

        // 3. Saat Klik Tombol "Potong & Simpan"
        $('#cropAndSave').click(function(){
            // Ambil hasil crop dalam format Canvas
            var canvas = cropper.getCroppedCanvas({
                width: 300,  // Resize otomatis biar gak kegedean filenya
                height: 300,
            });

            // Ubah ke Base64
            canvas.toBlob(function(blob){
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function(){
                    var base64data = reader.result;
                    
                    // a. Tampilkan preview di halaman
                    $avatarImage.attr('src', base64data);
                    
                    // b. Masukkan data ke Hidden Input untuk dikirim ke Controller
                    $hiddenInput.val(base64data);
                    
                    // c. Tutup modal
                    $modal.modal('hide');
                }
            });
        });
    });
</script>
@endpush
@endsection