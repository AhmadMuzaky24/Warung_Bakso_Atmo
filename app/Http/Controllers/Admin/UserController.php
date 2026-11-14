<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($data, $user) {
            // Lock active admin records before checking the last admin.
            $activeAdmins = User::query()
                ->where('role', 'admin')
                ->where('is_active', true)
                ->lockForUpdate()
                ->get();

            $targetUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            $isSelf = $targetUser->id === auth()->id();

            // Prevent an administrator from removing their own admin access.
            if ($isSelf && (
                $data['role'] !== 'admin'
                || ! (bool) $data['is_active']
            )) {
                throw ValidationException::withMessages([
                    'role' => 'Anda tidak dapat mengubah role atau menonaktifkan akun sendiri.',
                ]);
            }

            $removingAdminAccess =
                $targetUser->role === 'admin'
                && $targetUser->is_active
                && (
                    $data['role'] !== 'admin'
                    || ! (bool) $data['is_active']
                );

            if ($removingAdminAccess && $activeAdmins->count() <= 1) {
                throw ValidationException::withMessages([
                    'role' => 'Admin aktif terakhir tidak dapat diubah role atau dinonaktifkan.',
                ]);
            }

            // Password is optional when updating a user.
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $targetUser->update($data);
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'user' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);
        }

        DB::transaction(function () use ($user) {
            $activeAdmins = User::query()
                ->where('role', 'admin')
                ->where('is_active', true)
                ->lockForUpdate()
                ->get();

            $targetUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            if (
                $targetUser->role === 'admin'
                && $targetUser->is_active
                && $activeAdmins->count() <= 1
            ) {
                throw ValidationException::withMessages([
                    'user' => 'Admin aktif terakhir tidak dapat dihapus.',
                ]);
            }

            $targetUser->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
