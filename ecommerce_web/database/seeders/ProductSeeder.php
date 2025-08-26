<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo thư mục products nếu chưa có
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }

        // Lấy category IDs
        $categories = Category::pluck('id', 'name');

        $products = [
            // Văn học Việt Nam
            [
                'name' => 'Tôi thấy hoa vàng trên cỏ xanh',
                'author' => 'Nguyễn Nhật Ánh',
                'description' => 'Cuốn tiểu thuyết hay nhất của Nguyễn Nhật Ánh kể về tuổi thơ với những kỷ niệm đẹ đẽ ở quê hương.',
                'price' => 89000,
                'category_id' => $categories['Văn học Việt Nam'],
                'stock' => 50,
                'image_url' => $this->createBookImage('Tôi thấy hoa vàng trên cỏ xanh', 'Nguyễn Nhật Ánh', [255, 215, 0])
            ],
            [
                'name' => 'Dế Mèn phiêu lưu ký',
                'author' => 'Tô Hoài',
                'description' => 'Tác phẩm kinh điển của văn học thiếu nhi Việt Nam về cuộc phiêu lưu của chú dế Mèn.',
                'price' => 65000,
                'category_id' => $categories['Thiếu nhi'],
                'stock' => 75,
                'image_url' => $this->createBookImage('Dế Mèn phiêu lưu ký', 'Tô Hoài', [34, 139, 34])
            ],
            [
                'name' => 'Truyện Kiều',
                'author' => 'Nguyễn Du',
                'description' => 'Tác phẩm bất hủ của đại thi hào Nguyễn Du, được coi là kiệt tác của văn học Việt Nam.',
                'price' => 120000,
                'category_id' => $categories['Văn học Việt Nam'],
                'stock' => 30,
                'image_url' => $this->createBookImage('Truyện Kiều', 'Nguyễn Du', [220, 20, 60])
            ],
            [
                'name' => 'Số đỏ',
                'author' => 'Vũ Trọng Phụng',
                'description' => 'Tiểu thuyết phê phán hiện thực sắc bén về xã hội Việt Nam đầu thế kỷ 20.',
                'price' => 95000,
                'category_id' => $categories['Văn học Việt Nam'],
                'stock' => 25,
                'image_url' => $this->createBookImage('Số đỏ', 'Vũ Trọng Phụng', [255, 0, 0])
            ],
            [
                'name' => 'Chí Phèo',
                'author' => 'Nam Cao',
                'description' => 'Truyện ngắn nổi tiếng về số phận bi thảm của người nông dân nghèo.',
                'price' => 45000,
                'category_id' => $categories['Văn học Việt Nam'],
                'stock' => 60,
                'image_url' => $this->createBookImage('Chí Phèo', 'Nam Cao', [139, 69, 19])
            ],

            // Văn học nước ngoài
            [
                'name' => 'Harry Potter và Hòn đá Phù thủy',
                'author' => 'J.K. Rowling',
                'description' => 'Cuốn sách đầu tiên trong series Harry Potter nổi tiếng thế giới.',
                'price' => 150000,
                'category_id' => $categories['Văn học nước ngoài'],
                'stock' => 80,
                'image_url' => $this->createBookImage('Harry Potter', 'J.K. Rowling', [128, 0, 128])
            ],
            [
                'name' => 'Không gia đình',
                'author' => 'Hector Malot',
                'description' => 'Tiểu thuyết cảm động về cậu bé Remi và cuộc hành trình tìm kiếm gia đình.',
                'price' => 78000,
                'category_id' => $categories['Văn học nước ngoài'],
                'stock' => 40,
                'image_url' => $this->createBookImage('Không gia đình', 'Hector Malot', [70, 130, 180])
            ],
            [
                'name' => '1984',
                'author' => 'George Orwell',
                'description' => 'Tiểu thuyết kinh điển về chủ nghĩa toàn trị và sự kiểm soát xã hội.',
                'price' => 110000,
                'category_id' => $categories['Văn học nước ngoài'],
                'stock' => 35,
                'image_url' => $this->createBookImage('1984', 'George Orwell', [47, 79, 79])
            ],
            [
                'name' => 'Sherlock Holmes Toàn tập',
                'author' => 'Arthur Conan Doyle',
                'description' => 'Tuyển tập đầy đủ các câu chuyện về thám tử nổi tiếng Sherlock Holmes.',
                'price' => 250000,
                'category_id' => $categories['Văn học nước ngoài'],
                'stock' => 20,
                'image_url' => $this->createBookImage('Sherlock Holmes', 'Arthur Conan Doyle', [25, 25, 112])
            ],

            // Tâm lý - Kỹ năng sống
            [
                'name' => 'Đắc nhân tâm',
                'author' => 'Dale Carnegie',
                'description' => 'Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử với mọi người.',
                'price' => 86000,
                'category_id' => $categories['Tâm lý - Kỹ năng sống'],
                'stock' => 100,
                'image_url' => $this->createBookImage('Đắc nhân tâm', 'Dale Carnegie', [255, 140, 0])
            ],
            [
                'name' => 'Atomic Habits',
                'author' => 'James Clear',
                'description' => 'Hướng dẫn xây dựng thói quen tốt và loại bỏ thói quen xấu một cách hiệu quả.',
                'price' => 125000,
                'category_id' => $categories['Tâm lý - Kỹ năng sống'],
                'stock' => 65,
                'image_url' => $this->createBookImage('Atomic Habits', 'James Clear', [0, 191, 255])
            ],
            [
                'name' => '7 Thói quen của người thành đạt',
                'author' => 'Stephen Covey',
                'description' => 'Bảy nguyên tắc cơ bản để đạt được thành công trong cuộc sống và sự nghiệp.',
                'price' => 105000,
                'category_id' => $categories['Tâm lý - Kỹ năng sống'],
                'stock' => 45,
                'image_url' => $this->createBookImage('7 Thói quen', 'Stephen Covey', [50, 205, 50])
            ],

            // Kinh tế 
            [
                'name' => 'Nghĩ giàu làm giàu',
                'author' => 'Napoleon Hill',
                'description' => 'Cuốn sách kinh điển về tư duy và phương pháp tạo dựng sự giàu có.',
                'price' => 89000,
                'category_id' => $categories['Kinh tế'],
                'stock' => 70,
                'image_url' => $this->createBookImage('Nghĩ giàu làm giàu', 'Napoleon Hill', [255, 215, 0])
            ],
            [
                'name' => 'Dạy con làm giàu',
                'author' => 'Robert Kiyosaki',
                'description' => 'Hướng dẫn cách giáo dục tài chính cho con trẻ từ sớm.',
                'price' => 95000,
                'category_id' => $categories['Kinh tế'],
                'stock' => 55,
                'image_url' => $this->createBookImage('Dạy con làm giàu', 'Robert Kiyosaki', [0, 100, 0])
            ],
            [
                'name' => 'The Lean Startup',
                'author' => 'Eric Ries',
                'description' => 'Phương pháp khởi nghiệp tinh gọn cho các doanh nghiệp mới.',
                'price' => 135000,
                'category_id' => $categories['Kinh tế'],
                'stock' => 30,
                'image_url' => $this->createBookImage('The Lean Startup', 'Eric Ries', [30, 144, 255])
            ],

            // Công nghệ thông tin
            [
                'name' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'description' => 'Hướng dẫn viết code sạch và dễ bảo trì cho các lập trình viên.',
                'price' => 165000,
                'category_id' => $categories['Công nghệ thông tin'],
                'stock' => 40,
                'image_url' => $this->createBookImage('Clean Code', 'Robert C. Martin', [0, 0, 0])
            ],
            [
                'name' => 'Design Patterns',
                'author' => 'Gang of Four',
                'description' => 'Các mẫu thiết kế phần mềm cơ bản mà mọi lập trình viên nên biết.',
                'price' => 185000,
                'category_id' => $categories['Công nghệ thông tin'],
                'stock' => 25,
                'image_url' => $this->createBookImage('Design Patterns', 'GoF', [105, 105, 105])
            ],
            [
                'name' => 'JavaScript: The Good Parts',
                'author' => 'Douglas Crockford',
                'description' => 'Hướng dẫn sử dụng JavaScript một cách hiệu quả và chuyên nghiệp.',
                'price' => 145000,
                'category_id' => $categories['Công nghệ thông tin'],
                'stock' => 35,
                'image_url' => $this->createBookImage('JavaScript', 'Douglas Crockford', [240, 248, 55])
            ],

            // Lịch sử
            [
                'name' => 'Sapiens: Lược sử loài người',
                'author' => 'Yuval Noah Harari',
                'description' => 'Cuộc hành trình từ động vật đến thần linh của loài người qua các thời kỳ lịch sử.',
                'price' => 155000,
                'category_id' => $categories['Lịch sử'],
                'stock' => 60,
                'image_url' => $this->createBookImage('Sapiens', 'Yuval Noah Harari', [160, 82, 45])
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('Created ' . count($products) . ' products with local book cover images');
    }

    /**
     * Tạo ảnh bìa sách local
     */
    private function createBookImage($title, $author, $bgColor)
    {
        $width = 300;
        $height = 400; // Tỷ lệ sách
        $image = imagecreate($width, $height);
        
        // Màu nền chính
        $bg = imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 128, 128, 128);
        $lightGray = imagecolorallocate($image, 200, 200, 200);
        
        // Tạo gradient effect
        for ($i = 0; $i < $width; $i++) {
            $shade = imagecolorallocate($image, 
                max(0, min(255, $bgColor[0] + ($i % 50) - 25)), 
                max(0, min(255, $bgColor[1] + ($i % 30) - 15)), 
                max(0, min(255, $bgColor[2] + ($i % 40) - 20))
            );
            imageline($image, $i, 0, $i, $height, $shade);
        }
        
        // Vẽ border sách
        imagerectangle($image, 0, 0, $width-1, $height-1, $black);
        imagerectangle($image, 5, 5, $width-6, $height-6, $white);
        
        // Vùng title (1/3 trên)
        imagefilledrectangle($image, 20, 30, $width-20, 140, $white);
        imagerectangle($image, 20, 30, $width-20, 140, $black);
        
        // Vẽ title (chia nhiều dòng nếu dài)
        $titleLines = $this->wrapText($title, 20); // Max 20 ký tự/dòng
        $lineHeight = 20;
        $startY = 60 - (count($titleLines) * $lineHeight / 2);
        
        foreach ($titleLines as $index => $line) {
            $textWidth = strlen($line) * 8; // Ước tính width
            $x = ($width - $textWidth) / 2;
            $y = $startY + ($index * $lineHeight);
            imagestring($image, 4, $x, $y, $line, $black);
        }
        
        // Vùng hình minh họa đơn giản (giữa)
        $centerY = 200;
        imagefilledrectangle($image, 50, 160, $width-50, 240, $lightGray);
        imagerectangle($image, 50, 160, $width-50, 240, $gray);
        
        // Vẽ icon sách đơn giản
        for ($i = 0; $i < 3; $i++) {
            $bookX = 80 + ($i * 40);
            imagefilledrectangle($image, $bookX, 180, $bookX + 25, 220, $white);
            imagerectangle($image, $bookX, 180, $bookX + 25, 220, $black);
            imageline($image, $bookX + 5, 180, $bookX + 5, 220, $gray);
        }
        
        // Author name (dưới)
        $authorLines = $this->wrapText($author, 25);
        $authorY = 280;
        foreach ($authorLines as $index => $line) {
            $textWidth = strlen($line) * 6;
            $x = ($width - $textWidth) / 2;
            $y = $authorY + ($index * 15);
            imagestring($image, 3, $x, $y, $line, $white);
        }
        
        // Decoration
        for ($i = 0; $i < 4; $i++) {
            $x = 30 + ($i * 60);
            imageellipse($image, $x, 350, 8, 8, $white);
        }
        
        // Brand/Publisher area
        imagestring($image, 2, $width/2 - 30, $height - 30, 'NHA SACH', $white);
        
        // Tạo filename từ title
        $filename = 'products/book_' . $this->slugify($title) . '_' . time() . '.png';
        $fullPath = storage_path('app/public/' . $filename);
        
        // Tạo thư mục nếu cần
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        // Lưu file
        imagepng($image, $fullPath);
        imagedestroy($image);
        
        return $filename;
    }

    /**
     * Chia text thành nhiều dòng
     */
    private function wrapText($text, $maxLength)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        
        foreach ($words as $word) {
            if (strlen($currentLine . ' ' . $word) <= $maxLength) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }
        
        return $lines;
    }

    /**
     * Tạo slug từ title
     */
    private function slugify($text)
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = preg_replace('/\s+/', '_', $text);
        return substr($text, 0, 30);
    }
}
