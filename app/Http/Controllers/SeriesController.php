<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesFormRequest;
use App\Events\SeriesCreated;
use App\Models\Episode;
use App\Models\Season;
use App\Models\Series;
use App\Repositories\SeriesRepository;

use Illuminate\Http\Request;
use App\Http\Middleware\Authenticate;

class SeriesController extends Controller
{
    public function __construct(private SeriesRepository $repository)
    {
        $this->middleware(Authenticate::class)->except('index');
    }
    public function index(Request $request)
    {
        $series = Series::all();
        $mensagemSucesso = session('mensagem.sucesso');

        return view('series.index')->with('series', $series)
            ->with('mensagemSucesso', $mensagemSucesso);
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesFormRequest $request)
    {
        /* Adicionar Validação de tipo de arquivo */

        $coverPath = $request->file('cover')->store('series_cover', 'public');

        $request->coverPath = $coverPath;

        // Aplicar Design Pattern para reduzir o código
        $series = $this->repository->add($request);

        $seriesCreatedEvent = SeriesCreated::dispatch(
            $series->nome,
            $series->id,
            $request->seasonsQty,
            $request->episodesPerSeason,
        );
        return to_route('series.index')
            ->with('mensagem.sucesso', "Série \"$series->nome\" adicionada com sucesso");
    }

    public function destroy(Series $series)
    {
        $series->delete();

        return to_route('series.index')
            ->with('mensagem.sucesso', "Série \"$series->nome\" removida com sucesso");
    }

    public function edit(Series $series)
    {
        return view('series.edit')->with('serie', $series);
    }

    public function update(Series $series, SeriesFormRequest $request)
    {

        $coverPath = $request->file('cover')->store('series_cover', 'public');

        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('series_cover', 'public');
            // Update the cover path in the Series model
            $series->cover = $coverPath;
        }

        $series->update($request->except('cover'));

        // Handle file upload if a new file is uploaded
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('series_cover', 'public');
            $series->cover = $coverPath;
            $series->save();
        }

        // Save the series
        $series->save();

        $series->seasons()->delete();

        $seasons = [];
        for ($i = 1; $i <= $request->seasonsQty; $i++) {
            $seasons[] = [
                'series_id' => $series->id,
                'number' => $i,
            ];
        }
        Season::insert($seasons);

        $episodes = [];
        foreach ($series->seasons as $season) {
            for ($j = 1; $j <= $request->episodesPerSeason; $j++) {
                $episodes[] = [
                    'season_id' => $season->id,
                    'number' => $j
                ];
            }
        }

        Episode::insert($episodes);

        return to_route('series.index')
            ->with('mensagem.sucesso', "Série \"$series->nome\" atualizada com sucesso");
    }
}
