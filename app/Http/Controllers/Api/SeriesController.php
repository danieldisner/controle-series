<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\SeriesCreated;
use App\Http\Requests\SeriesRequest;
use App\Repositories\SeriesRepository;
use App\Models\Series;

class SeriesController extends Controller
{
    public function __construct(private SeriesRepository $repository)
    {
    }

    // Method to fetch all series
    public function index()
    {
        return Series::all();
    }

    // Method to store a new series
    public function store(SeriesRequest $request)
    {
        // Check if the request contains a file named 'cover'
        if ($request->hasFile('cover')) {
            // Store the 'cover' file in the 'series_cover' directory under the 'public' disk
            $coverPath = $request->file('cover')->store('series_cover', 'public');
        } else {
            // If no 'cover' file is found in the request, return a JSON response with an error message and status code 400 (Bad Request)
            return response()->json(['error' => 'Nenhum arquivo foi enviado.'], 400);
        }

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
}
