<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'survey_id' => 'required|integer|exists:surveys,id',
            'question_text' => 'required|string|max:500',
            'question_type' => 'required|string|in:text,single,multiple,scale,boolean',
            'options' => 'nullable|array|min:1|max:10',
            'options.*' => 'string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'survey_id.required' => 'El ID de la encuesta es obligatorio.',
            'survey_id.exists' => 'La encuesta especificada no existe.',
            'question_text.required' => 'El texto de la pregunta es obligatorio.',
            'question_text.max' => 'El texto de la pregunta no puede exceder 500 caracteres.',
            'question_type.required' => 'El tipo de pregunta es obligatorio.',
            'question_type.in' => 'El tipo de pregunta debe ser: text, single, multiple, scale o boolean.',
            'options.array' => 'Las opciones deben ser un array.',
            'options.min' => 'Debe haber al menos una opción.',
            'options.max' => 'No puede haber más de 10 opciones.',
            'options.*.string' => 'Cada opción debe ser un texto.',
            'options.*.max' => 'Cada opción no puede exceder 255 caracteres.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questionType = $this->input('question_type');
            $options = $this->input('options');

            // Validar que las preguntas de tipo single, multiple y scale tengan opciones
            if (in_array($questionType, ['single', 'multiple', 'scale']) && empty($options)) {
                $validator->errors()->add('options', 'Las preguntas de tipo ' . $questionType . ' requieren opciones.');
            }

            // Validar que las preguntas de tipo text y boolean no tengan opciones
            if (in_array($questionType, haystack: ['text']) && !empty($options)) {
                $validator->errors()->add('options', 'Las preguntas de tipo ' . $questionType . ' no pueden tener opciones.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
