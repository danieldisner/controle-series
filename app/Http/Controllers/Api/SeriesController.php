<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeriesRequest;
use App\Models\Series;

class SeriesController extends Controller
{
    // Method to fetch all series
    public function index()
    {
        return Series::all();
    }

    // Method to store a new series
    public function store(SeriesRequest $request)
    {
        // Retrieve the cover path from the request input
        $coverPath = $request->input('cover');

        // Normalize the path by replacing backslashes with forward slashes
        $coverPath = str_replace("\\", "/", $coverPath);

        // Create a new series record in the database with data from the request and return as JSON response with status code 201 (Created)
        return response()->json(Series::create($request->all()), 201);
    }

    // Method to handle file upload for series cover
    public function upload(SeriesRequest $request)
    {
        // Initialize variable to hold the path of the uploaded cover image
        $coverPath = null;

        // Check if the request contains a file named 'cover'
        if ($request->hasFile('cover')) {
            // If 'cover' file is found, store it in the 'series_cover' directory under the 'public' disk
            $coverPath = $request->file('cover')->store('series_cover', 'public');
        } else {
            // If no 'cover' file is found in the request, return a JSON response with an error message and status code 400 (Bad Request)
            return response()->json(['error' => 'Nenhum arquivo foi enviado.'], 400);
        }

        // Merge the coverPath into the request data. This might be useful for further processing or saving to a database.
        $request->merge(['coverPath' => $coverPath]);

        // Return a JSON response containing the file path of the uploaded cover image
        return response()->json(['file_path' => $coverPath]);
    }
}
