<?php
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=AQ.Ab8RN6ITZARszts4RstVKKULEAVsfw5wcTdURhHXL7uZvlQSjg";
$body = json_encode(["contents"=>[["parts"=>[["text"=>"Say hi in one sentence"]]]]]);

// Try with stream context
$context = stream_context_create([
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json\r\n",
        "content" => $body,
        "timeout" => 15,
        "ignore_errors" => true,
    ],
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ]
]);
$response = @file_get_contents($url, false, $context);
$error = error_get_last();
echo json_encode(["response"=>$response, "error"=>$error, "headers"=>$http_response_header??[]]);

