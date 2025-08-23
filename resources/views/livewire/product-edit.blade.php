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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Product</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-md mx-auto py-8 px-4">
        <div class="space-y-6">
            <!-- Loading State -->
            @if($loading && empty($product))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                    <div class="animate-spin inline-block w-6 h-6 border-[3px] border-current border-t-transparent text-indigo-600 rounded-full"></div>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Loading product...</p>
                </div>
            @else
                <!-- Success Message -->
                @if($success)
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-green-800 dark:text-green-200 text-sm">{{ $success }}</p>
                    </div>
                @endif

                <!-- Error Message -->
                @if($error)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-red-800 dark:text-red-200 text-sm">{{ $error }}</p>
                    </div>
                @endif

                <!-- Product Form -->
                <form wire:submit="updateProduct" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h2>
                        
                        <div class="space-y-4">
                            <!-- Product Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Name *
                                </label>
                                <input 
                                    wire:model="name"
                                    type="text" 
                                    id="name"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                @error('name') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- SKU (Read-only) -->
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    SKU
                                </label>
                                <input 
                                    type="text" 
                                    id="sku"
                                    value="{{ $sku }}"
                                    readonly
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
                                >
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="description"
                                    id="description"
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                ></textarea>
                                @error('description') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Price *
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input 
                                        wire:model="price"
                                        type="number" 
                                        step="0.01"
                                        id="price"
                                        class="block w-full pl-7 pr-12 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                    >
                                </div>
                                @error('price') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stock Quantity *
                                </label>
                                <input 
                                    wire:model="stock_quantity"
                                    type="number" 
                                    id="stock_quantity"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                >
                                @error('stock_quantity') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Suppliers Information (Read-only) -->
                    @if(count($suppliers) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Suppliers</h3>
                            <div class="space-y-3">
                                @foreach($suppliers as $supplier)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $supplier['name'] }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier['email'] }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $supplier['phone'] }}</p>
                                        @if(!empty($supplier['pivot']['supplier_sku']))
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Supplier SKU: {{ $supplier['pivot']['supplier_sku'] }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Update Button -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove>Update Product</span>
                            <span wire:loading>Updating...</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
