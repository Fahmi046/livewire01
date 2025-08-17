<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    @livewireStyles
</head>

<body>
    <h1>Selamat Datang di Aplikasi Saya</h1>
    <p>Di bawah ini adalah komponen Livewire interaktif.</p>

    {{-- Tag untuk memanggil komponen Livewire --}}
    <livewire:counter />
    <livewire:product-form />

    @livewireScripts
</body>

</html>
