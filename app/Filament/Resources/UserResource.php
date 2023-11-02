<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\ProjectCallType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas-user-gear';
    protected static ?int $navigationSort    = 10;

    public static function form(Form $form): Form
    {
        $roles = Role::all()->pluck('name', 'id');
        $projectCallTypes = ProjectCallType::all()
            ->mapWithKeys(fn (ProjectCallType $projectCallType) => [$projectCallType->id => $projectCallType->label_short]);

        $isManager = fn ($roleValues) => ($roles[$roleValues[0] ?? ''] ?? '') === 'manager';

        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('attributes.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(__('attributes.phone'))
                    ->email()
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label(__('attributes.role'))
                    ->options($roles->map(fn ($r) => __('admin.roles.' . $r)))
                    ->reactive(),
                Forms\Components\Select::make('projectCallTypes')
                    ->relationship('projectCallTypes', 'label_short')
                    ->label(__('attributes.managed_types'))
                    ->options($projectCallTypes)
                    ->multiple()
                    ->required(fn (Get $get) => $isManager($get('roles')))
                    ->hidden(fn (Get $get) => !$isManager($get('roles')))
                    // ->disabled(fn (Get $get) => !$isManager($get('roles')))
                    ->key('projectCallTypes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('attributes.first_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label(__('attributes.last_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roleName')
                    ->label(__('attributes.role'))
                    ->formatStateUsing(function (string $state, User $record) {
                        $suffix = '';
                        if ($record->roleName === 'manager') {
                            $projectCallTypes = $record->projectCallTypes->map(fn ($t) => $t->label_short)->join(' ; ');
                            $suffix = ' (' . $projectCallTypes . ')';
                        }
                        return Str::of(__('admin.roles.' . $state) . $suffix)
                            ->sanitizeHtml()
                            ->toHtmlString();
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrator' => 'danger',
                        'manager'       => 'warning',
                        'applicant'     => 'success',
                        'expert'        => 'info',
                        default         => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('attributes.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('attributes.phone'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('attributes.email_verified'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_active_at')
                    ->label(__('attributes.last_active_at'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('attributes.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label(__('admin.users.blocked_filter'))
                    ->placeholder(__('admin.users.all'))
                    ->trueLabel(__('admin.users.unblocked'))
                    ->falseLabel(__('admin.users.blocked')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.user_plural');
    }

    public static function getModelLabel(): string
    {
        return __('resources.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.user_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.sections.admin');
    }
}
