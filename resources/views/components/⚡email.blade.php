<?php

use Livewire\Component;

new class extends Component
{
    public $emailOption = null; // dropdown value: 0 = Yes, 1 = No
    public $email = '';          // email input value

    // Automatically clear email if "No" is selected
    public function updatedEmailOption($value)
    {
        if ($value == 1) { // "No" selected
            $this->email = '';
            $this->resetValidation(); // optional: clear errors
        }
    }
};
?>
<div class="mb-4">
    <label class="block text-gray-700 font-semibold mb-2">Email</label>

    <select wire:model.live="email" class="border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400">
        <option value="0">No Email</option>
        <option value="1">Add Email</option>
    </select>

    @if($email === '1')
        <input type="email"
               wire:model.lazy="email_address"
               placeholder="Enter your email..."
               class="mt-2 border rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-400" />
    @endif
</div>

