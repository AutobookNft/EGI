<div class="animation-container">
    <div id="dynamic-3d-container" class="w-full h-full bg-gradient-to-br from-gray-900/70 via-purple-950/70 to-black/70 rounded-2xl border border-purple-500/30 overflow-hidden relative">
        <!-- Elemento di caricamento -->
        <div id="loading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 transition-opacity duration-1000 backdrop-blur-sm z-10">
            <div class="text-white flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-t-purple-500 border-purple-300/20 rounded-full animate-spin mb-4"></div>
                <div class="text-purple-200 font-medium tracking-wide">Caricamento EGI...</div>
            </div>
        </div>

        <!-- Canvas per Three.js -->
        <canvas id="webgl-canvas" class="w-full h-full relative z-0 rounded-2xl"></canvas>

        <!-- Etichetta informativa -->
        <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
            3D Preview
        </div>
    </div>
</div>
