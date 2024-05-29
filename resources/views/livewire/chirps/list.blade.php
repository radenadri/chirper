<?php

use App\Models\Chirp;
use function Livewire\Volt\{on, state};

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

    $this->lastChirp = $chirps->last();
    $this->chirps = $this->chirps ? $this->chirps->merge($chirps) : $chirps;

    return $chirps;
};

$created = function (array $chirp) {
    $newChirp = Chirp::make($chirp)->load('user')->setConnection(config('database.default'));

    $this->chirps = $this->chirps->prepend($newChirp);
};

$edit = function (Chirp $chirp) {
    $this->editing = $chirp;
};

$delete = function (Chirp $chirp) {
    $this->authorize('delete', $chirp);

    $this->chirps = $this->chirps->except($chirp->id);

    $chirp->delete();
};

$disableEditing = function () {
    $this->editing = null;
};

state([
    'chirps' => $getAllChirps,
    'lastChirp' => null,
    'editing' => null,
]);

on([
    'load-more-chirps' => $getAllChirps,
    'chirp-created' => $created,
    'chirp-updated' => $disableEditing,
    'chirp-edit-canceled' => $disableEditing,
]);
?>

<section>
    <h1
        class="mt-6 mb-6 text-2xl font-extrabold leading-none tracking-tight text-neutral-900 md:text-3xl lg:text-4 dark:text-neutral-50">
        {{ __('Latest chirps') }}</h1>
    <div class="mt-6 divide-y rounded-lg shadow-sm bg-neutral-50">
        @forelse ($chirps as $chirp)
            <div class="flex p-6 space-x-4" wire:key="{{ $chirp->id }}">
                {{-- Default Avatar --}}
                <div class="relative w-10 h-10 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-600">
                    <svg class="absolute w-12 h-12 text-neutral-400 -left-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="flex-1">
                    <div class="flex items-center justify-between">
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400"
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
    <div x-data="loadMoreChirps" x-intersect="load" class="flex justify-center mt-6">
        <div x-show="shown" x-transition>
            <x-spinner />
        </div>
    </div>
</section>

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
