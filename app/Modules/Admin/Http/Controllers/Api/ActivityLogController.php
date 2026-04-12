<?php

namespace App\Modules\Admin\Http\Controllers\Api;

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
        return response()->json(ActivityLog::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(ActivityLog::rules());
        $item = ActivityLog::create($validated);
        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        return response()->json($activityLog);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        $validated = $request->validate(ActivityLog::rules());
        $activityLog->update($validated);
        return response()->json($activityLog);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return response()->json(null, 204);
    }
}
