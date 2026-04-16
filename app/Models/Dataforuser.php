<?php

namespace App\Models;

// استيراد كلاس الـ Authenticatable بدل الـ Model العادي
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dataforuser extends Authenticatable // التغيير هنا: خليناه يرث من Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'dataforusers'; // تأكد إن اسم الجدول صح

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'age',
        'country',
        'city',
        'height',
        'weight',
        'skin_color',
        'status',
        'education',
        'job',
        'is_subscribed',
        'is_admin',
        'last_seen',
        'is_online',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Check if user is online (active in last 5 minutes)
    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }

    // Get formatted last seen text
    public function getLastSeenTextAttribute()
    {
        if (!$this->last_seen) {
            return 'لم نشط من قبل';
        }

        if ($this->isOnline()) {
            return 'متصل الآن';
        }

        $diff = $this->last_seen->diffForHumans(now());
        
        // Convert English time units to Arabic
        $arabicDiff = str_replace([
            'second', 'seconds', 'minute', 'minutes', 'hour', 'hours', 
            'day', 'days', 'week', 'weeks', 'month', 'months', 'year', 'years', ' ago', 'before'
        ], [
            'ثانية', 'ثواني', 'دقيقة', 'دقائق', 'ساعة', 'ساعات',
            'يوم', 'أيام', 'أسبوع', 'أسابيع', 'شهر', 'أشهر', 'سنة', 'سنوات', '', 'قبل'
        ], $diff);
        
        return 'منذ ' . $arabicDiff;
    }
}
