<?php

namespace App\Modules\Admin\Http\Controllers\Api;

use App\Modules\Admin\Models\JobTitle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(JobTitle::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(JobTitle::rules());
        $item = JobTitle::create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobTitle $jobTitle)
    {
        return response()->json($jobTitle);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobTitle $jobTitle)
    {
        $validated = $request->validate(JobTitle::rules());
        $jobTitle->update($validated);
        return response()->json($jobTitle);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTitle $jobTitle)
    {
        $jobTitle->delete();
        return response()->json(null, 204);
    }
}
