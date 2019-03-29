<table class="w-100">
    <thead><th width="400px"></th><th></th></thead>
    @foreach ($entries as $key => $value)
        <tr>
            <th style="float:left;">{{ $key }}:</th>
            <td>{{ $value }}</td>
        </tr>
    @endforeach
</table>