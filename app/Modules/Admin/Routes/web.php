<?php

use Illuminate\Support\Facades\Route;



Route::group([
    'prefix' => 'access',
], function () {
    Route::get('access-control-management/{module}', function () {

        // Chech if only admin can access this view. If the user is not admin do not proceed
        if (!auth()->check() || !auth()->user()->hasRole(['admin', 'super_admin'])) {
            abort(403, 'Unauthorized');
        }


        return view('admin::access-control-management', [
            'selectedModule' => request("module"),
            'isUrlAccess' => true,
        ]);
    });
});


















// Routes for Location

// Create Route
Route::get('locations/create', function (\Illuminate\Http\Request $request) {
    return view('admin::locations.create', [
        'configKey' => 'admin.location',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('locations.create');

// Show Route
Route::get('locations/{id}', function (\Illuminate\Http\Request $request, $id) {
    return view('admin::locations.show', [
        'recordId' => (int) $id,
        'configKey' => 'admin.location',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('locations.show')->where('id', '[0-9]+'); 

// Edit Route
Route::get('locations/{id}/edit', function (\Illuminate\Http\Request $request, $id) {
    return view('admin::locations.edit', [
        'recordId' => (int) $id,
        'configKey' => 'admin.location',
        'returnParams' => $request->only(['page', 'perPage', 'search', 'sort', 'activeFilters'])
    ]);
})->name('locations.edit')->where('id', '[0-9]+'); // And here;
