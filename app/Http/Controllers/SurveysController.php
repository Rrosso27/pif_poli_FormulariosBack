<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\http\Requests\SurveysRequest;
use App\Services\SurveyService;

class SurveysController extends Controller
{
    protected $surveyService;

    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    /**
     * Store a newly created survey in storage.
     *
     * @param SurveysRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SurveysRequest $request)
    {
        try {
            return $this->surveyService->store($request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating survey: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Display a listing of the surveys.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            return $this->surveyService->getAll();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching surveys: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Display the specified survey.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            return $this->surveyService->getById($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching survey: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified survey in storage.
     *
     * @param SurveysRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SurveysRequest $request, $id)
    {
        try {
            return $this->surveyService->update($id, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating survey: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified survey from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            return $this->surveyService->delete($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting survey: ' . $e->getMessage(),
            ], 500);
        }
    }
}
