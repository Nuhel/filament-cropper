<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isAvatar() || $isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>

    @php
        $imageCropAspectRatio = $getImageCropAspectRatio();
        $imageResizeTargetHeight = $getImageResizeTargetHeight();
        $imageResizeTargetWidth = $getImageResizeTargetWidth();
        $imageResizeMode = $getImageResizeMode();
        $shouldTransformImage = $imageCropAspectRatio || $imageResizeTargetHeight || $imageResizeTargetWidth;
    @endphp


    <div class="relative" x-data="{
        fileHasUploaded : false,
        fileHasDeleted: false,
     }"
    >

        <div
            x-data="fileUploadFormComponent({
            acceptedFileTypes: {{ json_encode($getAcceptedFileTypes()) }},
            canDownload: {{ $canDownload() ? 'true' : 'false' }},
            canOpen: {{ $canOpen() ? 'true' : 'false' }},
            canPreview: {{ $canPreview() ? 'true' : 'false' }},
            canReorder: {{ $canReorder() ? 'true' : 'false' }},
            deleteUploadedFileUsing: async (fileKey) => {
                fileHasDeleted = true;
                fileHasUploaded = false;
                return await $wire.deleteUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            getUploadedFileUrlsUsing: async () => {
                return await $wire.getUploadedFileUrls('{{ $getStatePath() }}')
            },
            imageCropAspectRatio: {{ $imageCropAspectRatio ? "'{$imageCropAspectRatio}'" : 'null' }},
            imagePreviewHeight: {{ ($height = $getImagePreviewHeight()) ? "'{$height}'" : 'null' }},
            imageResizeMode: {{ $imageResizeMode ? "'{$imageResizeMode}'" : 'null' }},
            imageResizeTargetHeight: {{ $imageResizeTargetHeight ? "'{$imageResizeTargetHeight}'" : 'null' }},
            imageResizeTargetWidth: {{ $imageResizeTargetWidth ? "'{$imageResizeTargetWidth}'" : 'null' }},
            isAvatar: {{ $isAvatar() ? 'true' : 'false' }},
            loadingIndicatorPosition: '{{ $getLoadingIndicatorPosition() }}',
            locale: @js(app()->getLocale()),
            panelAspectRatio: {{ ($aspectRatio = $getPanelAspectRatio()) ? "'{$aspectRatio}'" : 'null' }},
            panelLayout: {{ ($layout = $getPanelLayout()) ? "'{$layout}'" : 'null' }},
            placeholder: @js($getPlaceholder()),
            maxSize: {{ ($size = $getMaxSize()) ? "'{$size} KB'" : 'null' }},
            minSize: {{ ($size = $getMinSize()) ? "'{$size} KB'" : 'null' }},
            removeUploadedFileUsing: async (fileKey) => {
                fileHasDeleted = true;
                fileHasUploaded = false;
                return await $wire.removeUploadedFile('{{ $getStatePath() }}', fileKey)
            },
            removeUploadedFileButtonPosition: '{{ $getRemoveUploadedFileButtonPosition() }}',
            reorderUploadedFilesUsing: async (files) => {
                return await $wire.reorderUploadedFiles('{{ $getStatePath() }}', files)
            },
            shouldAppendFiles: {{ $shouldAppendFiles() ? 'true' : 'false' }},
            shouldTransformImage: {{ $shouldTransformImage ? 'true' : 'false' }},
            state: $wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }},
            uploadButtonPosition: '{{ $getUploadButtonPosition() }}',
            uploadProgressIndicatorPosition: '{{ $getUploadProgressIndicatorPosition() }}',
            uploadUsing: (fileKey, file, success, error, progress) => {
                $wire.upload(`{{ $getStatePath() }}.${fileKey}`, file, () => {
                    fileHasUploaded = true;
                    fileHasDeleted = false;
                    success(fileKey)
                }, error, progress)
            },
        })"
            wire:ignore
            {!! ($id = $getId()) ? "id=\"{$id}\"" : null !!}
            style="min-height: {{ $isAvatar() ? '8em' : ($getPanelLayout() === 'compact' ? '2.625em' : '4.75em') }}"
            {{ $attributes->merge($getExtraAttributes())->class([
                'filament-forms-file-upload-component',
                'w-32 mx-auto' => $isAvatar(),
            ]) }}
            {{ $getExtraAlpineAttributeBag() }}
        >
            <input
                x-ref="input"
                {{ $isDisabled() ? 'disabled' : '' }}
                {{ $isMultiple() ? 'multiple' : '' }}
                type="file"
                {{ $getExtraInputAttributeBag() }}
                dusk="filament.forms.{{ $getStatePath() }}"

            />
        </div>

        @php
            $uniquemodalevent = \Illuminate\Support\Str::of($getStatePath())->replace('.','')->replace('_','');
        @endphp



        <input
            {{ $isDisabled() ? 'disabled' : '' }}
            type="file"
            accept="{{\Illuminate\Support\Arr::join($getAcceptedFileTypes(),',','')}}"

            x-show = "(({{$getState() == null  ? 'true':'false'}} && !fileHasUploaded) || fileHasDeleted) || {{$isMultiple()?'true':'false'}}"

            @class([
                    'croppie-image-picker',
                    "left-0 w-full cursor-pointer" => !$isAvatar(),
                    "avatar  w-32  cursor-pointer" => $isAvatar(),
            ])

            type="file"
            x-on:change = "function(){
                var fileType = event.target.files[0]['type'];
                if (!(fileType.search(`image`) >= 0)) {
                    new Notification()
                    .title('Error')
                        .danger()
                        .body('Selected file is not an valid image')
                        .send()
                        return;
                }
                $dispatch('on-cropper-modal-show-{{$uniquemodalevent}}', {
                    id: 'cropper-modal-{{ $getStatePath() }}',
                    files: event.target.files,
                })
            }" />


    </div>

    <div x-data="{files:null,}" @on-cropper-modal-show-{{ $uniquemodalevent }}.window="
            files = $event.detail.files;
            id = $event.detail.id;
            $dispatch('open-modal', {id: id})
        ">
        <x-filament::modal
            class=""
            width="{{$getModalSize()}}"
            id="cropper-modal-{{ $getStatePath() }}"
        >
            <x-slot name="heading">
                <x-filament::modal.heading>
                    {{$getModalHeading()}}
                </x-filament::modal.heading>
            </x-slot>
            <div class=" z-5 w-full h-full flex flex-col justify-between"

                 x-data="imageCropper({
                        imageUrl: '',
                        shape: `{{$isAvatar()?'circle':'square'}}`,
                        files: files,
                        width: `{{$getImageResizeTargetWidth()}}`,
                        height: `{{$getImageResizeTargetHeight()}}`,
                        statePath : `{{$getStatePath()}}`,
                        aspectRatio: {{$getImageCropAspectRatioForCropper()}},
                        rotatable: {{$isRotationEnabled()}}

                    })" x-cloak
            >
                <div class="h-full w-full" wire:ignore >
                    {{-- init Alpine --}}
                    <div class="h-full w-full "  >
                        <div  x-on:click.prevent class="bg-transparent h-full">
                            <img class=" block w-full" src="" x-ref="cropper">

                            @if($isRotationEnabled())
                                <div>
                                    <input
                                        type="range"
                                        class="w-full focus:outline-none focus:bg-primary-200 dark:focus:bg-primary-900 disabled:opacity-70 disabled:cursor-not-allowed filament-forms-range-component border-gray-300 bg-gray-200 dark:bg-white/10 w-90"
                                        min="-90"  max="90"  step="1"
                                        x-on:change="function(event){
                                        rotateByValue(event.target.value);
                                    }"/>
                                </div>
                            @endif



                            <div class="flex rounded-md shadow-sm w-full justify-center" role="group">

                                <button
                                    x-on:click.prevent="flip()"
                                    type="button" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                                    Flip
                                </button>
                                <button
                                    x-on:click.prevent="flipHorizontal()"
                                    type="button" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                                    Flip horizontal
                                </button>
                                <button
                                    x-on:click.prevent="flipVertical()"
                                    type="button" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                                    Flip vertical
                                </button>


                            </div>


                        </div>
                    </div>
                </div>


                <div class="flex justify-center items-center gap-2">
                    <x-filament::button type="button"  x-on:click.prevent="uploadCropperImage()">
                        @lang('filament::resources/pages/edit-record.form.actions.save.label')
                    </x-filament::button>
                </div>
            </div>




        </x-filament::modal>
    </div>

</x-dynamic-component>
