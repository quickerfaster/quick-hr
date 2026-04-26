@php
    /*use QuickerFaster\UILibrary\Services\Config\ConfigResolver;
    $resolver = app(ConfigResolver::class, ['configKey' => "hr.employee"]);
    $config = $resolver->getConfig();
    $customComponent = !empty($config['detailComponent']) ? $config['detailComponent'] : 'qf.data-table-detail';*/

    // Find employee linked to the logged-in user
    $employee = App\Modules\Hr\Models\Employee::where('user_id', Auth::id())->firstOrFail();
    $recordId = $employee->id;
    $returnParams = []; // no table state needed
    $customComponent = 'qf.employee-detail';

@endphp

<x-qf::navigation-layout configKey="hr.employee" context="people" moduleName="hr" :overrides="[
    'top_bar' => ['enabled' => true],
    'breadcrumb' => ['enabled' => false],
    'title' => ['enabled' => false],
    'titleRow' => ['enabled' => false],
    'context_menu' => ['enabled' => false],
]">
    @livewire($customComponent, ['inline' => true, 'recordId' => $recordId, 'configKey' => 'hr.employee', 'returnParams' => $returnParams])
</x-qf::navigation-layout>
