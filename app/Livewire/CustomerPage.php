<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class CustomerPage extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $editingCustomerId;

    // Tambahkan metode ini untuk mengirim event ke Blade
    public function focusEmail()
    {
        $this->dispatch('focus-email');
    }

    // Tambahkan metode ini untuk mengirim event ke Blade
    public function focusPhone_number()
    {
        $this->dispatch('focus-phone_number');
    }

    public function saveCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $this->editingCustomerId,
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($this->editingCustomerId) {
            $customer = Customer::find($this->editingCustomerId);
            $customer->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
            ]);
            session()->flash('success', 'Data pelanggan berhasil diperbarui!');
        } else {
            Customer::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
            ]);
            session()->flash('success', 'Pelanggan baru berhasil ditambahkan!');
        }

        $this->reset(['name', 'email', 'phone_number', 'editingCustomerId']);
        $this->dispatch('customer-saved'); // Mengirim event setelah penyimpanan berhasil
    }

    public function editCustomer($id)
    {
        $customer = Customer::find($id);

        $this->editingCustomerId = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone_number = $customer->phone_number;
        $this->dispatch('customer-edit');
    }

    public function deleteCustomer($id)
    {
        Customer::destroy($id);
        session()->flash('success', 'Pelanggan berhasil dihapus!');
        // Tambahkan dispatch event di sini
        $this->dispatch('customer-deleted');
    }

    public function render()
    {
        $customers = Customer::all();

        return view('livewire.customer-page', [
            'customers' => $customers,
        ]);
    }
}
