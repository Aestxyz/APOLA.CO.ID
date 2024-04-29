<?php

use App\Models\User;
use function Livewire\Volt\{computed, state, usesPagination};
use function Laravel\Folio\name;

name('customers');

state(['search'])->url();
usesPagination(theme: 'bootstrap');

$users = computed(function () {
    if ($this->search == null) {
        return User::query()->where('role', 'customer')->latest()->paginate(10);
    } else {
        return User::query()
            ->where('role', 'customer')
            ->where(function ($query) {
                $query
                    ->where('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('email', 'LIKE', "%{$this->search}%")
                    ->orWhere('telp', 'LIKE', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }
});

?>

<x-admin-layout>
    <div>
        <x-slot name="title">Pelanggan</x-slot>
        <x-slot name="header">
            <li class="breadcrumb-item"><a wire:navigate href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a wire:navigate href="{{ route('customers') }}">Pelanggan</a></li>
        </x-slot>

        @volt
            <div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <a wire:navigate href="{{ route('users.create') }}" class="btn btn-primary">Tambah
                                        Pelanggan</a>
                                </div>
                                <div class="col">
                                    <input wire:model.live="search" type="search" class="form-control" name=""
                                        id="" aria-describedby="helpId" placeholder="Masukkan nama pengguna" />
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped text-center rounded">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Telp</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->users as $no => $user)
                                        <tr>
                                            <td>{{ ++$no }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->telp }}</td>
                                            <td>{{ $user->address ?? '-' }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{ $this->users->links() }}
                        </div>

                    </div>
                </div>
            </div>
        @endvolt

    </div>
</x-admin-layout>
