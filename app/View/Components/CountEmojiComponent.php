<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CountEmojiComponent extends Component
{
    public $obj;
    public $size, $emojis, $firstEmoji = null, $secondEmoji = null, $thirdEmoji = null;
    public function __construct($obj,$size=18)
    {
        $this->obj = $obj;
        $this->size = $size;
        $this->emojis = $obj->emojis;
        if ($obj->countEmoji >= 1)
            $this->firstEmoji = $obj->emojis->get($obj->countEmoji - 1);
        if ($obj->countEmoji >= 2)
            $this->secondEmoji = $obj->emojis->get($obj->countEmoji - 2);
        if ($obj->countEmoji >= 3)
            $this->thirdEmoji = $obj->emojis->get($obj->countEmoji - 3);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.count-emoji-component',[
            'obj' => $this->obj,
            'size' => $this->size,
            'emojis' => $this->emojis,
            'firstEmoji' => $this->firstEmoji,
            'secondEmoji' => $this->secondEmoji,
            'thirdEmoji' => $this->thirdEmoji,
        ]);
    }
}
