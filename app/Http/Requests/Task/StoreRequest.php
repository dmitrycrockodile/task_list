<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
      return [
        'name.required' => 'Please write the task name',
        'name.string' => 'Task name must be a string',
        'name.max' => 'Task name must be shorter than 100 symbols',
        'description.required' => 'Please write the description',
        'description.string' => 'Description must be a string',
        'description.max' => 'Description must be shorter than 255 symbols',
      ];
    }
}   
