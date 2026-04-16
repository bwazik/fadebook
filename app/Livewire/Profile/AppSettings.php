<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AppSettings extends Component
{
    public function mount(): void
    {
        $this->dispatch('hide-bottom-nav');
    }

    public function render(): View
    {
        return view('livewire.profile.app-settings');
    }
}
