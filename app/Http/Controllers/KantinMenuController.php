<?php

namespace App\Http\Controllers;

use App\Models\KantinKategoriMenu;
use App\Models\KantinKios;
use App\Models\KantinMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KantinMenuController extends Controller
{
    public function index(): View
    {
        return view('app.menu.index', [
            'menus' => KantinMenu::query()->orderByDesc('id')->get(),
            'kios' => KantinKios::query()->orderBy('nama')->get(),
            'kategories' => KantinKategoriMenu::query()->orderBy('kategori_menu')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['required', 'string', 'max:500'],
            'kategori' => ['required', 'integer'],
            'nama_toko' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'pajak' => ['required', 'numeric', 'min:0'],
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        $data['foto'] = $request->file('foto')->store('menu', 'public');
        KantinMenu::query()->create($data);

        return back()->with('ok', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $menu = KantinMenu::query()->findOrFail($id);
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['required', 'string', 'max:500'],
            'kategori' => ['required', 'integer'],
            'nama_toko' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'pajak' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if (!empty($menu->foto) && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $data['foto'] = $request->file('foto')->store('menu', 'public');
        }

        $menu->update($data);

        return back()->with('ok', 'Menu berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $menu = KantinMenu::query()->findOrFail($id);
        if (!empty($menu->foto) && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }
        $menu->delete();

        return back()->with('ok', 'Menu berhasil dihapus.');
    }
}
