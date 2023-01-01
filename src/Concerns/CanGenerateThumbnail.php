<?php

namespace Nuhel\FilamentCropper\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\TemporaryUploadedFile;

trait CanGenerateThumbnail
{
    public bool | Closure $generateThumbnailImage = false;

    public ?Closure $generateThumbnailImageUsing = null;

    public ?Closure $removeThumbnailImageUsing = null;

    public string $thumbnailImageDirectory = 'thumbnailImages';

    protected int | Closure | null $thumbnailImageHeight = 150;

    protected int | Closure | null $thumbnailImageWidth = 150;

    public function generateThumbnailImage(bool|Closure $condition = true): static
    {
        $this->generateThumbnailImage = $condition;

        return $this;
    }

    public function shouldGenerateThumbnailImage(): bool
    {
        return $this->evaluate($this->generateThumbnailImage);
    }

    public function removeUploadedFile(string $fileKey): string|TemporaryUploadedFile|null
    {
        $files = $this->getState();

        $file = $files[$fileKey] ?? null;

        if ($file instanceof TemporaryUploadedFile) {
            if (!empty($this->removeThumbnailImageUsing)) {
                $this->callRemoveThumbnailImageUsing($file);
            } else {
                $this->removeThumbnailImage($file);
            }
        }

        return parent::removeUploadedFile($fileKey);
    }

    public function removeThumbnailImageUsing(Closure $callback): static
    {
        $this->removeThumbnailImageUsing = $callback;

        return $this;
    }

    public function callRemoveThumbnailImageUsing(TemporaryUploadedFile $file) : void {
        $this->evaluate($this->removeThumbnailImageUsing, [
            'file' => $file,
            'filePath' => $this->getThumbnailImagePath($file),
            'directory' => $this->getDirectory(),
            'diskName' => $this->getDiskName(),
            'visibility' => $this->getVisibility()
        ]);
    }

    public function removeThumbnailImage(TemporaryUploadedFile $file): void
    {
        Storage::disk($this->getDiskName() ?? 'public')->delete($this->getThumbnailImagePath($file));
    }

    public function callAfterStateUpdated(): static
    {
        if ($this->shouldGenerateThumbnailImage()) {
            $state = $this->getState();

            if (is_array($state)) {
                $file = Arr::last($state);
                if ($file instanceof TemporaryUploadedFile && $file->exists()) {
                    if (!empty($this->generateThumbnailImageUsing)) {
                        $this->callGenerateThumbnailUsing($file);
                    } else {
                        $this->storeThumbnailImage($file);
                    }
                }
            }
        }

        return parent::callAfterStateUpdated();
    }

    public function generateThumbnailImageUsing(Closure $callback): static
    {
        $this->generateThumbnailImage = true;

        $this->generateThumbnailImageUsing = $callback;

        return $this;
    }

    public function callGenerateThumbnailUsing(TemporaryUploadedFile $file) : void{
        $this->evaluate($this->generateThumbnailImageUsing, [
            'file' => $file,
            'filename' => $this->getUploadedFileNameForStorage($file),
            'directory' => $this->getDirectory(),
            'diskName' => $this->getDiskName(),
            'visibility' => $this->getVisibility()
        ]);
    }

    public function storeThumbnailImage(TemporaryUploadedFile $file): void
    {
        if (class_exists('Intervention\\Image\\ImageManager')) {
            Storage::disk($this->getDiskName() ?? 'public')->put($this->getThumbnailImagePath($file), $this->getGeneratedThumbnailImage($file), $this->getVisibility());
        }
    }

    public function thumbnailImageDirectory($directory = 'thumbnailImages'): static
    {
        $this->thumbnailImageDirectory = $directory;

        return $this;
    }

    public function getThumbnailImageDirectory(): string
    {
        return $this->thumbnailImageDirectory;
    }

    public function getThumbnailImagePath(TemporaryUploadedFile $file): string
    {
        return $this->getThumbnailImageDirectory() . DIRECTORY_SEPARATOR . $this->getUploadedFileNameForStorage($file);
    }

    public function getThumbnailImageHeight(): ?int
    {
        return $this->evaluate($this->thumbnailImageHeight);
    }

    public function thumbnailImageHeight(string | Closure | null $height): static
    {
        $this->thumbnailImageHeight = $height;

        return $this;
    }

    public function getThumbnailImageWidth(): ?int
    {
        return $this->evaluate($this->thumbnailImageWidth);
    }

    public function thumbnailImageWidth(string | Closure | null $height): static
    {
        $this->thumbnailImageWidth = $height;

        return $this;
    }

    public function getGeneratedThumbnailImage(TemporaryUploadedFile $file): \Intervention\Image\Image
    {
        return (new \Intervention\Image\ImageManager())->make($file->getRealPath())->resize($this->getThumbnailImageWidth(), $this->getThumbnailImageHeight(), function (\Intervention\Image\Constraint $constraint) {
            $constraint->aspectRatio();
        })->save();
    }
}
