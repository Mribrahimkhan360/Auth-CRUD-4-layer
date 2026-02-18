<?php

use Livewire\Component;

new class extends Component
{
    //
    public $birthCertificate=null;
};
?>

<div class="mb-4">
    <label for="" class="block text-gray-700 font-semibold mb-2"></label>
    <select name="" wire:model.live="birthCertificate"  id="" class="mt-2 border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="0">No Email</option>
        <option value="1">Add Email</option>
    </select>

    @if($birthCertificate==='1')
        <input type="text"
               wire:model.lazy="email_address"
               placeholder="Enter your Birth Certificate..."
               class="mt-2 border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" />
    @endif
</div>
