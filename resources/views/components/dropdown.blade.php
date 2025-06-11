<div x-data="{ open: false }" class="relative" @keydown.escape="open = false">
    <div @click="open = !open" @click.away="open = false">
        {{ $trigger }}
    </div>
    <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50">
        {{ $content }}
    </div>
</div>