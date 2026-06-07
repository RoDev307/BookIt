@extends('admin.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Panel Maestro: Control de Inquilinos</h1>
                <p class="text-xs text-slate-500 mt-1">Monitoreo, edición y control global de todos los comercios e
                    instancias activas en BookIt.</p>
            </div>
            <div>
                <span
                    class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-700 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Conexión Segura: Aiven Cluster
                </span>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-4">ID</th>
                        <th class="p-4">Nombre del Comercio</th>
                        <th class="p-4">Slug en URL</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    @foreach ($businesses as $b)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 text-slate-400">#{{ $b->id }}</td>
                            <td class="p-4 font-bold text-slate-900">{{ $b->name }}</td>
                            <td class="p-4 text-xs text-indigo-600 font-mono">{{ $b->slug }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('master.businesses.edit', $b->id) }}"
                                    class="text-xs bg-slate-900 text-white font-bold px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors">
                                    ⚙️ Controlar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
