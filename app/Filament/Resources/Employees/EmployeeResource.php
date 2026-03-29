<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\ManageEmployees;
use App\Models\Employee;
use App\Services\EmployeeService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel, $modelLabel = "Data Karyawan";
    protected static string|UnitEnum|null $navigationGroup = "Master";
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('fullname')
                    ->label('Nama Lengkap')
                    ->placeholder('Masukkan nama lengkap')
                    ->required(),
                TextInput::make('employee_code')
                    ->label('Nomor Karyawan')
                    ->placeholder('Masukkan nomor karyawan')
                    ->unique(
                        table: 'employees',
                        column: 'employee_code',
                        ignorable: fn($record) => $record
                    )
                    ->required(),
                Select::make('user_role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'employee' => 'Karyawan'
                    ])
                    ->selectablePlaceholder(false)
                    ->required(),
                Select::make('position_id')
                    ->relationship('position', 'name')
                    ->label('Jabatan')
                    ->selectablePlaceholder(false)
                    ->required(),
                TextInput::make('user_email')
                    ->label('Email')
                    ->placeholder('Masukkan email karyawan terdaftar')
                    ->email()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignorable: fn($record) => $record?->user
                    )
                    ->required(),
                TextInput::make('user_password')
                    ->label('Kata Sandi')
                    ->placeholder('Masukkan kata sandi untuk karyawan')
                    ->password()
                    ->revealable()
                    ->required(fn($record) => $record === null),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('fullname')
                    ->label('Nama Lengkap'),
                TextEntry::make('user.email')
                    ->label('Email'),
                TextEntry::make('employee_code')
                    ->label('Nomor Karyawan'),
                TextEntry::make('user.role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'admin' ? 'admin' : 'karyawan')
                    ->color(fn($state) => $state === 'admin' ? 'success' : 'primary'),
                TextEntry::make('position.name')
                    ->label('Jabatan'),
                TextEntry::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 0 ? 'nonaktif' : 'aktif')
                    ->color(fn($state) => $state === 0 ? 'warning' : 'success'),
                TextEntry::make('user.created_at')
                    ->datetime('l, d M Y H:i:s')
                    ->suffix(' WIB'),
                TextEntry::make('user.updated_at')
                    ->datetime('l, d M Y H:i:s')
                    ->suffix(' WIB'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(10)
            ->modifyQueryUsing(function (Builder $query) {
                $query
                    ->leftJoin('users', 'employees.user_id', '=', 'users.id')
                    ->leftJoin('sessions', 'users.id', '=', 'sessions.user_id')
                    ->orderByRaw('
                    CASE 
                        WHEN sessions.user_id IS NOT NULL THEN 0
                        ELSE 1
                    END
                ')
                    ->orderBy('employees.is_active', 'desc')
                    ->orderBy('employees.fullname', 'asc')
                    ->select('employees.*')
                    ->groupBy('employees.id');
            })
            ->columns([
                TextColumn::make('fullname')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('employee_code')
                    ->label('Nomor Karyawan')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->disabled(fn($record) => $record->user?->hasActiveSession())
                    ->tooltip(
                        fn($record) =>
                        $record->user?->hasActiveSession()
                            ? 'User sedang login dan tidak dapat menonaktifkan'
                            : null
                    ),
                TextColumn::make('position.name')
                    ->label('Jabatan'),
                TextColumn::make('user.role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'admin' ? 'admin' : 'karyawan')
                    ->color(fn($state) => $state === 'admin' ? 'success' : 'primary')
                    ->alignCenter(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->fillForm(fn(Employee $record) => [
                        'fullname' => $record->fullname,
                        'employee_code' => $record->employee_code,
                        'user_email' => $record->user->email,
                        'user_role' => $record->user->role,
                    ])
                    ->using(
                        fn(Employee $record, array $data) => app(EmployeeService::class)->update($record, $data)
                    ),
                DeleteAction::make()
                    ->action(fn(Employee $record) => app(EmployeeService::class)->delete($record))
                    ->hidden(fn($record) => $record->user?->hasActiveSession())
                    ->tooltip(
                        fn($record) =>
                        $record->user?->hasActiveSession()
                            ? 'User sedang login dan tidak dapat menghapus'
                            : null
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployees::route('/'),
        ];
    }
}
