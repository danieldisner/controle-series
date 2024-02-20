<form action="{{ $action }}" method="post" enctype="multipart/form-data">
    @csrf

    @if($update)
    @method('PUT')
    @endif

    <div class="mb-3 row">
        <div class="col-8">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text"
                id="nome"
                name="nome"
                class="form-control"
                autofocus
                value="{{ old('nome') }}" />
        </div>
        <div class="col-2">
            <label for="seasonsQty" class="form-label">Nº Temporadas</label>
            <input type="text"
                id="seasonsQty"
                name="seasonsQty"
                class="form-control"
                value="{{ old('seasonsQty') }}" />
        </div>
        <div class="col-2">
            <label for="episodesPerSeason" class="form-label">Eps / Temporada:</label>
            <input type="text"
                id="episodesPerSeason"
                name="episodesPerSeason"
                class="form-control"
                value="{{ old('episodesPerSeason') }}" />
        </div>
        <div class="mb-3 row">
            <div class="col-12">
                <label for="cover" class="form-lab">Capa</label>
                @if(isset($serie) && $serie->cover)
                    <img class="me-3" src="{{ asset('storage/' . $serie->cover) }}" width="60px">
                @endif
                <input type="file" id="cover" name="cover" class="form-control" accept="image/*">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Adicionar</button>
</form>
