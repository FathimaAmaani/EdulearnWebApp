<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIService;

class LearningPathController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function generatePath(Request $request)
    {
        $userInput = $request->input('progress');
        $aiResponse = $this->aiService->generateLearningPath($userInput);
        return response()->json(['learning_path' => $aiResponse]);
    }
}

