<?php

namespace App\Services;

use App\Models\Surveys;

class SurveyService
{
    /**
     * Get all surveys
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => Surveys::all(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching surveys: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Get survey by ID
     * @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        try {
            $survey = Surveys::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $survey,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching survey: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Create a new survey
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(array $data)
    {
        try {
            $survey = Surveys::create($data);
            return response()->json([
                'success' => true,
                'data' => $survey,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating survey: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Update an existing survey
     * @param string|int $id
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, array $data)
    {
        try {
            $survey = Surveys::findOrFail($id);
            $survey->update($data);
            return response()->json([
                'success' => true,
                'data' => $survey,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating survey: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Delete a survey
     * @param string|int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            $survey = Surveys::findOrFail($id);
            $survey->delete();
            return response()->json([
                'success' => true,
                'message' => 'Survey deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting survey: ' . $e->getMessage(),
            ], 500);
        }
    }


}
