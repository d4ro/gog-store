<?php

namespace GogStore\Pattern;

use GogStore\Pattern\Collection\Set;

trait ObservableTrait
{
    private ?Set $observers = null;

    private function initObservable(): void
    {
        $this->observers = new Set();
    }

    public function attach(Observer $observer): void
    {
        $this->observers->add($observer);
    }

    public function detach(Observer $observer): void
    {
        $this->observers->remove($observer);
    }

    public function notifyChanged(): void
    {
        if (null === $this->observers)
            return;

        foreach ($this->observers as $observer) {
            $observer->notify($this);
        }
    }
}