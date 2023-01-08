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
            imageCropAspectRatio: null,
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

            x-show="(({{$getState() == null  ? 'true':'false'}} && !fileHasUploaded) || fileHasDeleted) || {{$isMultiple()?'true':'false'}}"

            @class([
                    'cropper-image-picker',
                    "left-0 w-full cursor-pointer" => !$isAvatar(),
                    "avatar  w-32  cursor-pointer" => $isAvatar(),
            ])

            type="file"
            x-on:change="function(){
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
            }"/>


    </div>

    <div x-data="{files:null,}" @on-cropper-modal-show-{{ $uniquemodalevent }}.window="
            files = $event.detail.files;
            id = $event.detail.id;
            $dispatch('open-modal', {id: id})
        ">
        <x-filament-support::modal
            class=""
            width="{{$getModalSize()}}"
            id="cropper-modal-{{ $getStatePath() }}"
        >
            <x-slot name="heading">
                <x-filament-support::modal.heading>
                    {{$getModalHeading()}}
                </x-filament-support::modal.heading>
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
                        rotatable: {{$isRotationEnabled()?'true':'false'}},
                        rotateDegree: 0,
                        dragMode: '{{$getDragMode()}}',
                        viewMode: {{$getViewMode()}},
                        zoomable: {{$isZoomable()?'true':'false'}},

                    })" x-cloak
            >
                <div class="h-full w-full" wire:ignore>
                    {{-- init Alpine --}}
                    <div class="h-full w-full">
                        <div x-on:click.prevent @class(
                                        [
                                                'bg-transparent h-full',
                                                'circular-cropper' => $isAvatar()
                                        ]
                                    )>
                            <img class=" block w-full" src="" x-ref="cropper">

                            @if($isRotationEnabled())
                                <div class="py-2">
                                    <div class="flex">
                                        <input
                                            x-modal="rotateDegree"
                                            :value="rotateDegree"
                                            type="range"
                                            class="w-full focus:outline-none focus:bg-primary-200 dark:focus:bg-primary-900 disabled:opacity-70 disabled:cursor-not-allowed filament-forms-range-component border-gray-300 bg-gray-200 dark:bg-white/10 w-90"
                                            min="{{$getMinRotationalDegree()}}" max="{{$getMaxRotationalDegree()}}"
                                            step="{{$getRotationalStep()}}"
                                            x-on:change="function(event){
                                                rotateDegree = event.target.value
                                                rotateByValue(event.target.value);
                                            }"/>
                                        <div class="flex items-center justify-center">
                                            <span x-text="rotateDegree"></span>
                                        </div>

                                        <div class="flex px-2">
                                            <button
                                                tooltp="Reset Crop"
                                                x-on:click.prevent="resetRotate()"
                                                type="button" class="filament-button filament-button-size-sm ">
                                                <svg class="h-4 dark:fill-current" aria-hidden="true" focusable="false"
                                                     data-prefix="fas" data-icon="rotate-left" role="img"
                                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor"
                                                          d="M480 256c0 123.4-100.5 223.9-223.9 223.9c-48.84 0-95.17-15.58-134.2-44.86c-14.12-10.59-16.97-30.66-6.375-44.81c10.59-14.12 30.62-16.94 44.81-6.375c27.84 20.91 61 31.94 95.88 31.94C344.3 415.8 416 344.1 416 256s-71.69-159.8-159.8-159.8c-37.46 0-73.09 13.49-101.3 36.64l45.12 45.14c17.01 17.02 4.955 46.1-19.1 46.1H35.17C24.58 224.1 16 215.5 16 204.9V59.04c0-24.04 29.07-36.08 46.07-19.07l47.6 47.63C149.9 52.71 201.5 32.11 256.1 32.11C379.5 32.11 480 132.6 480 256z"></path>
                                                </svg>
                                            </button>
                                        </div>

                                    </div>
                                    <div class="text-center">
                                        <span class="text-gray-400">Rotate Image</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-4 justify-center items-center flex-wrap my-2">

                                @if($isFlippingEnabled())
                                    <div class="action-group" role="group">
                                        <button
                                            title="Flip"
                                            x-on:click.prevent="flip()"
                                            type="button" class="action">
                                            <svg class="fill-current" xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                 viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path
                                                        d="M18,15 L18,13.4774152 C18,13.3560358 18.0441534,13.2388009 18.1242243,13.147578 C18.3063883,12.9400428 18.622302,12.9194754 18.8298372,13.1016395 L21.7647988,15.6778026 C21.7814819,15.6924462 21.7971714,15.7081846 21.811763,15.7249133 C21.9932797,15.933015 21.9717282,16.2488631 21.7636265,16.4303797 L18.828665,18.9903994 C18.7375973,19.0698331 18.6208431,19.1135979 18.5,19.1135979 C18.2238576,19.1135979 18,18.8897403 18,18.6135979 L18,17 L16.445419,17 C14.5938764,17 12.8460429,16.1451629 11.7093057,14.6836437 L7.71198984,9.54423755 C6.95416504,8.56989138 5.7889427,8 4.55458097,8 L2,8 L2,6 L4.55458097,6 C6.40612357,6 8.15395708,6.85483706 9.29069428,8.31635632 L13.2880102,13.4557625 C14.045835,14.4301086 15.2110573,15 16.445419,15 L18,15 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                    <path
                                                        d="M18,6 L18,4.4774157 C18,4.3560363 18.0441534,4.23880134 18.1242243,4.14757848 C18.3063883,3.94004327 18.622302,3.9194759 18.8298372,4.10163997 L21.7647988,6.67780304 C21.7814819,6.69244668 21.7971714,6.70818509 21.811763,6.72491379 C21.9932797,6.93301548 21.9717282,7.24886356 21.7636265,7.43038021 L18.828665,9.99039986 C18.7375973,10.0698336 18.6208431,10.1135984 18.5,10.1135984 C18.2238576,10.1135984 18,9.88974079 18,9.61359842 L18,8 L16.445419,8 C15.2110573,8 14.045835,8.56989138 13.2880102,9.54423755 L9.29069428,14.6836437 C8.15395708,16.1451629 6.40612357,17 4.55458097,17 L2,17 L2,15 L4.55458097,15 C5.7889427,15 6.95416504,14.4301086 7.71198984,13.4557625 L11.7093057,8.31635632 C12.8460429,6.85483706 14.5938764,6 16.445419,6 L18,6 Z"
                                                        fill="#000000" fill-rule="nonzero"/>
                                                </g>
                                            </svg>
                                        </button>
                                        <button
                                            title="Flip Horizontal"
                                            x-on:click.prevent="flipHorizontal()"
                                            type="button" class="action">
                                            <svg class="fill-primary" xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                 viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path
                                                        d="M3.73851648,19 L8.5,19 C8.77614237,19 9,18.7761424 9,18.5 L9,6.5962912 C9,6.32014883 8.77614237,6.0962912 8.5,6.0962912 C8.29554771,6.0962912 8.11169333,6.22076667 8.03576165,6.41059586 L3.27427814,18.3143047 C3.17172143,18.5706964 3.29642938,18.8616816 3.55282114,18.9642383 C3.61188128,18.9878624 3.67490677,19 3.73851648,19 Z"
                                                        fill="#000000" opacity="0.3"/>
                                                    <path
                                                        d="M15.7385165,19 L20.5,19 C20.7761424,19 21,18.7761424 21,18.5 L21,6.5962912 C21,6.32014883 20.7761424,6.0962912 20.5,6.0962912 C20.2955477,6.0962912 20.1116933,6.22076667 20.0357617,6.41059586 L15.2742781,18.3143047 C15.1717214,18.5706964 15.2964294,18.8616816 15.5528211,18.9642383 C15.6118813,18.9878624 15.6749068,19 15.7385165,19 Z"
                                                        fill="#000000"
                                                        transform="translate(18.000000, 12.500000) scale(-1, 1) translate(-18.000000, -12.500000) "/>
                                                    <rect fill="#000000" opacity="0.3" x="11" y="2" width="2"
                                                          height="20" rx="1"/>
                                                </g>
                                            </svg>
                                        </button>
                                        <button
                                            title="Flip Vertical"
                                            x-on:click.prevent="flipVertical()"
                                            type="button" class="action">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                 viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path
                                                        d="M9.07117914,12.5710461 L13.8326627,12.5710461 C14.108805,12.5710461 14.3326627,12.3471885 14.3326627,12.0710461 L14.3326627,0.16733734 C14.3326627,-0.108805035 14.108805,-0.33266266 13.8326627,-0.33266266 C13.6282104,-0.33266266 13.444356,-0.208187188 13.3684243,-0.0183579985 L8.6069408,11.8853508 C8.50438409,12.1417426 8.62909204,12.4327278 8.8854838,12.5352845 C8.94454394,12.5589085 9.00756943,12.5710461 9.07117914,12.5710461 Z"
                                                        fill="#000000" opacity="0.3"
                                                        transform="translate(11.451854, 6.119192) rotate(-270.000000) translate(-11.451854, -6.119192) "/>
                                                    <path
                                                        d="M9.23851648,24.5 L14,24.5 C14.2761424,24.5 14.5,24.2761424 14.5,24 L14.5,12.0962912 C14.5,11.8201488 14.2761424,11.5962912 14,11.5962912 C13.7955477,11.5962912 13.6116933,11.7207667 13.5357617,11.9105959 L8.77427814,23.8143047 C8.67172143,24.0706964 8.79642938,24.3616816 9.05282114,24.4642383 C9.11188128,24.4878624 9.17490677,24.5 9.23851648,24.5 Z"
                                                        fill="#000000"
                                                        transform="translate(11.500000, 18.000000) scale(1, -1) rotate(-270.000000) translate(-11.500000, -18.000000) "/>
                                                    <rect fill="#000000" opacity="0.3"
                                                          transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                          x="11" y="2" width="2" height="20" rx="1"/>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                @endif

                                @if(!empty($getEnabledAspectRatios()))
                                    <div class="action-group" role="group" >
                                        @foreach($getEnabledAspectRatios() as $key => $ratio)
                                            <button
                                                title="Aspect Ratio {{$key}}"
                                                x-on:click.prevent="setAspectRatio({{$ratio}})"
                                                type="button" class="action"
                                                :class="{ 'active': appliedAspectRatio == {{$ratio}} }">
                                                {{$key}}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                @if($isZoomable() && $isZoomButtonEnabled())
                                    <div class="action-group" role="group">

                                        <button
                                            title="Zoom In"
                                            x-on:click.prevent="zoomByValue({{$getZoomStep()}})"
                                            type="button" class="action">
                                            <x-heroicon-o-zoom-in class="w-4 h-4"/>
                                        </button>

                                        <button
                                            title="Zoom Out"
                                            x-on:click.prevent="zoomByValue(-{{$getZoomStep()}})"
                                            type="button" class="action">
                                            <x-heroicon-o-zoom-out class="w-4 h-4"/>
                                        </button>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>

                <div class="flex justify-center items-center gap-2">
                    <x-filament-support::button type="button" x-on:click.prevent="uploadCropperImage()">
                        @lang('filament::resources/pages/edit-record.form.actions.save.label')
                    </x-filament-support::button>
                </div>
            </div>

        </x-filament-support::modal>
    </div>

</x-dynamic-component>
