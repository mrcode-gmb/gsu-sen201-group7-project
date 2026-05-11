<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-blue-700">Logged Activities</p>
            <p class="mt-3 text-3xl font-bold text-blue-950">{{ number_format($activities->total()) }}</p>
            <p class="mt-2 text-sm text-blue-600">Matching records for the current filter set.</p>
        </div>
        <div class="rounded-lg border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-blue-700">Current Page</p>
            <p class="mt-3 text-3xl font-bold text-blue-950">{{ number_format($activities->count()) }}</p>
            <p class="mt-2 text-sm text-blue-600">Rows visible on this page.</p>
        </div>
        <div class="rounded-lg border border-blue-100 bg-gradient-to-br from-blue-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-blue-700">Available Actions</p>
            <p class="mt-3 text-3xl font-bold text-blue-950">{{ number_format($actions->count()) }}</p>
            <p class="mt-2 text-sm text-blue-600">Distinct activity events captured by the system.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Activities</h3>
        <form method="GET" action="{{ route($routeName) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Description or subject"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            @if($showBusinessFilter)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Business</label>
                    <select name="business_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Businesses</option>
                        @foreach($businesses as $business)
                            <option value="{{ $business['id'] ?? $business->id }}" {{ (string) request('business_id') === (string) ($business['id'] ?? $business->id) ? 'selected' : '' }}>
                                {{ $business['name'] ?? $business->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route($routeName) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changed Fields</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        @php
                            $properties = $activity->properties instanceof \Illuminate\Support\Collection
                                ? $activity->properties->toArray()
                                : (array) $activity->properties;
                            $businessId = $properties['business_id'] ?? null;
                            $ipAddress = $properties['ip_address'] ?? 'N/A';
                            $changedFields = collect(array_unique(array_merge(
                                array_keys($properties['attributes'] ?? []),
                                array_keys($properties['old'] ?? [])
                            )))
                                ->reject(fn ($field) => in_array($field, ['updated_at', 'created_at', 'password', 'remember_token'], true))
                                ->values();
                            $subjectName = class_basename((string) ($activity->subject_type ?? '')) ?: ucfirst((string) $activity->log_name);
                            $actionName = ucfirst((string) ($activity->event ?: $activity->description));
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at->format('M d, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ optional($activity->causer)->name ?? 'System' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ optional($activity->causer)->email ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $businessId ? ($businessLookup[$businessId] ?? 'Business #' . $businessId) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if(strtolower($actionName) === 'created') bg-green-100 text-green-800
                                    @elseif(strtolower($actionName) === 'updated') bg-blue-100 text-blue-800
                                    @elseif(strtolower($actionName) === 'deleted') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $actionName }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $subjectName ?: 'System' }}</div>
                                @if($activity->subject_id)
                                    <div class="text-xs text-gray-500">ID: {{ $activity->subject_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($changedFields->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($changedFields->take(4) as $field)
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-50 text-blue-700">{{ $field }}</span>
                                        @endforeach
                                        @if($changedFields->count() > 4)
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">+{{ $changedFields->count() - 4 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500">No field diff captured</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ipAddress }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No activity logs found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
