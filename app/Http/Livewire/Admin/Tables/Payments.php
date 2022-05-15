<?php

namespace App\Http\Livewire\Admin\Tables;

use Livewire\Component;
use App\Traits\CustomWithPagination;

use App\Models\User;
use App\Models\RentPayment;

class Payments extends Component
{
    use CustomWithPagination;
    public $pageName = 'paymentss';
    protected $paginationTheme = 'bootstrap';
    //public $tenant;
    public $perPage = 10;
    protected $payments;
    

    public function render()
    {
        return view('livewire.admin.tables.payments',
            [
                'payments' => $this->payments
            ]
        );
    }

    public function mount()
    {
        $this->payments = RentPayment::select('rentpaymenttxnid', 'amount', 'rentpayments.spaceid', 'status', 'date',
                                      'description', 'rentpayments.inchannelid', 'inchanneltxnid', 'inchannels.inchannel',
                                      'receiptno', 'channelinfo', 'spaces.spacename', 'propertys.property')                                
                                ->leftjoin('inchannels', 'rentpayments.inchannelid', '=', 'inchannels.inchannelid')
                                ->leftjoin('spaces', 'rentpayments.spaceid', '=', 'spaces.spaceid')
                                ->leftjoin('propertys', 'spaces.propertyid', '=', 'propertys.propertyid')
                                ->paginate($this->perPage, ['*'], $this->pageName);
    }
}
