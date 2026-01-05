<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class DocViewerController extends Controller
{
    public function show($student, $type, $file)
    {
        // optional policy check
        // $this->authorize('view', $student);

        $path = "students/{$student}/{$type}/{$file}";
        if (!Storage::exists($path)) abort(404);

        $mime = Storage::mimeType($path);
        $content = Storage::get($path);

        // tell browser to *display* not download
        return Response::make($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($file) . '"'
        ]);
    }
}
