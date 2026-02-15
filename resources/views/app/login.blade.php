@extends('app.layouts.auth')

@section('title', 'Login Sakina Kantin')

@section('content')
<div class="card auth-card">
    <div class="card-body p-4 p-md-5">
        <h1 class="h4 text-center auth-title mb-4">Sakina Kantin</h1>

        @if ($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ url('/app/login') }}" class="needs-validation" novalidate>
            @csrf
            <div class="form-floating mb-3">
                <input id="username" name="username" type="text" class="form-control" value="{{ old('username') }}" placeholder="Username" required>
                <label for="username">ID Pegawai</label>
            </div>

            <div class="form-floating mb-3">
                <input id="password" name="password" type="password" class="form-control" placeholder="Password" required>
                <label for="password">Password</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Fallback legacy: <a href="{{ url('/legacy/login') }}">/legacy/login</a></small>
        </div>
    </div>
</div>
@endsection
