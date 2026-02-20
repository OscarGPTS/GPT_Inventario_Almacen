@if($items->hasPages())
<div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
    <p class="text-xs text-gray-500">
        Mostrando {{ $items->firstItem() }}–{{ $items->lastItem() }} de {{ number_format($items->total()) }} registros
    </p>
    <div class="flex gap-1">
        @if($items->onFirstPage())
            <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">‹ Ant.</span>
        @else
            <a href="{{ $items->previousPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">‹ Ant.</a>
        @endif

        @foreach($items->getUrlRange(max(1, $items->currentPage() - 2), min($items->lastPage(), $items->currentPage() + 2)) as $page => $url)
            @if($page == $items->currentPage())
                <span class="px-3 py-1 text-xs border rounded text-white font-semibold" style="background-color:{{ $acento }};border-color:{{ $acento }};">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">{{ $page }}</a>
            @endif
        @endforeach

        @if($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" class="px-3 py-1 text-xs border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">Sig. ›</a>
        @else
            <span class="px-3 py-1 text-xs border border-gray-200 rounded text-gray-300 cursor-not-allowed">Sig. ›</span>
        @endif
    </div>
</div>
@endif
