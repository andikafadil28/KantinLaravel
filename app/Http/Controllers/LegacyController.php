<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LegacyController extends Controller
{
    // Gunakan nama session khusus agar tidak bentrok dengan session Laravel.
    private const LEGACY_SESSION_NAME = 'LEGACYSESSID';
    // Mapping MIME agar asset legacy (css/js/font/gambar) terkirim dengan header benar.
    private const LEGACY_MIME_MAP = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'map' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'txt' => 'text/plain; charset=UTF-8',
    ];
    // Daftar halaman utama legacy yang diizinkan via parameter x.
    private const ALLOWED_X = [
        'home',
        'menu',
        'order',
        'orderitem',
        'user',
        'kios',
        'laporan',
        'history',
        'laporanrs',
        'laporantoko',
        'rekapmenurs',
        'rekaprs',
        'rekapkeuangan',
        'rekapkeuanganmenu',
        'login',
        'logout',
    ];
    // Ekstensi public yang boleh diserve langsung dari folder legacy_app.
    private const ALLOWED_LEGACY_PUBLIC_EXTENSIONS = [
        'css',
        'js',
        'map',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'svg',
        'webp',
        'ico',
        'pdf',
        'woff',
        'woff2',
        'ttf',
        'eot',
        'txt',
    ];

    public function root(Request $request): Response|RedirectResponse
    {
        // Kompatibilitas URL lama: /legacy?x=home -> /legacy/home
        $x = (string) $request->query('x', '');

        if ($x !== '') {
            $target = '/legacy/' . strtolower($x);
            $query = $request->query();
            unset($query['x']);

            return redirect($query === [] ? $target : ($target . '?' . http_build_query($query)));
        }

        if ($this->isLegacyLoggedIn()) {
            return redirect('/legacy/home');
        }

        return redirect('/legacy/login');
    }

    public function page(string $x): RedirectResponse
    {
        return redirect('/legacy/' . strtolower($x));
    }

    public function legacyPath(Request $request, ?string $path = null): Response|RedirectResponse|BinaryFileResponse
    {
        $path = trim((string) $path, '/');

        if ($path === '') {
            return $this->root($request);
        }

        if (in_array(strtolower($path), self::ALLOWED_X, true)) {
            // Path berupa halaman logical, dispatch ke flow legacy.
            return $this->dispatch($path, $request);
        }

        // Selain halaman logical, anggap sebagai file legacy (asset/php endpoint).
        return $this->serveLegacyFile($request, $path);
    }

    private function dispatch(string $x, Request $request): Response|RedirectResponse
    {
        $x = strtolower(trim($x));

        if (!in_array($x, self::ALLOWED_X, true)) {
            abort(404);
        }

        if ($x === 'logout') {
            // Logout khusus sesi legacy.
            $this->ensureLegacySession();
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            session_destroy();

            return redirect('/legacy/login');
        }

        if ($x === 'login' && $this->isLegacyLoggedIn()) {
            return redirect('/legacy/home');
        }

        if ($x !== 'login' && !$this->isLegacyLoggedIn()) {
            // Lindungi halaman legacy selain login.
            return redirect('/legacy/login');
        }

        return $this->renderLegacyIndex($x, $request);
    }

    private function renderLegacyIndex(string $x, Request $request): Response|RedirectResponse
    {
        $legacyDir = $this->legacyRoot();
        $legacyIndex = $legacyDir . DIRECTORY_SEPARATOR . 'index.php';

        if (!is_file($legacyIndex)) {
            abort(500, 'Legacy index.php tidak ditemukan.');
        }

        $query = $request->query();
        $query['x'] = $x;

        // Jalankan index.php legacy dengan query override agar router lama tetap bekerja.
        return $this->executePhpScript($request, $legacyIndex, $query);
    }

    private function serveLegacyFile(Request $request, string $path): Response|BinaryFileResponse
    {
        // Tolak path traversal.
        if (Str::contains($path, ['..', '\\'])) {
            abort(404);
        }

        if (!$this->isAllowedLegacyPath($path)) {
            abort(404);
        }

        $fullPath = $this->legacyRoot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (!is_file($fullPath)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if ($extension === 'php') {
            // Endpoint PHP lama tetap dieksekusi lewat bridge.
            return $this->executePhpScript($request, $fullPath);
        }

        $headers = [];
        if (isset(self::LEGACY_MIME_MAP[$extension])) {
            $headers['Content-Type'] = self::LEGACY_MIME_MAP[$extension];
        }

        return response()->file($fullPath, $headers);
    }

    private function isAllowedLegacyPath(string $path): bool
    {
        // Tolak dotfile/hidden path untuk keamanan.
        $cleanPath = ltrim($path, '/');
        $segments = array_filter(explode('/', $cleanPath), static fn (string $segment): bool => $segment !== '');
        foreach ($segments as $segment) {
            if (str_starts_with($segment, '.')) {
                return false;
            }
        }

        $extension = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
        if ($extension === 'php') {
            // File php dibatasi ketat ke folder endpoint tertentu saja.
            return $this->isAllowedLegacyPhpPath($cleanPath);
        }

        return in_array($extension, self::ALLOWED_LEGACY_PUBLIC_EXTENSIONS, true);
    }

    private function isAllowedLegacyPhpPath(string $path): bool
    {
        if ($path === 'index.php') {
            return true;
        }

        return preg_match('#^(validate|proses|excel_export|inc/modal)/[a-zA-Z0-9._/-]+\.php$#', $path) === 1;
    }

    private function executePhpScript(Request $request, string $scriptPath, ?array $getOverride = null): Response|RedirectResponse
    {
        // Simpan konteks global agar bisa dipulihkan setelah include script legacy.
        $scriptDir = dirname($scriptPath);
        $legacyRoot = $this->legacyRoot();
        $previousCwd = getcwd();
        $hadActiveSession = session_status() === PHP_SESSION_ACTIVE;
        $previousGet = $_GET ?? [];
        $previousPost = $_POST ?? [];
        $previousRequest = $_REQUEST ?? [];
        $previousFiles = $_FILES ?? [];
        $incomingFiles = $_FILES ?? [];
        $beforeHeaders = headers_list();

        $_GET = $getOverride ?? $request->query();
        $_POST = $request->post();
        $_FILES = $incomingFiles;
        $_REQUEST = array_merge($_GET, $_POST);
        $_SERVER['REQUEST_METHOD'] = strtoupper($request->method());

        if (!isset($_SERVER['DOCUMENT_ROOT'])) {
            $_SERVER['DOCUMENT_ROOT'] = public_path();
        }

        if ($hadActiveSession) {
            // Tutup sesi lama supaya session_name/path legacy bisa diterapkan aman.
            session_write_close();
        }
        $this->prepareLegacySessionContext();

        chdir($scriptDir);

        try {
            ob_start();
            include $scriptPath;
            $output = (string) ob_get_clean();
        } finally {
            $_GET = $previousGet;
            $_POST = $previousPost;
            $_REQUEST = $previousRequest;
            $_FILES = $previousFiles;
            if ($previousCwd !== false) {
                chdir($previousCwd);
            } else {
                chdir($legacyRoot);
            }
        }

        $afterHeaders = headers_list();
        $newHeaders = array_values(array_diff($afterHeaders, $beforeHeaders));
        $statusCode = 200;
        $responseHeaders = [];
        $location = null;

        foreach ($newHeaders as $headerLine) {
            [$name, $value] = array_pad(explode(':', $headerLine, 2), 2, '');
            $name = trim($name);
            $value = trim($value);
            if ($name === '') {
                continue;
            }

            if (strcasecmp($name, 'Location') === 0) {
                $location = $this->normalizeLegacyLocation($value);
                continue;
            }

            if (strcasecmp($name, 'HTTP/1.1') === 0 || strcasecmp($name, 'Status') === 0) {
                if (preg_match('/\b(\d{3})\b/', $value, $matches)) {
                    $statusCode = (int) $matches[1];
                }
                continue;
            }

            $responseHeaders[$name] = $value;
        }

        foreach (array_keys($responseHeaders) as $name) {
            header_remove($name);
        }
        header_remove('Location');

        if ($location !== null) {
            // Redirect dari script legacy dinormalisasi ke prefix /legacy.
            return redirect($location);
        }

        return response($output, $statusCode, $responseHeaders);
    }

    private function normalizeLegacyLocation(string $location): string
    {
        // Normalisasi berbagai format redirect lawas ke URL bridge Laravel.
        $location = trim($location);

        if ($location === '') {
            return '/legacy';
        }

        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $location = ltrim($location, './');
        $location = preg_replace('#^legacy/#', '', $location);
        $location = preg_replace('#^KantinSakina/#i', '', $location);
        $location = ltrim((string) $location, '/');

        if ($location === '') {
            return '/legacy';
        }

        return '/legacy/' . $location;
    }

    private function legacyRoot(): string
    {
        return base_path('legacy_app');
    }

    private function isLegacyLoggedIn(): bool
    {
        $this->ensureLegacySession();

        return !empty($_SESSION['username_kantin']);
    }

    private function ensureLegacySession(): void
    {
        // Pastikan sesi legacy aktif sebelum akses $_SESSION.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $this->prepareLegacySessionContext();
            session_start();
        }
    }

    private function prepareLegacySessionContext(): void
    {
        // Setup nama session + fallback save path khusus legacy.
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (session_name() !== self::LEGACY_SESSION_NAME) {
            session_name(self::LEGACY_SESSION_NAME);
        }

        $savePath = session_save_path();
        if ($savePath === '' || !is_dir($savePath) || !is_writable($savePath)) {
            $fallbackPath = storage_path('framework/legacy-sessions');
            if (!is_dir($fallbackPath)) {
                mkdir($fallbackPath, 0775, true);
            }
            session_save_path($fallbackPath);
        }
    }
}
