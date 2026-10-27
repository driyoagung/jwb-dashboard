@props(['name', 'class' => 'h-4 w-4'])

<svg class="{{ $class }}" aria-hidden="true"><use href="{{ asset('icons.svg') }}#i-{{ $name }}" /></svg>
