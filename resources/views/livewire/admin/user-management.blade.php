<div class="space-y-5">
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-56">
        </div>
        <div class="flex gap-2">
            <select wire:model.live="roleFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Roles</option>
                @foreach(['admin','manager','kitchen','waiter','bar','customer'] as $r)
                <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-stone-50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">User</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Role</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Phone</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Joined</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                         class="h-9 w-9 rounded-full object-cover flex-shrink-0 border border-stone-200">
                                @else
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                        {{ $user->initials() }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-stone-800 text-sm">{{ $user->name }}</div>
                                    <div class="text-stone-400 text-xs">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                {{ match($user->role) {
                                    'admin' => 'bg-purple-100 text-purple-700',
                                    'manager' => 'bg-blue-100 text-blue-700',
                                    'kitchen' => 'bg-orange-100 text-orange-700',
                                    'waiter' => 'bg-cyan-100 text-cyan-700',
                                    'bar' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-stone-100 text-stone-600'
                                } }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-full font-medium {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $user->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Edit</button>
                                @if($user->id !== auth()->id())
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Delete this user?" class="text-sm text-red-500 hover:text-red-600 font-medium">Delete</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-16 text-stone-400">No users found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-50">{{ $users->links() }}</div>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit User' : 'Add User' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                {{-- Avatar upload --}}
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Profile Photo</label>
                    <div class="flex items-center gap-4">
                        <div class="relative flex-shrink-0">
                            @if($avatarFile)
                                <img src="{{ $avatarFile->temporaryUrl() }}" class="h-16 w-16 rounded-full object-cover border-2 border-amber-200">
                            @elseif($existingAvatar)
                                <img src="{{ Storage::url($existingAvatar) }}" class="h-16 w-16 rounded-full object-cover border-2 border-stone-200">
                                <button type="button" wire:click="removeUserAvatar({{ $editingId }})"
                                        class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center"
                                        title="Remove photo">✕</button>
                            @else
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-lg">
                                    {{ $name ? strtoupper(substr($name, 0, 1)) : '?' }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input wire:model="avatarFile" type="file" accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                            <p class="mt-1 text-xs text-stone-400">JPG, PNG · Max 2MB</p>
                            <div wire:loading wire:target="avatarFile" class="mt-1 text-xs text-amber-600">Uploading…</div>
                            @error('avatarFile') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Full Name *</label>
                        <input wire:model="name" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Email *</label>
                        <input wire:model="email" type="email" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Phone</label>
                        <input wire:model="phone" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Role *</label>
                        <select wire:model="role" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            @foreach(['admin','manager','kitchen','waiter','bar','customer'] as $r)
                            <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Password {{ $editing ? '(leave blank to keep)' : '*' }}</label>
                        <input wire:model="password" type="password" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="h-4 w-4 accent-amber-500">
                    <span class="text-sm text-stone-700">Account Active</span>
                </label>
                <div class="flex gap-3 pt-2">
                    <button wire:click="save" class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">{{ $editing ? 'Update' : 'Create' }}</button>
                    <button wire:click="$set('showModal', false)" class="px-6 py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
