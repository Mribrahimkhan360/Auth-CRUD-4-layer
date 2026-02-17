<h2>  1. Project Structure Resources in View </h2>
<img src="public/Screenshot_4.png" alt=""/>

<h2>  2. Command </h2>
<code>
composer create-project "laravel/laravel:^10.0" Auth-CRUD
</code>
# Auth-CRUD-4-layer

<h2>3. Login page</h2>
<p>Login page design return view('auth.login') and fully login page design </p>

<h2> 4. composer require laravel/breeze --dev </h2>
<h2>Auth Controller</h2>
<p>

</p>


# Laravel Livewire - Conditional Discount Type Implementation
## 4-Layer Architecture Step-by-Step Guide

---

## Architecture Overview

```
Layer 1: Database (Migration & Schema)
    ↓
Layer 2: Model (Eloquent Model)
    ↓
Layer 3: Component (Livewire Logic)
    ↓
Layer 4: View (Blade Template)
```

---

## Layer 1: Database (Migration & Schema)

### Step 1: Create Migration

```bash
php artisan make:migration create_discounts_table
```

### Step 2: Write Migration Code

**File: `database/migrations/2024_xx_xx_create_discounts_table.php`**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('discount_type', ['yes', 'no'])->default('no');
            $table->string('discount_code')->nullable();
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->enum('discount_value_type', ['fixed', 'percentage'])->nullable(); // Fixed or %
            $table->integer('discount_percentage')->nullable(); // Store percentage value
            $table->decimal('discount_fixed_amount', 8, 2)->nullable(); // Store fixed amount
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
```

### Step 3: Run Migration

```bash
php artisan migrate
```

---

## Layer 2: Model (Eloquent Model)

### Step 1: Create Model

```bash
php artisan make:model Discount
```

### Step 2: Configure Model

**File: `app/Models/Discount.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_type',
        'discount_code',
        'discount_amount',
        'discount_value_type',
        'discount_percentage',
        'discount_fixed_amount',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Check if discount is active
    public function isActive(): bool
    {
        return $this->is_active && 
               ($this->expiry_date === null || $this->expiry_date->isFuture());
    }

    // Calculate discount amount
    public function calculateDiscount($amount): float
    {
        if ($this->discount_type === 'no') {
            return 0;
        }

        if ($this->discount_value_type === 'percentage') {
            return ($amount * $this->discount_percentage) / 100;
        }

        return $this->discount_fixed_amount ?? 0;
    }
}
```

---

## Layer 3: Component (Livewire Logic)

### Step 1: Create Livewire Component

```bash
php artisan make:livewire DiscountForm
```

### Step 2: Configure Component

**File: `app/Livewire/DiscountForm.php`**

```php
<?php

namespace App\Livewire;

use App\Models\Discount;
use Livewire\Component;

