<div>
    @props(['title', 'value', 'icon', 'color' => 'green'])

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                @if($icon)
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-{{ $color }}-500 rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $icon !!}
                        </svg>
                    </div>
                </div>
                @endif
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">{{ $title }}</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $value }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
</div>