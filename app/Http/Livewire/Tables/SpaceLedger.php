<?php

namespace App\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;
use App\Traits\Figures;

use App\Models\User;
use App\Models\RentPayment;
use App\Models\Space;
use App\Models\Property;
use App\Models\Ledger;

class SpaceLedger extends DataTableComponent
{
    use Figures;
    public bool $paginationEnabled = true;
    public bool $showPagination = true;
    public string $pageName = 'spaceledger';
    public Space $space;
    public bool $showSearch = false;
    protected $listeners = ['refreshComponent' => '$refresh'];


    public function columns(): array
    {
        return [
            Column::make('Date', 'date')
                    ->format(function($value){
                        return $value ? $value->format('D, d M Y H:i:s') : $value;
                    }),
            Column::make('Description', 'transactiontypeid')
                        ->format(function($value, $column, $row){
                            if($value == 1){
                                return $row->transactiontype . " ($row->startdate)";
                            }
                            else{
                                return $row->transactiontype;
                            }
                            
                        }),  
            Column::make('Debit', 'debit')
                    ->format(function($value){
                        return $this->ugx($value);
                    }),
            Column::make('Credit', 'credit')
                    ->format(function($value){
                        return $this->ugx($value);
                    }),
            Column::make('Balance', 'balance')
                    ->format(function($value){
                        return $this->ugx($value);
                    })
        ];
    }

    public function query(): Builder
    {
        $query = Ledger::query()
        ->select('ledgerid', 'ledger.tenureid', 'ledger.date', 'ledger.rentalperiodid',
                                      'ledger.transactiontypeid', 'ledger.debit', 'ledger.credit', 'ledger.balance',
                                      'rentalperiods.startdate', 'rentalperiods.enddate', 'transactiontypes.transactiontype')
        ->where('tenures.spaceid', $this->space->spaceid)
        ->orderBy('ledger.date', 'desc')
        ->leftjoin('rentalperiods', 'ledger.rentalperiodid', '=', 'rentalperiods.rentalperiodid')
        ->leftjoin('transactiontypes', 'ledger.transactiontypeid', '=', 'transactiontypes.transactiontypeid')
        ->leftjoin('tenures', 'ledger.tenureid', '=', 'tenures.tenureid')
       
        ->when($this->getFilter('startdate'), fn ($query, $startdate) => $query->where('date', '>=', $startdate))
        ->when($this->getFilter('enddate'), fn ($query, $enddate) => $query->where('date', '<=', $enddate));

        return $query;

    }

    public function filters(): array
    {
        return [
            'startdate' => Filter::make('Start Date')
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ]),
            'enddate' => Filter::make('End Date')
                ->date([
                    'min' => now()->subYear()->format('Y-m-d'), // Optional
                    'max' => now()->format('Y-m-d') // Optional
                ]),
        ];
    }


    public function mount(Space $space)
    {
        $this->space = $space;
    }  

    public function setTableDataClass(Column $column, $row): ?string
    {
        $class = null;
        if($column->column() === 'balance' && $row->balance < 0)
        {
            $class =  'text-danger';
            return $class;
        }

        if($class == null){return null;}
    }
    
    
}
