<?php

namespace App\Filament\Pages;

use App\Models\AttendanceSetting;
use BackedEnum;
use Fahiem\FilamentPinpoint\Pinpoint;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;
use UnitEnum;

class AttendanceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.attendance-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    protected static ?string $navigationLabel = 'Pengaturan Presensi';
    protected ?string $heading = 'Pengaturan Presensi';
    protected ?string $subheading = 'Pengaturan lokasi, jam kehadiran, kuota cuti, dan presensi karyawan';
    protected static string|UnitEnum|null $navigationGroup = "Master";
    protected static ?int $navigationSort = 3;

    public function getTitle(): string
    {
        return 'Pengaturan Absensi';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $setting = AttendanceSetting::first();

        if (!$setting) {
            $setting = AttendanceSetting::create([
                'latitude' => -6.175392,
                'longitude' => 106.794952,
                'radius_attendance' => 5,
                'check_in_setting' => '08:00:00',
                'check_out_setting' => '17:00:00',
                'leave_quota' => 12,
                'overtime_tolerance' => 5,
            ]);
        }

        $this->form->fill($setting->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Pinpoint::make('location')
                    ->label('Lokasi Presensi')
                    ->radiusField('radius_attendance')
                    ->latField('latitude')
                    ->lngField('longitude')
                    ->height(300)
                    ->searchable(false)
                    ->defaultZoom(20)
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->label('Latitude')
                    ->readOnly(),
                TextInput::make('longitude')
                    ->label('Longitude')
                    ->readOnly(),
                TextInput::make('radius_attendance')
                    ->label('Radius Presensi')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('meter')
                    ->columnSpan(2),
                TextInput::make('check_in_setting')
                    ->label('Jam Masuk')
                    ->suffix('WIB'),
                TextInput::make('check_out_setting')
                    ->label('Jam Keluar')
                    ->suffix('WIB'),
                TextInput::make('leave_quota')
                    ->label('Kuota Cuti')
                    ->numeric()
                    ->suffix('hari/tahun'),
                TextInput::make('overtime_tolerance')
                    ->label('Toleransi Lembur')
                    ->numeric()
                    ->suffix('menit'),
            ])
            ->columns(4);
    }


    public function save(): void
    {
        $data = $this->form->getState();

        $setting = AttendanceSetting::first();

        if (!$setting) {
            AttendanceSetting::create($data);
        } else {
            $setting->update($data);
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->body('Pengaturan presensi telah diperbarui. Silakan refresh halaman untuk melihat perubahan map.')
            ->success()
            ->send();
    }
}
