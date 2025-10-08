<div class="w-full border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 py-3">
        <ol class="grid grid-cols-5 gap-2 text-center text-xs md:text-sm">
            @php($steps = ['schedule','passenger','review','payment','confirmation'])
            @foreach($steps as $step)
                @php($isActive = ($current ?? '') === $step)
                <li class="px-2 py-2 rounded border flex items-center justify-center gap-2
                    {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
                    <span class="uppercase tracking-wide">{{ $step }}</span>
                </li>
            @endforeach
        </ol>
    </div>
</div>