@php
    use QuickerFaster\UILibrary\Services\Config\ConfigResolver;
    $resolver = app(ConfigResolver::class, ['configKey' => "hr.document"]);
    $config = $resolver->getConfig();
    $customComponent = !empty($config['detailComponent']) ? $config['detailComponent'] : 'qf.data-table-detail';
@endphp

<x-qf::navigation-layout configKey="hr.document" context="people" moduleName="hr" :overrides="[
        'top_bar' => ['enabled' => true],
        'breadcrumb' => ['enabled' => false],
        'title' => ['enabled' => false],
        'titleRow' => ['enabled' => false],
        'context_menu' => ['enabled' => true],
    ]">
    @livewire($customComponent, ["inline" => true, "recordId" => $recordId, "configKey" => "hr.document", "returnParams" => $returnParams])
</x-qf::navigation-layout>