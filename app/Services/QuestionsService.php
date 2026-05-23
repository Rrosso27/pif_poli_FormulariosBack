<?php

namespace App\Services;

use App\Models\Questions;

class QuestionsService
{
    /**
     * get all questions
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        try {
            return response()->json([
                'success' => true,
                'data' =>  Questions::all(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching questions: ' . $e->getMessage(),
            ], 500);
        }
    }


    /** get by id question
     *  @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getById($id)
    {
        try {
            $question = Questions::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $question,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new question
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(array $data)
    {
        try {
            $question = Questions::create($data);
            return response()->json([
                'success' => true,
                'data' => $question,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating question: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, array $data)
    {
        try {
            $question = Questions::findOrFail($id);
            $question->update($data);
            return response()->json([
                'success' => true,
                'data' => $question,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a question
     * @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $question = Questions::findOrFail($id);
            $question->delete();
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all questions  of  surveys_id
     * @param int $perPage
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBySurveyId(int $surveyId, int $perPage = 10)
    {
        try {
            $questions = Questions::where('survey_id', $surveyId)->paginate($perPage);
            return response()->json([
                'success' => true,
                'data' => $questions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching questions: ' . $e->getMessage(),
            ], 500);
        }
    }
}
