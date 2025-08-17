<div>
    <nav
        style="background-color: #4CAF50; padding: 10px; color: white; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-weight: bold; font-size: 18px;">
            Manajemen Produk
        </div>
        <div>
            <a href="#" style="color: white; margin-right: 15px; text-decoration: none;">Home</a>
            <a href="/produk" style="color: white; margin-right: 15px; text-decoration: none;">Produk</a>
            <a href="#" style="color: white; text-decoration: none;">Logout</a>
        </div>
    </nav>

    <div x-data="{
        focusInput(id) {
            document.getElementById(id).focus();
        }
    }" x-init="$wire.on('focus-price', () => focusInput('price'));
    $wire.on('focus-description', () => focusInput('description'));
    $wire.on('product-saved', () => {
        focusInput('name');
        $wire.on('focus-name', () => focusInput('name'));
    });">
        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="saveProduct">
            <div>
                <label for="name">Nama Barang</label>
                <input type="text" id="name" wire:model.live="name" autofocus
                    wire:keydown.enter.prevent="focusPrice">
            </div>

            <div>
                <label for="price">Harga</label>
                <input type="text" id="price" x-data
                    x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                    wire:model.live="price" wire:keydown.enter.prevent="focusDescription" inputmode="numeric" />
            </div>

            <div>
                <label for="description">Deskripsi</label>
                <textarea id="description" wire:model.live="description" wire:keydown.enter.prevent="saveProduct"></textarea>
            </div>

            <button type="submit">
                {{ $editingProductId ? 'Update Barang' : 'Simpan Barang' }}
            </button>
        </form>
        <h3>Daftar Barang</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>{{ $product->description }}</td>
                        <td>
                            <button wire:click="editProduct({{ $product->id }})">Edit</button>
                            <button wire:click="deleteProduct({{ $product->id }})"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
