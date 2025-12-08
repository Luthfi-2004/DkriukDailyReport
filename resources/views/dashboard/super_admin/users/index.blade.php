@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- Flash Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line mr-1 align-middle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        @endif

        {{-- Error Validation Message (Muncul kalau edit gagal) --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line mr-1 align-middle"></i> Ada kesalahan input. Silakan cek kembali.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Daftar Pengguna Sistem</h4>
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target="#addUserModal">
                        <i class="ri-user-add-line align-middle mr-1"></i> Tambah User Baru
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td>
                                    <h5 class="font-size-14 mb-1">{{ $u->name }}</h5>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->isAdmin())
                                        <span class="badge badge-info font-size-12">ADMIN</span>
                                    @else
                                        <span class="badge badge-secondary font-size-12">USER STAFF</span>
                                    @endif
                                </td>
                                <td>{{ $u->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        {{-- TOMBOL EDIT --}}
                                        {{-- Kita simpan data user di attribute "data-*" agar bisa diambil JS --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning mr-2" 
                                            onclick="editUser(this)"
                                            data-id="{{ $u->id }}"
                                            data-name="{{ $u->name }}"
                                            data-email="{{ $u->email }}"
                                            data-role="{{ $u->role }}"
                                            data-url="{{ route('manage-users.update', $u->id) }}"
                                            title="Edit User">
                                            <i class="ri-pencil-line"></i>
                                        </button>

                                        {{-- TOMBOL HAPUS --}}
                                        <form action="{{ route('manage-users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user {{ $u->name }}? Akses login akan dicabut permanen.');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('manage-users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrasi User Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Nama Asli">
                    </div>
                    <div class="form-group">
                        <label>Email Login</label>
                        <input type="email" name="email" class="form-control" required placeholder="email@perusahaan.com">
                    </div>
                    <div class="form-group">
                        <label>Password Awal</label>
                        <input type="text" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label>Jabatan / Role</label>
                        <select name="role" class="form-control">
                            <option value="user">User Staff (Input Data)</option>
                            <option value="admin">Admin (Analisis Data)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT USER --}}
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Action Form akan diisi otomatis oleh JS --}}
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT') {{-- Wajib untuk Update Laravel --}}
                
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email Login</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jabatan / Role</label>
                        <select name="role" id="edit_role" class="form-control">
                            <option value="user">User Staff (Input Data)</option>
                            <option value="admin">Admin (Analisis Data)</option>
                        </select>
                    </div>
                    
                    <hr>
                    <div class="form-group mb-0">
                        <label class="text-danger">Reset Password (Opsional)</label>
                        <input type="text" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                        <small class="text-muted">Isi hanya jika user lupa password dan minta diganti.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editUser(button) {
        // 1. Ambil data dari tombol yang diklik
        let url = button.getAttribute('data-url');
        let name = button.getAttribute('data-name');
        let email = button.getAttribute('data-email');
        let role = button.getAttribute('data-role');

        // 2. Isi data ke dalam Modal Edit
        document.getElementById('editUserForm').action = url; // Set rute update
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;

        // 3. Tampilkan Modal (Pakai jQuery bawaan template Bootstrap)
        $('#editUserModal').modal('show');
    }
</script>
@endpush

@endsection