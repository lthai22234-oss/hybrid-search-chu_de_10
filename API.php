<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

$products = [
    // NHÓM CÔNG NGHỆ - ĐIỆN THOẠI & LAPTOP
    ["title" => "Điện thoại Samsung Galaxy S24 Ultra", "description" => "Màn hình phẳng 6.8 inch, camera 200MP chụp đêm xuất sắc, tích hợp AI thông minh."],
    ["title" => "Điện thoại iPhone 15 Pro Max 256GB", "description" => "Thiết kế vỏ titan siêu nhẹ, chip A17 Pro mạnh mẽ, camera zoom quang học 5x cực nét."],
    ["title" => "Điện thoại OPPO A93 12GB/512GB", "description" => "Smartphone bộ nhớ siêu khủng 512GB, RAM 12GB đa nhiệm mượt mà, camera AI làm đẹp tự nhiên."],
    ["title" => "Laptop Gaming ASUS TUF 15", "description" => "Cấu hình mạnh mẽ, màn hình tần số quét cao, tản nhiệt tốt, chiến mượt các tựa game nặng và làm đồ họa."],
    ["title" => "MacBook Air M2 8GB/256GB", "description" => "Laptop mỏng nhẹ của Apple, thiết kế sang trọng, pin siêu trâu thích hợp cho dân văn phòng."],
    
    // NHÓM PHỤ KIỆN & GAMING
    ["title" => "Tai nghe Bluetooth AirPods Pro 2", "description" => "Tai nghe không dây chống ồn chủ động ANC, âm thanh không gian 3 chiều, pin 30 giờ."],
    ["title" => "Tai nghe chụp tai Sony WH-1000XM5", "description" => "Tai nghe bluetooth chống ồn tốt nhất thế giới, chất âm hi-res đỉnh cao cho người yêu nhạc."],
    ["title" => "Nút bấm chơi game (Trigger) di động", "description" => "Hỗ trợ ngắm bắn và thao tác 4 ngón tay, ngàm kẹp siêu nhạy, thích hợp leo rank Free Fire và PUBG."],
    ["title" => "Tay cầm chơi game PC không dây", "description" => "Tay cầm form Xbox, analog xoay 360 độ siêu nhạy, tương thích tốt với các game đá bóng như FC Online."],
    ["title" => "Chuột không dây Logitech G304", "description" => "Chuột gaming công nghệ Lightspeed không độ trễ, mắt đọc HERO siêu nhạy."],
    ["title" => "Bàn phím cơ không dây Logitech", "description" => "Switch gõ siêu êm không ồn, đèn nền RGB tự động, kết nối 3 thiết bị cùng lúc."],
    
    // NHÓM NHIẾP ẢNH
    ["title" => "Máy ảnh không gương lật Canon EOS RP", "description" => "Máy ảnh Full-frame nhỏ gọn, lấy nét siêu tốc, hỗ trợ quay phim 4K và chụp ảnh chân dung cực đỉnh."],
    ["title" => "Ống kính Canon RF 50mm f/1.8 STM", "description" => "Lens fix tiêu cự vàng chuyên chụp ảnh chân dung, khẩu độ lớn f/1.8 thu sáng tốt, xóa phông mượt mà."],
    ["title" => "Chân máy ảnh (Tripod) hợp kim nhôm", "description" => "Chân đế vững chắc, có thể điều chỉnh độ cao linh hoạt, dùng được cho cả máy ảnh và điện thoại."],
    
    // NHÓM THỜI TRANG & PHỤ KIỆN XẾ NỔ
    ["title" => "Áo khoác gió dù nam chống nước", "description" => "Chất liệu vải dù cao cấp siêu nhẹ, chống nước cực tốt, giữ ấm mùa đông đi mưa lất phất."],
    ["title" => "Áo khoác nỉ hoodie form rộng", "description" => "Áo hoodie nam nữ vải nỉ bông dày dặn, kiểu dáng ulzzang Hàn Quốc, có mũ trùm đầu."],
    ["title" => "Quần jean nam dáng suông vintage", "description" => "Quần bò nam form ống rộng vừa phải, màu xanh nhạt phong cách retro dễ phối đồ."],
    ["title" => "Giày thể thao Sneaker trắng nam nữ", "description" => "Giày chạy bộ đế cao su êm ái, mặt lưới thoáng khí, trọng lượng siêu nhẹ."],
    ["title" => "Bao da bọc chìa khóa Smartkey Honda SH", "description" => "Bao da bò thật bảo vệ remote xe máy tay ga chống trầy xước, va đập, sang trọng."],
    ["title" => "Mũ bảo hiểm 3/4 kính âm chống lóa", "description" => "Nón bảo hiểm đi phượt có lớp lót kháng khuẩn, kính dấu bên trong chống nắng và bụi rát mặt."],
    
    // NHÓM ĐỒ GIA DỤNG & NHÀ BẾP
    ["title" => "Máy hút bụi cầm tay Dyson V12", "description" => "Lực hút lốc xoáy siêu mạnh, thiết kế không dây tiện lợi, làm sạch lông thú cưng và sofa."],
    ["title" => "Robot hút bụi lau nhà thông minh", "description" => "Tự động lập bản đồ nhà, lực hút 4000Pa, né vật cản tốt, điều khiển qua app điện thoại."],
    ["title" => "Nồi chiên không dầu điện tử 5L", "description" => "Công nghệ khí nóng giảm 90% mỡ thừa, nướng gà nguyên con chín đều vàng giòn."],
    ["title" => "Bếp nướng điện không khói", "description" => "Bếp nướng vân đá chống dính, công suất lớn, thích hợp làm tiệc nướng BBQ tại nhà."],
    
    // NHÓM THỰC PHẨM & LÀM ĐẸP
    ["title" => "Chai sốt ướp thịt nướng Hàn Quốc BBQ", "description" => "Gia vị chuẩn vị Hàn, dùng để ướp thịt ba chỉ, thịt bò nướng trên bếp than hoặc bếp điện siêu ngon."],
    ["title" => "Gel nha đam dưỡng ẩm phục hồi da", "description" => "Chiết xuất lô hội tự nhiên, làm dịu da cháy nắng, cấp ẩm tức thì cho mặt và toàn thân."],
    ["title" => "Sữa rửa mặt trị mụn trà xanh", "description" => "Làm sạch sâu lỗ chân lông, kiềm dầu, ngăn ngừa mụn cám và mụn đầu đen hiệu quả."],
    ["title" => "Nước hoa nam quyến rũ 100ml", "description" => "Hương thơm gỗ tuyết tùng nam tính, mạnh mẽ, lưu hương cực lâu trên cơ thể phong cách lịch lãm."],
    
    // NHÓM BALO, SÁCH & HỌC TẬP
    ["title" => "Balo laptop 15.6 inch chống nước", "description" => "Balo nhiều ngăn thông minh, đệm lưng chống gù, có khe cắm sạc dự phòng bên ngoài."],
    ["title" => "Sách Đắc Nhân Tâm - Dale Carnegie", "description" => "Cuốn sách kỹ năng sống, nghệ thuật giao tiếp và thu phục lòng người bán chạy nhất mọi thời đại."],
    ["title" => "Giáo trình Lập trình Hướng đối tượng C#", "description" => "Sách IT hướng dẫn cơ bản đến nâng cao về lập trình C#, cấu trúc dữ liệu, thuật toán."],
    ["title" => "Sách Clean Code (Mã Sạch)", "description" => "Bí kíp cho lập trình viên phần mềm, hướng dẫn viết code dễ đọc, dễ bảo trì và làm việc nhóm hiệu quả."]
];

echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>