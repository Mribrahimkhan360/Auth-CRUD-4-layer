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


Ah, you want to implement a **Livewire component in Laravel** where a **Discount field appears only if “Yes” is selected** for “Discount Type,” following a **4-layer architecture** (Controller → Service → Repository → Model). Let me guide you step by step with a proper structure.

---

## **Step 1: Database & Model**

Assume you have a `products` table with:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->boolean('discount_type')->default(0); // 0 = No, 1 = Yes
    $table->decimal('discount_price', 8, 2)->nullable();
    $table->timestamps();
});
```

**Product Model**:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'discount_type', 'discount_price'];
}
```

---

## **Step 2: Repository Layer**

Create `ProductRepository.php` in `App\Repositories\Eloquent`:

```php
namespace App\Repositories\Eloquent;

use App\Models\Product;

class ProductRepository
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product;
    }
}
```

---

## **Step 3: Service Layer**

Create `ProductService.php` in `App\Services`:

```php
namespace App\Services;

use App\Repositories\Eloquent\ProductRepository;

class ProductService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function saveProduct(array $data)
    {
        // If discount_type is No, ignore discount_price
        if (isset($data['discount_type']) && $data['discount_type'] == 0) {
            $data['discount_price'] = null;
        }
        return $this->productRepo->create($data);
    }
}
```

---

## **Step 4: Livewire Component**

Create Livewire component:

```bash
php artisan make:livewire ProductDiscount
```

`ProductDiscount.php`:

```php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\ProductService;

class ProductDiscount extends Component
{
    public $name;
    public $discount_type = 0; // 0 = No, 1 = Yes
    public $discount_price;

    protected $rules = [
        'name' => 'required|string',
        'discount_type' => 'required|boolean',
        'discount_price' => 'nullable|numeric',
    ];

    protected $productService;

    public function mount(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function save()
    {
        $this->validate();

        $this->productService->saveProduct([
            'name' => $this->name,
            'discount_type' => $this->discount_type,
            'discount_price' => $this->discount_price,
        ]);

        session()->flash('message', 'Product saved successfully!');
    }

    public function render()
    {
        return view('livewire.product-discount');
    }
}
```

---

## **Step 5: Livewire Blade View**

`product-discount.blade.php`:

```html
<div>
    <form wire:submit.prevent="save">
        <div>
            <label>Product Name</label>
            <input type="text" wire:model="name" placeholder="Enter product name">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div>
            <label>Discount Type</label>
            <select wire:model="discount_type">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        @if($discount_type == 1)
            <div>
                <label>Discount Price</label>
                <input type="number" wire:model="discount_price" placeholder="Enter discount price">
                @error('discount_price') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        @endif

        <button type="submit">Save</button>
    </form>

    @if (session()->has('message'))
        <div class="text-green-500">{{ session('message') }}</div>
    @endif
</div>
```

✅ **How it works**:

1. Selecting **Yes** shows the discount price input.
2. Selecting **No** hides it and sets `discount_price` to `null`.
3. The **4-layer architecture** keeps the logic clean: Controller/Livewire → Service → Repository → Model.

---

If you want, I can **also show how to make it fully dynamic** without page reloads using **Livewire real-time validation and auto-hiding the field** while maintaining the 4-layer approach.

Do you want me to do that?
