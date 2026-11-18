@extends('layouts.public')

@section('title', $menu->name . ' - ' . config('bakso_atmo.name'))

@section('content')
<section class="section">
    <div class="container">
        <a href="{{ route('public.home') }}#menu" class="back-link">&larr; Kembali ke menu</a>

        <div class="menu-detail">
            <div class="menu-detail__photo">
                @if ($menu->image_path)
                    <img src="{{ Storage::disk('public')->url($menu->image_path) }}" alt="{{ $menu->name }}">
                @else
                    <div class="menu-card__placeholder">Belum ada foto</div>
                @endif
            </div>

            <div class="menu-detail__info">
                <span class="badge badge--category">{{ $menu->category->name }}</span>
                <h1>{{ $menu->name }}</h1>
                <p class="menu-detail__price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>

                <p class="menu-detail__status {{ $menu->is_available ? 'is-ok' : 'is-out' }}">
                    {{ $menu->is_available ? 'Tersedia' : 'Sedang habis' }}
                </p>

                @if ($menu->description)
                    <p class="menu-detail__desc">{{ $menu->description }}</p>
                @endif

                <x-marketplace-buttons />
            </div>
        </div>
    </div>
</section>
@endsection