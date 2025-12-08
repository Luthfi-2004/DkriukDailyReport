@extends('layouts.app')

@section('title', 'Master Data Barang')

@section('content')
    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-check-line mr-1 align-middle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri-error-warning-line mr-1 align-middle"></i> Gagal menyimpan. Periksa inputan Anda.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title">Daftar Barang Operasional</h4>
                {{-- Tombol Trigger Modal Tambah --}}
                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal"
                    data-target="#addItemModal">
                    <i class="ri-add-line align-middle mr-1"></i> Tambah Barang
                </button>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Harga / Unit</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td><span class="badge badge-soft-primary font-size-12">{{ $item->unit }}</span></td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        {{-- TOMBOL EDIT --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning mr-2"
                                            onclick="editItem(this)" data-url="{{ route('master-items.update', $item->id) }}"
                                            data-name="{{ $item->name }}" data-unit="{{ $item->unit }}"
                                            data-price="{{ $item->price }}" data-active="{{ (int) $item->is_active }}"
                                            title="Edit Barang">
                                            <i class="ri-pencil-line"></i>
                                        </button>

                                        {{-- TOMBOL HAPUS --}}
                                        <form action="{{ route('master-items.destroy', $item->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Hapus barang ini? Data laporan lama akan tetap aman.');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus">
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

    {{-- MODAL TAMBAH BARANG --}}
    <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('master-items.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="name" class="form-control" required placeholder="Contoh: Ayam Fillet">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" name="unit" class="form-control" required placeholder="Kg / Ekor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="price" class="form-control rupiah-input" required
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Status default Aktif (Hidden) --}}
                        <input type="hidden" name="is_active" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT BARANG --}}
    <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editItemForm" method="POST">
                    @csrf
                    @method('PUT') {{-- Method Spoofing untuk Update --}}

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Barang</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" name="unit" id="edit_unit" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="price" id="edit_price" class="form-control rupiah-input"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status Ketersediaan</label>
                            {{-- Tambahkan class 'select2' di sini --}}
                            <select name="is_active" id="edit_active" class="form-control select2" style="width: 100%;">
                                <option value="1">Aktif (Bisa Dipilih User)</option>
                                <option value="0">Non-Aktif (Disembunyikan)</option>
                            </select>
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
            $(document).ready(function() {
                // Inisialisasi Select2 pada Dropdown Status di Modal Edit
                $('#edit_active').select2({
                    dropdownParent: $('#editItemModal'), // WAJIB: Agar dropdown tidak tertutup modal
                    minimumResultsForSearch: Infinity,   // Hilangkan kolom search karena opsinya sedikit
                    width: '100%'
                });
            });

            // 1. Logic Format Rupiah (Auto Titik)
            const rupiahInputs = document.querySelectorAll('.rupiah-input');

            rupiahInputs.forEach(input => {
                input.addEventListener('keyup', function (e) {
                    this.value = formatRupiah(this.value);
                });
            });

            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            function editItem(button) {
                let url = button.getAttribute('data-url');
                let name = button.getAttribute('data-name');
                let unit = button.getAttribute('data-unit');
                let price = button.getAttribute('data-price');
                let active = button.getAttribute('data-active');

                document.getElementById('editItemForm').action = url;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_unit').value = unit;

                // Format Harga ke Rupiah
                let cleanPrice = parseFloat(price).toString();
                document.getElementById('edit_price').value = formatRupiah(cleanPrice);

                // Set value Select2 dan trigger change agar UI berubah
                $('#edit_active').val(active).trigger('change');

                $('#editItemModal').modal('show');
            }
        </script>
    @endpush
@endsection