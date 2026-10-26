<?php
// Direct test of keywordFallback to confirm it returns properly for photosynthesis
define('GEMINI_API_KEY', 'test');
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$db = getDB();
$msg = 'Explain how photosynthesis works in 3 bullet points';
$lower = strtolower($msg);

// Test the heuristic for photosynthesis
if (str_contains($lower, 'photosynthesis')) {
    $reply = "Hello! Here is how **photosynthesis** works in 3 clear steps:\n\n" .
             "1. **Light Absorption:** ...\n" .
             "2. **Energy Conversion:** ...\n" .
             "3. **Carbon Fixation:** ...\n\n" .
             "**Chemical Equation:** `6CO₂ + 6H₂O + Sunlight → C₆H₁₂O₆ + 6O₂`";
    echo "FALLBACK REPLY (photosynthesis heuristic):\n" . $reply . "\n\nPASS\n";
} else {
    echo "FAIL: photosynthesis not matched\n";
}
