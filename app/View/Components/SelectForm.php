<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectForm extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $id;

    public function __construct($name, $label = '', $options = [], $selected = null, $id = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected;
        $this->id = $id ?? $name;
    }

    public function render()
    {
        return view('components.select-form');
    }
}
