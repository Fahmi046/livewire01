<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class SalesForm extends Component
{
    public $customerSearchTerm = '';
    public $productSearchTerm = '';
    public $selectedCustomer = null;
    public $selectedProduct = null;
    public $quantity = 1;
    public $cart = [];

    public $showNewProductForm = false;
    public $newProductPrice = '';

    public $showNewCustomerForm = false;
    public $newCustomerEmail = '';

    // Properti baru untuk perhitungan pembayaran
    public $shipping_cost = 0;
    public $service_fee = 0;
    public $discount = 0;
    public $received_amount = 0;


    protected $listeners = ['focus-quantity' => 'focusQuantity'];

    public function mount()
    {
        $this->dispatch('focus-product-search');
    }

    public function selectCustomerWithKeyboard($index)
    {
        $customer = $this->customers->get($index);
        if ($customer) {
            $this->selectedCustomer = $customer;
            $this->reset('customerSearchTerm');
            $this->dispatch('focus-product-search');
        }
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->reset('customerSearchTerm');
    }

    public function updatedCustomerSearchTerm()
    {
        $this->reset('selectedCustomer', 'showNewCustomerForm');
    }

    public function updatedProductSearchTerm()
    {
        $this->reset('selectedProduct', 'quantity', 'showNewProductForm');
    }

    public function selectProductWithKeyboard($index)
    {
        $product = $this->products->get($index);
        if ($product) {
            $this->selectedProduct = $product;
            $this->quantity = 1;
            $this->dispatch('focus-quantity');
        } else {
            if (strlen($this->productSearchTerm) >= 1 && count($this->products) === 0) {
                $this->startNewProduct();
            }
        }
    }

    public function selectProduct($productId)
    {
        $this->selectedProduct = Product::find($productId);
        $this->quantity = 1;
        $this->dispatch('focus-quantity');
    }

    public function addToCart()
    {
        $this->validate([
            'selectedProduct' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $found = false;
        foreach ($this->cart as $index => $item) {
            if ($item['id'] === $this->selectedProduct->id) {
                $this->cart[$index]['quantity'] += (int)$this->quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $this->cart[] = [
                'id' => $this->selectedProduct->id,
                'name' => $this->selectedProduct->name,
                'price' => (float)$this->selectedProduct->price,
                'quantity' => (int)$this->quantity,
            ];
        }

        $this->reset('productSearchTerm', 'selectedProduct', 'quantity');
        $this->dispatch('focus-product-search');
    }

    public function removeCartItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->dispatch('focus-product-search');
    }

    public function editCartItem($index)
    {
        $item = $this->cart[$index];
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->selectedProduct = \App\Models\Product::find($item['id']);
        $this->quantity = $item['quantity'];
        $this->productSearchTerm = $item['name'];
        $this->dispatch('focus-quantity');
    }

    public function startNewProduct()
    {
        $this->showNewProductForm = true;
        $this->newProductPrice = '';
        $this->dispatch('focus-new-product-price');
    }

    public function updatedNewProductPrice($value)
    {
        $cleanValue = preg_replace('/[^0-9]/', '', $value);
        if (is_numeric($cleanValue)) {
            $this->newProductPrice = number_format((int)$cleanValue, 0, ',', '.');
        } else {
            $this->newProductPrice = '';
        }
    }

    public function saveNewProduct()
    {
        $cleanPrice = (int)str_replace('.', '', $this->newProductPrice);

        $this->validate([
            'productSearchTerm' => 'required|unique:products,name',
            'newProductPrice' => 'required|numeric|min:0',
        ], [
            'productSearchTerm.unique' => 'Barang dengan nama ini sudah ada.',
            'newProductPrice.required' => 'Harga barang wajib diisi.',
            'newProductPrice.numeric' => 'Harga barang harus berupa angka.',
            'newProductPrice.min' => 'Harga barang tidak boleh kurang dari 0.',
        ]);

        $product = Product::create([
            'name' => $this->productSearchTerm,
            'price' => $cleanPrice,
        ]);

        session()->flash('success', "Barang '{$product->name}' berhasil ditambahkan.");
        $this->showNewProductForm = false;
        $this->selectProduct($product->id);
    }

    public function startNewCustomer()
    {
        $this->showNewCustomerForm = true;
        $this->newCustomerEmail = '';
        $this->dispatch('focus-new-customer-email');
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'customerSearchTerm' => 'required|unique:customers,name',
            'newCustomerEmail' => 'nullable|email|unique:customers,email',
        ], [
            'customerSearchTerm.unique' => 'Pelanggan dengan nama ini sudah ada.',
            'newCustomerEmail.unique' => 'Pelanggan dengan email ini sudah terdaftar.',
        ]);

        $customer = Customer::create([
            'name' => $this->customerSearchTerm,
            'email' => $this->newCustomerEmail ?: null,
        ]);

        session()->flash('success', "Pelanggan '{$customer->name}' berhasil ditambahkan.");
        $this->showNewCustomerForm = false;
        $this->selectCustomer($customer->id);
    }

    // Metode untuk mengkonversi nilai string yang diformat menjadi angka
    public function updatedShippingCost($value)
    {
        $this->shipping_cost = (float) str_replace(['.', ','], ['', '.'], $value);
    }
    public function updatedServiceFee($value)
    {
        $this->service_fee = (float)str_replace(['.', ','], '', $value);
    }
    public function updatedDiscount($value)
    {
        $this->discount = (float)str_replace(['.', ','], '', $value);
    }
    public function updatedReceivedAmount($value)
    {
        // Hapus pemisah ribuan (.) dan ubah koma (,) jadi titik
        $clean = str_replace(['.', ','], ['', '.'], $value);

        // Simpan sebagai float supaya desimal tidak hilang
        $this->received_amount = is_numeric($clean) ? (float)$clean : 0;
    }

    public function getSubtotalProperty()
    {
        if ($this->selectedProduct) {
            return (float)$this->selectedProduct->price * (int)$this->quantity;
        }
        return 0;
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return (float)$item['price'] * (int)$item['quantity'];
        });
    }

    public function getFinalPaymentProperty()
    {
        return $this->cartTotal + $this->shipping_cost + $this->service_fee - $this->discount;
    }

    public function getChangeProperty()
    {
        return $this->received_amount - $this->finalPayment;
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang tidak boleh kosong. Tambahkan barang terlebih dahulu.');
            return;
        }

        $this->validate([
            'received_amount' => 'required|numeric|min:' . $this->finalPayment,
        ], [
            'received_amount.required' => 'Jumlah uang yang diterima wajib diisi.',
            'received_amount.numeric' => 'Jumlah uang yang diterima harus berupa angka.',
            'received_amount.min' => 'Jumlah uang yang diterima tidak boleh kurang dari total pembayaran.',
        ]);

        try {
            DB::beginTransaction();
            $sale = Sale::create([
                'customer_id' => $this->selectedCustomer ? $this->selectedCustomer->id : null,
                'total_amount' => $this->finalPayment,
            ]);
            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'subtotal' => (float)$item['price'] * (int)$item['quantity'],
                ]);
            }
            DB::commit();
            $this->dispatch('sale-completed');

            session()->flash('success', 'Penjualan berhasil dicatat!');
            $this->reset(['customerSearchTerm', 'productSearchTerm', 'selectedCustomer', 'selectedProduct', 'quantity', 'cart', 'shipping_cost', 'service_fee', 'discount', 'received_amount']);
            $this->dispatch('focus-product-search');
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function getCustomersProperty()
    {
        if (strlen($this->customerSearchTerm) >= 1) {
            return Customer::where('name', 'like', '%' . $this->customerSearchTerm . '%')->get();
        }
        return collect([]);
    }

    public function getProductsProperty()
    {
        if (strlen($this->productSearchTerm) >= 1) {
            return Product::where('name', 'like', '%' . $this->productSearchTerm . '%')->get();
        }
        return collect([]);
    }

    public function render()
    {
        return view('livewire.sales-form');
    }
}
