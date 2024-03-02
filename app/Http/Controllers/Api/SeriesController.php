<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\SeriesCreated;
use App\Http\Requests\SeriesRequest;
use App\Repositories\SeriesRepository;
use App\Models\Series;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function __construct(private SeriesRepository $repository)
    {
    }

    // Method to fetch all series
    public function index(Request $request)
    {
        $query = Series::query();
        if ($request->has('nome')) {
            $query->where('nome', $request->nome);
            //return Series::paginate();
        }
        return $query->paginate();
    }

    // Method to store a new series
    public function store(SeriesRequest $request)
    {
        // Check if the request contains a file named 'cover'
        // Store the 'cover' file in the 'series_cover' directory under the 'public' disk
        $coverPath = $request->hasFile('cover')? $request->file('cover')->store('series_cover', 'public') : null;

        $request->merge(['coverPath' => $coverPath]);

        // Aplicar Design Pattern para reduzir o código
        $series = $this->repository->add($request);

        $seriesCreatedEvent = SeriesCreated::dispatch(
            $series->nome,
            $series->id,
            $request->seasonsQty,
            $request->episodesPerSeason,
        );

        return response()->json(['message' => 'Série criada com sucesso.', 'series' => $series], 201);
    }

    public function show(Series $series)
    {
        $seriesModel = Series::find($series);
        if($seriesModel == null){
            return response()->json(['message' => 'Series not found'], 404);
        }
        return $series;
    }

    public function update(Series $series, SeriesRequest $request)
    {
        $series->fill($request->all());
        $series->save();

        return $series;
    }

    public function destroy(int $series, Authenticatable $user)
    {
        dd($user->tokenCan('series:delete'));
        Series::destroy($series);
        return response()->noContent();
    }
}
