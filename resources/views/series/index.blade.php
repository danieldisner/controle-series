<x-layout title="Séries" :mensagem-sucesso="$mensagemSucesso">
    @auth
        <a href="{{ route('series.create') }}" class="mb-2 btn btn-dark">Adicionar</a>
    @endauth

    <ul class="list-group">
        @foreach ($series as $serie)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <img class="me-3" src="{{ asset('storage/' . (isset($serie->cover) ? $serie->cover : 'series_cover/default.jpg')) }}" width="60px">
            @auth <a href="{{ route('seasons.index', $serie->id) }}"> @endauth
                {{ $serie->nome }}
            @auth</a>
            <span class="d-flex">
                <a href="{{ route('series.edit', $serie->id) }}" class="btn btn-primary btn-sm">
                    E
                </a>

                <form action="{{ route('series.destroy', $serie->id) }}" method="post" class="ms-2">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                        X
                    </button>
                </form>
            </span>
            @endauth
        </li>
        @endforeach
        {{ $series->links() }}
    </ul>
</x-layout>
