<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Hanya Super Admin yang boleh akses, kita kunci di sini atau di Middleware nanti.
        // Untuk sekarang return true dulu, keamanan kita handle di Route.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50', // Ekor, Kg, Pcs
            'price' => 'required|numeric|min:0', // Harus angka dan tidak boleh minus
        ];
    }
    /**
     * Bersihkan data sebelum validasi.
     * Ubah "10.000" jadi "10000".
     */
    protected function prepareForValidation()
    {
        // Ambil input price
        if ($this->has('price')) {
            $this->merge([
                // Hapus titik, hapus 'Rp', hapus spasi
                'price' => str_replace(['.', 'Rp', ' '], '', $this->price) 
            ]);
        }
    }
}
