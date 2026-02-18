<?php

use Livewire\Component;

new class extends Component
{
    public $emailOption = null; // dropdown value: 0 = Yes, 1 = No
    public $nid = '';          // email input value


};
?>

<div class="mb-4">
    <label class="block text-gray-700 font-semibold mb-2">Email</label>

    <select wire:model.live="nid" class="border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="0">No NID</option>
        <option value="1">Add NID</option>
    </select>

    @if($nid === '1')
        <input type="text"
               wire:model.lazy="email_address"
               placeholder="Enter your nid..."
               class="mt-2 border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" />
    @endif
</div>
