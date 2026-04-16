<?php

namespace App\Helpers;

class UserStatusHelper
{
    /**
     * Get user status based on last seen time
     */
    public static function getUserStatus($lastSeen)
    {
        if (!$lastSeen) {
            return [
                'status' => 'offline',
                'text' => 'offline',
                'arabic_text' => 'غير متصل',
                'last_seen_arabic' => 'آخر تواصل غير معروف'
            ];
        }

        $now = now();
        $lastSeenTime = \Carbon\Carbon::parse($lastSeen);
        $diffInMinutes = $now->diffInMinutes($lastSeenTime);

        if ($diffInMinutes < 5) {
            return [
                'status' => 'online',
                'text' => 'online',
                'arabic_text' => 'متصل',
                'last_seen_arabic' => 'متصل الآن'
            ];
        } elseif ($diffInMinutes < 1) {
            return [
                'status' => 'offline',
                'text' => 'offline less than a minute ago',
                'arabic_text' => 'غير متصل',
                'last_seen_arabic' => 'آخر تواصل: منذ أقل من دقيقة'
            ];
        } elseif ($diffInMinutes < 60) {
            $minutes = $diffInMinutes;
            $arabicMinutes = $minutes == 1 ? 'دقيقة' : ($minutes == 2 ? 'دقيقتان' : $minutes . ' دقائق');
            return [
                'status' => 'offline',
                'text' => 'offline ' . $minutes . ' minutes ago',
                'arabic_text' => 'غير متصل',
                'last_seen_arabic' => 'آخر تواصل: منذ ' . $arabicMinutes
            ];
        } elseif ($diffInMinutes < 1440) {
            $hours = floor($diffInMinutes / 60);
            $remainingMinutes = $diffInMinutes % 60;
            $arabicHours = $hours == 1 ? 'ساعة' : ($hours == 2 ? 'ساعتان' : $hours . ' ساعات');
            return [
                'status' => 'offline',
                'text' => 'offline ' . $hours . ' hours ago',
                'arabic_text' => 'غير متصل',
                'last_seen_arabic' => 'آخر تواصل: منذ ' . $arabicHours
            ];
        } else {
            $days = floor($diffInMinutes / 1440);
            $arabicDays = $days == 1 ? 'يوم' : ($days == 2 ? 'يومان' : $days . ' أيام');
            return [
                'status' => 'offline',
                'text' => 'offline ' . $days . ' days ago',
                'arabic_text' => 'غير متصل',
                'last_seen_arabic' => 'آخر تواصل: منذ ' . $arabicDays
            ];
        }
    }
}
