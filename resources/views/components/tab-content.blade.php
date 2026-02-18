@props(['tab'])

<div x-show="tab === '{{ $tab }}'" style="display: none;">
    {{ $slot }}
</div>
