<?php

namespace App\Http\Livewire\Pages;

use Livewire\Component;

use App\Models\Property;

use App\Traits\CustomWithPagination;
use Illuminate\Support\Facades\Auth;


use App\Models\User;
use App\Models\Rentpayment;
use App\Models\Space;

use Carbon\Carbon;

class RevenuesPage extends Component
{
    use CustomWithPagination;
    public $pageName = 'paymentss';
    protected $paginationTheme = 'bootstrap';
    //public $tenant;
    public $perPage = 10;
    protected $payments;
    public User $user;
    public $propertys;
    public $landlords;
    public $spaces;
    public $time;

    public function mount()
    {
        $this->user = User::select('*')->where('id', Auth::user()->id)->first();
    }
    public function render()
    {
        return view('livewire.pages.revenues-page',
        [
            'user' => $this->user

        ]
    );
    }
}
