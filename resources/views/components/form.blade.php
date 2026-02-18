<form {{ $attributes }}>
    @csrf

    @if (strtoupper($attributes->get('method')) !== 'GET')
    @method($attributes->get('method'))
    @endif

    {{ $slot }}
</form>
