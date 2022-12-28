<?php

namespace Nuhel\FilamentCropper\Concerns;

trait CanRotateImage
{
    protected bool|Closure $isRotationEnabled = false;

    protected int|Closure $minRotationalDegree = -180;

    protected int|Closure $maxRotationalDegree = 180;

    protected int|Closure $rotationalStep = 1;


    public function enableImageRotation(bool|Closure $condition = true): static
    {
        $this->isRotationEnabled = $condition;

        return $this;
    }

    public function isRotationEnabled(): bool
    {
        return $this->evaluate($this->isRotationEnabled);
    }

    public function getMinRotationalDegree(): int
    {
        return $this->evaluate($this->minRotationalDegree);
    }

    public function minRotationalDegree(int|Closure $minRotationalDegree = -180): static
    {
        $this->minRotationalDegree = $minRotationalDegree;

        return $this;
    }

    public function getMaxRotationalDegree(): int
    {
        return $this->evaluate($this->maxRotationalDegree);
    }

    public function maxRotationalDegree(int|Closure $maxRotationalDegree = 180): static
    {
        $this->maxRotationalDegree = $maxRotationalDegree;

        return $this;
    }

    public function getRotationalStep(): int
    {
        $step = $this->evaluate($this->rotationalStep);

        return is_numeric($step) && $step > 0 ? $step : 1;
    }

    public function rotationalStep(int|Closure $rotationalStep): static
    {
        $this->rotationalStep = $rotationalStep;
        return $this;
    }


}
