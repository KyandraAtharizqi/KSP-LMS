<?php
namespace App\Http\Controllers;

use App\Models\ELearningVideo;
use Illuminate\Http\Request;

class ElearningVideoController extends Controller
{
    public function index(Request $request)
    {
        $query = ELearningVideo::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('topic')) {
            $query->where('topic', $request->topic);
        }

        $videos = $query->latest()->get();
        $topics = ELearningVideo::select('topic')->distinct()->pluck('topic');

        return view('pages.elearning.videos.index', compact('videos', 'topics'));
    }

    public function show(ELearningVideo $video)
    {
        return view('pages.elearning.videos.show', compact('video'));
    }

}
