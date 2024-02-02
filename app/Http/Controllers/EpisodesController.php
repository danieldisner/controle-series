<?php

namespace App\Http\Controllers;
use App\Models\Season;
use App\Models\Episode;
use Illuminate\Http\Request;

class EpisodesController
{
    public function index(Season $season)
    {
        return view('episodes.index', [
            'episodes' => $season->episodes,
            'mensagemSucesso' => session('mensagem.sucesso')]);
    }

    public function update(Request $request, Season $season)
    {
        $watchedEpisodes = $request->episodes;

        // Melhorar performance nessa função:
        // Está disparando várias selects ao mesmo tempo
        // Porém está funcional
        $season->episodes->each(function (Episode $episode) use ($watchedEpisodes){
            $episode->watched = in_array($episode->id, $watchedEpisodes);
        });

        $season->push();

        return to_route('episodes.index', $season->id)
        ->with('mensagem.sucesso', "Episódios Marcados como Assistidos");
    }
}
