<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageItemComponent extends Component
{
    public $image,$morePhotos;
    public function __construct($image,$morePhotos=0)
    {
        $this->image=$image;
        $this->morePhotos=$morePhotos;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image-item-component',['image'=>$this->image,'morePhotos'=>$this->morePhotos]);
    }
}
