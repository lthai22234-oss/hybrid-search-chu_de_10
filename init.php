<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

$pdo->exec("
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        description TEXT,
        embedding JSON,
        FULLTEXT(name, description)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
$pdo->exec("TRUNCATE TABLE products");

echo "<h3>Đang kéo 30+ sản phẩm từ API và nhúng AI (Sẽ mất khoảng 1-2 phút). Vui lòng không đóng trang...</h3>";
ob_flush(); flush();

$apiUrl = 'http://localhost/hybrid-search/api.php';
$apiData = file_get_contents($apiUrl);

if ($apiData === false) {
    die("Không thể tải dữ liệu từ API.");
}

$externalProducts = json_decode($apiData, true);
$stmt = $pdo->prepare("INSERT INTO products (name, description, embedding) VALUES (?, ?, ?)");
$count = 0;

foreach ($externalProducts as $item) {
    $name = $item['title']; 
    $description = $item['description'];
    $textToEmbed = $name . " " . $description;
    
    // Gọi AI nhúng Vector
    $vector = getEmbedding($textToEmbed);
    
    $stmt->execute([$name, $description, json_encode($vector)]);
    
    $count++;
    echo "✅ [$count] Đã nạp xong: <i>" . $name . "</i><br>";
    ob_flush(); flush();
    
    // BẮT BUỘC: Nghỉ nửa giây (500,000 micro-giây) để tránh bị Google khóa API miễn phí
    usleep(500000); 
}

echo "<br><h3 style='color:green;'>🎉 KHỞI TẠO SIÊU DỮ LIỆU THÀNH CÔNG!</h3>";
?>