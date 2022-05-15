<?php

namespace App\Http\Livewire\Tables;

use App\Models\User;
use App\Models\Property;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Traits\Figures;


class Landlords extends DataTableComponent
{
    use Figures;
    public bool $paginationEnabled = true;
    public bool $showPagination = true;
    public string $pageName = 'landlords';

    

    public function columns(): array
    {
        return [
            Column::make('Landlord', 'firstname')
                ->sortable()
                ->format(function($value, $column, $row) {
                    return "$row->firstname $row->lastname";
                })
                ->searchable(),
            Column::make('E-mail', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Properties', 'propertiesCount')
                ->format(function($value, $column, $row) {
                    return Property::select('*')->where('userid' , $row->id)->count();
                    //return $value;
                }),
      //      Column::make('Spaces', 'spacesCount'),
      //      Column::make('Tenants', 'tenants'),
            // Column::make('Occupancy')
            //     ->format(function($value, $column, $row) {
            //         //$properties = Property::select('*')->where('userid' , $row->id)->count();
            //         return '<div class="progress progress-sm progress-half-rounded m-0 mt-1">
            //         <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 10%;">
            //             '. $this->percentage($row->spacesCount, $row->tenants) .' %
            //         </div>';
            //     })
            //     ->asHtml(),
            Column::make('Action', 'id')
                ->format(function($value, $column, $row) {
                    $this->setTableDataClass(Column::make('Action', 'id'), $row);
                    return '<a class="ws-normal pointer btn btn-md" href="'. url("/landlords/$value") .'"><i class="icons icon-options"></i></a>';
                })
                ->asHtml(),
        ];
    }

    public function query(): Builder
    {
        return User::query()
                    ->selectRaw(
                        'id, firstname, lastname, email'
                     )
                    // ->selectRaw(
                    //     'id, firstname, lastname, email, 
                    //     (select count(*) from propertys where propertys.userid = users.id) as propertiesCount, 
                    //     count(spaces.propertyid) as spacesCount, 
                    //     count(if(spaces.occupied = 1, spaces.occupied, null)) as tenants'
                    //     )
                    ->where('usercategoryid', 200)
                    // ->leftjoin('propertys', 'users.id', '=', 'propertys.userid')
                    // ->leftjoin('spaces', 'propertys.propertyid', '=', 'spaces.propertyid')
                    // ->groupBy( 'users.id' )
                    
                    ;
    }

    public function setTableDataClass(Column $column, $row): ?string
    {
        return 'actions-hover';
    }
}
