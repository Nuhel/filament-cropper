<?php

namespace Nuhel\FilamentCropper\Concerns;

use Illuminate\Support\Str;
use Nuhel\FilamentCropper\Values\ViewMode;

trait HasAspectRatio
{

    protected array|Closure $enabledAspectRatios = [];

    protected bool|Closure $enableAspectRatioFreeMode = false;


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
