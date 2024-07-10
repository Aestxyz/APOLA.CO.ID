<?php

use function Livewire\Volt\{state, rules, usesFileUploads, computed};
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use function Laravel\Folio\name;

name('products.create');
usesFileUploads();

state([
    'categories' => fn() => Category::get(),
    'productId' => '',
    'category_id',
    'title',
    'price',
    // 'quantity',
    'image',
    'weight',
    'description',
]);

rules([
    'category_id' => 'required|exists:categories,id',
    'title' => 'required|min:5',
    'price' => 'required|numeric',
    // 'quantity' => 'required|numeric',
    'image' => 'required',
    'weight' => 'required|numeric',
    'description' => 'required|min:10',
]);

$redirectProductsPage = function () {
    $this->redirectRoute('products.index');
};

$createdProduct = function () {
    $validate = $this->validate();
    $validate['image'] = $this->image->store('public/images');

    if ($this->productId == null) {
        $product = Product::create($validate);
        $this->productId = $product->id;
    } else {
        $product = Product::find($this->productId);
        $product->update($validate);
    }
};
?>


<x-admin-layout>
    <x-slot name="title">Produk</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.create') }}">Produk Baru</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-body">
                    <form wire:submit="createdProduct" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md mb-3">
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="img rounded object-fit-cover"
                                        alt="image" loading="lazy" height="525px" width="100%" />
                                @else
                                    <img src="" class="img rounded object-fit-cover placeholder " alt="image"
                                        loading="lazy" height="525px" width="100%" />
                                @endif
                            </div>
                            <div class="col-md">

                                <div class="mb-3">
                                    <label for="title" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        wire:model="title" id="title" aria-describedby="titleId"
                                        placeholder="Enter product title" />
                                    @error('title')
                                        <small id="titleId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga Produk</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        wire:model="price" id="price" aria-describedby="priceId"
                                        placeholder="Enter product price" />
                                    @error('price')
                                        <small id="priceId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Produk</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        wire:model="image" id="image" aria-describedby="imageId"
                                        placeholder="Enter product image" />
                                    @error('image')
                                        <small id="imageId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- <div class="mb-3">
                                    <label for="quantity" class="form-label">Jumlah Produk</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                        wire:model="quantity" id="quantity" aria-describedby="quantityId"
                                        placeholder="Enter product quantity" />
                                    @error('quantity')
                                        <small id="quantityId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div> --}}

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Kategori Produk</label>
                                    <select class="form-select" wire:model="category_id" id="category_id">
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
                                        placeholder="Enter product weight" />
                                    @error('weight')
                                        <small id="weightId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Penjelasan Produk</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" id="description"
                                    aria-describedby="descriptionId" placeholder="Enter product description" rows="8"></textarea>

                                @error('description')
                                    <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="text-end">
                                <x-action-message wire:loading on="save">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </x-action-message>
                                <button type="submit" class="btn btn-primary">
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
    {{-- <script>
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
    </script> --}}


</x-admin-layout>
