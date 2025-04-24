<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api-inference.huggingface.co/models/';

    public function __construct()
    {
        $this->apiKey = env('HUGGINGFACE_API_KEY');
    }

    public function generateLearningPath($topic)
    {
        $prompt = <<<EOD
You are an expert educational consultant. Create a detailed, step-by-step learning path for a student struggling with the topic: "$topic". The learning path should include:
- 3-5 specific, actionable steps to master the topic.
- For each step, provide a brief description and one relevant resource (e.g., a video, article, or practice site).
- Format the response as a clear, numbered list.
Example:
1. **Understand Variables**: Learn what variables are in Python. Watch the video "Python Variables Explained" on FreeCodeCamp.
2. **Practice Variable Assignment**: Complete exercises on variables at Codecademy's Python course.
3. **Apply Variables in Projects**: Build a simple calculator using variables with guidance from Real Python's tutorial.
EOD;

        return $this->makeRequest('mistralai/Mixtral-8x7B-Instruct-v0.1', [
            'inputs' => $prompt,
            'wait_for_model' => true,
            'parameters' => ['max_new_tokens' => 500]
        ]);
    }

    public function provideFeedback($answer)
    {
        $prompt = <<<EOD
You are an experienced educator providing constructive feedback on a student's answer. Review the following answer: "$answer". Provide feedback that:
- Identifies one strength in the answer.
- Highlights one area for improvement.
- Suggests a specific action to improve.
- Keep the tone encouraging and professional.
Example:
**Feedback**:
- **Strength**: Your explanation of the Pythagorean theorem is clear and includes a relevant example.
- **Improvement**: You didn't explain why the theorem only applies to right triangles.
- **Action**: Review the geometric principles of right triangles in Khan Academy's geometry section and include this in your explanation.
EOD;

        return $this->makeRequest('mistralai/Mixtral-8x7B-Instruct-v0.1', [
            'inputs' => $prompt,
            'wait_for_model' => true,
            'parameters' => ['max_new_tokens' => 300]
        ]);
    }

    public function getTutorResponse($question)
    {
        $prompt = <<<EOD
You are an AI tutor with expertise in all academic subjects. A student has asked: "$question". Provide a clear, accurate, and concise answer suitable for a high school or college student. Include:
- A direct answer to the question.
- A brief explanation or example to clarify.
- Keep the response educational and engaging.
Example:
**Question**: What is Newton's First Law?
**Answer**: Newton's First Law states that an object at rest stays at rest, and an object in motion stays in motion, unless acted upon by an external force.
**Explanation**: For example, a book on a table remains still unless you push it, and a rolling ball stops only when friction or another force acts on it.
EOD;

        return $this->makeRequest('mistralai/Mixtral-8x7B-Instruct-v0.1', [
            'inputs' => $prompt,
            'wait_for_model' => true,
            'parameters' => ['max_new_tokens' => 300]
        ]);
    }

    public function testConnection()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get('https://api-inference.huggingface.co/status');

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function makeRequest($model, $payload)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . $model, $payload);

            Log::info('Hugging Face API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $model
            ]);

            if ($response->failed()) {
                Log::error('Hugging Face API Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return "Error: Unable to get response. Please try again later.";
            }

            $result = $response->json();

            // Handle array response
            if (is_array($result) && isset($result[0])) {
                $text = $result[0]['generated_text'] ?? "No response generated.";
                return $this->cleanResponse($text);
            }

            // Handle direct text response
            if (isset($result['generated_text'])) {
                return $this->cleanResponse($result['generated_text']);
            }

            return "Sorry, unexpected response format.";

        } catch (\Exception $e) {
            Log::error('AI Service Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "An error occurred. Please try again.";
        }
    }

    protected function cleanResponse($text)
    {
        // Remove any prompt text that might be included in the response
        $cleaned = preg_replace("/^.*?(?:Example:.*?\n\n|\n\n)/s", "", $text);
        // Trim whitespace and ensure proper formatting
        return trim($cleaned);
    }
}