<?php
// Test Gemini API - HAPUS FILE INI SETELAH TESTING
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = file(__DIR__ . '/../.env');
$apiKey = '';
foreach ($dotenv as $line) {
    if (str_starts_with(trim($line), 'GEMINI_API_KEY')) {
        $apiKey = trim(explode('=', $line, 2)[1] ?? '');
        $apiKey = trim($apiKey, " '\"");
        break;
    }
}

if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
    die('<b style="color:red">GEMINI_API_KEY belum diisi di .env</b>');
}

// Test embed
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key={$apiKey}";
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'model' => 'models/gemini-embedding-001',
        'content' => ['parts' => [['text' => 'test koneksi']]],
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 15,
]);
$result = json_decode(curl_exec($ch), true);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h3>Test Gemini Embedding</h3>";
if (isset($result['embedding']['values'])) {
    $dim = count($result['embedding']['values']);
    echo "<p style='color:green'>✅ Berhasil! Dimensi vector: <b>{$dim}</b></p>";
} else {
    echo "<p style='color:red'>❌ Gagal (HTTP {$httpCode})</p>";
    echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
}

// Test chat
$url2 = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
$ch2 = curl_init($url2);
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'contents' => [['role'=>'user','parts'=>[['text'=>'Jawab singkat: 1+1=?']]]],
        'generationConfig' => ['maxOutputTokens' => 50],
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 15,
]);
$result2 = json_decode(curl_exec($ch2), true);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "<h3>Test Gemini Chat</h3>";
if (isset($result2['candidates'][0]['content']['parts'][0]['text'])) {
    $ans = $result2['candidates'][0]['content']['parts'][0]['text'];
    echo "<p style='color:green'>✅ Berhasil! Jawaban: <b>" . htmlspecialchars($ans) . "</b></p>";
} else {
    echo "<p style='color:red'>❌ Gagal (HTTP {$httpCode2})</p>";
    echo "<pre>" . json_encode($result2, JSON_PRETTY_PRINT) . "</pre>";
}
