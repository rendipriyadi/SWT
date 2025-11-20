<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Admin mode - no authentication
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert deadline date from dd/mm/yyyy to Y-m-d
        if ($this->has('tenggat_waktu') && $this->tenggat_waktu) {
            $this->merge([
                'tenggat_waktu' => $this->convertDateFormat($this->tenggat_waktu),
            ]);
        }

        // Convert completion date from dd/mm/yyyy to Y-m-d
        if ($this->has('Tanggal') && $this->Tanggal) {
            $this->merge([
                'Tanggal' => $this->convertDateFormat($this->Tanggal),
            ]);
        }
    }

    /**
     * Convert date from dd/mm/yyyy to Y-m-d
     */
    private function convertDateFormat($date)
    {
        // If already in Y-m-d format, return as is
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Convert from dd/mm/yyyy to Y-m-d
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return $date;
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
            'status' => 'nullable|in:Assigned,Completed',
            'Foto' => 'nullable|array',
            'Foto.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'additional_pics' => 'nullable|array',
            'additional_pics.*' => 'nullable|exists:penanggung_jawab,id',
            'Tanggal' => 'nullable|date',
            'completion_description' => 'nullable|string',
            'completion_photos' => 'nullable|array',
            'completion_photos.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ];
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
            'status.in' => 'Status must be Assigned or Completed.',
            'Foto.*.image' => 'File harus berupa gambar.',
            'Foto.*.mimes' => 'Format gambar harus jpg, png, jpeg, gif, atau svg.',
            'Foto.*.max' => 'Ukuran gambar maksimal 2MB.',
            'additional_pics.*.exists' => 'Person in Charge yang dipilih tidak valid.',
        ];
    }
}
