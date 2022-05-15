<?php

namespace App\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Traits\Figures;

use App\Models\Property;
use App\Models\User;
use App\Models\Space;

class LandlordSpaces extends DataTableComponent
{
    use Figures;
    public bool $paginationEnabled = true;
    public bool $showPagination = true;
    public string $pageName = 'spaces';
    public User $landlord;
    public array $spacesToFetch = array(
        0 => 'for the owner to view',
        1 => 'for a specific landlord',
        2 => 'for selected landlords'
    );

    public int $spacesToFetchSelected = 0;

    

    public function columns(): array
    {
        return [
            Column::make('Space Name', 'spacename')
                    ->sortable()
                    ->searchable(),
            Column::make('Space Code', 'spaceid')
                    ->searchable(),
            // Column::make('Status', 'occupied')
            //         ->format(function($value){
            //             $badge = $value ? 'badge-success' : 'badge-danger';
            //             $badgeLabel = $value ? 'Occupied' : 'Vacant';
                        
            //             return '<span class="badge '. $badge .'">'. $badgeLabel .'</span>';
            //         })
            //         ->asHtml(),
            // Column::make('Tenant', 'status')
            //         ->format(function($value, $column, $row){
            //             return $row->firstname . " " . $row->lastname;
            //         }),
            Column::make('Property', 'property')
                    ->searchable(),
            Column::make('Daily Charge', 'rentprice')
                    ->sortable()
                    ->format(function($value){
                        return $this->ugx($value);
                    }),
            Column::make('Outstanding Charges', 'balance')
                    ->sortable()
                    ->format(function($value, $column, $row){
                        return $this->ugx($value);
                    }),
            Column::make('Action', 'spaceid')
                ->format(function($value, $column, $row) {
                    
                    return '<a class="ws-normal pointer btn btn-xs" href="'. url("/spaces/space/$value") .'"><i class="icons icon-options"></i></a>';
                })
                ->asHtml()
        ];
    }

    public function query(): Builder
    {
        $query = Space::query()
                        ->selectRaw('spaces.spacename, spaces.spaceid, spaces.occupied, spaces.rentprice,
                                    spaces.balance, propertys.property, users.firstname, users.lastname, users.id')
                        
                        
                        ->leftjoin('propertys', 'spaces.propertyid', '=', 'propertys.propertyid') 
                        ->leftjoin('users', 'spaces.tenantid', '=', 'users.id');

        switch ($this->spacesToFetchSelected) {
            case 1:
                $query->where('propertys.userid', $this->landlord->id);
            break;
        }
        return $query;

    }

    public function setTableDataClass(Column $column, $row): ?string
    {
        
        $class = null;
        if($column->column() === 'balance' && $row->balance < 0)
        {
            $class =  'text-danger';
            return $class;
        }

        if($column->column() === 'spaceid') {
            $class = 'actions-hover';
            return $class;
        }

        if($class == null){return null;}
        
    }

    public function mount(User $landlord, $fetchFor)
    {
        $this->landlord = $landlord;
        $this->spacesToFetchSelected = $fetchFor;
    }
}
