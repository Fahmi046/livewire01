<div style="padding: 20px; font-family: sans-serif;">
    <h2>Daftar Produk</h2>
    <hr>
    <ul>
        @foreach ($products as $product)
            <li>
                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                <button wire:click="addProductToCart({{ $product->id }})">
                    Tambah ke Keranjang
                </button>
            </li>
        @endforeach
    </ul>

    <hr>

    <h2>Keranjang Belanja</h2>
    @if (empty($cart))
        <p>Keranjang Anda kosong.</p>
    @else
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $index => $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>
                            <button wire:click="decreaseQuantity({{ $index }})">-</button>
                            {{ $item['quantity'] }}
                            <button wire:click="increaseQuantity({{ $index }})">+</button>
                        </td>
                        <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                        <td>
                            <button wire:click="removeProductFromCart({{ $index }})">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" align="right">Total Harga:</td>
                    <td>Rp {{ number_format($this->cartTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif
</div>
