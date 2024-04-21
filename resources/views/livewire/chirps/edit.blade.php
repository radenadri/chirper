<?php

use function Livewire\Volt\{mount, rules, state};

state(['chirp', 'message']);

rules(['message' => 'required|string|max:255']);

mount(fn() => ($this->message = $this->chirp->message));

$update = function () {
    $this->authorize('update', $this->chirp);

    $validated = $this->validate();

    $this->chirp->update($validated);

    $this->dispatch('chirp-updated');
};

$cancel = fn() => $this->dispatch('chirp-edit-canceled');

?>

<div>
    <form wire:submit="update">
        <textarea wire:model="message"
            class="block w-full border-neutral-300 focus:border-neutral-300 focus:ring focus:ring-neutral-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>

        <x-input-error :messages="$errors->get('message')" class="mt-2" />
        <x-primary-button class="mt-4">{{ __('Save') }}</x-primary-button>
        <button class="mt-4" wire:click.prevent="cancel">Cancel</button>
    </form>
</div>
