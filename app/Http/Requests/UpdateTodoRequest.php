<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 1. Authorize the request
        // Get the todo model being updated from the route parameters
        $todo = $this->route('todo');

        // Allow the update ONLY if the authenticated user owns this todo
        return $todo && $this->user()->id === $todo->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'sometimes' means validate this rule ONLY if the 'task' key is present in the request
            'task' => 'sometimes|string|max:255',

            // 'sometimes' combined with Enum validation restricts it only to our allowed values
            'status' => [
                'sometimes',
                Rule::in(['pending', 'completed']),
            ],
        ];
    }
}
