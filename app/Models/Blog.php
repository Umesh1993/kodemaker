<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BlogLike;

class Blog extends Model
{
    protected $table = "blog";
    
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'blog_image',
        'page_description',
        'is_active',
        'is_delete',
        'created_date',
        'updated_date'
    ];

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    public function blocklike(){
        return $this->belongTo(BlogLike::class,'blog_id');
    }
}
