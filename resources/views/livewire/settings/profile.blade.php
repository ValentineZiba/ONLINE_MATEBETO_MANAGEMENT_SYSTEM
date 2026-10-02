<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

use Livewire\WithFileUploads;

new #[Layout('components.layouts.public', ['title' => 'Settings'])] class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatarFile = null;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatarFile' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill(['name' => $validated['name'], 'email' => $validated['email']]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->avatarFile) {
            $user->avatar = $this->avatarFile->store('avatars', 'public');
            $this->avatarFile = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();
        if ($user->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="max-w-4xl mx-auto px-4 sm:px-6 py-10 w-full animate-fade-in-up">
    @include('partials.settings-heading')

    <x-settings.layout heading="Profile" subheading="Update your name and email address">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            {{-- Avatar --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-3">Profile Photo</label>
                <div class="flex items-center gap-5">
                    <div class="relative flex-shrink-0">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Profile photo"
                                 class="w-20 h-20 rounded-full object-cover border-2 border-stone-200">
                            <button type="button" wire:click="removeAvatar"
                                    class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center transition-colors"
                                    title="Remove photo">✕</button>
                        @else
                            <div class="w-20 h-20 rounded-full bg-amber-100 border-2 border-amber-200 flex items-center justify-center text-2xl font-bold text-amber-600">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input wire:model="avatarFile" type="file" accept="image/*"
                               class="block w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                        <p class="mt-1 text-xs text-stone-400">JPG, PNG or GIF · Max 2MB</p>
                        <div wire:loading wire:target="avatarFile" class="mt-1 text-xs text-amber-600">Uploading…</div>
                        @error('avatarFile') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @if($avatarFile)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $avatarFile->temporaryUrl() }}" class="w-10 h-10 rounded-full object-cover border border-stone-200">
                                <span class="text-xs text-stone-500">Preview — save to apply</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <flux:input wire:model="name" label="{{ __('Name') }}" type="text" name="name" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" label="{{ __('Email') }}" type="email" name="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="mt-2 text-sm text-stone-800">
                            {{ __('Your email address is unverified.') }}

                            <button
                                wire:click.prevent="resendVerificationNotification"
                                class="rounded-md text-sm text-amber-600 underline hover:text-amber-700 focus:outline-hidden focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                            >
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
