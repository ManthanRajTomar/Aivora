<?php
$apiKey = 'AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg';
$model = 'gemini-3.1-flash-lite';
$payload = json_encode([
    'contents' => [['parts' => [['text' => 'Solve 3x + 15 = 45']]]]
]);
$t0 = microtime(true);
$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
echo "Code: $code in " . round(microtime(true) - $t0, 2) . "s | Err: $err\n";
$data = json_decode($res, true);
echo ($data['candidates'][0]['content']['parts'][0]['text'] ?? $res) . "\n";
