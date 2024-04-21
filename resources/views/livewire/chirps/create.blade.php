<?php

use function Livewire\Volt\{rules, state};

state(['message' => '']);

rules([
    'message' => ['required', 'string', 'max:255'],
]);

$store = function () {
    $validated = $this->validate();

    $chirp = auth()->user()->chirps()->create($validated);

    $this->reset('message');

    $this->dispatch('chirp-created', $chirp);
};

?>

<div>
    <form wire:submit="store">
        <div x-data="{
            message: '',
            limit: $el.dataset.limit,
            get remaining() {
                return this.limit - this.message.length
            },
            get hasReachedLimit() {
                return this.message.length >= this.limit
            }
        }" data-limit="120">
            <textarea x-model="message" wire:model="message" placeholder="{{ __('What\'s on your mind?') }}" rows="3"
                x-bind:maxlength="limit"
                class="block w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
            <div class="flex items-center justify-between mt-2">
                <x-input-error :messages="$errors->get('message')" />
                <x-input-error x-cloak x-bind:class="{ 'opacity-0': !hasReachedLimit }"
                    messages="Oops, looks like the message is too long to post!" />
                <small class="text-sm block text-right text-neutral-400" x-text="remaining"></small>
            </div>
        </div>
        <x-primary-button class="mt-4">{{ __('Chirp') }}</x-primary-button>
    </form>
</div>
