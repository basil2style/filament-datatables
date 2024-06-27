<?php

namespace App\Filament\Resources;

use App\Exports\SutdentExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->autofocus()->placeholder('Enter Student Name'),
                TextInput::make('email')->required()->placeholder('Enter Email'),
                TextInput::make('phone_number')->required()->placeholder('Enter Phone Number'),
                TextInput::make('address')->required()->placeholder('Enter Address'),
                //reactive() make the form re-render when the value changes
                Select::make('class_id')->relationship('classes', 'name')->reactive(),
                Select::make('section_id')->options(function (callable $get) {
                    $classId = $get('class_id');

                    if ($classId) {
                        return Section::where('class_id', $classId)->pluck('name', 'id')->toArray();
                    }
                })
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('phone_number')->sortable()->searchable(),
                TextColumn::make('address')->sortable()->searchable()->wrap(),
                TextColumn::make('classes.name')->sortable()->searchable(),
                TextColumn::make('section.name')->sortable()->searchable(),
                TextColumn::make('created_at')->date()->sortable(),
                TextColumn::make('updated_at')->date()->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => (new SutdentExport($records))->download('students.xlsx'))
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
