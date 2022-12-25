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



    protected bool | Closure $isRotationEnabled = true;

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

    public function isRotationEnabled() : bool{
        return $this->evaluate($this->isRotationEnabled);
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
