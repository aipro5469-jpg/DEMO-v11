<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="grid gap-6">
        @foreach ($groupedOptions as $group => $options)
            <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                <h3 class="font-bold text-lg mb-4 capitalize text-primary-600 dark:text-primary-400">
                    {{ $group }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($options as $value => $label)
                        <label class="flex items-center space-x-3 rtl:space-x-reverse cursor-pointer">
                            <x-filament::input.checkbox
                                wire:model.defer="{{ $getStatePath() }}"
                                value="{{ $value }}"
                            />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
