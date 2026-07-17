<x-qf::navigation-layout
    configKey="hr.dashboards.dashboard"
    context="people"
    moduleName="hr"
    :overrides="[
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => false],
    ]"
>
    <livewire:qf.settings-panel mode="user" />
</x-qf::navigation-layout>
