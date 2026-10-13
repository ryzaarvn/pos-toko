<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:20|unique:products,code',
            'name' => 'required|string|max:150',
            'unit' => 'required|string|max:20',
            'price' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
        ];
    }
    public function messages(): array
    {
        return [
            'code.unique' => 'Kode produk sudah digunakan, gunakan kode lain.',
        ];
    }
}