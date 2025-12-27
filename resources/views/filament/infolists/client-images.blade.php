<div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4">
    @if(is_array($getState()) && count($getState()) > 0)
        @foreach($getState() as $image)
            <div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                
                {{-- Image Container --}}
                <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                    <img 
                        src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" 
                        alt="Client Image" 
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                        loading="lazy"
                    >
                    
                    {{-- Overlay Gradient (Subtle) --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </div>
                
                {{-- Actions Footer --}}
                <div class="flex items-center justify-between gap-2 p-3">
                    {{-- Preview Button --}}
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($image) }}" 
                        target="_blank" 
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-primary-50 py-2 text-sm font-semibold text-primary-600 transition-colors hover:bg-primary-100 dark:bg-primary-400/10 dark:text-primary-400 dark:hover:bg-primary-400/20"
                        title="معاينة"
                    >
                        @svg('heroicon-m-eye', 'w-4 h-4')
                        <span>معاينة</span>
                    </a>

                    {{-- Download Button --}}
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($image) }}" 
                        download
                        class="flex items-center justify-center rounded-xl bg-gray-50 p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-700/50 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                        title="تحميل"
                    >
                        @svg('heroicon-m-arrow-down-tray', 'w-5 h-5')
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 py-12 text-center dark:border-gray-700">
            <div class="mb-4 rounded-full bg-gray-50 p-4 dark:bg-gray-800">
                @svg('heroicon-o-photo', 'w-8 h-8 text-gray-400')
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">لا توجد صور</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">لم يتم رفع أي صور لهذا العميل بعد.</p>
        </div>
    @endif
</div>
