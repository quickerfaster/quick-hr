<x-qf::navigation-layout configKey="hr.location" context="Company Profile" moduleName="hr" :overrides="[
        'top_bar' => ['enabled' => true],
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => true],
    ]">
    <livewire:qf.data-table-form configKey="hr.location" :recordId="$recordId" :inline="true" :returnParams="$returnParams" />
</x-qf::navigation-layout>