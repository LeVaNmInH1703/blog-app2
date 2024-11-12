<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GetEmojiComponent extends Component
{
    public $emoji,$isShowName,$size,$isShowNull;
    public function __construct($emoji=null,$isShowName=false,$size=18,$isShowNull=true)
    {
        $this->emoji=$emoji;
        $this->isShowNull=$isShowNull;
        $this->isShowName=$isShowName;
        $this->size=$size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.get-emoji-component',[
            'emoji' => $this->emoji,
            'isShowName' => $this->isShowName,
            'size' => $this->size,
            'isShowNull' => $this->isShowNull,
        ]);
    }
}
