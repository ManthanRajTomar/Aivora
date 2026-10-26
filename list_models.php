<?php
$apiKey = 'AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg';
$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey;
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_TIMEOUT => 15
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Code: $code\n";
$data = json_decode($res, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        if (in_array('generateContent', $m['supportedGenerationMethods'] ?? [])) {
            echo "- " . $m['name'] . " (" . ($m['displayName'] ?? '') . ")\n";
        }
    }
} else {
    echo "Response: " . substr($res, 0, 500) . "\n";
}
