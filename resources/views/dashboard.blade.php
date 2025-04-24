<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">🎓 EduLearn AI Assistant</h2>

                    <!-- Personalized Learning Path -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2">📚 Personalized Learning Path</h4>
                        <textarea id="progress" class="w-full border rounded p-2" rows="2" placeholder="e.g., struggling with algebra"></textarea>
                        <button onclick="getLearningPath()" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Generate Path</button>
                        <div id="learning-path-result" class="mt-2 text-green-600"></div>
                    </div>

                    <!-- Real-Time Feedback -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2">📝 Real-Time Feedback</h4>
                        <textarea id="student-answer" class="w-full border rounded p-2" rows="2" placeholder="Paste your answer here..."></textarea>
                        <button onclick="getFeedback()" class="mt-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Get Feedback</button>
                        <div id="feedback-result" class="mt-2 text-green-600"></div>
                    </div>

                    <!-- AI tutor -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-2">🤖 Ask Tutor</h4>
                        <input type="text" id="question" class="w-full border rounded p-2" placeholder="e.g., What is Newton's First Law?">
                        <button onclick="askTutor()" class="mt-2 bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded">Ask</button>
                        <div id="tutor-response" class="mt-2 text-green-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function getLearningPath() {
            let progress = $('#progress').val();
            if (!progress) {
                $('#learning-path-result').html('<p class="text-red-600">Please enter a topic.</p>');
                return;
            }
            $('#learning-path-result').html('<p>Loading...</p>');
            $.post('/learning-path', {
                progress: progress,
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.learning_path.startsWith('Error') || response.learning_path.includes('unexpected response')) {
                    $('#learning-path-result').html('<p class="text-red-600">' + response.learning_path + '</p>');
                } else {
                    // Format as a list
                    let formatted = response.learning_path.replace(/\n/g, '<br>').replace(/(\d+\.\s*\*\*[^:]+:\*\*)/g, '<li><strong>$1</strong>');
                    $('#learning-path-result').html('<ul>' + formatted + '</ul>');
                }
            }).fail(function() {
                $('#learning-path-result').html('<p class="text-red-600">Failed to connect to AI service.</p>');
            });
        }

        function getFeedback() {
            let answer = $('#student-answer').val();
            if (!answer) {
                $('#feedback-result').html('<p class="text-red-600">Please enter an answer.</p>');
                return;
            }
            $('#feedback-result').html('<p>Loading...</p>');
            $.post('/feedback', {
                answer: answer,
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.feedback.startsWith('Error') || response.feedback.includes('unexpected response')) {
                    $('#feedback-result').html('<p class="text-red-600">' + response.feedback + '</p>');
                } else {
                    $('#feedback-result').html(response.feedback.replace(/\n/g, '<br>'));
                }
            }).fail(function() {
                $('#feedback-result').html('<p class="text-red-600">Failed to connect to AI service.</p>');
            });
        }

        function askTutor() {
            let question = $('#question').val();
            if (!question) {
                $('#tutor-response').html('<p class="text-red-600">Please enter a question.</p>');
                return;
            }
            $('#tutor-response').html('<p>Loading...</p>');
            $.post('/tutor', {
                question: question,
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.tutor_response.startsWith('Error') || response.tutor_response.includes('unexpected response')) {
                    $('#tutor-response').html('<p class="text-red-600">' + response.tutor_response + '</p>');
                } else {
                    $('#tutor-response').html(response.tutor_response.replace(/\n/g, '<br>'));
                }
            }).fail(function() {
                $('#tutor-response').html('<p class="text-red-600">Failed to connect to AI service.</p>');
            });
        }
    </script>
</x-app-layout>