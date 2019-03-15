<div class="card card-with-shadow mb-3" id="{{ isset($border) ? 'card-border' : null }}">
    <a href="{{ route(isset($route) ? $route : 'camps.by_category', $object->id) }}">
        @php $image = isset($getter) ? $object->{$getter} : $object @endphp
        <img class="card-img-top" src={{ asset("/images/{$folder}/{$image}.png") }}>
    </a>
</div>