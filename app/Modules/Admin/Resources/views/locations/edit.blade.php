<x-qf::navigation-layout configKey="admin.location" context="Company Profile" moduleName="admin" :overrides="[
        'top_bar' => ['enabled' => true],
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => true],
    ]">
    <livewire:qf.data-table-form configKey="admin.location" :recordId="$recordId" :inline="true" :returnParams="$returnParams" />
</x-qf::navigation-layout>