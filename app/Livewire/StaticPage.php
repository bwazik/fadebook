<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\SettingsService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StaticPage extends Component
{
    public string $title = '';

    public string $content = '';

    public function mount(SettingsService $settingsService, string $page): void
    {
        if ($page === 'terms') {
            $this->title = 'شروط الاستخدام';
            $this->content = $settingsService->get('terms_content', '<p>شروط الاستخدام قيد التحديث...</p>');
        } elseif ($page === 'privacy') {
            $this->title = 'سياسة الخصوصية';
            $this->content = $settingsService->get('privacy_content', '<p>سياسة الخصوصية قيد التحديث...</p>');
        } else {
            abort(404);
        }

        $this->dispatch('hide-bottom-nav');
    }

    public function render()
    {
        return view('livewire.static-page');
    }
}
