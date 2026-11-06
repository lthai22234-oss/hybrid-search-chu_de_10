<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
error_reporting(0);
ini_set('display_errors', 0);

require 'config.php';

$originalQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($originalQuery)) {
    echo json_encode([]);
    exit;
}

// ==========================================
// 1. HÀM CHUẨN HÓA TIẾNG VIỆT KHÔNG DẤU
// ==========================================
function removeVietnameseTones($str) {
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
    $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
    $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
    $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
    $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
    $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
    $str = preg_replace('/(Đ)/', 'D', $str);
    return strtolower(trim($str));
}

$normalizedQuery = removeVietnameseTones($originalQuery);

// LOGIC CỦA BẠN: LẤY "TỪ ĐẦU TIÊN" CỦA CÂU TÌM KIẾM (Vd: "ao chong ret" -> "ao")
$queryWords = explode(' ', $normalizedQuery);
$firstQueryWord = $queryWords[0];

// ==========================================
// 2. VECTOR HÓA BẰNG AI
// ==========================================
$queryVector = getEmbedding($originalQuery);
if (!is_array($queryVector) || count($queryVector) == 0) {
    echo json_encode([]);
    exit;
}

$stmtAll = $pdo->query("SELECT id, name, description, embedding FROM products");
$allProducts = $stmtAll->fetchAll();

$finalResults = [];

// ==========================================
// 3. LUẬT SUY LUẬN TỪ ĐẦU TIÊN (FIRST-WORD INFERENCING)
// ==========================================
foreach ($allProducts as $product) {
    $productVector = json_decode($product['embedding'], true);
    $sim = cosineSimilarity($queryVector, $productVector);
    
    // Lột sạch dấu của Tên sản phẩm
    $normalizedName = removeVietnameseTones($product['name']);
    
    // LẤY "TỪ ĐẦU TIÊN" CỦA TÊN SẢN PHẨM (Vd: "ao khoac..." -> "ao")
    $nameWords = explode(' ', $normalizedName);
    $firstProductWord = $nameWords[0];
    
    // SO SÁNH 2 TỪ ĐẦU TIÊN CÓ KHỚP NHAU KHÔNG? ("ao" == "ao")
    $isFirstWordMatch = ($firstQueryWord === $firstProductWord);
    
    // NẾU TỪ ĐẦU KHỚP NHAU: Mở cửa khoan hồng (điểm AI chỉ cần >= 0.45 là qua).
    // NẾU KHÔNG KHỚP TỪ ĐẦU: Kỷ luật thép (điểm AI phải > 0.70 để chặn mọi loại rác).
    if ( ($isFirstWordMatch && $sim >= 0.45) || $sim > 0.70 ) {
        $finalResults[] = [
            "id" => $product['id'],
            "name" => $product['name'],
            "description" => $product['description'],
            // Nếu khớp từ đầu, cộng luôn 1 điểm thưởng để nó bay lên vị trí Top 1
            "score" => $isFirstWordMatch ? ($sim + 1.0) : $sim 
        ];
    }
}

// ==========================================
// 4. XẾP HẠNG VÀ TRẢ KẾT QUẢ
// ==========================================
usort($finalResults, function($a, $b) { return $b['score'] <=> $a['score']; });
$finalResults = array_slice($finalResults, 0, 6);

echo json_encode($finalResults);
?>