<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;


use App\Traits\Models;
use App\Models\User;

class EditLandlord extends Component
{
    use Models;
    public $landlordid;
    public $landlord;
    public $landlordFirstname = '';
    public $landlordLastname = '';
    public $landlordOthernames = '';
    public $landlordSex = '';
    public $landlordStatus = '';
    public $landlordPhone = '';
    public $landlordEmail = '';
    public $landlordPassword = '';
    protected $rules = [
        'landlordFirstname' => 'required',
        'landlordLastname' => 'required',
        'landlordSex' => 'required',
        'landlordStatus' => 'required',
        'landlordPhone' => 'required',
        'landlordEmail' => 'required'        
        ];

    public $count = 0;

    
    public function render()
    {

        return view('livewire.admin.edit-landlord',
            [
                'landlord' => $this->landlord
            ]
        );
    }

    public function mount($landlordid)
    {
        $this->landlordid = $landlordid;

        $this->landlord = User::select('*')->where('id', $this->landlordid)->first();
        $this->landlordFirstname = $this->landlord->firstname;
        $this->landlordLastname = $this->landlord->lastname;
        $this->landlordOthernames = $this->landlord->othernames;
        $this->landlordSex = $this->landlord->sex;
        $this->landlordStatus = $this->landlord->status;
        $this->landlordPhone = $this->landlord->phone;
        $this->landlordEmail = $this->landlord->email;


    }

    public function submit()
    {
        $this->validate();
        
        User::where('id', $this->landlordid)->update([
            'firstname' => $this->landlordFirstname,
            'lastname' => $this->landlordLastname,
            'othernames' => $this->landlordOthernames,
            'sex' => $this->landlordSex,
            'phone' => $this->landlordPhone,
            'status' => $this->landlordStatus,
            'email' => $this->landlordEmail,
        ]);

        $this->count++;
        $this->flash('success', 'Record Updated Successfully' , [
            'position' =>  'top-end', 
            'timer' =>  3000,  
            'toast' =>  true, 
            'text' =>  '', 
      ]);
      return redirect()->to("/landlords/$this->landlordid");
        
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
