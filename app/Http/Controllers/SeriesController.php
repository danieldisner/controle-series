<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesRequest;
use App\Events\SeriesCreated;
use App\Models\Episode;
use App\Models\Season;
use App\Models\Series;
use App\Repositories\SeriesRepository;
use Illuminate\Support\Facades\Storage;
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
        $series = Series::paginate(15);
        $mensagemSucesso = session('mensagem.sucesso');

        return view('series.index')->with('series', $series)
            ->with('mensagemSucesso', $mensagemSucesso);
    }

    public function create()
    {
        return view('series.create');
    }

    public function store(SeriesRequest $request)
    {
        /* Adicionar Validação de tipo de arquivo */
        $coverPath = $request->file('cover') ? $request->file('cover')->store('series_cover', 'public') : null;

        /*
        *
        *   // $request->coverPath = $coverPath;
        *   By merging coverPath into the request,
        *   you're ensuring that the property is defined before
        *   attempting to access it, thus avoiding the "Undefined property"
        *   error.
        */
        $request->merge(['coverPath' => $coverPath]);

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
        // Delete the cover image if it exists
        if ($series->cover) {
            if (Storage::disk('public')->exists($series->cover)) {
                Storage::disk('public')->delete($series->cover);
            }
        }

        // Delete the series
        $series->delete();

        return redirect()->route('series.index')
            ->with('mensagem.sucesso', "Série \"$series->nome\" removida com sucesso");
    }

    public function edit(Series $series)
    {
        return view('series.edit')->with('serie', $series);
    }

    /*
    * Reduzir esse método, está muito grande, aplicar desing pattern
    */
    public function update(Series $series, SeriesRequest $request)
    {
        // Handle Cover Image Update
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('series_cover', 'public');

            // Delete the old cover image
            if ($series->cover && Storage::disk('public')->exists($series->cover)) {
                Storage::disk('public')->delete($series->cover);
            }

            // Update cover path in Series model
            $series->cover = $coverPath;
        }

        // Update Series Information
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
