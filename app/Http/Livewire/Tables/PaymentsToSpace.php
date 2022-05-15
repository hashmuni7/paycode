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

class PaymentsToSpace extends DataTableComponent
{
    use Figures;
    public bool $paginationEnabled = true;
    public bool $showPagination = true;
    public string $pageName = 'spacepayments';
    public Space $space;
    public bool $showSearch = false;
    
    public function columns(): array
    {
        return [
            Column::make('Status', 'status')
                    ->format(function($value){
                        $badge = $value ? 'badge-success' : 'badge-danger';
                        $badgeLabel = $value ? 'Success' : 'Processing';
                        
                        return '<span class="badge '. $badge .'">'. $badgeLabel .'</span>';
                    })
                    ->asHtml(),
            Column::make('Date', 'date')
                    ->format(function($value){
                        return $value ? $value->format('D, d M Y H:i:s') : $value;
                    }),
            Column::make('Space Code', 'spaceid'),    
            Column::make('Amount', 'amount')
                    ->format(function($value){
                        return $this->ugx($value);
                    }),
            Column::make('Receipt No', 'rentpaymenttxnid'),
            Column::make('Property', 'property'),
            Column::make('Channel', 'inchannel'),
            Column::make('Channel Txn ID', 'inchanneltxnid'),
            Column::make('Channel Memo', 'channelinfo')
        ];
    }

    public function filters(): array
{
    // $spaces = Space::selectRaw('spaces.spacename, spaces.spaceid, spaces.occupied, spaces.rentprice,
    //                             spaces.balance, users.firstname, users.lastname, users.id')
    //                 ->where('propertys.propertyid', $this->property->propertyid)
                    
    //                 ->leftjoin('propertys', 'spaces.propertyid', '=', 'propertys.propertyid') 
    //                 ->leftjoin('users', 'spaces.tenantid', '=', 'users.id')->get();

    // $spacesEditted = ['' => 'Any'];
    // foreach ($spaces as $key => $value) {
    //     $spacesEditted["$value->spaceid"] = "$value->spaceid";
    // }

   
    return [
        // 'spaceid' => Filter::make('Space Code')
        //     ->select($spacesEditted),
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

    public function query(): Builder
    {
        $query = RentPayment::query()
        ->select('rentpaymenttxnid', 'amount', 'rentpayments.spaceid', 'status', 'date',
                              'description', 'rentpayments.inchannelid', 'inchanneltxnid', 'inchannels.inchannel',
                              'receiptno', 'channelinfo', 'spaces.spacename', 'propertys.property')
        ->where('spaces.spaceid', $this->space->spaceid)
        ->leftjoin('inchannels', 'rentpayments.inchannelid', '=', 'inchannels.inchannelid')
        ->leftjoin('spaces', 'rentpayments.spaceid', '=', 'spaces.spaceid')
        ->leftjoin('propertys', 'spaces.propertyid', '=', 'propertys.propertyid')
        ->orderby('date', 'desc')
       
        ->when($this->getFilter('startdate'), fn ($query, $startdate) => $query->where('date', '>=', $startdate))
        ->when($this->getFilter('enddate'), fn ($query, $enddate) => $query->where('date', '<=', $enddate));

        // if($this->hasFilter('spaceid')) 
        // {
            
        //     $query->where('spaces.spaceid', $this->getFilter('spaceid'));
        // }
        
        return $query;

    }

    public function setTableDataClass(Column $column, $row): ?string
    {
        return 'actions-hover';
    }

    public function mount(Space $space)
    {
        $this->space = $space;
    }
    // public function render()
    // {
    //     return view('livewire.tables.payments-to-space', 
    //     [
    //         'spacePayments' => $this->spacePayments
    //     ]);
    // }

    // public function mount()
    // {
    //     $this->spacePayments = RentPayment::select('rentpaymenttxnid', 'amount', 'rentpayments.spaceid', 'status', 'date',
    //                                   'description', 'rentpayments.inchannelid', 'inchanneltxnid', 'inchannels.inchannel',
    //                                   'receiptno', 'channelinfo', 'spaces.spacename', 'propertys.property')
    //                             ->where('rentpayments.spaceid', $this->space->spaceid)
    //                             ->leftjoin('inchannels', 'rentpayments.inchannelid', '=', 'inchannels.inchannelid')
    //                             ->leftjoin('spaces', 'rentpayments.spaceid', '=', 'spaces.spaceid')
    //                             ->leftjoin('propertys', 'spaces.propertyid', '=', 'propertys.propertyid')
    //                             ->orderBy('date', 'desc')
    //                             ->paginate($this->perPage, ['*'], $this->pageName);
    // }
}
