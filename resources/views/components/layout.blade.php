<!-- resources/views/components/layout.blade.php -->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }} - Controle de Séries</title>
    <link rel="stylesheet" href="{{ asset('build/assets/app-8ae1c78e.css') }}"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
    <div class="container">
        <h1>{{ $title }}</h1>
        @isset($mensagemSucesso)
        <div class="alert alert-success">
            {{ $mensagemSucesso }}
        </div>
        @endisset
        {{ $slot }}

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ( $errors->all() as $error )
                        <li> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif
    </div>
</body>
</html>
