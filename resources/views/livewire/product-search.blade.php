<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button 
                        wire:click="goBack"
                        class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                    >
                        ← Back
                    </button>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Search Products</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto py-8 px-4">
        <div class="space-y-6">
            <!-- Search Input -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Product Name
                </label>
                <div class="relative">
                    <input 
                        wire:model.live.debounce.500ms="search"
                        type="text" 
                        id="search"
                        placeholder="Type product name..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    >
                    <div wire:loading.delay class="absolute right-3 top-3">
                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                
                @if(strlen($search) > 0 && strlen($search) < 2)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Type at least 2 characters to search
                    </p>
                @endif
            </div>

            <!-- Error Message -->
            @if($error)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-red-800 dark:text-red-200 text-sm">{{ $error }}</p>
                </div>
            @endif

            <!-- Products List -->
            @if(count($products) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Search Results ({{ count($products) }})
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                            <button 
                                wire:click="selectProduct({{ $product['id'] }})"
                                class="w-full px-6 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            >
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $product['name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            SKU: {{ $product['sku'] }}
                                        </p>
                                        @if(!empty($product['description']))
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                {{ Str::limit($product['description'], 100) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="ml-4 text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($product['price'], 2) }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Stock: {{ $product['stock_quantity'] }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @elseif(strlen($search) >= 2 && !$loading)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        No products found for "{{ $search }}"
                    </p>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                        Try a different search term
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
