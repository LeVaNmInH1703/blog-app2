<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CountFeedbackComponent extends Component
{
    public $obj;
    public $size, $feedbacks, $firstFeedback = null, $secondFeedback = null, $thirdFeedback = null;
    public function __construct($obj,$size=18)
    {
        $this->obj = $obj;
        $this->size = $size;
        $this->feedbacks = $obj->feedbacks;
        if ($obj->countFeedback >= 1)
            $this->firstFeedback = $obj->feedbacks->get($obj->countFeedback - 1);
        if ($obj->countFeedback >= 2)
            $this->secondFeedback = $obj->feedbacks->get($obj->countFeedback - 2);
        if ($obj->countFeedback >= 3)
            $this->thirdFeedback = $obj->feedbacks->get($obj->countFeedback - 3);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.count-feedback-component',[
            'obj' => $this->obj,
            'size' => $this->size,
            'feedbacks' => $this->feedbacks,
            'firstFeedback' => $this->firstFeedback,
            'secondFeedback' => $this->secondFeedback,
            'thirdFeedback' => $this->thirdFeedback,
        ]);
    }
}
