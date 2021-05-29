@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">

            {{-- Menu --}}

            {{-- Se comenta ya que solo existe un modulo --}}

            {{-- <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">
                                <span data-feather="users"></span>
                                Usuarios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav> --}}

            <main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-4">

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <template>
                    <user></user>
                </template>
            </main>
        </div>
    </div>        
@endsection
