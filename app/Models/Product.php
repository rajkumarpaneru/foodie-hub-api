<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100);
    }

    public function getImageUrlAttribute()
    {
        $url = $this->getFirstMediaUrl();
        return $url == "" ? null : $url;
    }

    public function getThumbnailUrlAttribute()
    {
        $mediaItems = $this->getMedia();
        if (count($mediaItems) > 0)
            return $mediaItems[0]->getUrl('thumb');
        else return null;
    }

    public function tags()
    {
        return $this->belongsToMany(FoodTag::class, 'product_tags', 'product_id', 'tag_id');
    }

}
