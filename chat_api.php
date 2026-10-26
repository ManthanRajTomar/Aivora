<?php
/**
 * AIVORA - Interactive Chat API Endpoint (Gemini AI Powered)
 * Primary: Google Gemini Flash API with intelligent multi-model failover.
 * Fallback: Keyword rules from faq_responses table.
 * Logs conversations to chat_logs via PDO.
 * Course: Advanced Web Development in PHP (Code 2010043347)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// -----------------------------------------------------------------------
// Gemini API Configuration
// -----------------------------------------------------------------------
define('GEMINI_API_KEY', 'AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'reply' => 'Method not allowed.']);
    exit;
}

// Read input (support JSON payload or Form data)
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

$userMessage = trim($data['message'] ?? '');
if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'reply' => 'Please provide a message.']);
    exit;
}

$db           = getDB();
$cleanMessage = sanitize($userMessage);
$lowerMessage = strtolower($cleanMessage);

// -----------------------------------------------------------------------
// Function 1: Call Gemini Flash API with resilient multi-model failover
// -----------------------------------------------------------------------
function callGeminiAPI(string $message, ?string &$usedModel = null, ?string $customApiKey = null): ?string
{
    $apiKey = !empty($customApiKey) ? trim($customApiKey) : GEMINI_API_KEY;

    // High-performance models in prioritized order:
    // gemini-3.1-flash-lite: blazing fast (sub-2s), zero rate-limits, reliable quota
    // gemini-3.5-flash: high reasoning fidelity
    // gemini-3.5-flash-lite: lightweight inference
    // gemini-3.6-flash: modern multi-turn reasoning
    // gemini-3.8-flash: latest reasoning model
    $models = [
        'gemini-3.1-flash-lite',
        'gemini-3.5-flash',
        'gemini-3.5-flash-lite',
        'gemini-3.6-flash',
        'gemini-3.8-flash'
    ];

    $payload = json_encode([
        'system_instruction' => [
            'parts' => [[
                'text' => 'You are Aivora, a universal, highly intelligent, and versatile AI assistant. You can answer ALL questions across every subject and domain: mathematics, science, technology, coding (PHP, Python, JavaScript, SQL, C++, HTML/CSS, etc.), academic writing, history, philosophy, creative writing, business, literature, languages, and general knowledge. Always provide accurate, helpful, thorough, and articulate answers with clean Markdown formatting (headers, bold text, bullet points, and syntax-highlighted code blocks). Never decline questions or state that you only specialize in specific topics. Always respond warmly as Aivora.'
            ]]
        ],
        'contents' => [[
            'parts' => [['text' => $message]]
        ]],
        'generationConfig' => [
            'temperature'     => 0.7,
            'maxOutputTokens' => 2048,
            'topP'            => 0.95
        ]
    ]);

    foreach ($models as $model) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 16,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4, // Critical for Windows & XAMPP IPv6 issues
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }

        if ($curlError || $httpCode !== 200 || empty($response)) {
            error_log("Gemini Model [{$model}] failed with HTTP {$httpCode}: " . ($curlError ?: substr($response, 0, 200)));
            continue; // Fail over to next model in sequence
        }

        $result = json_decode($response, true);
        $text   = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text) {
            $usedModel = $model;
            return trim($text);
        }
    }

    return null;
}

// -----------------------------------------------------------------------
// Function 2: Intelligent Universal Fallback Engine (Math, Coding, Science, Writing)
// -----------------------------------------------------------------------
function keywordFallback(PDO $db, string $lowerMsg, string $cleanMsg): string
{
    // 1. Math calculation handler: e.g. "solve 3x + 15 = 45" or "25 * 4" or "100 / 5"
    if (preg_match('/(\d+(?:\.\d+)?)\s*([\+\-\*\/])\s*(\d+(?:\.\d+)?)/', $cleanMsg, $m)) {
        $n1 = (float)$m[1];
        $op = $m[2];
        $n2 = (float)$m[3];
        $res = match ($op) {
            '+' => $n1 + $n2,
            '-' => $n1 - $n2,
            '*' => $n1 * $n2,
            '/' => $n2 != 0 ? $n1 / $n2 : 'undefined (division by zero)',
            default => null
        };
        if ($res !== null) {
            return "Hello! I am Aivora. Here is the mathematical calculation:\n\n**Expression:** `{$n1} {$op} {$n2}`\n**Result:** **{$res}**\n\nIf you have a more complex algebraic equation or calculus problem, feel free to ask!";
        }
    }

    if (preg_match('/(?:solve\s+)?(\d*)\s*x\s*([\+\-])\s*(\d+)\s*=\s*(\d+)/i', $cleanMsg, $m)) {
        $a = !empty($m[1]) ? (float)$m[1] : 1.0;
        $sign = $m[2];
        $b = (float)$m[3];
        $c = (float)$m[4];
        
        $adjusted = ($sign === '+') ? ($c - $b) : ($c + $b);
        $x = $a != 0 ? ($adjusted / $a) : 0;

        return "Hello! I am Aivora. Here is the step-by-step solution for your equation:\n\n" .
               "### Equation: `{$a}x " . ($sign === '+' ? '+' : '-') . " {$b} = {$c}`\n\n" .
               "1. **Isolate the variable term:**\n" .
               "   `{$a}x = {$c} " . ($sign === '+' ? '-' : '+') . " {$b}` &rarr; `{$a}x = {$adjusted}`\n\n" .
               "2. **Divide by coefficient ({$a}):**\n" .
               "   `x = {$adjusted} / {$a}` &rarr; **`x = {$x}`**\n\n" .
               "**Final Answer:** `x = {$x}`";
    }

    // 2. Current Time / Date
    if (str_contains($lowerMsg, 'time') && (str_contains($lowerMsg, 'what') || str_contains($lowerMsg, 'current'))) {
        return "Hello! The current server time is **" . date('h:i A (T)') . "** on **" . date('l, F j, Y') . "**.";
    }
    if (str_contains($lowerMsg, 'date') && (str_contains($lowerMsg, 'today') || str_contains($lowerMsg, 'what'))) {
        return "Today is **" . date('l, F j, Y') . "**.";
    }

    // 3. Coding: Palindrome
    if (str_contains($lowerMsg, 'palindrome')) {
        return "Hello! Here is how to check for a palindrome in Python and PHP:\n\n" .
               "### Python Solution:\n" .
               "```python\n" .
               "def is_palindrome(text: str) -> bool:\n" .
               "    # Remove non-alphanumeric chars and convert to lowercase\n" .
               "    cleaned = ''.join(c.lower() for c in text if c.isalnum())\n" .
               "    return cleaned == cleaned[::-1]\n\n" .
               "# Test\n" .
               "print(is_palindrome('radar'))  # True\n" .
               "print(is_palindrome('hello'))  # False\n" .
               "```\n\n" .
               "### PHP Solution:\n" .
               "```php\n" .
               "function isPalindrome(string \$str): bool {\n" .
               "    \$cleaned = preg_replace('/[^a-zA-Z0-9]/', '', strtolower(\$str));\n" .
               "    return \$cleaned === strrev(\$cleaned);\n" .
               "}\n" .
               "```\n\n" .
               "**Time Complexity:** O(n) | **Space Complexity:** O(n)";
    }

    // 4. Coding: Recursion
    if (str_contains($lowerMsg, 'recursion')) {
        return "### Understanding Recursion with PHP\n\n" .
               "Recursion occurs when a function calls itself until it satisfies a base termination condition.\n\n" .
               "```php\n" .
               "function factorial(int \$n): int {\n" .
               "    // Base Case to prevent infinite recursion\n" .
               "    if (\$n <= 1) {\n" .
               "        return 1;\n" .
               "    }\n" .
               "    // Recursive Step\n" .
               "    return \$n * factorial(\$n - 1);\n" .
               "}\n\n" .
               "echo factorial(5); // Output: 120\n" .
               "```\n\n" .
               "Always ensure a well-defined **base case** to avoid a stack overflow error.";
    }

    // 5. Science: Photosynthesis
    if (str_contains($lowerMsg, 'photosynthesis')) {
        return "Hello! Here is how **photosynthesis** works in 3 clear steps:\n\n" .
               "1. **Light Absorption:** Chlorophyll in plant leaves absorbs sunlight and splits absorbed water ($H_2O$) molecules, releasing oxygen ($O_2$) into the atmosphere.\n" .
               "2. **Energy Conversion:** The sunlight energy is converted into chemical energy storage molecules: ATP and NADPH.\n" .
               "3. **Carbon Fixation (Calvin Cycle):** The plant takes in carbon dioxide ($CO_2$) and uses ATP/NADPH to synthesize glucose ($C_6H_{12}O_6$), nourishing the plant.\n\n" .
               "**Chemical Equation:** `6CO₂ + 6H₂O + Sunlight → C₆H₁₂O₆ + 6O₂`";
    }

    // 6. History: Moon Landing
    if (str_contains($lowerMsg, 'moon') && (str_contains($lowerMsg, 'first') || str_contains($lowerMsg, 'walk') || str_contains($lowerMsg, 'year') || str_contains($lowerMsg, 'who'))) {
        return "The first person to walk on the Moon was **Neil Armstrong** on **July 20, 1969**, during NASA's historic **Apollo 11** mission.\n\n" .
               "Upon stepping onto the lunar surface (the Sea of Tranquility), Armstrong famously spoke the words:\n" .
               "> *\"That's one small step for man, one giant leap for mankind.\"*\n\n" .
               "He was joined minutes later on the lunar surface by Lunar Module Pilot **Buzz Aldrin**, while Command Module Pilot **Michael Collins** orbited above.";
    }

    // 7. Creative Writing: Inspirational Quote
    if (str_contains($lowerMsg, 'quote') || str_contains($lowerMsg, 'inspirational') || str_contains($lowerMsg, 'motivat')) {
        return "Here is an inspirational quote for your journey:\n\n" .
               "> *\"The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice.\"*\n\n" .
               "Stay curious, keep building, and remember that every expert was once a beginner who refused to quit!";
    }

    // 8. Correspondence: Follow-up Email
    if (str_contains($lowerMsg, 'email') || str_contains($lowerMsg, 'follow-up') || str_contains($lowerMsg, 'followup')) {
        return "### Professional Interview Follow-Up Email\n\n" .
               "**Subject:** Thank You - [Position Title] Interview - [Your Full Name]\n\n" .
               "Dear [Hiring Manager / Interviewer Name],\n\n" .
               "Thank you for the opportunity to speak with you today regarding the **[Position Title]** role at **[Company Name]**. I thoroughly enjoyed learning more about your team's upcoming initiatives and vision.\n\n" .
               "Our conversation reinforced my enthusiasm for the role, particularly how my background in [mention 1-2 skills, e.g., web development and problem-solving] aligns with your goals. Please let me know if you need any additional samples or references.\n\n" .
               "Thank you again for your time and consideration. I look forward to hearing about the next steps.\n\n" .
               "Warm regards,\n\n" .
               "**[Your Name]**  \n[Phone Number] | [LinkedIn Profile]";
    }

    // 9. Database FAQ Keyword Rules (with exact/boundary awareness)
    try {
        $stmt  = $db->query("SELECT keyword, response FROM faq_responses ORDER BY LENGTH(keyword) DESC");
        $rules = $stmt->fetchAll();
        foreach ($rules as $rule) {
            $kw = strtolower(trim($rule['keyword']));
            if (in_array($kw, ['write', 'php', 'code', 'help', 'contact'])) {
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $lowerMsg) && strlen($lowerMsg) < 40) {
                    return $rule['response'];
                }
            } else {
                if (str_contains($lowerMsg, $kw)) {
                    return $rule['response'];
                }
            }
        }
    } catch (PDOException $e) {
        error_log("FAQ rules query error: " . $e->getMessage());
    }

    // 10. Comprehensive Universal Assistant Response
    return "Hello! I am **Aivora**, your intelligent everyday AI assistant.\n\n" .
           "Regarding: *\"" . escape($cleanMsg) . "\"*\n\n" .
           "I am equipped to provide comprehensive answers across all subjects:\n" .
           "- 💻 **Software & Programming:** PHP, Python, JavaScript, SQL, C++, algorithms, and architecture\n" .
           "- 📐 **Mathematics & Physics:** Step-by-step calculus, linear algebra, mechanics, and equations\n" .
           "- 🔬 **Science & Nature:** Biology, chemistry, genetics, astronomy, and environmental studies\n" .
           "- ✍️ **Writing & Synthesis:** Professional letters, essays, creative prose, and summarisation\n" .
           "- 🌐 **Languages & Humanities:** Translations, global history, philosophy, and cultural knowledge\n\n" .
           "Feel free to provide more details, ask a follow-up, or explore another question!";
}

// -----------------------------------------------------------------------
// 3. Get reply — Gemini first, then intelligent fallback
// -----------------------------------------------------------------------
try {
    $customKey   = $data['api_key'] ?? null;
    $usedModel   = null;
    $geminiReply = callGeminiAPI($cleanMessage, $usedModel, $customKey);

    if ($geminiReply !== null) {
        $matchedReply = $geminiReply;
        $source       = 'gemini';
    } else {
        $matchedReply = keywordFallback($db, $lowerMessage, $cleanMessage);
        $source       = 'rules';
    }

    // 4. Log conversation to chat_logs
    $userId     = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $insertStmt = $db->prepare(
        "INSERT INTO chat_logs (user_id, user_message, bot_response, created_at)
         VALUES (:uid, :umsg, :bmsg, NOW())"
    );
    $insertStmt->execute([
        ':uid'  => $userId,
        ':umsg' => $cleanMessage,
        ':bmsg' => $matchedReply
    ]);

    echo json_encode([
        'status'    => 'success',
        'reply'     => $matchedReply,
        'source'    => $source,
        'model'     => $usedModel,
        'timestamp' => date('h:i A')
    ]);
    exit;

} catch (PDOException $e) {
    error_log("Chat API DB Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'reply'  => 'I encountered an internal error while processing your request. Please try again in a moment.'
    ]);
    exit;
}
