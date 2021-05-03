<?php

namespace App\Observers;

use App\Models\Realty;
use App\Traits\ModelsImageController;

class RealtyObserver
{
    use ModelsImageController;

    /**
     * Handle the Realty "created" event.
     *
     * @param Realty $realty
     * @return void
     */
    public function created(Realty $realty)
    {
        //
    }

    /**
     * Handle the Realty "updated" event.
     *
     * @param Realty $realty
     * @return void
     */
    public function updated(Realty $realty)
    {
        $this->removeImgOnUpdateEvent($realty, 'img_path');
        $this->removeImgMultipleOnUpdateEvent($realty, 'photo');
    }

    /**
     * Handle the Realty "deleted" event.
     *
     * @param Realty $realty
     * @return void
     */
    public function deleted(Realty $realty)
    {
        $this->removeImgOnDeleteEvent($realty, 'img_path');
        $this->removeImgMultipleOnDeleteEvent($realty, 'photo');
    }

    /**
     * Handle the Realty "restored" event.
     *
     * @param Realty $realty
     * @return void
     */
    public function restored(Realty $realty)
    {
        //
    }

    /**
     * Handle the Realty "force deleted" event.
     *
     * @param Realty $realty
     * @return void
     */
    public function forceDeleted(Realty $realty)
    {
        //
    }
}
