
<x-mail::message>
    # {{ $nomeSerie }} foi criada.
    A série {{  $nomeSerie }} com {{ $qtdTemporadas }} temporadas e {{ $episodiosPorTemporada }} episódios.

    Acesse aqui:

<x-mail::button :url="route('seasons.index', $idSerie)">
    Ver Série
</x-mail::button>
</x-mail::message>
