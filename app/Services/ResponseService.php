<?php

namespace App\Services;

use App\Models\Response;
use App\Models\Questions;  // Corregido: usar el nombre correcto del modelo
use App\Models\Surveys;
use function Laravel\Prompts\select;

class ResponseService
{
    /**
     * Get all responses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Response::all(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching responses: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get response by ID.
     *
     * @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        try {
            $response = Response::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get responses by authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByAuthUser( $survey_id)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $responses = Response::join('questions', 'responses.question_id', '=', 'questions.id')
                ->select(
                    'responses.id',
                    'responses.user_id',
                    'responses.question_id',
                    'responses.answer',
                    'responses.created_at',
                    'responses.updated_at',
                    'questions.question_text',  // Campo de la tabla questions
                    'questions.survey_id'       // Si también necesitas el survey_id
                )
                ->where('responses.user_id', auth()->id())
                ->where('questions.survey_id', $survey_id)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $responses,
                'count' => $responses->count(),
                'message' => $responses->count() > 0 ? 'Responses retrieved successfully' : 'No responses found for authenticated user'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching responses: ' . $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Create responses (multiple formats supported).
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(array $data)
    {
        try {
            $userId = auth()->id();

            // Verificar que el usuario esté autenticado
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $createdResponses = [];

            // Detectar el formato de los datos
            if ($this->isSurveyWithResponsesFormat($data)) {
                // Nuevo formato: {"survey_id": 1, "responses": [{"question_id": 1, "answer": "Junior"}, ...]}
                if (!isset($data['survey_id']) || !isset($data['responses'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Survey response format must have 'survey_id' and 'responses' fields",
                    ], 422);
                }

                $surveyId = $data['survey_id'];
                $responses = $data['responses'];

                // Verificar que la encuesta existe
                $surveyExists = Surveys::where('id', $surveyId)->exists();
                if (!$surveyExists) {
                    return response()->json([
                        'success' => false,
                        'message' => "Survey with ID {$surveyId} does not exist",
                    ], 422);
                }

                // Validar que responses es un array
                if (!is_array($responses)) {
                    return response()->json([
                        'success' => false,
                        'message' => "The 'responses' field must be an array",
                    ], 422);
                }

                // Procesar cada respuesta
                foreach ($responses as $index => $responseData) {
                    if (!isset($responseData['question_id']) || !isset($responseData['answer'])) {
                        return response()->json([
                            'success' => false,
                            'message' => "Response at index {$index} must have 'question_id' and 'answer' fields",
                        ], 422);
                    }

                    // Verificar que la pregunta existe y pertenece a la encuesta
                    $questionExists = Questions::where('id', $responseData['question_id'])
                        ->where('survey_id', $surveyId)
                        ->exists();

                    if (!$questionExists) {
                        return response()->json([
                            'success' => false,
                            'message' => "Question with ID {$responseData['question_id']} does not exist or does not belong to survey {$surveyId}",
                        ], 422);
                    }

                    // Convertir arrays a JSON si es necesario
                    $processedAnswer = is_array($responseData['answer']) ? json_encode($responseData['answer']) : $responseData['answer'];

                    // Crear la respuesta
                    $response = Response::create([
                        'user_id' => $userId,
                        'question_id' => $responseData['question_id'],
                        'answer' => $processedAnswer,
                    ]);

                    $createdResponses[] = $response;
                }

                return response()->json([
                    'success' => true,
                    'data' => $createdResponses,
                    'count' => count($createdResponses),
                    'survey_id' => $surveyId,
                    'message' => 'Survey responses created successfully',
                ], 201);
            } elseif ($this->isSurveyResponseFormat($data)) {
                // Formato anterior: {"survey_id": "1", "answers": {"1": "Mid", "2": "ddasddad", ...}}
                if (!isset($data['survey_id']) || !isset($data['answers'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Survey response format must have 'survey_id' and 'answers' fields",
                    ], 422);
                }

                $surveyId = $data['survey_id'];
                $answers = $data['answers'];

                // Verificar que la encuesta existe
                $surveyExists = Surveys::where('id', $surveyId)->exists();
                if (!$surveyExists) {
                    return response()->json([
                        'success' => false,
                        'message' => "Survey with ID {$surveyId} does not exist",
                    ], 422);
                }

                // Procesar cada respuesta
                foreach ($answers as $questionId => $answer) {
                    // Verificar que la pregunta existe y pertenece a la encuesta
                    $questionExists = Questions::where('id', $questionId)
                        ->where('survey_id', $surveyId)
                        ->exists();

                    if (!$questionExists) {
                        return response()->json([
                            'success' => false,
                            'message' => "Question with ID {$questionId} does not exist or does not belong to survey {$surveyId}",
                        ], 422);
                    }

                    // Convertir arrays a JSON si es necesario
                    $processedAnswer = is_array($answer) ? json_encode($answer) : $answer;

                    // Crear la respuesta
                    $response = Response::create([
                        'user_id' => $userId,
                        'question_id' => $questionId,
                        'answer' => $processedAnswer,
                    ]);

                    $createdResponses[] = $response;
                }

                return response()->json([
                    'success' => true,
                    'data' => $createdResponses,
                    'count' => count($createdResponses),
                    'survey_id' => $surveyId,
                    'message' => 'Survey responses created successfully',
                ], 201);
            } elseif ($this->isArrayOfResponses($data)) {
                // Formato: [{"question_id": 1, "answer": "Senior"}, ...]
                foreach ($data as $index => $responseData) {
                    if (!isset($responseData['question_id']) || !isset($responseData['answer'])) {
                        return response()->json([
                            'success' => false,
                            'message' => "Response at index {$index} must have 'question_id' and 'answer' fields",
                        ], 422);
                    }

                    $questionExists = Questions::where('id', $responseData['question_id'])->exists();
                    if (!$questionExists) {
                        return response()->json([
                            'success' => false,
                            'message' => "Question with ID {$responseData['question_id']} does not exist",
                        ], 422);
                    }

                    $processedAnswer = is_array($responseData['answer']) ? json_encode($responseData['answer']) : $responseData['answer'];

                    $response = Response::create([
                        'user_id' => $userId,
                        'question_id' => $responseData['question_id'],
                        'answer' => $processedAnswer,
                    ]);

                    $createdResponses[] = $response;
                }

                return response()->json([
                    'success' => true,
                    'data' => $createdResponses,
                    'count' => count($createdResponses),
                    'message' => 'Multiple responses created successfully',
                ], 201);
            } else {
                if (!isset($data['question_id']) || !isset($data['answer'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Response must have 'question_id' and 'answer' fields",
                    ], 422);
                }

                $questionExists = Questions::where('id', $data['question_id'])->exists();
                if (!$questionExists) {
                    return response()->json([
                        'success' => false,
                        'message' => "Question with ID {$data['question_id']} does not exist",
                    ], 422);
                }

                $processedAnswer = is_array($data['answer']) ? json_encode($data['answer']) : $data['answer'];

                $response = Response::create([
                    'user_id' => $userId,
                    'question_id' => $data['question_id'],
                    'answer' => $processedAnswer,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $response,
                    'message' => 'Response created successfully',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if the data is in new survey response format with responses array
     *
     * @param array $data
     * @return bool
     */
    private function isSurveyWithResponsesFormat(array $data): bool
    {
        return isset($data['survey_id']) && isset($data['responses']) && is_array($data['responses']);
    }

