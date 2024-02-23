<?php

namespace Tests\Feature;

use App\Http\Requests\SeriesRequest;
use App\Repositories\SeriesRepository;
use Tests\TestCase; // Import Laravel's TestCase
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeriesRepositoryTest extends TestCase
{

    use RefreshDatabase;

    public function test_when_a_series_is_created_its_seasons_and_episodes_must_also_be_created(): void
    {
        // Arrange
        $repository = $this->app->make(SeriesRepository::class);
        $request = new SeriesRequest();

        $request->merge([
            'nome' => 'Nome da Série',
            'seasonsQty' => 1,
            'episodesPerSeason' => 1
         ]);

        // Act
        $repository->add($request);

        // Assert
        $this->assertDatabaseHas('series', ['nome' => 'Nome da série']);
        $this->assertDatabaseHas('seasons', ['number' => 1]);
        $this->assertDatabaseHas('episodes', ['number' => 1]);
    }
}
