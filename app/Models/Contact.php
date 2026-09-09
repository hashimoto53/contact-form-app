<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'category_id',
        'detail',
    ];

    // お問い合わせの種類（Category）とのリレーション
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // タグ（Tag）とのリレーション（多対多）
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}