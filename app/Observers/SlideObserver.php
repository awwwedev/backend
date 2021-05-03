<?php

namespace App\Observers;

use App\Models\Slide;
use App\Traits\ModelsImageController;

class SlideObserver
{
    use ModelsImageController;

    /**
     * Handle the Slide "created" event.
     *
     * @param Slide $slide
     * @return void
     */
    public function created(Slide $slide)
    {
        //
    }

    /**
     * Handle the Slide "updated" event.
     *
     * @param Slide $slide
     * @return void
     */
    public function updated(Slide $slide)
    {
        $this->removeImgOnUpdateEvent($slide, 'image');
    }

    /**
     * Handle the Slide "deleted" event.
     *
     * @param Slide $slide
     * @return void
     */
    public function deleted(Slide $slide)
    {
        $this->removeImgOnDeleteEvent($slide, 'image');
    }

    /**
     * Handle the Slide "restored" event.
     *
     * @param Slide $slide
     * @return void
     */
    public function restored(Slide $slide)
    {
        //
    }

    /**
     * Handle the Slide "force deleted" event.
     *
     * @param Slide $slide
     * @return void
     */
    public function forceDeleted(Slide $slide)
    {
        //
    }
}
