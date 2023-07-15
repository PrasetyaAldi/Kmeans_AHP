@props([
    'data' => [],
    'active' => null, // integer atau id dari criteria
    'column_key' => 'id',
    'column_val' => 'name',
])
<ul class="navbar-nav text-center">
    @foreach ($data as $item)
        <li class="nav-item">
            <span style="cursor: pointer"
                class="nav-link rounded bg-light fw-semibold text-dark mb-2 nav-criteria {{ $item->{$column_key} == $active ? 'active-criteria' : '' }}"
                onclick="selectCriteria('{{ $item->{$column_key} }}')">
                {{ ucwords(str_replace('_', ' ', $item->{$column_val})) }}</span>
        </li>
    @endforeach
</ul>
