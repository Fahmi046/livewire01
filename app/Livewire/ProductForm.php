<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductForm extends Component
{
    public $name;
    public $price;
    public $description;
    public $editingProductId; // Properti baru untuk menyimpan ID barang yang sedang diedit

    public function focusPrice()
    {
        $this->dispatch('focus-price');
    }

    public function focusDescription()
    {
        $this->dispatch('focus-description');
    }

    public function updatedPrice($value)
    {
        // Hapus titik agar Livewire menerima angka bersih
        $this->price = str_replace('.', '', $value);
    }

    public function saveProduct()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($this->editingProductId) {
            // Jika ada ID, ini adalah mode EDIT
            $product = Product::find($this->editingProductId);
            $product->update([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Barang berhasil diperbarui!');
        } else {
            // Jika tidak ada ID, ini adalah mode SIMPAN
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'description' => $this->description,
            ]);

            session()->flash('success', 'Barang berhasil disimpan!');
        }

        $this->reset(['name', 'price', 'description', 'editingProductId']);
        $this->dispatch('product-saved');
    }

    public function editProduct($id)
    {
        $product = Product::find($id);

        $this->editingProductId = $product->id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->description = $product->description;
    }

    public function deleteProduct($id)
    {
        Product::destroy($id);
        session()->flash('success', 'Barang berhasil dihapus!');
        $this->dispatch('focus-name');
    }

    public function render()
    {
        $products = Product::all();

        return view('livewire.product-form', [
            'products' => $products,
        ]);
    }
}
