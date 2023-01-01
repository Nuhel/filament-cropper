<?php

namespace Nuhel\FilamentCropper\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Nuhel\FilamentCropper\Concerns\CanGenerateThumbnail;
use Nuhel\FilamentCropper\Concerns\CanRotateImage;
use Nuhel\FilamentCropper\Concerns\CanFlipImage;
use Nuhel\FilamentCropper\Concerns\CanZoomImage;
use Nuhel\FilamentCropper\Concerns\HasAspectRatio;
use Nuhel\FilamentCropper\Concerns\HasViewMode;
use Nuhel\FilamentCropper\Values\DragMode;


class Cropper extends FileUpload
{
    use CanFlipImage, CanRotateImage, CanZoomImage, HasViewMode, HasAspectRatio, CanGenerateThumbnail;

    protected string $view = 'filament-cropper::components.cropper';

    protected string|Closure|null $imageResizeTargetHeight = '400';

    protected string|Closure|null $imageResizeTargetWidth = '400';

    protected string|Closure|null $modalSize = '6xl';

    protected string|Closure|null $modalHeading = 'Manage Image';




    protected DragMode|Closure $dragMode;

    public function getAcceptedFileTypes(): ?array
    {
        $this->acceptedFileTypes([
            "image/png", " image/gif", "image/jpeg"
        ]);

        return parent::getAcceptedFileTypes();
    }

    public function modalSize(string|Closure|null $modalSize): static
    {
        $this->modalSize = $modalSize;

        return $this;
    }

    public function getModalSize(): ?string
    {
        return $this->evaluate($this->modalSize);
    }

    public function modalHeading(string|Closure|null $modalHeading): static
    {
        $this->modalHeading = $modalHeading;

        return $this;
    }

    public function getModalHeading(): ?string
    {
        return $this->evaluate($this->modalHeading);
    }



    public function dragMode(DragMode|Clousure $dragMode): static
    {
        $this->dragMode = $dragMode;
        return $this;
    }

    public function getDragMode(): DragMode
    {
        if (empty($this->dragMode)) {
            return DragMode::NONE;
        }
        return $this->evaluate($this->dragMode);
    }



}
