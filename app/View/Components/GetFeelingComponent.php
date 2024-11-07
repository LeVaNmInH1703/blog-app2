<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GetFeelingComponent extends Component
{
    public $feeling,$isShowName,$size,$isShowNull;
    public function __construct($feeling=null,$isShowName=false,$size=18,$isShowNull=true)
    {
        $this->feeling=$feeling;
        $this->isShowNull=$isShowNull;
        $this->isShowName=$isShowName;
        $this->size=$size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.get-feeling-component',[
            'feeling' => $this->feeling,
            'isShowName' => $this->isShowName,
            'size' => $this->size,
            'isShowNull' => $this->isShowNull,
        ]);
    }
}
