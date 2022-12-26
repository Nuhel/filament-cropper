<?php

namespace Nuhel\FilamentCropper\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Nuhel\FilamentCropper\Values\DragMode;
use Nuhel\FilamentCropper\Values\ViewMode;
use PhpParser\Node\Expr\ClosureUse;

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

    protected array | Closure $enabledAspectRatios = [];

    protected ViewMode | Closure $viewMode;

    protected DragMode | Closure $dragMode;


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

    public function enableImageRotation(bool | Closure $condition = true): static
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
        return $this->isRatioValid($this->getImageCropAspectRatio())??1;
    }

    public function enabledAspectRatios(array | Closure $ratios) : static
    {
        $this->enabledAspectRatios = $ratios;

        return $this;
    }

    public function getEnabledAspectRatios() : array
    {
        $ratios = $this->evaluate($this->enabledAspectRatios);


        if(is_array($ratios)){

            $ratios = [
                $this->getImageCropAspectRatio(),
                ...$ratios
            ];
            $filtered = [];

            foreach ($ratios as $ratio)
            {
                $actualRation = $this->isRatioValid($ratio);

                if($actualRation)
                {
                    $filtered[$ratio] = $actualRation;
                }

            }

            return $filtered;
        }

        return [];
    }

    public function viewMode(ViewMode | Clousure $viewMode) : static{
        $this->viewMode = $viewMode;
        return $this;
    }

    public function getViewMode() : ViewMode{
        if(empty($this->viewMode)){
            return ViewMode::FIT_FILL_CANVAS;
        }
        return $this->evaluate($this->viewMode);
    }

    public function dragMode(DragMode | Clousure $dragMode) : static{
        $this->dragMode = $dragMode;
        return $this;
    }

    public function getDragMode() : DragMode{
        if(empty($this->dragMode)){
            return DragMode::NONE;
        }
        return $this->evaluate($this->dragMode);
    }

    protected function isRatioValid(string | null $ratio):bool | float | int
    {
        if(empty($ratio))
        {
            return false;
        }

        $explodedRation = Str::of($ratio)->explode(':');

        if($explodedRation->count() != 2)
        {
            return false;
        }

        if (!(is_numeric($explodedRation->first()) && is_numeric($explodedRation->last())) || $explodedRation->last() == 0)
        {
            return false;
        }

        return $explodedRation->first() / $explodedRation->last();
    }

}
