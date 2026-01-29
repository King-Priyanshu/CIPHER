<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('subscriber.projects.index', compact('projects'));
    }
}
