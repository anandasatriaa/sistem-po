<table id="table-detail" class="table table-bordered table-striped align-middle" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>No PO</th>
            <th>Cabang</th>
            <th>Category</th>
            <th>Barang</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data akan diisi secara dinamis melalui AJAX -->
        @foreach ($detail as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                <td>{{ $item->no_po }}</td>
                <td>{{ $item->cabang_name }}</td>
                <td>{{ $item->category_name }}</td>
                <td>{{ $item->barang }}</td>
                <td>{{ $item->qty }} {{ $item->unit }}</td>
                <td>Rp. {{ number_format($item->unit_price, 0, ',', '.') }},-</td>
                <td>Rp. {{ number_format($item->amount_price, 0, ',', '.') }},-</td>
            </tr>
        @endforeach
    </tbody>
</table>
