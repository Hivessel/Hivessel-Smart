<?php

namespace App\Http\Controllers\Plugins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssessmentToolController extends Controller
{
    public function index(){
        return Inertia::render('Client/Plugins/AssessmentTool');
    }
}
