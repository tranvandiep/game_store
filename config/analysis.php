<?php
/**
 * Analysis API - Crawl & Phân tích Video YouTube theo chuẩn crawler_video_skill.md
 * 
 * Method: GET
 * Parameters:
 *   - url (bắt buộc): Đường dẫn Kênh YouTube hoặc Playlist hoặc Video
 *       Ví dụ Kênh:     analysis.php?url=https://www.youtube.com/@ChuChuTV/
 *       Ví dụ Playlist: analysis.php?url=https://www.youtube.com/playlist?list=PLOLeQiLqmt9s
 *   - format (tùy chọn): 'raw' (mặc định - trả về mảng video [ { video_id, ... } ] chuẩn all.min.json)
 *                        'details' (trả về đối tượng đầy đủ { status, type, title, thumbnail, total, ages, videos })
 *   - details (tùy chọn): '1' tương đương format=details
 *   - max_playlists (tùy chọn): Số playlist tối đa cần quét khi cào Kênh (mặc định: 25)
 */

// Thiết lập Headers & CORS
if (!headers_sent()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    if (!headers_sent()) {
        http_response_code(204);
    }
    exit;
}

// Bỏ giới hạn thời gian thực thi cho tác vụ cào dữ liệu
set_time_limit(180);
ini_set('memory_limit', '256M');

$url = trim($_GET['url'] ?? '');
if (empty($url)) {
    if (!headers_sent()) {
        http_response_code(400);
    }
    echo json_encode([
        'status' => 'error',
        'message' => "Thiếu tham số 'url'. Ví dụ: analysis.php?url=https://www.youtube.com/@ChuChuTV/ hoặc analysis.php?url=https://www.youtube.com/playlist?list=PLOLeQiLqmt9s"
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

// Tùy chọn định dạng trả về
$format = strtolower(trim($_GET['format'] ?? ''));
$isDetails = ($format === 'details' || isset($_GET['details']) || isset($_GET['info']) || $format === 'object');
$maxPlaylists = isset($_GET['max_playlists']) ? max(1, min(100, (int)$_GET['max_playlists'])) : 25;

/**
 * Gửi HTTP GET request bằng cURL giả lập trình duyệt
 */
function fetchHttp(string $targetUrl): string {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $targetUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7'
        ]
    ]);
    $content = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception("cURL Error: " . $error);
    }
    return $content ?: '';
}

/**
 * Trích xuất biến ytInitialData từ HTML trang YouTube
 */
