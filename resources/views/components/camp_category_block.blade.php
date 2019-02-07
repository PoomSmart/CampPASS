<div class="card-light mb-3">
    <a href="{{ route('camp_browser.by_category', $object) }}">
        <img class="card-img-top" src={{ asset("/images/{$folder}/{$object}.png") }} alt="Card image of {{ $object }}">
    </a>
</div>