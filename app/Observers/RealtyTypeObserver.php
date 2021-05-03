<?php

namespace App\Observers;

use App\Models\RealtyType;
use App\Traits\ModelsImageController;

class RealtyTypeObserver
{
    use ModelsImageController;

    /**
     * Handle the RealtyType "created" event.
     *
     * @param RealtyType $realtyType
     * @return void
     */
    public function created(RealtyType $realtyType)
    {
        //
    }

    /**
     * Handle the RealtyType "updated" event.
     *
     * @param RealtyType $realtyType
     * @return void
     */
    public function updated(RealtyType $realtyType)
    {
        $this->removeImgOnUpdateEvent($realtyType, 'img_path');
    }

    /**
     * Handle the RealtyType "deleted" event.
     *
     * @param RealtyType $realtyType
     * @return void
     */
    public function deleted(RealtyType $realtyType)
    {
        $this->removeImgOnDeleteEvent($realtyType, 'img_path');
    }

    /**
     * Handle the RealtyType "restored" event.
     *
     * @param RealtyType $realtyType
     * @return void
     */
    public function restored(RealtyType $realtyType)
    {
        //
    }

    /**
     * Handle the RealtyType "force deleted" event.
     *
     * @param RealtyType $realtyType
     * @return void
     */
    public function forceDeleted(RealtyType $realtyType)
    {
        //
    }
}
