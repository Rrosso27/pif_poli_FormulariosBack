<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use App\Http\Requests\ResponseRequest;

class ResponseController extends Controller
{
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /**
     * Get all responses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->responseService->getAll();
    }

    /**
     * Get response by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->responseService->getById($id);
    }

    /**
     * Get responses by authenticated user.
     *
     * @param int $survey_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByAuthUser($survey_id)
    {
        return $this->responseService->getByAuthUser($survey_id);
    }

    /**
     * Debug method to check authentication status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugAuth()
    {
        return $this->responseService->debugAuth();
    }



    /**
     * Store responses (single or multiple, various formats).
     *
     * @param ResponseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ResponseRequest $request)
    {
        try {
            $data = $request->validated();
            return $this->responseService->store($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified response.
     *
     * @param ResponseRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ResponseRequest $request, $id)
    {
        try {
            return $this->responseService->update($id, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified response.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            return $this->responseService->delete($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting response: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Debug method to check authenticated user and responses.
     * Remove this method in production.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function debugAuthUser()
    // {
    //     return $this->responseService->debugAuthUser();
    // }
}
