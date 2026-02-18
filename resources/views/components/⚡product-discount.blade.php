<?php

use Livewire\Component;

new class extends Component
{
    public $price = null;

    // discount toggle
    public $discount_type = '0';   // string important
    public $discount_price = null;

    public function updatedDiscountType($value)
    {
        if ($value==='0')
        {
            $this->discount_price = null;
            $this->resetValidation();
        }
    }

    public function rules()
    {
        return[
            'price'             =>  'required|numeric|min:1',
            'discount_type'     =>  'required',
            'discount_price'    =>  $this->discount_type == '1'
                ? 'required|numeric|min:1|lt:price'
                : 'nullable',
        ];
    }

    // realtime calculated final price

    public function getFinalPriceProperty()
    {
        if (!$this->price)
        {
            return 0;
        }
        if ($this->discount_type == '1')
        {
            return max($this->price-$this->discount_price,0);
        }
        return $this->price;
    }

    public function save()
    {
        $this->validate();
        session()->flash('success',"Final Price = {$this->finalPrice}");
    }
};
?>

<div class="p-4 space-y-4">

    {{-- PRICE --}}
    <div>
        <label class="font-bold">Product Price</label>
        <input type="number"
               wire:model.live="price"
               class="border p-2 w-full"
               name="price"
               placeholder="Enter price">

        @error('price')
        <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>


    {{-- DISCOUNT TYPE --}}
    <div>
        <label class="font-bold">Discount Type</label>

        <select wire:model.live="discount_type" class="border p-2 w-full">
            <option value="0">No Discount</option>
            <option value="1">Add Discount</option>
        </select>
    </div>


    {{-- DISCOUNT FIELD --}}
    @if($discount_type === '1')
        <div>
            <label class="font-bold">Discount Amount</label>

            <input type="number"
                   wire:model.live="discount_price"
                   class="border p-2 w-full"
                   name="discount_price"
                   placeholder="Enter discount">

            @error('discount_price')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
    @endif


    {{-- LIVE RESULT --}}
    <div class="bg-gray-100 p-3 rounded">
        <strong>Final Price:</strong>
        <span class="text-green-600 text-lg">
            {{ $this->finalPrice }}
        </span>
    </div>


    {{-- SUBMIT --}}
    <form wire:submit.prevent="save">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">
            Save Product
        </button>
    </form>


    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="text-green-600 font-bold">
            {{ session('success') }}
        </div>
    @endif

</div>
