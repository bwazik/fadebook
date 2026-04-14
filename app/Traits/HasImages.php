<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasImages
{
    /**
     * Boot the trait and register deletion hooks.
     */
    public static function bootHasImages(): void
    {
        // Detect if the model uses SoftDeletes to hook into the correct event
        $event = method_exists(static::class, 'isForceDeleting') ? 'forceDeleting' : 'deleting';

        static::$event(function ($model) {
            $model->purgeAllImages();
        });
    }

    /**
     * Base relationship for all images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Efficiently wipe all images from disk and database.
     */
    public function purgeAllImages(): void
    {
        // We use each->delete() so that the Image model's "deleting" event is fired
        // which handles the disk cleanup automatically.
        $this->images()->get()->each->delete();
    }

    /**
     * Clear a specific collection from disk and database.
     */
    public function clearCollection(string $collection): void
    {
        $this->images()->where('collection', $collection)->get()->each->delete();
    }

    /**
     * Get a single image relationship by collection name.
     */
    public function getImage(string $collection): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')
            ->where('collection', $collection);
    }

    /**
     * Get multiple images relationship by collection name.
     */
    public function getImages(string $collection, ?string $orderBy = null, string $direction = 'asc'): MorphMany
    {
        $query = $this->morphMany(Image::class, 'imageable')
            ->where('collection', $collection);

        if ($orderBy !== null) {
            $query->orderBy($orderBy, $direction);
        }

        return $query;
    }
}
