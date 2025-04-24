<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIService;

class TutorController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function askTutor(Request $request)
    {
        $question = $request->input('question');
        $aiResponse = $this->aiService->getTutorResponse($question);
        return response()->json(['tutor_response' => $aiResponse]);
    }
}

