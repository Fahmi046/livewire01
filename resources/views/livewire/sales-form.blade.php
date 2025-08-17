<div x-data="{
    customerSelectedIndex: -1,
    productSelectedIndex: -1,
    focusInput(id) {
        document.getElementById(id).focus();
    },
    selectCustomerWithKeyboard() {
        if (this.customerSelectedIndex !== -1) {
            $wire.call('selectCustomerWithKeyboard', this.customerSelectedIndex);
            this.customerSelectedIndex = -1;
        } else {
            $wire.call('startNewCustomer');
        }
    },
    selectProductWithKeyboard() {
        if (this.productSelectedIndex !== -1) {
            $wire.call('selectProductWithKeyboard', this.productSelectedIndex);
            this.productSelectedIndex = -1;
        } else {
            $wire.call('selectProductWithKeyboard', -1);
        }
    },
    saveNewProductWithKeyboard() {
        $wire.call('saveNewProduct');
    }
}" x-init="$wire.on('focus-customer-search', () => focusInput('customer-search'));
$wire.on('focus-product-search', () => focusInput('product-search'));
$wire.on('focus-quantity', () => focusInput('quantity'));
$wire.on('focus-new-product-price', () => focusInput('new-price'));
$wire.on('focus-new-customer-email', () => focusInput('new-email'));
$wire.on('focus-shipping-cost', () => focusInput('shipping-cost'));
$wire.on('focus-service-fee', () => focusInput('service-fee'));
$wire.on('focus-discount', () => focusInput('discount'));
$wire.on('focus-received-amount', () => focusInput('received-amount'));" @keydown.f8.prevent="$wire.completeSale()">
    <div style="font-family: sans-serif; display: flex; gap: 20px;">
        <div style="flex: 1; border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
            <h3>Input Transaksi</h3>

            @if (session()->has('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px;">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Formulir Pelanggan --}}
            <div>
                <label for="customer-search">Cari Pelanggan</label>
                <input type="text" id="customer-search" wire:model.live="customerSearchTerm"
                    @keydown.down.prevent="customerSelectedIndex = Math.min(customerSelectedIndex + 1, {{ count($this->customers) }} - 1)"
                    @keydown.up.prevent="customerSelectedIndex = Math.max(customerSelectedIndex - 1, 0)"
                    @keydown.enter.prevent="selectCustomerWithKeyboard()" style="width: 100%; padding: 8px;">
            </div>
            @if (strlen($customerSearchTerm) >= 1 && count($this->customers) > 0)
                <ul
                    style="list-style: none; padding: 0; margin: 10px 0; border: 1px solid #ddd; max-height: 200px; overflow-y: auto;">
                    @foreach ($this->customers as $index => $customer)
                        <li style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;"
                            x-on:click="$wire.selectCustomer({{ $customer->id }})"
                            x-bind:style="customerSelectedIndex === {{ $index }} ? 'background-color: #f0f0f0;' : ''">
                            {{ $customer->name }} - {{ $customer->email }}
                        </li>
                    @endforeach
                </ul>
            @endif
            @if (strlen($customerSearchTerm) > 1 && count($this->customers) === 0 && !$this->showNewCustomerForm)
                <div style="margin-top: 10px;">
                    <p>Pelanggan tidak ditemukan.</p>
                    <button wire:click="startNewCustomer" style="width: 100%; padding: 10px;">Tambah Pelanggan
                        Baru</button>
                </div>
            @endif
            @if ($this->showNewCustomerForm)
                <div style="background-color: #f9f9f9; padding: 10px; border: 1px solid #ddd; margin-top: 10px;">
                    <h4>Tambah Pelanggan Baru</h4>
                    <div>
                        <label for="new-email">Email (Opsional)</label>
                        <input type="email" id="new-email" wire:model.live="newCustomerEmail"
                            style="width: 100%; padding: 8px;">
                        @error('newCustomerEmail')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                        @error('customerSearchTerm')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                    </div>
                    <button wire:click="saveNewCustomer" style="width: 100%; padding: 10px; margin-top: 10px;">Simpan &
                        Pilih</button>
                </div>
            @endif

            <hr>

            {{-- Formulir Produk --}}
            <div>
                <label for="product-search">Cari Barang</label>
                <input type="text" id="product-search" wire:model.live="productSearchTerm" autofocus
                    @keydown.down.prevent="productSelectedIndex = Math.min(productSelectedIndex + 1, {{ count($this->products) }} - 1)"
                    @keydown.up.prevent="productSelectedIndex = Math.max(productSelectedIndex - 1, 0)"
                    @keydown.enter.prevent="selectProductWithKeyboard()" style="width: 100%; padding: 8px;">
            </div>
            @if (strlen($productSearchTerm) >= 1 && count($this->products) > 0)
                <ul
                    style="list-style: none; padding: 0; margin: 10px 0; border: 1px solid #ddd; max-height: 200px; overflow-y: auto;">
                    @foreach ($this->products as $index => $product)
                        <li style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;"
                            x-on:click="$wire.selectProduct({{ $product->id }})"
                            x-bind:style="productSelectedIndex === {{ $index }} ? 'background-color: #f0f0f0;' : ''">
                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Tombol Tambah Barang Baru --}}
            @if (strlen($productSearchTerm) > 1 && count($this->products) === 0 && !$this->showNewProductForm)
                <div style="margin-top: 10px;">
                    <p>Barang tidak ditemukan.</p>
                    <button wire:click="startNewProduct" style="width: 100%; padding: 10px;">Tambah Barang Baru</button>
                </div>
            @endif

            <hr>

            {{-- Formulir Tambah Barang Baru --}}
            @if ($this->showNewProductForm)
                <div style="background-color: #f9f9f9; padding: 10px; border: 1px solid #ddd;">
                    <h4>Tambah Barang Baru</h4>
                    <div>
                        <label for="new-price">Harga Barang</label>
                        <input type="text" id="new-price" wire:model.live="newProductPrice"
                            wire:keydown.enter.prevent="saveNewProduct" style="width: 100%; padding: 8px;">
                        @error('newProductPrice')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                        @error('productSearchTerm')
                            <span style="color: red;">{{ $message }}</span>
                        @enderror
                    </div>
                    <button wire:click="saveNewProduct" style="width: 100%; padding: 10px; margin-top: 10px;">Simpan &
                        Masukkan</button>
                </div>
                <hr>
            @endif

            {{-- Input Kuantitas --}}
            <div>
                <p>Barang terpilih: <b>
                        @if ($selectedProduct)
                            {{ $selectedProduct->name }}
                        @else
                            -
                        @endif
                    </b></p>
                <p>Harga: <b>
                        @if ($selectedProduct)
                            Rp {{ number_format($selectedProduct->price, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </b></p>
                <div>
                    <label for="quantity">Jumlah</label>
                    <input type="number" id="quantity" wire:model.live="quantity" min="1"
                        wire:keydown.enter.prevent="addToCart" style="width: 100%; padding: 8px;">
                </div>
                <p>Subtotal: <b>
                        @if ($selectedProduct)
                            Rp {{ number_format($this->subtotal, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </b></p>
                <button wire:click="addToCart" @if (!$selectedProduct) disabled @endif
                    style="padding: 10px; width: 100%;">
                    Tambah ke Keranjang
                </button>
            </div>
        </div>

        {{-- Tampilan Keranjang dan Total --}}
        <div style="flex: 1; border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
            <h3>Ringkasan Penjualan</h3>

            @if ($selectedCustomer)
                <p>Pelanggan: <b>{{ $selectedCustomer->name }}</b></p>
                <p>Email: <b>{{ $selectedCustomer->email }}</b></p>
            @else
                <p>Pelanggan: Belum dipilih</p>
            @endif

            <hr>

            <h4>Item Keranjang</h4>
            @if (empty($cart))
                <p>Keranjang masih kosong.</p>
            @else
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px;">Nama</th>
                            <th style="border: 1px solid #ccc; padding: 8px;">Jml</th>
                            <th style="border: 1px solid #ccc; padding: 8px;">Harga</th>
                            <th style="border: 1px solid #ccc; padding: 8px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $index => $item)
                            <tr>
                                <td style="border: 1px solid #ccc; padding: 8px;">{{ $item['name'] }}</td>
                                <td style="border: 1px solid #ccc; padding: 8px;">{{ $item['quantity'] }}</td>
                                <td style="border: 1px solid #ccc; padding: 8px;">Rp
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                <td style="border: 1px solid #ccc; padding: 8px;">
                                    <button wire:click="editCartItem({{ $index }})"
                                        style="margin-right: 5px;">Edit</button>
                                    <button wire:click="removeCartItem({{ $index }})">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" align="right" style="border: 1px solid #ccc; padding: 8px;">
                                <b>Subtotal</b>
                            </td>
                            <td colspan="2" style="border: 1px solid #ccc; padding: 8px;"><b>Rp
                                    {{ number_format($this->cartTotal, 0, ',', '.') }}</b></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            <hr>

            {{-- Formulir Biaya Tambahan, Potongan & Pembayaran --}}
            <div>
                <p><b>Tambahan & Diskon</b></p>
                <div>
                    <label for="shipping-cost">Ongkos Kirim</label>
                    <input type="text" id="shipping-cost" wire:model.live.debounce.150ms="shipping_cost"
                        style="width: 100%; padding: 8px;" x-data x-mask:dynamic="$money($input, '.', ',')">
                </div>
                <div style="margin-top: 10px;">
                    <label for="service-fee">Biaya Layanan</label>
                    <input type="text" id="service-fee" wire:model.live.debounce.150ms="service_fee"
                        style="width: 100%; padding: 8px;" x-data x-mask:dynamic="$money($input, '.', ',')">
                </div>
                <div style="margin-top: 10px;">
                    <label for="discount">Potongan Harga</label>
                    <input type="text" id="discount" wire:model.live.debounce.150ms="discount"
                        style="width: 100%; padding: 8px;" x-data x-mask:dynamic="$money($input, '.', ',')">
                </div>
            </div>

            <hr>

            {{-- Total Pembayaran & Kembalian --}}
            <div style="margin-top: 10px;">
                <p style="font-size: 1.2em; font-weight: bold;">Total Pembayaran</p>
                <p style="font-size: 1.5em; color: #28a745; font-weight: bold;">Rp
                    {{ number_format($this->finalPayment, 0, ',', '.') }}</p>
            </div>

            <div style="margin-top: 10px;">
                <label for="received-amount" style="font-size: 1.2em; font-weight: bold;">Uang Diterima</label>
                <input type="text" id="received-amount" wire:model.live.debounce.150ms="received_amount"
                    style="width: 100%; padding: 8px; font-size: 1.2em; font-weight: bold; text-align: right;" x-data
                    x-mask:dynamic="$money($input, '.', ',')">
                @error('received_amount')
                    <span style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-top: 10px;">
                <p style="font-size: 1.2em; font-weight: bold;">Kembalian</p>
                <p style="font-size: 1.5em; color: #007bff; font-weight: bold;">Rp
                    {{ number_format($this->change, 0, ',', '.') }}</p>
            </div>

            <hr>

            <button wire:click="completeSale"
                style="padding: 15px; font-size: 18px; width: 100%; background-color: #28a745; color: white; border: none; border-radius: 5px;">
                Selesaikan Penjualan
            </button>
        </div>
    </div>
</div>
