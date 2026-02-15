<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login Sakina Kantin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        /* Layout halaman autentikasi (login) */
        body {
            font-family: "Nunito", sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at top right, #6f8ef6 0%, #4e73df 35%, #224abe 100%);
            margin: 0;
        }

        .auth-card {
            /* Kartu login utama */
            width: min(420px, 92vw);
            border: 0;
            border-radius: .9rem;
            box-shadow: 0 .8rem 2.2rem rgba(17, 24, 39, .28);
        }

        .auth-title {
            /* Judul brand di halaman login */
            font-weight: 800;
            color: #4e73df;
        }
    </style>
</head>
<body>
@yield('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
