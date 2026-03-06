<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $avatar;

    public function saveAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $this->avatar->storeAs(
            'avatars',
            $user->id.'-'.uniqid().'.'.$this->avatar->getClientOriginalExtension(),
            'public'
        );

        $user->forceFill(['avatar_path' => $path])->save();

        $this->avatar = null;
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->forceFill(['avatar_path' => null])->save();
        }

        $this->avatar = null;
        $this->dispatch('profile-updated', name: $user->name);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-neutral-900">
            {{ __('Profile Photo') }}
        </h2>

        <p class="mt-1 text-sm text-neutral-600">
            {{ __('Update your profile photo. Max 2MB. JPG, PNG, GIF, or WebP.') }}
        </p>
    </header>

    <div class="mt-6 flex items-center gap-6">
        @php
            $user = Auth::user();
        @endphp

        @if ($avatar)
            <div class="shrink-0">
                @if (method_exists($avatar, 'isPreviewable') && $avatar->isPreviewable())
                    <img src="{{ $avatar->temporaryUrl() }}"
                         alt="{{ __('Preview') }}"
                         class="w-16 h-16 rounded-full object-cover" />
                @else
                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center">
                        <span class="text-neutral-500 text-xs">{{ __('File selected') }}</span>
                    </div>
                @endif
            </div>
        @else
            <div class="shrink-0">
                <x-avatar :user="$user" size="lg" />
            </div>
        @endif

        <div class="flex flex-col gap-2">
            <input type="file"
                   wire:model="avatar"
                   accept="image/*"
                   class="block w-full text-sm text-neutral-500 file:me-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-neutral-100 file:text-neutral-800 hover:file:bg-neutral-200" />

            @error('avatar')
                <x-input-error :messages="[$message]" class="mt-2" />
            @enderror

            @if ($user->avatar_path)
                <x-secondary-button wire:click="removeAvatar" wire:loading.attr="disabled">
                    {{ __('Remove Photo') }}
                </x-secondary-button>
            @endif
        </div>
    </div>

    @if ($avatar)
        <div class="mt-4 flex items-center gap-4">
            <x-primary-button wire:click="saveAvatar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="saveAvatar">{{ __('Save Photo') }}</span>
                <span wire:loading wire:target="saveAvatar">{{ __('Saving...') }}</span>
            </x-primary-button>
            <x-secondary-button wire:click="$set('avatar', null)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
        </div>
    @endif
</section>
