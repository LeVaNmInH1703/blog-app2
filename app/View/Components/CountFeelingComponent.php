<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CountFeelingComponent extends Component
{
    public $obj;
    public $size, $feelings, $firstFeeling = null, $secondFeeling = null, $thirdFeeling = null;
    public function __construct($obj,$size=18)
    {
        $this->obj = $obj;
        $this->size = $size;
        $this->feelings = $obj->feelings;
        if ($obj->countFeeling >= 1)
            $this->firstFeeling = $obj->feelings->get($obj->countFeeling - 1);
        if ($obj->countFeeling >= 2)
            $this->secondFeeling = $obj->feelings->get($obj->countFeeling - 2);
        if ($obj->countFeeling >= 3)
            $this->thirdFeeling = $obj->feelings->get($obj->countFeeling - 3);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.count-feeling-component',[
            'obj' => $this->obj,
            'size' => $this->size,
            'feelings' => $this->feelings,
            'firstFeeling' => $this->firstFeeling,
            'secondFeeling' => $this->secondFeeling,
            'thirdFeeling' => $this->thirdFeeling,
        ]);
    }
}