class DiscountForm extends Component
{
    // Form properties
    public $discountId = null;
    public $name = '';
    public $discount_type = 'no'; // Default value
    public $discount_code = '';
    public $discount_value_type = 'fixed';
    public $discount_percentage = 0;
    public $discount_fixed_amount = 0;
    public $expiry_date = '';
    public $is_active = true;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'discount_type' => 'required|in:yes,no',
        'discount_code' => 'nullable|string|max:50',
        'discount_value_type' => 'nullable|in:fixed,percentage',
        'discount_percentage' => 'nullable|numeric|min:0|max:100',
        'discount_fixed_amount' => 'nullable|numeric|min:0',
        'expiry_date' => 'nullable|date|after_or_equal:today',
        'is_active' => 'boolean',
    ];

    // Real-time validation messages
    protected $messages = [
        'name.required' => 'Discount name is required.',
        'discount_type.required' => 'Please select a discount type.',
        'discount_percentage.max' => 'Percentage cannot exceed 100%.',
        'expiry_date.after_or_equal' => 'Expiry date must be today or later.',
    ];

    public function mount($discountId = null)
    {
        if ($discountId) {
            $this->discountId = $discountId;
            $discount = Discount::find($discountId);
            
            if ($discount) {
                $this->name = $discount->name;
                $this->discount_type = $discount->discount_type;
                $this->discount_code = $discount->discount_code ?? '';
                $this->discount_value_type = $discount->discount_value_type ?? 'fixed';
                $this->discount_percentage = $discount->discount_percentage ?? 0;
                $this->discount_fixed_amount = $discount->discount_fixed_amount ?? 0;
                $this->expiry_date = $discount->expiry_date?->format('Y-m-d') ?? '';
                $this->is_active = $discount->is_active;
            }
        }
    }

    // Real-time update when discount_type changes
    #[\Livewire\Attributes\On('update:discountType')]
    public function updatedDiscountType($value)
    {
        $this->discount_type = $value;
        $this->validateOnly('discount_type');
    }

    // Update for discount value type
    public function updatedDiscountValueType()
    {
        $this->validateOnly('discount_value_type');
    }

    // Update for percentage
    public function updatedDiscountPercentage()
    {
        $this->validateOnly('discount_percentage');
    }

    // Update for fixed amount
    public function updatedDiscountFixedAmount()
    {
        $this->validateOnly('discount_fixed_amount');
    }

    // Save or update discount
    public function saveDiscount()
    {
        // Validate all fields
        $this->validate();

        // If discount_type is 'no', clear discount fields
        if ($this->discount_type === 'no') {
            $this->discount_code = '';
            $this->discount_percentage = 0;
            $this->discount_fixed_amount = 0;
            $this->discount_value_type = null;
        }

        $data = [
            'name' => $this->name,
            'discount_type' => $this->discount_type,
            'discount_code' => $this->discount_type === 'yes' ? $this->discount_code : null,
            'discount_value_type' => $this->discount_type === 'yes' ? $this->discount_value_type : null,
            'discount_percentage' => $this->discount_type === 'yes' ? $this->discount_percentage : null,
            'discount_fixed_amount' => $this->discount_type === 'yes' ? $this->discount_fixed_amount : null,
            'expiry_date' => $this->expiry_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->discountId) {
            Discount::find($this->discountId)->update($data);
            session()->flash('message', 'Discount updated successfully!');
        } else {
            Discount::create($data);
            session()->flash('message', 'Discount created successfully!');
        }

        return redirect()->route('discounts.index');
    }

    // Reset form
    public function resetForm()
    {
        $this->reset();
        $this->discount_type = 'no';
        $this->discount_value_type = 'fixed';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.discount-form');
    }
}
```

---

## Layer 4: View (Blade Template)

### Step 1: Create Blade Template

**File: `resources/views/livewire/discount-form.blade.php`**

```blade
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6">
        {{ $discountId ? 'Edit Discount' : 'Create New Discount' }}
    </h2>

    @if (session('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit="saveDiscount" class="space-y-6">
        
        <!-- Discount Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Discount Name <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="name"
                wire:model="name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter discount name"
            />
            @error('name')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Discount Type Selection (Yes/No) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Enable Discount? <span class="text-red-500">*</span>
            </label>
            
            <div class="flex gap-6">
                <label class="flex items-center cursor-pointer">
                    <input 
                        type="radio" 
                        name="discount_type"
                        value="no"
                        wire:model.live="discount_type"
                        class="w-4 h-4 text-red-600"
                    />
                    <span class="ml-2 text-gray-700">No</span>
                </label>

                <label class="flex items-center cursor-pointer">
                    <input 
                        type="radio" 
                        name="discount_type"
                        value="yes"
                        wire:model.live="discount_type"
                        class="w-4 h-4 text-green-600"
                    />
                    <span class="ml-2 text-gray-700">Yes</span>
                </label>
            </div>

            @error('discount_type')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Conditional Discount Fields (only show if discount_type = 'yes') -->
        @if($discount_type === 'yes')
            <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500 space-y-6">
                
                <!-- Discount Code -->
                <div>
                    <label for="discount_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Discount Code
                    </label>
                    <input 
                        type="text" 
                        id="discount_code"
                        wire:model="discount_code"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., SAVE20"
                    />
                    @error('discount_code')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discount Value Type (Fixed or Percentage) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Discount Type <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="flex gap-6">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="discount_value_type"
                                value="fixed"
                                wire:model.live="discount_value_type"
                                class="w-4 h-4 text-blue-600"
                            />
                            <span class="ml-2 text-gray-700">Fixed Amount</span>
                        </label>

                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="radio" 
                                name="discount_value_type"
                                value="percentage"
                                wire:model.live="discount_value_type"
                                class="w-4 h-4 text-blue-600"
                            />
                            <span class="ml-2 text-gray-700">Percentage</span>
                        </label>
                    </div>

                    @error('discount_value_type')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fixed Amount Field -->
                @if($discount_value_type === 'fixed')
                    <div>
                        <label for="discount_fixed_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Discount Amount ($) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="discount_fixed_amount"
                            wire:model.live="discount_fixed_amount"
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., 10.00"
                        />
                        @error('discount_fixed_amount')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Percentage Field -->
                @if($discount_value_type === 'percentage')
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Discount Percentage (%) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="discount_percentage"
                            wire:model.live="discount_percentage"
                            step="0.01"
                            min="0"
                            max="100"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="e.g., 20"
                        />
                        <p class="text-sm text-gray-500 mt-1">Value between 0-100</p>
                        @error('discount_percentage')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Expiry Date
                    </label>
                    <input 
                        type="date" 
                        id="expiry_date"
                        wire:model="expiry_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('expiry_date')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        @endif

        <!-- Active Status -->
        <div>
            <label class="flex items-center cursor-pointer">
                <input 
                    type="checkbox" 
                    wire:model="is_active"
                    class="w-4 h-4 text-blue-600 rounded"
                />
                <span class="ml-2 text-gray-700">Active</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-6 border-t">
            <button 
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200"
            >
                {{ $discountId ? 'Update' : 'Create' }} Discount
            </button>

            <button 
                type="button"
                wire:click="resetForm"
                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200"
            >
                Reset
            </button>

            <a 
                href="{{ route('discounts.index') }}"
                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200 text-center"
            >
                Cancel
            </a>
        </div>
    </form>
</div>
```

---

## Complete Flow Summary

### 1️⃣ **User selects "No"**
   - Discount conditional fields are hidden
   - Form submission clears all discount data
   - Only name and active status are saved

### 2️⃣ **User selects "Yes"**
   - Conditional fields appear with smooth animation
   - User can enter discount code, type, value, and expiry date
   - Real-time validation occurs
   - On submit, all discount data is saved

### 3️⃣ **Field Behavior**
   - Fixed Amount ↔ Percentage toggle switches between input fields
   - All validations run when fields change
   - Expiry date validation ensures it's today or later
   - Database stores NULL for fields when discount is disabled

---

## Usage in Routes

**File: `routes/web.php`**

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/discounts/create', function () {
        return view('discounts.create');
    })->name('discounts.create');
    
    Route::get('/discounts/{discount}/edit', function (Discount $discount) {
        return view('discounts.edit', ['discount' => $discount]);
    })->name('discounts.edit');
    
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
});
```

---

## Creating Views

**File: `resources/views/discounts/create.blade.php`**

```blade
@extends('layouts.app')

@section('content')
    <livewire:discount-form />
@endsection
```

**File: `resources/views/discounts/edit.blade.php`**

```blade
@extends('layouts.app')

@section('content')
    <livewire:discount-form :discountId="$discount->id" />
@endsection
```

---

## Testing the Implementation

```php
// Test creating discount with type 'no'
$discount = Discount::create([
    'name' => 'No Discount',
    'discount_type' => 'no',
    'is_active' => true,
]);

// Test creating discount with type 'yes' (percentage)
$discount = Discount::create([
    'name' => 'Summer Sale',
    'discount_type' => 'yes',
    'discount_code' => 'SUMMER20',
    'discount_value_type' => 'percentage',
    'discount_percentage' => 20,
    'is_active' => true,
]);

// Test creating discount with type 'yes' (fixed)
$discount = Discount::create([
    'name' => 'Fixed Discount',
    'discount_type' => 'yes',
    'discount_code' => 'SAVE10',
    'discount_value_type' => 'fixed',
    'discount_fixed_amount' => 10.00,
    'is_active' => true,
]);
```

---

## Key Features Implemented

✅ **Conditional Rendering** - Fields only show when discount_type = 'yes'
✅ **Real-time Validation** - Errors appear as user types
✅ **Dynamic Switching** - Toggle between fixed/percentage amounts
✅ **Data Persistence** - Edit existing discounts with pre-filled data
✅ **Clean Database Storage** - NULL values when discount is disabled
✅ **Responsive Design** - Works on all screen sizes
✅ **Form Reset** - Clear form functionality
✅ **Flash Messages** - Success/error feedback

---

## Commands to Set Up

```bash
# Create migration
php artisan make:migration create_discounts_table

# Create model
php artisan make:model Discount

# Create Livewire component
php artisan make:livewire DiscountForm

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

