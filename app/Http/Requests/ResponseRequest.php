<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Questions;

class ResponseRequest extends FormRequest
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
        $data = $this->all();

        // Detectar si es el nuevo formato con survey_id y responses
        if ($this->isSurveyWithResponsesFormat($data)) {
            return [
                'survey_id' => 'required|integer|exists:surveys,id',
                'responses' => 'required|array|min:1',
                'responses.*.question_id' => 'required|integer|exists:questions,id',
                'responses.*.answer' => 'required',
            ];
        }

        // Detectar si es el formato anterior con survey_id y answers
        if ($this->isSurveyResponseFormat($data)) {
            return [
                'survey_id' => 'required|integer|exists:surveys,id',
                'answers' => 'required|array|min:1',
                'answers.*' => 'required',
            ];
        }

        // Detectar si es un array de respuestas
        if ($this->isArrayOfResponses($data)) {
            return [
                '*.question_id' => 'required|integer|exists:questions,id',
                '*.answer' => 'required',
            ];
        }

        // Formato individual (por defecto)
        return [
            'question_id' => 'required|integer|exists:questions,id',
            'answer' => 'required',
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
            'survey_id.integer' => 'El ID de la encuesta debe ser un número entero.',

            'responses.required' => 'Las respuestas son obligatorias.',
            'responses.array' => 'Las respuestas deben ser un array.',
            'responses.min' => 'Debe proporcionar al menos una respuesta.',

            'responses.*.question_id.required' => 'El ID de la pregunta es obligatorio en cada respuesta.',
            'responses.*.question_id.integer' => 'El ID de la pregunta debe ser un número entero.',
            'responses.*.question_id.exists' => 'Una o más preguntas especificadas no existen.',
            'responses.*.answer.required' => 'La respuesta es obligatoria en cada elemento.',

            'answers.required' => 'Las respuestas son obligatorias.',
            'answers.array' => 'Las respuestas deben ser un array.',
            'answers.min' => 'Debe proporcionar al menos una respuesta.',
            'answers.*.required' => 'Cada respuesta es obligatoria.',

            'question_id.required' => 'El ID de la pregunta es obligatorio.',
            'question_id.integer' => 'El ID de la pregunta debe ser un número entero.',
            'question_id.exists' => 'La pregunta especificada no existe.',
            'answer.required' => 'La respuesta es obligatoria.',

            '*.question_id.required' => 'El ID de la pregunta es obligatorio en cada respuesta.',
            '*.question_id.integer' => 'El ID de la pregunta debe ser un número entero.',
            '*.question_id.exists' => 'Una o más preguntas especificadas no existen.',
            '*.answer.required' => 'La respuesta es obligatoria en cada elemento.',
        ];
    }

    /**
     * Validate that questions belong to the survey (custom validation)
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            if ($this->isSurveyWithResponsesFormat($data)) {
                $surveyId = $data['survey_id'];
                $responses = $data['responses'] ?? [];

                foreach ($responses as $index => $response) {
                    if (isset($response['question_id'])) {
                        $questionExists = Questions::where('id', $response['question_id'])
                            ->where('survey_id', $surveyId)
                            ->exists();

                        if (!$questionExists) {
                            $validator->errors()->add(
                                "responses.{$index}.question_id",
                                "La pregunta con ID {$response['question_id']} no pertenece a la encuesta especificada."
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Check if the data is in new survey response format with responses array
     */
    private function isSurveyWithResponsesFormat(array $data): bool
    {
        return isset($data['survey_id']) && isset($data['responses']) && is_array($data['responses']);
    }

    /**
     * Check if the data is in old survey response format with answers object
     */
    private function isSurveyResponseFormat(array $data): bool
    {
        return isset($data['survey_id']) && isset($data['answers']) && is_array($data['answers']);
    }

    /**
     * Check if the data is an array of response objects
     */
    private function isArrayOfResponses(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (isset($data['survey_id'])) {
            return false;
        }

        if (isset($data[0]) && is_array($data[0]) && array_key_exists('question_id', $data[0])) {
            return true;
        }

        if (array_key_exists('question_id', $data)) {
            return false;
        }

        return array_keys($data) === range(0, count($data) - 1);
    }

    /**
     * Get custom messages for validator errors.
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
