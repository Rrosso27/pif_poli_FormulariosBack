<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionRequest;
use App\Services\QuestionsService;

class QuestionsController extends Controller
{
    protected $questionService;

    public function __construct(QuestionsService $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * Display a listing of questions.
     */
    public function index()
    {
        try {
            return $this->questionService->get();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching questions: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created question.
     */
    public function store(QuestionRequest $request)
    {
        try {
            return $this->questionService->store($request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified question.
     */
    public function show($id)
    {
        try {
            return $this->questionService->getById($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified question.
     */
    public function update(QuestionRequest $request, $id)
    {
        try {
            return $this->questionService->update($id, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified question.
     */
    public function destroy($id)
    {
        try {
            return $this->questionService->delete($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting question: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * getAllBySurveyId.
     */
    public function getAllBySurveyId($surveyId)
    {
        try {
            return $this->questionService->getAllBySurveyId($surveyId);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching questions for survey: ' . $e->getMessage(),
                ], 500);
        }
    }
}
