<?php

namespace App\Modules\Admin\Http\Controllers;

use App\Modules\Admin\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ActivityLog::paginate(15);
        return view('admin.activity-logs.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.activity-logs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(ActivityLog::rules());
        $item = ActivityLog::create($validated);
        return redirect()->route('admin.activity_log.index')
            ->with('success', 'ActivityLog created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.show', ['activityLog' => $activityLog]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityLog $activityLog)
    {
        return view('admin.activity-logs.edit', ['activityLog' => $activityLog]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        $validated = $request->validate(ActivityLog::rules());
        $activityLog->update($validated);
        return redirect()->route('admin.activity_log.index')
            ->with('success', 'ActivityLog updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return redirect()->route('admin.activity_log.index')
            ->with('success', 'ActivityLog deleted successfully.');
    }
}
