<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultivationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'activity',
        'fertilizer_used',
        'pesticide_used',
        'image_url',
        'video_url',
        'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    /**
     * Mutator để trích xuất và lưu chỉ video ID từ URL YouTube
     */
    public function setVideoUrlAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['video_url'] = null;
            return;
        }

        // Trích xuất video ID từ các dạng URL YouTube
        $videoId = $this->extractYouTubeVideoId($value);
        $this->attributes['video_url'] = $videoId;
    }

    /**
     * Trích xuất video ID từ URL YouTube
     */
    protected function extractYouTubeVideoId($url)
    {
        // Các pattern để trích xuất video ID
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',           // https://youtube.com/watch?v=abc123
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',                      // https://youtu.be/abc123
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',           // https://youtube.com/shorts/abc123
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1]; // Trả về video ID (11 ký tự)
            }
        }

        return null; // Trả về null nếu không trích xuất được
    }
}