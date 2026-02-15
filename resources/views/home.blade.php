<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KantinApp - Laravel</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f3f7ff, #e4f6f1);
            color: #1f2937;
            display: grid;
            place-items: center;
        }
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 16px;
            width: min(640px, 92vw);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.12);
        }
        h1 { margin-top: 0; }
        a {
            display: inline-block;
            text-decoration: none;
            background: #0f766e;
            color: #fff;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        code {
            background: #eef2ff;
            padding: 2px 6px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>KantinApp sudah pakai Laravel</h1>
        <p>Proyek utama sekarang berjalan dengan struktur Laravel.</p>
        <p>
            Aplikasi PHP lama disimpan sementara di
            <code>public/legacy</code> untuk transisi bertahap.
        </p>
        <a href="/legacy">Buka aplikasi versi lama</a>
    </main>
</body>
</html>
