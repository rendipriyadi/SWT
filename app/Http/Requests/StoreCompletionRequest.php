<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompletionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Admin mode - no authentication
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'status' => 'required|string|in:Assigned,Completed',
        ];

        // Additional validation if status is Completed
        if ($this->status === 'Completed') {
            $rules['Tanggal'] = 'required|date';
            $rules['deskripsi_penyelesaian'] = 'required|string';
            $rules['Foto'] = 'nullable|array';
            $rules['Foto.*'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status must be Assigned or Completed.',
            'Tanggal.required' => 'Tanggal penyelesaian harus diisi.',
            'Tanggal.date' => 'Format tanggal tidak valid.',
            'deskripsi_penyelesaian.required' => 'Deskripsi penyelesaian harus diisi.',
            'Foto.*.image' => 'File harus berupa gambar.',
            'Foto.*.mimes' => 'Format gambar harus jpg, png, jpeg, gif, atau svg.',
            'Foto.*.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
