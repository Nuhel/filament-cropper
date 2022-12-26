document.addEventListener('alpine:init', () => {
    Alpine.data('imageCropper', (config) => ({
        showCropper: false,
        filename: '',
        filetype: '',
        width: config.width,
        height:config.height,
        shape: config.shape,
        statePath: config.statePath,
        aspectRatio: config.aspectRatio,
        appliedAspectRatio: config.aspectRatio,
        rotatable: config.rotatable,
        rotateDegree: config.rotateDegree,
        dragMode : config.dragMode,
        viewMode: config.viewMode,

        scales:{

            flipHorizontal: 1,
            flipVertical: 1,
        },

        cropper: null,
        init() {
            this.$nextTick(
                () => {

                }
            )
            this.$watch('files', async (value) => {

                let ref = this;

                let reader, files = this.files
                if( files == null ||files[0] == undefined){
                    return;
                }
                this.filename = files[0].name;
                this.filetype = files[0].type

                reader = new FileReader()
                reader.onload = (e) => {
                    ref.$refs.cropper.src = e.target.result;
                    setTimeout(() => {
                        if(this.cropper != null){

                        }
                        ref.destroyCropper();
                        ref.initCropper()
                    }, 500)
                }
                await reader.readAsDataURL(files[0])


            })
        },

        destroyCropper(){
            if(this.cropper == null)
                return;
            this.cropper.destroy();
            this.cropper = null;
        },

        async initCropper() {
            this.cropper = new Cropper(
                this.$refs.cropper, {
                    aspectRatio: this.aspectRatio,
                    rotatable: this.rotatable,
                    dragMode: this.dragMode,
                    viewMode: this.viewMode,
                    crop(event) {

                    },
                })
        },

        rotateByValue(value){
            console.log(value);
            const previousRotate = this.cropper.getImageData().rotate;
            this.cropper.rotate(value-previousRotate)
        },
        resetRotate(){
            let previousRotate = this.cropper.getImageData().rotate;
            previousRotate = (previousRotate )-(previousRotate * 2);
            this.rotateDegree = 0;
            this.cropper.rotate(previousRotate)
        },

        setAspectRatio(ratio){
            this.cropper.setAspectRatio(ratio);
            this.appliedAspectRatio = ratio;
        },

        flip (){
            if(this.scales.flipVertical < 0){
                this.scales.flipVertical = 1;
            }else{
                this.scales.flipVertical = -1;
            }

            if(this.scales.flipHorizontal < 0){
                this.scales.flipHorizontal = 1;
            }else{
                this.scales.flipHorizontal = -1;
            }

            this.cropper.scale(this.scales.flipHorizontal, this.scales.flipVertical)
        },
        flipHorizontal (){
            if(this.scales.flipHorizontal < 0){
                this.scales.flipHorizontal = 1;
            }else{
                this.scales.flipHorizontal = -1;
            }

            this.cropper.scale(this.scales.flipHorizontal, this.scales.flipVertical)
        },
        flipVertical (){
            if(this.scales.flipVertical < 0){
                this.scales.flipVertical = 1;
            }else{
                this.scales.flipVertical = -1;
            }
            this.cropper.scale(this.scales.flipHorizontal, this.scales.flipVertical)
        },

        uploadCropperImage(){
            let ref = this;
            this.cropper.getCroppedCanvas().toBlob((croppedImage) => {
                let input = document.getElementById(ref.statePath).getElementsByTagName('input')[0]
                let event = new Event('change');
                let fileName = ref.filename;
                let filetype = ref.filetype;
                let file = new File(
                    [croppedImage],
                    fileName,
                    {type:filetype, lastModified:new Date().getTime()},
                    'utf-8'
                );
                let container = new DataTransfer();
                container.items.add(file);

                input.files = container.files;
                ref.$dispatch("close-modal", {id: "cropper-modal-"+ref.statePath, files: null})
                input.dispatchEvent(event);
            });

        }
    }))
})
