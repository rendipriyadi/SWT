<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
        return [
            'area_id' => 'required|exists:areas,id',
            'penanggung_jawab_id' => 'nullable|exists:penanggung_jawab,id',
            'problem_category_id' => 'required|exists:problem_categories,id',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
            'Foto' => 'nullable|array',
            'Foto.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'additional_pics' => 'nullable|array',
            'additional_pics.*' => 'nullable|exists:penanggung_jawab,id|distinct',
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if additional_pics contains the same ID as penanggung_jawab_id
            $mainPicId = $this->input('penanggung_jawab_id');
            $additionalPics = $this->input('additional_pics', []);
            
            if ($mainPicId && !empty($additionalPics)) {
                // Filter out empty values
                $additionalPics = array_filter($additionalPics);
                
                if (in_array($mainPicId, $additionalPics)) {
                    $validator->errors()->add(
                        'additional_pics',
                        'Additional Person in Charge cannot be the same as the main Person in Charge.'
                    );
                }
            }
        });
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'area_id.required' => 'Area harus dipilih.',
            'area_id.exists' => 'Area yang dipilih tidak valid.',
            'problem_category_id.required' => 'Kategori masalah harus dipilih.',
            'problem_category_id.exists' => 'Kategori masalah yang dipilih tidak valid.',
            'deskripsi_masalah.required' => 'Deskripsi masalah harus diisi.',
            'tenggat_waktu.required' => 'Tenggat waktu harus diisi.',
            'tenggat_waktu.date' => 'Format tenggat waktu tidak valid.',
            'Foto.*.image' => 'File harus berupa gambar.',
            'Foto.*.mimes' => 'Format gambar harus jpg, png, jpeg, gif, atau svg.',
            'Foto.*.max' => 'Ukuran gambar maksimal 2MB.',
            'additional_pics.*.exists' => 'Person in Charge yang dipilih tidak valid.',
            'additional_pics.*.distinct' => 'Tidak boleh ada duplikasi Person in Charge pada Additional PICs.',
        ];
    }
}
