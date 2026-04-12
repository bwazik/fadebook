<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Search extends Component
{
    #[Url]
    public string $query = '';

    public $results;

    public function search(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = collect([]);

            return;
        }

        $this->results = Shop::active()
            ->with(['area', 'images', 'barbers' => fn ($q) => $q->active()])
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->query.'%')
                    ->orWhereHas('area', function ($q) {
                        $q->where('name', 'like', '%'.$this->query.'%');
                    });
            })
            ->limit(20)
            ->get();
    }

    public function updatedQuery(): void
    {
        $this->search();
    }

    public function mount(): void
    {
        $this->results = collect();

        if ($this->query) {
            $this->search();
        }
    }

    public function render(): View
    {
        return view('livewire.search')->layout('components.layout.app');
    }
}
