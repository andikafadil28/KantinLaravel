<?php

namespace App\Http\Controllers;

use App\Models\KantinKios;
use App\Models\KantinUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KantinAdminController extends Controller
{
    public function users(Request $request): View
    {
        // Modul user hanya boleh diakses level admin.
        $this->ensureAdmin($request);

        return view('app.admin.users', [
            'users' => KantinUser::query()->orderByDesc('id')->get(),
            'kios' => KantinKios::query()->orderBy('nama')->get(),
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        // Validasi data user baru.
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:4'],
            'level' => ['required', 'integer'],
            'Kios' => ['required', 'string', 'max:50'],
        ]);

        if (KantinUser::query()->where('username', $data['username'])->exists()) {
            return back()->withErrors(['user' => 'Username sudah terdaftar.']);
        }

        // Password langsung disimpan dalam bentuk hash.
        KantinUser::query()->create([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'level' => $data['level'],
            'Kios' => $data['Kios'],
        ]);

        return back()->with('ok', 'User berhasil ditambahkan.');
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin($request);
        $user = KantinUser::query()->findOrFail($id);

        // Password bersifat opsional saat edit user.
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:4'],
            'level' => ['required', 'integer'],
            'Kios' => ['required', 'string', 'max:50'],
        ]);

        $payload = [
            'username' => $data['username'],
            'level' => $data['level'],
            'Kios' => $data['Kios'],
        ];
        if (!empty($data['password'])) {
            $payload['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $user->update($payload);

        return back()->with('ok', 'User berhasil diperbarui.');
    }

    public function deleteUser(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin($request);
        KantinUser::query()->where('id', $id)->delete();

        return back()->with('ok', 'User berhasil dihapus.');
    }

    public function kios(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('app.admin.kios', [
            'kios' => KantinKios::query()->orderByDesc('id')->get(),
        ]);
    }

    public function storeKios(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);
        // Tambah master kios baru.
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
        ]);

        if (KantinKios::query()->where('nama', $data['nama'])->exists()) {
            return back()->withErrors(['kios' => 'Kios sudah terdaftar.']);
        }

        KantinKios::query()->create($data);

        return back()->with('ok', 'Kios berhasil ditambahkan.');
    }

    public function updateKios(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin($request);
        $kios = KantinKios::query()->findOrFail($id);
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
        ]);
        $kios->update($data);

        return back()->with('ok', 'Kios berhasil diperbarui.');
    }

    public function deleteKios(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdmin($request);
        KantinKios::query()->where('id', $id)->delete();

        return back()->with('ok', 'Kios berhasil dihapus.');
    }

    private function ensureAdmin(Request $request): void
    {
        // Level 1 = admin penuh.
        if ((int) $request->session()->get('level_kantin', 0) !== 1) {
            abort(403);
        }
    }
}
