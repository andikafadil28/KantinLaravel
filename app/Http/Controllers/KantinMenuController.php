<?php

namespace App\Http\Controllers;

use App\Models\KantinKategoriMenu;
use App\Models\KantinKios;
use App\Models\KantinMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KantinMenuController extends Controller
{
    private function denyKasirMenuMutation(Request $request): ?RedirectResponse
    {
        if ((int) $request->session()->get('level_kantin', 0) === 2) {
            return back()->withErrors([
                'menu' => 'Akses ditolak. Kasir hanya bisa melihat data menu.',
            ]);
        }

        return null;
    }

    public function index(): View
    {
        $activeKiosNames = KantinKios::query()
            ->where('status', 1)
            ->pluck('nama');

        // Ambil seluruh master data untuk kebutuhan form dan tabel menu.
        return view('app.menu.index', [
            'menus' => KantinMenu::query()
                ->whereIn('nama_toko', $activeKiosNames)
                ->orderByDesc('id')
                ->get(),
            'kios' => KantinKios::query()->where('status', 1)->orderBy('nama')->get(),
            'kategories' => KantinKategoriMenu::query()->orderBy('kategori_menu')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($denied = $this->denyKasirMenuMutation($request)) {
            return $denied;
        }

        // Validasi input + upload gambar menu.
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['required', 'string', 'max:500'],
            'kategori' => ['required', 'integer'],
            'nama_toko' => [
                'required',
                'string',
                'max:100',
                Rule::exists('tb_kios', 'nama')->where(fn ($query) => $query->where('status', 1)),
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'pajak' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['foto'] = $request->hasFile('foto')
            ? $request->file('foto')->store('menu', 'public')
            : '';
        $data['status'] = 1;
        KantinMenu::query()->create($data);

        return back()->with('ok', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        if ($denied = $this->denyKasirMenuMutation($request)) {
            return $denied;
        }

        $menu = KantinMenu::query()->findOrFail($id);
        // Foto opsional saat update.
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'kategori' => ['required', 'integer'],
            'nama_toko' => [
                'required',
                'string',
                'max:100',
                Rule::exists('tb_kios', 'nama')->where(fn ($query) => $query->where('status', 1)),
            ],
            'harga' => ['required', 'numeric', 'min:0'],
            'pajak' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);
        $data['keterangan'] = $data['keterangan'] ?? '';

        if ($request->hasFile('foto')) {
            // Hapus foto lama agar storage tidak menumpuk.
            if (!empty($menu->foto) && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);

        return back()->with('ok', 'Menu berhasil diperbarui.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        if ($denied = $this->denyKasirMenuMutation($request)) {
            return $denied;
        }

        $menu = KantinMenu::query()->findOrFail($id);
        $data = $request->validate([
            'status' => ['required', 'in:0,1'],
        ]);

        $menu->update([
            'status' => (int) $data['status'],
        ]);

        return back()->with('ok', 'Status menu berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        if ($denied = $this->denyKasirMenuMutation($request)) {
            return $denied;
        }

        $menu = KantinMenu::query()->findOrFail($id);
        // Bersihkan file foto ketika data menu dihapus.
        if (!empty($menu->foto) && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }
        $menu->delete();

        return back()->with('ok', 'Menu berhasil dihapus.');
    }
}
