<div class="card-light mb-3" id="{{ isset($border) ? 'card-border' : null }}">
    <a href="{{ route('camp_browser.by_category', $object) }}">
        <?php $image = isset($getter) ? $object->{$getter} : $object ?>
        <img class="card-img-top" src={{ asset("/images/{$folder}/{$image}.png") }}>
    </a>
</div>