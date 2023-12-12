<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];

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
}
