<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product; // Pastikan model Product diimpor

class ShoppingCart extends Component
{
    public $cart = [];

    // Hapus properti $products yang di-hardcode di sini

    public function addProductToCart($productId)
    {
        // Cari produk dari database, bukan dari array statis
        $product = Product::find($productId);

        if ($product) {
            $found = false;
            foreach ($this->cart as $index => $item) {
                if ($item['id'] == $productId) {
                    $this->cart[$index]['quantity']++;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->cart[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => 1,
                ];
            }
        }
    }

    public function removeProductFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }

    public function increaseQuantity($index)
    {
        $this->cart[$index]['quantity']++;
    }

    public function decreaseQuantity($index)
    {
        if ($this->cart[$index]['quantity'] > 1) {
            $this->cart[$index]['quantity']--;
        }
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function render()
    {
        // Ambil semua produk dari database
        $products = Product::all();

        return view('livewire.shopping-cart', [
            'products' => $products, // Kirim data produk ke tampilan
        ]);
    }
}
