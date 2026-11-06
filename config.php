<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// API Key 
define('$API_KEY = "NHAP_API_KEY_CUA_BAN_VAO_DAY";);

$host = '127.0.0.1';
$db   = 'hybrid_search_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

function getEmbedding($text) {
    $apiKey = trim(GEMINI_API_KEY);
    
    // BƯỚC 1: TỰ ĐỘNG HỎI GOOGLE XEM TÀI KHOẢN ĐƯỢC PHÉP DÙNG MODEL NÀO
    $modelUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey;
    $ch = curl_init($modelUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $modelsResult = curl_exec($ch);
    curl_close($ch);
    
    $modelsData = json_decode($modelsResult, true);
    $embeddingModel = ''; 
    
    if (isset($modelsData['models'])) {
        foreach ($modelsData['models'] as $model) {
            // Lọc ra đúng model hỗ trợ tính năng embedContent (Nhúng Vector)
            if (isset($model['supportedGenerationMethods']) && in_array('embedContent', $model['supportedGenerationMethods'])) {
                $embeddingModel = $model['name']; 
                break;
            }
        }
    }

    if (empty($embeddingModel)) {
        die("Lỗi: Tài khoản Google của bạn hiện không hỗ trợ bất kỳ model tạo vector nào.");
    }

    // BƯỚC 2: CHẠY API VỚI ĐÚNG TÊN MODEL VỪA QUÉT ĐƯỢC (Đã sửa lỗi URL tại đây)
    $url = 'https://generativelanguage.googleapis.com/v1beta/' . $embeddingModel . ':embedContent?key=' . $apiKey;
    
    $data = [
        "model" => $embeddingModel,
        "content" => [
            "parts" => [ ["text" => $text] ]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    if ($result === false) {
        die('Lỗi cURL: ' . curl_error($ch));
    }
    curl_close($ch);
    
    $response = json_decode($result, true);
    
    if (isset($response['error'])) {
        die("Lỗi từ Google API: " . $response['error']['message']);
    }
    
    if (!isset($response['embedding']['values'])) {
        die("Lỗi không xác định: Phản hồi API không chứa dữ liệu Vector. " . $result);
    }
    
    return $response['embedding']['values'];
}

function cosineSimilarity($vecA, $vecB) {
    $dotProduct = 0.0;
    $normA = 0.0;
    $normB = 0.0;
    $count = count($vecA);
    for ($i = 0; $i < $count; $i++) {
        if (!isset($vecB[$i])) break;
        $dotProduct += $vecA[$i] * $vecB[$i];
        $normA += $vecA[$i] * $vecA[$i];
        $normB += $vecB[$i] * $vecB[$i];
    }
    if ($normA == 0 || $normB == 0) return 0;
    return $dotProduct / (sqrt($normA) * sqrt($normB));
}
?>