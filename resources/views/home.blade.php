@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200">
                <h1 class="text-lg font-semibold text-slate-800">
                    Dashboard
                </h1>
            </div>

            <div class="p-6 space-y-4">

                @if (session('status'))
                    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="text-slate-600">
                    You are logged in!
                </p>

            </div>
        </div>

    </div>
@endsection