<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Settings extends Page
{


    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'الإعدادات العامة';
    protected static ?string $title = 'الإعدادات العامة';
    protected static ?string $slug = 'settings';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.admin.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('معلومات التطبيق')
                    ->description('إعدادات الهوية الأساسية للتطبيق')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('site_name')
                            ->label('اسم التطبيق')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('site_description')
                            ->label('وصف التطبيق')
                            ->rows(3),
                        \Filament\Forms\Components\FileUpload::make('site_logo')
                            ->label('شعار التطبيق')
                            ->image()
                            ->directory('settings')
                            ->columnSpanFull(),
                    ])->columns(2),

                \Filament\Forms\Components\Section::make('المظهر والتخصيص')
                    ->description('تخصيص ألوان وواجهة التطبيق')
                    ->icon('heroicon-o-paint-brush')
                    ->schema([
                        \Filament\Forms\Components\ColorPicker::make('primary_color')
                            ->label('اللون الأساسي'),
                        \Filament\Forms\Components\Select::make('app_theme')
                            ->label('سمة التطبيق')
                            ->options([
                                'default' => 'الافتراضي',
                                'al_jabali' => 'الجبلي',
                            ])
                            ->default('default'),
                        \Filament\Forms\Components\Toggle::make('dark_mode_default')
                            ->label('تفعيل الوضع الليلي افتراضياً'),
                    ])->columns(2),

                \Filament\Forms\Components\Section::make('معلومات التواصل')
                    ->description('بيانات التواصل التي تظهر في التطبيق')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('support_email')
                            ->label('بريد الدعم الفني')
                            ->email(),
                        \Filament\Forms\Components\TextInput::make('support_phone')
                            ->label('هاتف الدعم الفني')
                            ->tel(),
                        \Filament\Forms\Components\TextInput::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        \Filament\Notifications\Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
