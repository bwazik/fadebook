<?php

namespace App\Traits;

trait WithToast
{
    /**
     * Dispatch a toast message directly to the global <x-toaster> component.
     *
     * @param  string  $type  success|error|info
     */
    public function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', message: $message, type: $type);
    }

    /**
     * Convenience method for error toasts.
     */
    public function toastError(string $message): void
    {
        $this->toast($message, 'error');
    }

    /**
     * Convenience method for success toasts.
     */
    public function toastSuccess(string $message): void
    {
        $this->toast($message, 'success');
    }
}
