<?php

use Livewire\Component;

new class extends Component
{
    //
    public $spouse=null;
};
?>

<div>
    <label for="">Spouse</label>
    <select name="" id="" wire:model.live="spouse" class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="0">No spouse</option>
        <option value="1">Yes spouse</option>
    </select>

    @if($spouse==='1')
        <input type="text" class="mt-2 border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" name="" placeholder="Spouse Name">
    @endif
</div>
