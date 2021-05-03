<?php


namespace App\Traits;


use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;


trait ModelsImageController
{
    /**
     * @param Model $model
     * @param string $imageAttributeName
     */
    protected function removeImgOnUpdateEvent (Model $model, string $imageAttributeName) {
        if ($model->isDirty($imageAttributeName)) {
            $this->removeImg($model->getOriginal($imageAttributeName));
        }
    }

    /**
     * @param Model $model
     * @param string $imageAttributeName
     */
    protected function removeImgOnDeleteEvent (Model $model, string $imageAttributeName) {
        $this->removeImg($model->getOriginal($imageAttributeName));
    }

    /**
     * @param Model $model
     * @param string $imageAttributeName
     */
    protected function removeImgMultipleOnDeleteEvent (Model $model, string $imageAttributeName) {
        $this->removeImgMultiple($model->$imageAttributeName);
    }

    /**
     * @param Model $model
     * @param string $imageAttributeName
     */
    protected function removeImgMultipleOnUpdateEvent (Model $model, string $imageAttributeName) {
        $prev = collect($model->getOriginal($imageAttributeName));
        $imgPathsForRemove = $prev->diff($model->$imageAttributeName);

        $this->removeImgMultiple($imgPathsForRemove->toArray());
    }

    /**
     * @param string $path
     */
    protected function removeImg (string $path) {
        $this->removeImgFromDisk($path, Storage::disk('public'));
    }

    /**
     * @param array $imagePaths
     */
    protected function removeImgMultiple(array $imagePaths)
    {
        $disk = Storage::disk('public');

        foreach ($imagePaths as $imagePath) {
            $this->removeImgFromDisk($imagePath, $disk);
        }
    }

    /**
     * @param string $path
     * @param Filesystem $disk
     */
    protected function removeImgFromDisk(string $path, Filesystem $disk)
    {
        $diskPath = str_replace('/storage', '', $path);

        $disk->delete($diskPath);
    }
}
