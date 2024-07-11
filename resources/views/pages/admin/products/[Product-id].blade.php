<?php

use function Livewire\Volt\{state, rules, usesFileUploads};
use App\Models\Category;
use App\Models\Product;
use function Laravel\Folio\name;

name('products.edit');

usesFileUploads();

state([
    'categories' => fn() => Category::get(),
    'category_id' => fn() => $this->product->category_id,
    'title' => fn() => $this->product->title,
    'price' => fn() => $this->product->price,
    // 'quantity' => fn() => $this->product->quantity,
    'weight' => fn() => $this->product->weight,
    'description' => fn() => $this->product->description,
    'productId' => fn() => $this->product->id,
    'image',
    'product',
]);

rules([
    'category_id' => 'required|exists:categories,id',
    'title' => 'required|min:5',
    'price' => 'required|numeric',
    // 'quantity' => 'required|numeric',
    'image' => 'nullable',
    'weight' => 'required|numeric',
    'description' => 'required|min:10',
]);

$save = function () {
    $validate = $this->validate();
    if ($this->image) {
        $validate['image'] = $this->image->store('public/images');
        Storage::delete($this->product->image);
    } else {
        $validate['image'] = $this->product->image;
    }
    product::whereId($this->product->id)->update($validate);

    $this->redirectRoute('products.index', navigate: true);
};

$redirectProductsPage = function () {
    $this->redirectRoute('products.index');
};

?>
<x-admin-layout>
    <x-slot name="title">Produk</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk Toko</a></li>
        <li class="breadcrumb-item"><a
                href="{{ route('products.edit', ['product' => $product->id]) }}">{{ $product->title }}</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-body">
                    <form wire:submit="save" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md mb-3">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="img rounded object-fit-cover"
                                        alt="image" loading="lazy" height="490px" width="100%" />
                                @elseif ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="img rounded object-fit-cover"
                                        alt="image" loading="lazy" height="490px" width="100%" />
                                @endif
                            </div>
                            <div class="col-md">

                                <div class="mb-3">
                                    <label for="title" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        wire:model="title" id="title" aria-describedby="titleId"
                                        placeholder="Enter product title"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('title')
                                        <small id="titleId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga Produk</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        wire:model="price" id="price" aria-describedby="priceId"
                                        placeholder="Enter product price"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('price')
                                        <small id="priceId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Produk</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        wire:model="image" id="image" aria-describedby="imageId"
                                        placeholder="Enter product image"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('image')
                                        <small id="imageId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Kategori Produk</label>
                                    <select class="form-select" wire:model="category_id" id="category_id"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }}>
                                        <option>Pilih salah satu</option>
                                        @foreach ($this->categories as $category)
                                            <option value="{{ $category->id }}">- {{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('category_id')
                                        <small id="category_id" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="weight" class="form-label">Berat Produk</label>
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror"
                                        wire:model="weight" id="weight" aria-describedby="weightId"
                                        placeholder="Enter product weight"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('weight')
                                        <small id="weightId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Penjelasan Produk</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" id="description"
                                    aria-describedby="descriptionId" placeholder="Enter product description" rows="8"
                                    {{ auth()->user()->role == 'superadmin' ?: 'disabled' }}></textarea>

                                @error('description')
                                    <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="text-end">
                                <x-action-message wire:loading on="save">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </x-action-message>
                                <button type="submit"
                                    class="btn btn-primary {{ auth()->user()->role == 'superadmin' ?: 'd-none' }}">
                                    {{ $productId == null ? 'Submit' : 'Edit' }}
                                </button>
                            </div>
                    </form>
                </div>

                <hr>

                @if ($productId)
                    @livewire('pages.products.createOrUpdateVariants', ['productId' => $productId, 'title' => $title])

                    <button type="button"
                        wire:confirm.prompt="Yakin Ingin Sudah Selesai Menambahkan Produk?\n\nTulis 'ya' untuk konfirmasi!|ya"
                        wire:click='redirectProductsPage' class="btn btn-primary">Selesai</button>
                @endif
            </div>
        </div>
    @endvolt

    <!-- JavaScript untuk beforeUnloadHandler -->
    <script>
        const beforeUnloadHandler = (event) => {
            event.preventDefault();
            event.returnValue = true; // Untuk dukungan legacy, mis. Chrome/Edge < 119
        };

        document.addEventListener("DOMContentLoaded", function() {
            const nameInput = document.querySelector("#title");

            nameInput.addEventListener("input", (event) => {
                if (event.target.value !== "") {
                    window.addEventListener("beforeunload", beforeUnloadHandler);
                } else {
                    window.removeEventListener("beforeunload", beforeUnloadHandler);
                }
            });
        });
    </script>

</x-admin-layout>
