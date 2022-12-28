<?php

namespace Nuhel\FilamentCropper\Components;

use Closure;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Nuhel\FilamentCropper\Concerns\CanRotateImage;
use Nuhel\FilamentCropper\Concerns\CanFlipImage;
use Nuhel\FilamentCropper\Concerns\CanZoomImage;
use Nuhel\FilamentCropper\Concerns\HasViewMode;
use Nuhel\FilamentCropper\Values\DragMode;


class Cropper extends FileUpload
{
    use CanFlipImage, CanRotateImage, CanZoomImage, HasViewMode;

    protected string $view = 'filament-cropper::components.cropper';

    protected string|Closure|null $imageResizeTargetHeight = '400';

    protected string|Closure|null $imageResizeTargetWidth = '400';

    protected string|Closure|null $modalSize = '6xl';

    protected string|Closure|null $modalHeading = 'Manage Image';

    protected array|Closure $enabledAspectRatios = [];

    protected bool|Closure $enableAspectRatioFreeMode = false;


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

    public function getImageCropAspectRatioForCropper(): float|int
    {
        return $this->isRatioValid($this->getImageCropAspectRatio()) ?? 1;
    }

    public function enableAspectRatioFreeMode(bool|Closure $enabled = true): static
    {
        $this->enableAspectRatioFreeMode = $enabled;

        return $this;
    }

    public function isAspectRatioFreeModeEnabled(): bool
    {
        return $this->evaluate($this->enableAspectRatioFreeMode);
    }

    public function enabledAspectRatios(array|Closure $ratios): static
    {
        $this->enabledAspectRatios = $ratios;

        return $this;
    }

    public function getEnabledAspectRatios(): array
    {
        $ratios = $this->evaluate($this->enabledAspectRatios);

        if (is_array($ratios)) {

            $ratios = [
                $this->getImageCropAspectRatio(),
                ...$ratios
            ];
            $filtered = [];

            foreach ($ratios as $ratio) {
                $actualRation = $this->isRatioValid($ratio);

                if ($actualRation) {
                    $filtered[$ratio] = $actualRation;
                }

            }
            if ($this->isAspectRatioFreeModeEnabled()) {
                $filtered['Free'] = 'NaN';
            }
            return $filtered;
        }

        return $this->isAspectRatioFreeModeEnabled() ? ['Free' => 'NaN'] : [];
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

    protected function isRatioValid(string|null $ratio): bool|float|int
    {
        if (empty($ratio)) {
            return false;
        }

        $explodedRation = Str::of($ratio)->explode(':');

        if ($explodedRation->count() != 2) {
            return false;
        }

        if (!(is_numeric($explodedRation->first()) && is_numeric($explodedRation->last())) || $explodedRation->last() == 0) {
            return false;
        }

        return $explodedRation->first() / $explodedRation->last();
    }

}
