<x-shop-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rebate Details #{{ $rebate->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('rebates.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Back to Rebates
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Rebate Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rebate ID</dt>
                                <dd class="text-sm text-gray-900">#{{ $rebate->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Purchase ID</dt>
                                <dd class="text-sm text-gray-900">#{{ $rebate->purchase_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Product</dt>
                                <dd class="text-sm text-gray-900">{{ $rebate->purchase->product->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantity Added Back</dt>
                                <dd class="text-lg font-semibold text-green-600">{{ number_format($rebate->quantity) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Unit Purchase Price</dt>
                                <dd class="text-sm text-gray-900">₦{{ number_format($rebate->unit_purchase_price, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Cost</dt>
                                <dd class="text-lg font-semibold text-blue-600">₦{{ number_format($rebate->total_cost, 2) }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                <dd class="text-sm text-gray-900">{{ $rebate->creator->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="text-sm text-gray-900">{{ $rebate->created_at->format('M d, Y \a\t g:i A') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Current Inventory Quantity</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($rebate->purchase->quantity ?? 0, 1) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Note</dt>
                                <dd class="text-sm text-gray-900">{{ $rebate->note ?: 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('rebates.create', ['purchase_id' => $rebate->purchase_id]) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Record Another Rebate
                    </a>
                    <form method="POST" action="{{ route('rebates.destroy', $rebate) }}"
                        onsubmit="return confirm('Delete this rebate and revert its stock increase?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            Delete Rebate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>

