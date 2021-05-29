@extends('layouts.app')

@section('content')        
    <div class="container">

        <form method="POST" action="{{ route('login') }}" class="form-signin"> 
            @csrf
            <img class="mb-4" src="{{ asset('inicio.png') }}" alt="" width="300" height="150">
            <h1 class="h3 mb-3 font-weight-normal">{{ __('Login') }}</h1>

            <label for="inputEmail" class="sr-only">Correo electrónico</label>

            <input id="email" type="email" placeholder="Correo electrónico" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="inputPassword" class="sr-only">contraseña</label>

            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="contraseña" name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <button type="submit" class="btn btn-lg btn-primary btn-block">
                {{ __('Login') }}
            </button>

            @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                    ¿Ha olvidado su contraseña?
                </a>
            @endif

            @if (Route::has('register'))
                <a class="btn btn-link" href="{{ route('register') }}">
                    ¿No tienes una cuenta? Regístrate
                </a>
            @endif

            <p class="mt-5 mb-3 text-muted">&copy; {{ date('Y') }}</p>
        </form>
    </div>
@endsection
