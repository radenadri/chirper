<?php

use App\Models\Chirp;
use function Livewire\Volt\{mount, on, placeholder, state};

placeholder('<div class="mt-4">Loading...</div>');

mount(function () {
    // dd($this->lastChirp);
});

$getAllChirps = function () {
    $lastChirpId = $this->lastChirp ? $this->lastChirp->id : null;
    $chirps = Chirp::query()
        ->with('user')
        ->latest()
        ->when($lastChirpId, function ($query) use ($lastChirpId) {
            return $query->where('id', '<', $lastChirpId);
        })
        ->take(6)
        ->get();

    $this->chirps = $this->chirps ? $this->chirps->merge($chirps) : $chirps;
    $this->lastChirp = $chirps->last();

    return $chirps;
};

$edit = function (Chirp $chirp) {
    $this->editing = $chirp;

    $this->getAllChirps();
};

$delete = function (Chirp $chirp) {
    $this->authorize('delete', $chirp);

    $chirp->delete();

    $this->getAllChirps();
};

$disableEditing = function () {
    $this->editing = null;

    return $this->getAllChirps();
};

state([
    'chirps' => $getAllChirps,
    'lastChirp' => null,
    'editing' => null,
]);

on([
    'load-more-chirps' => $getAllChirps,
    'chirp-created' => $getAllChirps,
    'chirp-updated' => $disableEditing,
    'chirp-edit-canceled' => $disableEditing,
]);
?>

<section>
    <h1
        class="mt-6 mb-6 text-2xl font-extrabold leading-none tracking-tight text-neutral-900 md:text-3xl lg:text-4 dark:text-neutral-50">
        {{ __('Latest chirps') }}</h1>
    <div class="mt-6 bg-neutral-50 shadow-sm rounded-lg divide-y">
        @forelse ($chirps as $chirp)
            <div class="p-6 flex space-x-2" wire:key="{{ $chirp->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <div class="flex-1">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-800">{{ $chirp->user->name }}</span>
                            <small
                                class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                            @unless ($chirp->created_at->eq($chirp->updated_at))
                                <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                            @endunless
                        </div>
                        @if ($chirp->user->is(auth()->user()))
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <button>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link wire:click="edit({{ $chirp['id'] }})">
                                        {{ __('Edit') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link wire:click="delete({{ $chirp['id'] }})"
                                        wire:confirm="Are you sure to delete this chirp?">
                                        {{ __('Delete') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endif
                    </div>
                    @if ($chirp->is($editing))
                        <livewire:chirps.edit :chirp="$chirp" :key="$chirp->id" />
                    @else
                        <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6">
                <p class="text-gray-600">{{ __('No Chirps yet. Be the first!') }}</p>
            </div>
        @endforelse
    </div>
    <div x-data="loadMoreChirps" x-intersect="load" class="mt-6 justify-center flex">
        <div x-show="shown" x-transition>
            <x-spinner />
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('loadMoreChirps', () => ({
                shown: false,

                load() {
                    this.shown = true;



                    setTimeout(() => {
                        this.$dispatch('load-more-chirps');

                        this.shown = false;
                    }, 2000);
                },
            }))
        })
    </script>
@endpush
