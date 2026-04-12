<?php

namespace App\Modules\Admin\Http\Controllers;

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
        $items = JobTitle::paginate(15);
        return view('admin.job-titles.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.job-titles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(JobTitle::rules());
        $item = JobTitle::create($validated);
        return redirect()->route('admin.job_title.index')
            ->with('success', 'JobTitle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobTitle $jobTitle)
    {
        return view('admin.job-titles.show', ['jobTitle' => $jobTitle]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobTitle $jobTitle)
    {
        return view('admin.job-titles.edit', ['jobTitle' => $jobTitle]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobTitle $jobTitle)
    {
        $validated = $request->validate(JobTitle::rules());
        $jobTitle->update($validated);
        return redirect()->route('admin.job_title.index')
            ->with('success', 'JobTitle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTitle $jobTitle)
    {
        $jobTitle->delete();
        return redirect()->route('admin.job_title.index')
            ->with('success', 'JobTitle deleted successfully.');
    }
}
