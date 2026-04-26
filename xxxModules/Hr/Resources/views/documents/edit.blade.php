<x-qf::navigation-layout configKey="hr.document" context="people" moduleName="hr" :overrides="[
        'top_bar' => ['enabled' => true],
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => true],
    ]">
    <livewire:qf.data-table-form configKey="hr.document" :recordId="$recordId" :inline="true" :returnParams="$returnParams" />
</x-qf::navigation-layout>