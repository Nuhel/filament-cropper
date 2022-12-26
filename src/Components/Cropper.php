<?php

namespace Nuhel\FilamentCropper\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class Cropper extends FileUpload
{
    protected string $view = 'filament-cropper::components.cropper';

    protected string | Closure | null $imageResizeTargetHeight = '400';

    protected string | Closure | null $imageResizeTargetWidth = '400';

    protected string | Closure | null $modalSize = '6xl';

    protected string | Closure | null $modalHeading = 'Manage Image';

    protected bool | Closure $isRotationEnabled = false;

    protected int | Closure $minRotationalDegree = -180;

    protected int | Closure $maxRotationalDegree = 180;

    protected int | Closure $rotationalStep = 1;

    protected bool | Closure $isFlippingEnabled = false;


    public function getAcceptedFileTypes(): ?array
    {
        $this->acceptedFileTypes([
            "image/png"," image/gif","image/jpeg"
        ]);

        return parent::getAcceptedFileTypes();
    }

    public function modalSize(string | Closure | null $modalSize) : static
    {
        $this->modalSize = $modalSize;

        return $this;
    }

    public function getModalSize(): ?string
    {
        return $this->evaluate($this->modalSize);
    }

    public function modalHeading(string | Closure | null $modalHeading) : static
    {
        $this->modalHeading = $modalHeading;

        return $this;
    }

    public function getModalHeading(): ?string
    {
        return $this->evaluate($this->modalHeading);
    }

    public function enableImageRotation(bool | Closure $condition = true, int $minDeg = null, $maxDeg = null): static
    {
        $this->isRotationEnabled = $condition;

        return $this;
    }

    public function isRotationEnabled() : bool
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

        return is_numeric($step) && $step> 0? $step:1;
    }

    public function rotationalStep(int|Closure $rotationalStep): static
    {
        $this->rotationalStep = $rotationalStep;
        return $this;
    }

    public function enableImageFlipping(bool | Closure $condition = true): static
    {
        $this->isFlippingEnabled = $condition;

        return $this;
    }

    public function isFlippingEnabled() : bool
    {
        return $this->evaluate($this->isFlippingEnabled);
    }

    public function getImageCropAspectRatioForCropper(): float|int
    {
        $ratio = $this->getImageCropAspectRatio();
        if(empty($ratio)){
            return 1;
        }

        $explodedRation = Str::of($ratio)->explode(':');
        if($explodedRation->count() != 2){
            return 1;
        }

        if (!(is_numeric($explodedRation->first()) && is_numeric($explodedRation->last())) || $explodedRation->last() == 0){
            return 1;
        }

        return $explodedRation->first()/ $explodedRation->last();
    }

}
