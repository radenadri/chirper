<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-neutral-800 border border-transparent rounded-md font-semibold text-xs text-neutral-50 uppercase tracking-widest hover:bg-neutral-700 focus:bg-neutral-700 active:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
