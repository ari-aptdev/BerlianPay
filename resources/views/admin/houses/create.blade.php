@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Tambah rumah</h2>
<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.houses.store') }}">
        @include('admin.houses._form')
    </form>
</div>
@endsection
