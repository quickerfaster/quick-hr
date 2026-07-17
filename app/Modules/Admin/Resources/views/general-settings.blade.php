<div>
<x-qf::navigation-layout
    configKey="admin.dashboards.dashboard"
    context="General Settings"
    moduleName="admin"
    :overrides="[
        'top_bar' => ['enabled' => false],
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => false],
    ]"
>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button onclick="history.back()" class="btn btn-sm btn-outline-secondary">&larr; Back</button>
    </div>
<br /><br/>
    <livewire:qf.settings-panel mode="system" />

</x-qf::navigation-layout>
</div>

