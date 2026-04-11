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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
