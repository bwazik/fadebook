<?php

namespace App\Traits;

trait NotificationDataStructure
{
    protected function getStandardData(): array
    {
        return [
            'id' => $this->getEntityId(),
            'type' => $this->getNotificationType(),
            'entity_type' => $this->getEntityType(),
            'title' => $this->getTitle(),
            'message' => $this->getShortMessage(),
            'icon' => $this->getIcon(),
            'icon_bg' => $this->getIconBg(),
            'action_url' => $this->getActionUrl(),
            'action_text' => $this->getActionText(),
            'data' => $this->getCustomData(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    protected function getShortMessage(): string
    {
        return $this->getMessage();
    }

    public function getWhatsAppTemplate(): ?string
    {
        return $this->getNotificationType();
    }

    public function getWhatsAppData(): array
    {
        return $this->getCustomData();
    }

    public function getWhatsAppPriority(): string
    {
        return 'default';
    }

    public function getRelatedShopId(): ?int
    {
        return null;
    }

    abstract protected function getEntityId();

    abstract protected function getNotificationType(): string;

    abstract protected function getEntityType(): string;

    abstract protected function getTitle(): string;

    abstract protected function getMessage(): string;

    abstract protected function getIcon(): string;

    abstract protected function getIconBg(): string;

    abstract protected function getActionUrl(): string;

    abstract protected function getActionText(): string;

    abstract protected function getCustomData(): array;
}
