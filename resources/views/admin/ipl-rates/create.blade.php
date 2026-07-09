@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Tambah tarif IPL</h2>
<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.ipl-rates.store') }}">
        @include('admin.ipl-rates._form')
    </form>
</div>
@endsection
