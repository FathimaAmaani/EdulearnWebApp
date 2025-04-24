<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIService;

class FeedbackController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function getFeedback(Request $request)
    {
        $userAnswer = $request->input('answer');
        $aiResponse = $this->aiService->provideFeedback($userAnswer);
        return response()->json(['feedback' => $aiResponse]);
    }
}

