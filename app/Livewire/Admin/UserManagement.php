<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'User Management'])]
class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

    #[Url] public string $search = '';
    #[Url] public string $roleFilter = '';

    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'customer';
    public string $password = '';
    public bool $isActive = true;
    public $avatarFile = null;
    public ?string $existingAvatar = null;

    public function openCreate(): void
    {
        $this->reset(['name','email','phone','password','editingId','avatarFile','existingAvatar']);
        $this->role = 'customer';
        $this->isActive = true;
        $this->editing = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->isActive = $user->is_active;
        $this->password = '';
        $this->avatarFile = null;
        $this->existingAvatar = $user->avatar;
        $this->editing = true;
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            'role'       => 'required|in:admin,manager,kitchen,waiter,customer',
            'avatarFile' => 'nullable|image|max:2048',
        ];
        if (!$this->editing || $this->password) {
            $rules['password'] = 'required|string|min:8';
        }
        $this->validate($rules);

        $data = ['name' => $this->name, 'email' => $this->email, 'phone' => $this->phone ?: null, 'role' => $this->role, 'is_active' => $this->isActive];
        if ($this->password) $data['password'] = Hash::make($this->password);

        if ($this->avatarFile) {
            $data['avatar'] = $this->avatarFile->store('avatars', 'public');
        }

        if ($this->editing && $this->editingId) {
            $user = User::findOrFail($this->editingId);
            $previousRole = $user->role;
            if ($this->avatarFile && $user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->update($data);
            if ($previousRole !== $this->role) {
                ActivityLog::record(
                    'user.role_changed',
                    "Changed {$user->name}'s role from {$previousRole} to {$this->role}",
                    $user,
                    ['from' => $previousRole, 'to' => $this->role]
                );
            }
            session()->flash('success', 'User updated.');
        } else {
            $user = User::create(array_merge($data, ['email_verified_at' => now()]));
            ActivityLog::record('user.created', "Created user {$user->name} ({$user->role})", $user, ['role' => $user->role]);
            session()->flash('success', 'User created.');
        }
        $this->showModal = false;
    }

    public function removeUserAvatar(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        if ($this->editingId === $id) {
            $this->existingAvatar = null;
        }
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return;
        $user->update(['is_active' => !$user->is_active]);
        ActivityLog::record(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            ($user->is_active ? 'Activated' : 'Deactivated')." user {$user->name}",
            $user
        );
    }

    public function delete(int $id): void
    {
        if ($id === auth()->id()) return;
        $user = User::findOrFail($id);
        ActivityLog::record('user.deleted', "Deleted user {$user->name} ({$user->email})", $user, ['email' => $user->email, 'role' => $user->role]);
        $user->delete();
    }

    public function render()
    {
        $users = User::when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->withCount('orders')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.admin.user-management', ['users' => $users]);
    }
}
