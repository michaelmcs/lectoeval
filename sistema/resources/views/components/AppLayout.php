<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function render(): View
    {
        // Asegúrate de que esté apuntando a la vista correcta
        return view('components.app-layout');
    }
}
