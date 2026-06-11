@if(session('status'))
    <div class="mb-4 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
        {{ session('status') }}
    </div>
@endif
