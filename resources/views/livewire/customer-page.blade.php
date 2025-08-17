<div x-data="{
    focusInput(id) {
        document.getElementById(id).focus();
    }
}" x-init="$wire.on('focus-email', () => focusInput('email'));
$wire.on('focus-phone_number', () => focusInput('phone_number'));
$wire.on('customer-edit', () => focusInput('name'));
$wire.on('customer-deleted', () => focusInput('name'));
$wire.on('customer-saved', () => focusInput('name'));">
    @if (session()->has('success'))
        <div
            style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="saveCustomer">
        <h3>{{ $editingCustomerId ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h3>

        <div>
            <label for="name">Nama</label>
            <input type="text" id="name" wire:model.live="name" autofocus wire:keydown.enter.prevent="focusEmail">
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" wire:model.live="email" wire:keydown.enter.prevent="focusPhone_number">
        </div>

        <div>
            <label for="phone_number">No. Telepon</label>
            <input type="text" id="phone_number" wire:model.live="phone_number"
                wire:keydown.enter.prevent="saveCustomer">
        </div>

        <button type="submit">
            {{ $editingCustomerId ? 'Update Pelanggan' : 'Simpan Pelanggan' }}
        </button>
    </form>

    <hr>

    <h3>Daftar Pelanggan</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No. Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>
                        <button wire:click="editCustomer({{ $customer->id }})">Edit</button>
                        <button wire:click="deleteCustomer({{ $customer->id }})"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
