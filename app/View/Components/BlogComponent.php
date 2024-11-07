<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;
use Stichoza\GoogleTranslate\GoogleTranslate;
class BlogComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public $blog,$feelings;
    public function __construct($blog,$feelings=null)
    {
       $this->blog=$blog;
       $this->feelings=$feelings;
       $tr = new GoogleTranslate(session('locale'));
       $this->blog->contentTranslated = $tr->translate($this->blog->content??'');
       $this->blog->getLastDetectedSource=$this->blog->content?$tr->getLastDetectedSource():session('locale');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.blog-component');
    }
}
