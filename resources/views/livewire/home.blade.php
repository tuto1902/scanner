<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Scanner App</h1>
                <button
                    wire:click="logout"
                    class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                >
                    Logout
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto py-8 px-4">
        <div class="space-y-8">
            <!-- Barcode Scanner Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Scan Barcode</h2>

                <!-- Scanner Area -->
                <div
                    x-data="barcodeScanner()"
                    x-init="init()"
                    class="space-y-4"
                >
                    <!-- Placeholder when not scanning -->
                    <div
                        class="w-full h-64 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center"
                        x-show="!isScanning"
                    >
                        <p class="text-gray-500 dark:text-gray-400 text-center">
                            Camera will appear here when scanning
                        </p>
                    </div>

                    <!-- Scanner element that shows during scanning -->
                    <div
                        id="scanner"
                        class="w-full h-64 rounded-lg overflow-hidden"
                        x-show="isScanning"
                    ></div>

                    <div class="flex gap-3">
                        <button
                            @click="startScanner()"
                            :disabled="isScanning"
                            class="flex-1 bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-text="isScanning ? 'Scanning...' : 'Start Scanner'"></span>
                        </button>

                        <button
                            @click="stopScanner()"
                            x-show="isScanning"
                            class="bg-red-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-red-700"
                        >
                            Stop
                        </button>
                    </div>

                    <div x-show="scannedCode" class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-200">
                            Scanned: <span x-text="scannedCode" class="font-mono font-bold"></span>
                        </p>
                    </div>
                </div>

                <!-- Search Button -->
                @if($scannedBarcode)
                    <div class="mt-4">
                        <button
                            wire:click="searchByBarcode"
                            wire:loading.attr="disabled"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove>Search Product</span>
                            <span wire:loading>Searching...</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Search Product Button -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Or Search Manually</h2>
                <button
                    wire:click="goToSearch"
                    class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700"
                >
                    Search Product by Name
                </button>
            </div>

            <!-- Error Message -->
            @if($error)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-200 text-sm">{{ $error }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function barcodeScanner() {
    return {
        isScanning: false,
        scannedCode: '',

        init() {
            // Listen for scanner events
        },

        async startScanner() {
            this.isScanning = true;
            try {
                await Alpine.store('app').startScanner(
                    'scanner',
                    (decodedText, decodedResult) => {
                        this.stopScanner();
                        this.isScanning = false;
                        this.$wire.dispatch('barcode-scanned', {text: decodedText});
                        console.log(`Scan result: ${decodedText}`, decodedResult);
                    },
                    (error) => {
                        console.log('Scanner error:', error);
                    }
                );
            } catch (err) {
                this.isScanning = false;
                console.error('Failed to start scanner:', err);
            }
        },

        async stopScanner() {
            await Alpine.store('app').stopScanner();
            this.isScanning = false;
        }
    }
}
</script>
