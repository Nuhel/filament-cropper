<?php

namespace Nuhel\FilamentCropper\Concerns;

trait CanFlipImage
{
    protected bool|Closure $isFlippingEnabled = false;

    public function enableImageFlipping(bool|Closure $condition = true): static
    {
        $this->isFlippingEnabled = $condition;

        return $this;
    }

    public function isFlippingEnabled(): bool
    {
        return $this->evaluate($this->isFlippingEnabled);
    }

}
