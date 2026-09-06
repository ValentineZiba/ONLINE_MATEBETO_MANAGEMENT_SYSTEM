@props(['model', 'label' => 'Photo'])

<div
    x-data="{
        stream: null,
        capturedImage: @entangle($model),
        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                this.$refs.video.srcObject = this.stream;
            } catch (e) {
                alert('Camera access denied or unavailable. Please allow camera permission to capture this photo.');
            }
        },
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },
        capture() {
            const video = this.$refs.video;
            const maxDim = 1280;
            let w = video.videoWidth, h = video.videoHeight;
            if (w > maxDim || h > maxDim) {
                const scale = maxDim / Math.max(w, h);
                w = Math.round(w * scale);
                h = Math.round(h * scale);
            }
            const canvas = this.$refs.canvas;
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(video, 0, 0, w, h);
            this.capturedImage = canvas.toDataURL('image/jpeg', 0.8);
            this.stopCamera();
        },
        retake() {
            this.capturedImage = null;
            this.startCamera();
        }
    }"
    x-on:livewire:navigating.window="stopCamera"
    class="space-y-2"
>
    <label class="block text-sm font-semibold text-stone-700">{{ $label }}</label>

    <template x-if="!capturedImage && !stream">
        <button type="button" x-on:click="startCamera" class="w-full py-6 border-2 border-dashed border-stone-300 rounded-xl text-sm text-stone-500 hover:border-amber-400 hover:text-amber-600 transition-colors">
            📷 Open Camera
        </button>
    </template>

    <template x-if="stream && !capturedImage">
        <div class="space-y-2">
            <video x-ref="video" x-init="$watch('stream', v => v && $nextTick(() => $refs.video.play()))" autoplay playsinline muted class="w-full rounded-xl bg-black aspect-video object-cover"></video>
            <div class="flex gap-2">
                <button type="button" x-on:click="capture" class="flex-1 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors">Capture</button>
                <button type="button" x-on:click="stopCamera" class="px-4 py-2 border border-stone-200 rounded-xl text-sm text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
            </div>
        </div>
    </template>

    <template x-if="capturedImage">
        <div class="space-y-2">
            <img :src="capturedImage" class="w-full rounded-xl border border-stone-200 aspect-video object-cover">
            <button type="button" x-on:click="retake" class="w-full py-2 border border-stone-200 rounded-xl text-sm text-stone-600 hover:bg-stone-50 transition-colors">Retake Photo</button>
        </div>
    </template>

    <canvas x-ref="canvas" class="hidden"></canvas>
</div>
