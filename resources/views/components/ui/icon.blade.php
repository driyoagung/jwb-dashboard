@props(['name', 'class' => 'h-4 w-4', 'useClass' => null])
<svg {{ $attributes->merge(['class' => $class]) }} aria-hidden="true" focusable="false">
    <use @if ($useClass) class="{{ $useClass }}" @endif href="{{ asset('icons.svg') }}#i-{{ $name }}"></use>
</svg>

