@php
    use App\Helpers\PermissionHelper;
@endphp

@include('modals.add_users')
@include('modals.nuevo_pais')
@if(PermissionHelper::canViewModule('academias'))
    @include('modals.academias_modal')
@endif
@include('modas.asigpermis_modal')
@include('modals.ciudad_modal')
@include('modals.depto_modal')
@include('modals.federaciones_modal')
@include('modals.fides_modal')
@include('modals.inscripciones_modal')
@include('modals.miembros_modal)
@include('modals.pais_modal')
@include('modals.partidasbusqueda_modal')
@include('modals.torneo_modal')
