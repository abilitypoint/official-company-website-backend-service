<?php header('Access-Control-Allow-Origin: https://official-company-website.vercel.app');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$name = trim((string) ($data['name'] ?? ''));
$phone = trim((string) ($data['phone'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$payload = [
    'chat_id' => -5492262707,
    'text' => "<pre><b>Incoming Request</b>\nName: {$name}\nPhone Number: {$phone}</pre>",
    'parse_mode' => 'HTML',
];

$url = 'https://api.telegram.org/bot8731078318:AAHOXSj00vZXW-nImaxRzfnNLWPU7g4yVMU/sendMessage';
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode($payload),
        'ignore_errors' => true,
    ],
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Telegram request failed']);
    exit;
}

$body = json_decode($response, true);
if (!($body['ok'] ?? false)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $body['description'] ?? 'Telegram rejected the request']);
    exit;
}

http_response_code(200);
echo json_encode(['ok' => true]);
?>