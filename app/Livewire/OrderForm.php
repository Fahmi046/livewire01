<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product; // Pastikan model Product diimpor

class OrderForm extends Component
{
    public $searchTerm = '';
    public $selectedProduct = null;
    public $quantity = 1;
    public $cart = [];

    // Metode ini dipanggil saat pencarian berubah
    public function updatedSearchTerm()
    {
        $this->reset('selectedProduct'); // Reset produk terpilih saat pencarian berubah
    }

    // Metode ini dipanggil saat produk dipilih dari daftar
    public function selectProduct($productId)
    {
        $this->selectedProduct = Product::find($productId);
        $this->quantity = 1; // Reset kuantitas ke 1
        $this->dispatch('focus-quantity'); // Kirim event untuk memindahkan kursor
    }

    // Metode untuk menambah item ke keranjang
    public function addToCart()
    {
        if ($this->selectedProduct && $this->quantity >= 1) {
            $found = false;
            foreach ($this->cart as $index => $item) {
                if ($item['id'] === $this->selectedProduct->id) {
                    $this->cart[$index]['quantity'] += $this->quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->cart[] = [
                    'id' => $this->selectedProduct->id,
                    'name' => $this->selectedProduct->name,
                    'price' => $this->selectedProduct->price,
                    'quantity' => $this->quantity,
                ];
            }

            // Reset formulir setelah ditambahkan ke keranjang
            $this->reset(['searchTerm', 'selectedProduct', 'quantity']);
            $this->dispatch('focus-search'); // Kirim event untuk kembali ke kolom pencarian
        }
    }

    // Metode untuk menghapus item dari keranjang
    public function removeCartItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    // Properti terhitung untuk menghitung subtotal item yang dipilih
    public function getSubtotalProperty()
    {
        if ($this->selectedProduct) {
            return $this->selectedProduct->price * $this->quantity;
        }
        return 0;
    }

    // Properti terhitung untuk menghitung total keseluruhan keranjang
    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function render()
    {
        $products = collect([]);
        if (strlen($this->searchTerm) >= 1) {
            $products = Product::where('name', 'like', '%' . $this->searchTerm . '%')->get();
        }

        return view('livewire.order-form', [
            'products' => $products,
        ]);
    }
}
