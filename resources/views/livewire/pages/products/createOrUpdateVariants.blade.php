<?php

use function Livewire\Volt\{state, computed};
use App\Models\Product;
use App\Models\Variant;

state([
    'productId' => '',
    'title' => '',
    'variantId' => '',
    'type',
    'stock',
]);

$createOrUpdateVariant = function (Variant $variant) {
    $validateData = $this->validate([
        'productId' => 'required|exists:products,id',
        'type' => 'required',
        'stock' => 'required|numeric',
    ]);

    if ($this->variantId == null) {
        $variant->create([
            'product_id' => $this->productId,
            'type' => $this->type,
            'stock' => $this->stock,
        ]);
    } else {
        $variantUpdate = Variant::find($this->variantId);
        $variantUpdate->update($validateData);
    }

    $this->reset('type', 'stock', 'variantId');
};

$editVariant = function (Variant $variant) {
    $variant = Variant::find($variant->id);
    $this->variantId = $variant->id;
    $this->type = $variant->type;
    $this->stock = $variant->stock;
};

$destroyVariant = function (Variant $variant) {
    $variant->delete();
    $this->reset('type', 'stock', 'variantId');
};

$resetVariant = function () {
    $this->reset('type', 'stock', 'variantId');
};

$variants = computed(function () {
    return Variant::where('product_id', $this->productId)->get();
});

?>

<div>
    <p class="fw-bold mb-3">Tambahkan Varian Product {{ $title }}</p>
    @if ($this->variants !== null)
        <div class="table-responsive border rounded mb-3">
            <table class="table text-center text-nowrap">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Varian</th>
                        <th>stock</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->variants as $no => $item)
                        <tr>
                            <td>{{ ++$no }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->stock }}</td>
                            <td>
                                <div class="btn-group">
                                    <a type="button" wire:click='editVariant({{ $item->id }})'
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <button
                                        wire:confirm.prompt="Yakin Ingin Menghapus?\n\nTulis 'hapus' untuk konfirmasi!|hapus"
                                        wire:loading.attr='disabled' wire:click='destroyVariant({{ $item->id }})'
                                        class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <form wire:submit.prevent="createOrUpdateVariant">
        @csrf
        @error('productId')
            <p class="text-danger fw-bold">Tetapkan data produk dahulu!</p>
        @enderror

        <div class="input-group mb-3">

            <input type="text" class="form-control
                            @error('type') is-invalid @enderror"
                wire:model="type" id="type" aria-describedby="typeId" placeholder="Enter type product" />


            <input type="number" class="form-control
                            @error('stock') is-invalid @enderror"
                wire:model="stock" id="stock" aria-describedby="stockId" placeholder="Enter stock product" />
            <button type="submit" class="btn btn-primary">
                Submit
            </button>

            <button type="reset" wire:click='resetVariant' class="btn btn-danger">
                Reset
            </button>

        </div>

        <p>
            @error('type')
                <small id="typeId" class="form-text text-danger">{{ $message }}</small>,
            @enderror
            @error('stock')
                <small id="stockId" class="form-text text-danger">{{ $message }}</small>
            @enderror
        </p>
    </form>
</div>
