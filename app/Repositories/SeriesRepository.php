<?php
    namespace App\Repositories;

use App\Http\Requests\SeriesRequest;
use App\Models\Series;


interface SeriesRepository
{
    public function add(SeriesRequest $request) : Series;
}
