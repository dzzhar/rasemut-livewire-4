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
                    ->required(),
                TextInput::make('employee_code')
                    ->label('Nomor Karyawan')
                    ->required()
                    ->unique(
                        table: 'employees',
                        column: 'employee_code',
                        ignorable: fn($record) => $record
                    ),
                TextInput::make('user_email')
                    ->label('Email')
                    ->email()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignorable: fn($record) => $record?->user
                    )
                    ->required(),
                TextInput::make('user_password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required(fn($record) => $record === null),
                Select::make('user_role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'employee' => 'Employee'
                    ])
                    ->required(),
                Select::make('position_id')
                    ->relationship('position', 'position_name')
                    ->label('Jabatan')
                    ->required(),
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
                    ->color(fn($state) => match ($state) {
                        'employee' => 'warning',
                        'admin' => 'success'
                    }),
                TextEntry::make('position.position_name')
                    ->label('Jabatan'),
                TextEntry::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        0 => 'nonaktif',
                        1 => 'aktif',
                    })
                    ->color(fn($state) => match ($state) {
                        0 => 'danger',
                        1 => 'success'
                    }),
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
            ->columns([
                TextColumn::make('fullname')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee_code')
                    ->label('Nomor Karyawan')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('position.position_name')
                    ->label('Jabatan'),
                TextColumn::make('user.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'employee' => 'warning',
                        'admin' => 'success'
                    })
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                //
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
                    ->using(fn(Employee $record, array $data) => app(EmployeeService::class)->update($record, $data)),
                DeleteAction::make()->action(fn(Employee $record) => app(EmployeeService::class)->delete($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployees::route('/'),
        ];
    }
}
