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

    /**
     * Flash a toast message to the session for persistence across redirects.
     */
    public function flashToast(string $message, string $type = 'success'): void
    {
        session()->flash('toast', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * Convenience method for session error toasts.
     */
    public function flashToastError(string $message): void
    {
        $this->flashToast($message, 'error');
    }

    /**
     * Convenience method for session success toasts.
     */
    public function flashToastSuccess(string $message): void
    {
        $this->flashToast($message, 'success');
    }
}
