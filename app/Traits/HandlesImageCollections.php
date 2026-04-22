<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait HandlesImageCollections
{
    /**
     * Store temporary image paths before saving
     * Format: ['collection_name' => path|array of paths]
     */
    protected array $imagePaths = [];

    /**
     * Store original image paths for edit operations
     * Format: ['collection_name' => path|array of paths]
     */
    protected array $originalImagePaths = [];

    /**
     * Remove image fields from form data and store them temporarily
     *
     * @param  array  $imageFields  Array of field names that contain images
     */
    protected function extractImageFields(array $data, array $imageFields): array
    {
        foreach ($imageFields as $field) {
            if (isset($data[$field])) {
                $this->imagePaths[$field] = $data[$field];
                unset($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Load existing images from database for edit operations
     *
     * @param  array  $imageFields  Array of field names that contain images
     * @param  bool  $singleImage  Set to true if the field should return a single image (e.g., logo)
     */
    protected function loadExistingImages(Model $record, array $imageFields, bool $singleImage = false): array
    {
        $data = [];

        foreach ($imageFields as $field) {
            if ($singleImage) {
                // For single images (like logo)
                $image = Image::where('imageable_type', get_class($record))
                    ->where('imageable_id', $record->id)
                    ->where('collection', $field)
                    ->first();

                if ($image) {
                    $data[$field] = $image->path;
                    $this->originalImagePaths[$field] = $image->path;
                }
            } else {
                // For multiple images (like gallery, menu)
                $images = Image::where('imageable_type', get_class($record))
                    ->where('imageable_id', $record->id)
                    ->where('collection', $field)
                    ->orderBy('sort_order')
                    ->get();

                if ($images->isNotEmpty()) {
                    $data[$field] = $images->pluck('path')->toArray();
                    $this->originalImagePaths[$field] = $data[$field];
                }
            }
        }

        return $data;
    }

    /**
     * Save image collections for a record
     * Works for both create and edit operations
     *
     * @param  array  $imageFields  Array of field names that contain images
     * @param  bool  $singleImage  Set to true if the field should handle a single image (e.g., logo)
     */
    protected function saveImageCollections(Model $record, array $imageFields, bool $singleImage = false): void
    {
        foreach ($imageFields as $field) {
            if (! isset($this->imagePaths[$field])) {
                continue;
            }

            $newPaths = $this->imagePaths[$field];

            // Convert single image to array for consistent handling
            if ($singleImage) {
                $newPaths = $newPaths ? [$newPaths] : [];
            } else {
                $newPaths = is_array($newPaths) ? $newPaths : [];
            }

            // Check if record exists (for edit operations)
            $isEdit = $record->exists && $record->id;

            if ($isEdit) {
                // For edit: handle deletions, additions, and reordering
                $this->updateImageCollection($record, $field, $newPaths);
            } else {
                // For create: just add all images
                $this->createImageCollection($record, $field, $newPaths);
            }
        }
    }

    /**
     * Create image collection (for new records)
     */
    protected function createImageCollection(Model $record, string $collection, array $paths): void
    {
        foreach ($paths as $index => $path) {
            if (empty($path)) {
                continue;
            }

            Image::create([
                'imageable_type' => get_class($record),
                'imageable_id' => $record->id,
                'path' => $path,
                'disk' => 'public',
                'collection' => $collection,
                'sort_order' => $index,
            ]);
        }
    }

    /**
     * Update image collection (for existing records)
     * Handles deletions, additions, and reordering
     */
    protected function updateImageCollection(Model $record, string $collection, array $newPaths): void
    {
        // Get existing images from database
        $existingImages = Image::where('imageable_type', get_class($record))
            ->where('imageable_id', $record->id)
            ->where('collection', $collection)
            ->get();

        $existingPaths = $existingImages->pluck('path')->toArray();
        $newPaths = $newPaths ?? [];

        // Find images to delete (removed from collection)
        $pathsToDelete = array_diff($existingPaths, $newPaths);

        // Delete removed images
        foreach ($pathsToDelete as $pathToDelete) {
            $imageToDelete = $existingImages->where('path', $pathToDelete)->first();

            if ($imageToDelete) {
                // Delete file from storage
                if (Storage::disk($imageToDelete->disk)->exists($imageToDelete->path)) {
                    Storage::disk($imageToDelete->disk)->delete($imageToDelete->path);
                }

                // Delete from database
                $imageToDelete->delete();
            }
        }

        // Find new images to add
        $pathsToAdd = array_diff($newPaths, $existingPaths);

        // Add new images
        foreach ($pathsToAdd as $pathToAdd) {
            if (empty($pathToAdd)) {
                continue;
            }

            $sortOrder = array_search($pathToAdd, $newPaths);

            Image::create([
                'imageable_type' => get_class($record),
                'imageable_id' => $record->id,
                'path' => $pathToAdd,
                'disk' => 'public',
                'collection' => $collection,
                'sort_order' => $sortOrder,
            ]);
        }

        // Update sort order for existing images (in case they were reordered)
        foreach ($newPaths as $index => $path) {
            if (empty($path)) {
                continue;
            }

            $existingImage = $existingImages->where('path', $path)->first();

            if ($existingImage && $existingImage->sort_order != $index) {
                $existingImage->update(['sort_order' => $index]);
            }
        }
    }

    /**
     * Check if an image collection has changed
     * Useful for optimizing updates (only update if changed)
     */
    protected function hasImageCollectionChanged(string $field): bool
    {
        if (! isset($this->imagePaths[$field]) || ! isset($this->originalImagePaths[$field])) {
            return isset($this->imagePaths[$field]);
        }

        $newPaths = $this->imagePaths[$field];
        $originalPaths = $this->originalImagePaths[$field];

        // Convert to arrays for comparison
        if (! is_array($newPaths)) {
            $newPaths = [$newPaths];
        }
        if (! is_array($originalPaths)) {
            $originalPaths = [$originalPaths];
        }

        return $newPaths !== $originalPaths;
    }
}
