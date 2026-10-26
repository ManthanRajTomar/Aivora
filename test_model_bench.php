<?php
$apiKey = 'AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg';
$testModels = [
    'gemini-2.5-flash-lite',
    'gemini-2.5-flash',
    'gemini-flash-lite-latest',
    'gemini-flash-latest',
    'gemini-3.1-flash-lite',
    'gemini-3.7-flash',
    'gemini-3.5-flash-lite',
    'gemini-3.5-flash'
];

$msg = 'Write a Python function to check if a string is a palindrome';
$payload = json_encode([
    'contents' => [['parts' => [['text' => $msg]]]],
    'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 300]
]);

foreach ($testModels as $model) {
    $t0 = microtime(true);
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $dur = round((microtime(true) - $t0), 2);
    $err = curl_error($ch);
    echo "Model: $model | Code: $code | Time: {$dur}s";
    if ($code === 200) {
        $data = json_decode($res, true);
        $snippet = substr($data['candidates'][0]['content']['parts'][0]['text'] ?? '', 0, 70);
        $snippet = str_replace(["\r", "\n"], ' ', $snippet);
        echo " | SUCCESS: $snippet\n";
    } else {
        echo " | ERR: " . ($err ?: substr($res, 0, 70)) . "\n";
    }
}
