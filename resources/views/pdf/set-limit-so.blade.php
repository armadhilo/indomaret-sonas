<table style="font-family: Arial; font-size: 10px;">
    <tr>
        <th>PLU</th>
        <th>Deskripsi</th>
        <th>Lokasi</th>
        <th>Divisi</th>
        <th>Departemen</th>
        <th>Kategori</th>
        <th>Toko</th>
        <th>Gudang</th>
        <th>Total Plano</th>
    </tr>
    @foreach ($data as $item)
    <tr>
        <td>{{ $item->plu }}</td>
        <td>{{ $item->deskripsi }}</td>
        <td>{{ $item->lso_lokasi }}</td>
        <td>{{ $item->divisi }}</td>
        <td>{{ $item->departement }}</td>
        <td>{{ $item->kategori }}</td>
        <td>{{ $item->areatoko }}</td>
        <td>{{ $item->areagudang }}</td>
        <td>{{ $item->total }}</td>
    </tr>
    @endforeach
</table>
