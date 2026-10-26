<?php
$apiKey = 'AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg';
$models = ['gemma-4-31b-it', 'gemini-pro-latest', 'gemini-3.1-flash-lite'];

foreach ($models as $m) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key=" . $apiKey;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['contents'=>[['parts'=>[['text'=>'What is 3 + 4?']]]]]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15
    ]);
    $t0 = microtime(true);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $t = round(microtime(true) - $t0, 2);
    echo "$m -> HTTP $code in {$t}s\n";
    $json = json_decode($res, true);
    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        echo "Response: " . trim($json['candidates'][0]['content']['parts'][0]['text']) . "\n";
    } else {
        echo "Error: " . substr($res, 0, 150) . "\n";
    }
}
