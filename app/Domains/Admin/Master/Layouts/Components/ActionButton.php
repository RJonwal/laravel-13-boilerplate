<?php

namespace App\Domains\Admin\Master\Layouts\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Gate;

class ActionButton extends Component
{
    public string $route;
    public string $class;
    public bool $btnType;
    public string $btnUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(string $route, string $class = '', $btnType="", $btnUrl) {
        $this->route = $route;
        $this->class = $class;
        $this->btnType = $btnType;
        $this->btnUrl = $btnUrl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('Components::action-button');
    }
}