function extractYtInitialData(string $html): ?array {
    if (preg_match('/var ytInitialData\s*=\s*({.+?});<\/script>/s', $html, $matches) ||
        preg_match('/ytInitialData\s*=\s*({.+?});/s', $html, $matches)) {
        $decoded = json_decode($matches[1], true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    return null;
}

/**
 * Phân loại danh mục môn học / nhóm kỹ năng chuẩn (loại bỏ tên riêng kênh)
 * Ưu tiên tiêu đề video trước, sau đó đối soát tiêu đề playlist
 */
function determineCategory(string $videoTitle, string $playlistTitle = ''): string {
    $vLower = mb_strtolower($videoTitle, 'UTF-8');

    // 1. Kiểm tra tiêu đề video trước
    if (preg_match('/abc|alphabet|phonics|english|rhyme|nursery|tiếng anh|reading|spelling/i', $vLower)) {
        return 'Tiếng Anh';
    }
    if (preg_match('/math|count|phép cộng|toán|đếm số/i', $vLower) || preg_match('/\b(123|number|numbers|shapes)\b/i', $vLower)) {
        return 'Toán';
    }
    if (preg_match('/draw|color|art|paint|craft|tô màu|vẽ tranh|thủ công|hội họa|diy/i', $vLower)) {
        return 'Vẽ';
    }
    if (preg_match('/science|dinosaur|planet|space|animal|nature|body|khoa học|động vật|vũ trụ|tàu hỏa|xe|máy kéo|máy bay|xe tăng|excavator|truck|tractor|khủng long/i', $vLower)) {
        return 'Khoa Học';
    }
    if (preg_match('/habit|manner|safety|brush|wash|clean|kỹ năng|thói quen|an toàn|bác sĩ|giáo dục mầm non/i', $vLower)) {
        return 'Kỹ Năng Sống';
    }
    if (preg_match('/lullaby|music|melody|sleep|bedtime|nhạc|ru ngủ|âm nhạc|con vịt|con heo|chú ếch|chú thỏ|ca nhạc|hát|bài hát|remix/i', $vLower)) {
        return 'Âm Nhạc';
    }
    if (preg_match('/history|lịch sử|danh nhân|cổ tích/i', $vLower)) {
        return 'Lịch Sử';
    }
    if (preg_match('/hoạt hình|cartoon|animation|story|chuyện/i', $vLower)) {
        return 'Giải Trí';
    }

    // 2. Dự phòng đối soát theo tiêu đề playlist
    if (!empty($playlistTitle)) {
        $pLower = mb_strtolower($playlistTitle, 'UTF-8');
        if (preg_match('/abc|alphabet|phonics|english|rhyme|nursery|tiếng anh/i', $pLower)) return 'Tiếng Anh';
        if (preg_match('/math|count|phép cộng|toán|đếm số/i', $pLower) || preg_match('/\b(123|number|shapes)\b/i', $pLower)) return 'Toán';
        if (preg_match('/draw|color|art|paint|craft|tô màu|vẽ tranh|thủ công|hội họa|diy/i', $pLower)) return 'Vẽ';
        if (preg_match('/science|dinosaur|planet|space|animal|nature|body|khoa học|động vật|vũ trụ|tàu hỏa|xe|máy kéo|máy bay|xe tăng/i', $pLower)) return 'Khoa Học';
        if (preg_match('/habit|manner|safety|brush|wash|clean|kỹ năng|thói quen|an toàn|bác sĩ/i', $pLower)) return 'Kỹ Năng Sống';
        if (preg_match('/lullaby|music|melody|sleep|bedtime|nhạc|ru ngủ|âm nhạc|con vịt|con heo|chú ếch|chú thỏ|ca nhạc|hát|bài hát/i', $pLower)) return 'Âm Nhạc';
        if (preg_match('/history|lịch sử|danh nhân|cổ tích/i', $pLower)) return 'Lịch Sử';
    }

    return 'Giải Trí';
}

/**
 * Gán mảng độ tuổi phù hợp dựa trên nội dung & danh mục
 */
function determineAges(string $category, string $text): array {
    $lower = mb_strtolower($text, 'UTF-8');
    if (preg_match('/nursery|baby|toddler|lullaby|con vịt|con heo|chú thỏ|chú chim non|ăn ngon/i', $lower)) {
        return [1, 2, 3, 4, 5, 6];
    }
    if ($category === 'Toán' || $category === 'Khoa Học' || $category === 'Lịch Sử') {
        return [4, 5, 6, 7, 8, 9, 10];
    }
    if ($category === 'Vẽ' || $category === 'Kỹ Năng Sống') {
        return [3, 4, 5, 6, 7, 8];
    }
    if (preg_match('/barbie|mario|paw patrol|búp bê|đồ chơi/i', $lower)) {
        return [3, 4, 5, 6, 7, 8, 9, 10];
    }
    return [1, 2, 3, 4, 5, 6];
}

/**
 * Bóc tách video từ dữ liệu trang Playlist của YouTube
 */
function parseVideosFromPlaylistData(array $ytData, string $playlistTitle, array &$videoMap, array &$allAgesSet): int {
    $added = 0;
    $sContents = $ytData['contents']['twoColumnBrowseResultsRenderer']['tabs'][0]['tabRenderer']['content']['sectionListRenderer']['contents'] ?? [];
    $itemSection = $sContents[0]['itemSectionRenderer']['contents'] ?? [];

    foreach ($itemSection as $item) {
        $vId = null;
        $vTitle = null;

        if (isset($item['lockupViewModel']['contentId'])) {
            $vId = $item['lockupViewModel']['contentId'];
            $vTitle = $item['lockupViewModel']['metadata']['lockupMetadataViewModel']['title']['content'] ?? '';
        } elseif (isset($item['playlistVideoRenderer']['videoId'])) {
            $vId = $item['playlistVideoRenderer']['videoId'];
            $vTitle = $item['playlistVideoRenderer']['title']['runs'][0]['text'] ?? '';
        }

        if ($vId && $vTitle && !isset($videoMap[$vId])) {
            $cat = determineCategory($vTitle, $playlistTitle);
            $ages = determineAges($cat, $playlistTitle . ' ' . $vTitle);
            foreach ($ages as $a) {
                $allAgesSet[$a] = true;
            }

            $videoMap[$vId] = [
                'video_id' => $vId,
                'thumbnail' => "https://i.ytimg.com/vi/{$vId}/hqdefault.jpg",
                'title' => $vTitle,
                'category' => $cat,
                'ages' => $ages
            ];
            $added++;
        }
    }
    return $added;
}

try {
    $cleanUrl = preg_replace('/^youtube:/i', '', $url);
    $cleanUrl = rtrim($cleanUrl, '/');

    // 1. Nhận diện loại URL
    $isPlaylistUrl = preg_match('/[?&]list=([a-zA-Z0-9_-]+)/', $cleanUrl, $plMatch);
    $isSingleVideoUrl = preg_match('/(?:watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $cleanUrl, $videoMatch);

    $videoMap = [];
    $allAgesSet = [];
    $title = '';
    $thumbnail = '';

    if ($isSingleVideoUrl) {
        // ----------------------------------------------------
        // TRƯỜNG HỢP: VIDEO ĐƠN LẺ
        // ----------------------------------------------------
        $videoId = $videoMatch[1];
        $videoUrl = "https://www.youtube.com/watch?v={$videoId}";
        $html = fetchHttp($videoUrl);
        $ytData = extractYtInitialData($html);

        $vTitle = $ytData['microformat']['playerMicroformatRenderer']['title']['simpleText']
            ?? $ytData['contents']['twoColumnWatchNextResults']['results']['results']['contents'][0]['videoPrimaryInfoRenderer']['title']['runs'][0]['text']
            ?? "YouTube Video {$videoId}";

        $title = $vTitle;
        $thumbnail = "https://i.ytimg.com/vi/{$videoId}/hqdefault.jpg";
        $cat = determineCategory($vTitle);
        $ages = determineAges($cat, $vTitle);
        foreach ($ages as $a) {
            $allAgesSet[$a] = true;
        }

        $videoMap[$videoId] = [
            'video_id' => $videoId,
            'thumbnail' => $thumbnail,
            'title' => $vTitle,
            'category' => $cat,
            'ages' => $ages
        ];

    } elseif ($isPlaylistUrl) {
        // ----------------------------------------------------
        // TRƯỜNG HỢP: PLAYLIST
        // ----------------------------------------------------
        $playlistId = $plMatch[1];
        $playlistUrl = "https://www.youtube.com/playlist?list={$playlistId}";
        $html = fetchHttp($playlistUrl);
        $ytData = extractYtInitialData($html);

        if (!$ytData) {
            throw new Exception("Không thể phân tích dữ liệu từ playlist: {$playlistId}");
        }

        $title = $ytData['metadata']['playlistMetadataRenderer']['title']
            ?? $ytData['header']['playlistHeaderRenderer']['title']['runs'][0]['text']
            ?? 'YouTube Playlist';

        $thumbnail = $ytData['microformat']['microformatDataRenderer']['thumbnail']['thumbnails'][0]['url']
            ?? '';

        parseVideosFromPlaylistData($ytData, $title, $videoMap, $allAgesSet);

    } else {
        // ----------------------------------------------------
        // TRƯỜNG HỢP: KÊNH YOUTUBE (CHANNEL)
        // ----------------------------------------------------
        $playlistTabUrl = str_ends_with($cleanUrl, '/playlists') ? $cleanUrl : "{$cleanUrl}/playlists";
        $html = fetchHttp($playlistTabUrl);
        $ytData = extractYtInitialData($html);

        if (!$ytData) {
            throw new Exception("Không thể bóc tách dữ liệu từ kênh: {$cleanUrl}");
        }

        $title = $ytData['metadata']['channelMetadataRenderer']['title'] ?? 'YouTube Channel';
        $avatarList = $ytData['metadata']['channelMetadataRenderer']['avatar']['thumbnails'] ?? [];
        $thumbnail = !empty($avatarList) ? end($avatarList)['url'] : '';

        // Lấy danh sách playlist từ Tab Danh sách phát
        $tabs = $ytData['contents']['twoColumnBrowseResultsRenderer']['tabs'] ?? [];
        $plTab = null;
        foreach ($tabs as $t) {
            $tabRenderer = $t['tabRenderer'] ?? null;
            if ($tabRenderer && (!empty($tabRenderer['selected']) || preg_match('/playlist|danh sách phát/i', $tabRenderer['title'] ?? ''))) {
                $plTab = $tabRenderer;
                break;
            }
        }

        $sectionList = $plTab['content']['sectionListRenderer']['contents'] ?? [];
        $gridRenderer = $sectionList[0]['itemSectionRenderer']['contents'][0]['gridRenderer'] ?? null;
        $items = $gridRenderer['items'] ?? [];

        $playlists = [];
        foreach ($items as $it) {
            $lockup = $it['lockupViewModel'] ?? null;
            if ($lockup) {
                $plId = $lockup['contentId'] ?? null;
                $plTitle = $lockup['metadata']['lockupMetadataViewModel']['title']['content'] ?? '';
                if ($plId && $plTitle && !preg_match('/short/i', $plTitle)) {
                    $playlists[] = [
                        'id' => preg_replace('/^VL/', '', $plId),
                        'title' => $plTitle
                    ];
                }
            }
        }

        // Giới hạn số lượng playlist quét để tránh timeout
        $playlists = array_slice($playlists, 0, $maxPlaylists);

        // Duyệt qua từng playlist để cào video
        foreach ($playlists as $pl) {
            try {
                $plPageUrl = "https://www.youtube.com/playlist?list={$pl['id']}";
                $plHtml = fetchHttp($plPageUrl);
                $plData = extractYtInitialData($plHtml);
                if ($plData) {
                    parseVideosFromPlaylistData($plData, $pl['title'], $videoMap, $allAgesSet);
                }
                // Nghỉ nhẹ 50ms giữa các playlist
                usleep(50000);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    $videos = array_values($videoMap);
    $sortedAges = array_keys($allAgesSet);
    sort($sortedAges, SORT_NUMERIC);
    if (empty($sortedAges)) {
        $sortedAges = [1, 2, 3, 4, 5, 6];
    }

    if (empty($thumbnail) && !empty($videos)) {
        $thumbnail = $videos[0]['thumbnail'];
    }

    // Xuất kết quả
    if ($isDetails) {
        $type = 'channel';
        if ($isSingleVideoUrl) $type = 'video';
        elseif ($isPlaylistUrl) $type = 'playlist';

        echo json_encode([
            'status' => 'success',
            'type' => $type,
            'title' => $title,
            'thumbnail' => $thumbnail,
            'total' => count($videos),
            'ages' => $sortedAges,
            'videos' => $videos
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    } else {
        // Trả về mảng video thuần túy theo đúng khung all.min.json
        echo json_encode($videos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

} catch (Exception $e) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
