<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GetFeedbackComponent extends Component
{
    public $feedback,$isShowName,$size,$isShowNull;
    public function __construct($feedback=null,$isShowName=false,$size=18,$isShowNull=true)
    {
        $this->feedback=$feedback;
        $this->isShowNull=$isShowNull;
        $this->isShowName=$isShowName;
        $this->size=$size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.get-feedback-component',[
            'feedback' => $this->feedback,
            'isShowName' => $this->isShowName,
            'size' => $this->size,
            'isShowNull' => $this->isShowNull,
        ]);
    }
}
