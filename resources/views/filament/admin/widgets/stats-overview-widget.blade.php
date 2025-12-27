<x-filament-widgets::widget>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->getCachedStats() as $stat)
            @php
                $color = $stat->getColor() ?? 'gray';
                $icon = $stat->getDescriptionIcon();
                $description = $stat->getDescription();
                $value = $stat->getValue();
                $label = $stat->getLabel();
                
                // Map colors to Tailwind classes
                $colorClasses = match ($color) {
                    'success' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                    'danger' => 'text-rose-400 bg-rose-400/10 border-rose-400/20',
                    'warning' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
                    'primary' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                    default => 'text-gray-400 bg-gray-400/10 border-gray-400/20',
                };

                $iconColor = match ($color) {
                    'success' => 'text-emerald-400',
                    'danger' => 'text-rose-400',
                    'warning' => 'text-amber-400',
                    'primary' => 'text-blue-400',
                    default => 'text-gray-400',
                };
            @endphp

            <div class="relative overflow-hidden rounded-2xl bg-gray-900 p-6 shadow-lg border border-gray-800 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-gray-700 group">
                <!-- Glow Effect -->
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-gradient-to-br from-white/5 to-white/0 blur-2xl transition duration-500 group-hover:from-white/10"></div>
                
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-400">{{ $label }}</p>
                        <h3 class="mt-2 text-3xl font-bold text-white tracking-tight">{{ $value }}</h3>
                    </div>
                    
                    <div class="rounded-xl p-3 {{ $colorClasses }}">
                        @if ($icon)
                            <x-filament::icon
                                :icon="$icon"
                                class="h-6 w-6 {{ $iconColor }}"
                            />
                        @endif
                    </div>
                </div>

                @if ($description)
                    <div class="mt-4 flex items-center gap-2">
                        @if ($stat->getDescriptionIcon())
                            <x-filament::icon
                                :icon="$stat->getDescriptionIcon()"
                                class="h-4 w-4 {{ $iconColor }}"
                            />
                        @endif
                        <span class="text-xs font-medium {{ $iconColor }}">
                            {{ $description }}
                        </span>
                    </div>
                @endif
                
                <!-- Bottom Gradient Line -->
                <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-transparent via-{{ $color == 'primary' ? 'blue' : ($color == 'warning' ? 'amber' : ($color == 'success' ? 'emerald' : 'gray')) }}-500/50 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
