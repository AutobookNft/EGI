{{-- Fallback Image semplice --}}
<img @foreach($getImgAttributes() as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach>