    /**
     * Check if the data is in old survey response format with answers object
     *
     * @param array $data
     * @return bool
     */
    private function isSurveyResponseFormat(array $data): bool
    {
        return isset($data['survey_id']) && isset($data['answers']) && is_array($data['answers']);
    }

    /**
     * Check if the data is an array of response objects
     *
     * @param array $data
     * @return bool
     */
    private function isArrayOfResponses(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (isset($data['survey_id']) && (isset($data['responses']) || isset($data['answers']))) {
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
     * Update an existing response.
     *
     * @param string|int $id
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, array $data)
    {
        try {
            $response = Response::findOrFail($id);

            // Validar que la pregunta existe si se está actualizando
            if (isset($data['question_id'])) {
                $questionExists = Questions::where('id', $data['question_id'])->exists();
                if (!$questionExists) {
                    return response()->json([
                        'success' => false,
                        'message' => "Question with ID {$data['question_id']} does not exist",
                    ], 422);
                }
            }

            if (isset($data['answer']) && is_array($data['answer'])) {
                $data['answer'] = json_encode($data['answer']);
            }

            $response->update($data);

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Response updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a response.
     *
     * @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $response = Response::findOrFail($id);
            $response->delete();
            return response()->json([
                'success' => true,
                'message' => 'Response deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Debug method to check authentication and response data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugAuth()
    {
        try {
            $user = auth()->user();
            $isAuthenticated = auth()->check();
            $userId = auth()->id();

            $allResponses = Response::all();
            $userResponses = $isAuthenticated ? Response::where('user_id', $userId)->get() : collect();

            return response()->json([
                'success' => true,
                'debug_info' => [
                    'is_authenticated' => $isAuthenticated,
                    'user_id' => $userId,
                    'user' => $user,
                    'total_responses_in_db' => $allResponses->count(),
                    'user_responses_count' => $userResponses->count(),
                    'user_responses' => $userResponses,
                    'all_responses' => $allResponses
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
