<!-- Photo Crop Modal -->
<div x-data="photoCropModal()" 
     x-show="showCropModal" 
     @open-crop-modal.window="openCropModal($event.detail)"
     @keydown.escape.window="closeCropModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="showCropModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm"
         @click="closeCropModal"></div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="showCropModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.away="closeCropModal"
             class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Crop & Sesuaikan Foto</h3>
                        <p class="text-blue-100 text-sm">Geser, zoom, dan crop foto sesuai keinginan Anda</p>
                    </div>
                </div>
                <button @click="closeCropModal" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Crop Container -->
            <div class="p-6">
                <!-- Cropper Canvas -->
                <div class="bg-gray-900 rounded-xl overflow-hidden mb-6" style="max-height: 500px;">
                    <img x-ref="cropImage" id="crop-image" :src="imageSrc" alt="Crop preview" class="max-w-full">
                </div>

                <!-- Zoom & Rotate Controls -->
                <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 mb-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-semibold text-gray-700 w-20">Zoom:</label>
                        <input type="range" min="0" max="2" step="0.1" value="1" 
                               @input="zoomImage($event.target.value)"
                               class="flex-1 h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                        <button @click="resetZoom" 
                                class="px-3 py-1 bg-white border-2 border-gray-300 rounded-lg text-xs font-semibold hover:bg-gray-50 transition-all">
                            Reset
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="rotateLeft" 
                                class="flex-1 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Putar Kiri
                        </button>
                        <button @click="rotateRight" 
                                class="flex-1 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6-6m6 6l-6 6" />
                            </svg>
                            Putar Kanan
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="flipHorizontal" 
                                class="flex-1 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            Flip Horizontal
                        </button>
                        <button @click="flipVertical" 
                                class="flex-1 px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                            Flip Vertical
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between gap-4">
                    <button @click="closeCropModal" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>

                    <button @click="cropAndSave" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Gunakan Foto Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function photoCropModal() {
    return {
        showCropModal: false,
        imageSrc: '',
        cropper: null,
        scaleX: 1,
        scaleY: 1,
        
        openCropModal(data) {
            this.imageSrc = data.src;
            this.showCropModal = true;
            
            // Initialize cropper after modal is shown
            this.$nextTick(() => {
                const image = this.$refs.cropImage;
                
                // Import Cropper dynamically
                import('cropperjs/dist/cropper.css');
                import('cropperjs').then((module) => {
                    const Cropper = module.default;
                    
                    this.cropper = new Cropper(image, {
                        aspectRatio: 1, // Square crop
                        viewMode: 2,
                        dragMode: 'move',
                        autoCropArea: 1,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        responsive: true,
                        minContainerWidth: 200,
                        minContainerHeight: 200,
                    });
                });
            });
        },
        
        closeCropModal() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            this.showCropModal = false;
            this.scaleX = 1;
            this.scaleY = 1;
        },
        
        zoomImage(value) {
            if (this.cropper) {
                this.cropper.zoomTo(parseFloat(value));
            }
        },
        
        resetZoom() {
            if (this.cropper) {
                this.cropper.reset();
            }
        },
        
        rotateLeft() {
            if (this.cropper) {
                this.cropper.rotate(-90);
            }
        },
        
        rotateRight() {
            if (this.cropper) {
                this.cropper.rotate(90);
            }
        },
        
        flipHorizontal() {
            this.scaleX = this.scaleX === 1 ? -1 : 1;
            if (this.cropper) {
                this.cropper.scaleX(this.scaleX);
            }
        },
        
        flipVertical() {
            this.scaleY = this.scaleY === 1 ? -1 : 1;
            if (this.cropper) {
                this.cropper.scaleY(this.scaleY);
            }
        },
        
        cropAndSave() {
            if (!this.cropper) return;
            
            // Get cropped canvas
            const canvas = this.cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            // Convert to blob and dispatch event
            canvas.toBlob((blob) => {
                const croppedUrl = URL.createObjectURL(blob);
                
                // Dispatch event with cropped image data
                window.dispatchEvent(new CustomEvent('photo-cropped', {
                    detail: {
                        blob: blob,
                        url: croppedUrl,
                    }
                }));
                
                this.closeCropModal();
            }, 'image/jpeg', 0.9);
        }
    }
}
</script>
