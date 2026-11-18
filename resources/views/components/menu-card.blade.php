@props(['menu'])

<a href="{{ route('public.menus.show', $menu) }}"
   class="menu-card {{ $menu->is_available ? '' : 'is-unavailable' }}"
   data-category="{{ $menu->category_id }}">
    <div class="menu-card__photo">
        @if ($menu->image_path)
            <img src="{{ Storage::disk('public')->url($menu->image_path) }}"
                 alt="{{ $menu->name }}" loading="lazy">
        @else
            <div class="menu-card__placeholder">Belum ada foto</div>
        @endif

        @unless ($menu->is_available)
            <span class="menu-card__badge">Habis</span>
        @endunless
    </div>

    <div class="menu-card__body">
        <h3>{{ $menu->name }}</h3>
        <p class="menu-card__price">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
    </div>
</a>