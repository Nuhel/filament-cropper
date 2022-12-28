<?php

namespace Nuhel\FilamentCropper\Concerns;

trait CanZoomImage
{


    protected bool|Closure $isZoomable = true;

    public function zoomable(bool|Closure $condition = true): static
    {
        $this->isZoomable = $condition;

        return $this;
    }

    public function isZoomable(): bool
    {
        return $this->evaluate($this->isZoomable);
    }

    protected bool|Closure $isZoomButtonEnabled = false;

    public function enableZoomButtons(bool|Closure $condition = true): static
    {
        $this->isZoomButtonEnabled = $condition;

        return $this;
    }

    public function isZoomButtonEnabled(): bool
    {
        return $this->evaluate($this->isZoomButtonEnabled);
    }

    protected float|Closure $zoomStep = 0.1;

    public function getZoomStep(): float
    {
        return $this->evaluate($this->zoomStep);
    }

    public function zoomStep(float|Closure $zoomStep = 0.1): static
    {
        $this->zoomStep = $zoomStep;

        return $this;
    }
}
