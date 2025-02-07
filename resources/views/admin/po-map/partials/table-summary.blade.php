<table id="table-summary" class="table table-bordered table-striped align-middle" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Bulan</th>
            @foreach ($cabangList as $cabang)
                <th>{{ $cabang }}</th>
            @endforeach
            <th>Periode</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach ($summary->groupBy('periode') as $periode => $dataBulan)
            @php
                // Ambil nilai filter dari request (jika tersedia)
                $filterStartDate = request()->filled('date') ? explode(' to ', request('date'))[0] : null;
                $filterEndDate = request()->filled('date')
                    ? explode(' to ', request('date'))[1] ?? $filterStartDate
                    : null;

                // Gunakan nilai filter jika tersedia, jika tidak gunakan nilai dari database
                $startDate = $filterStartDate
                    ? date('d M, Y', strtotime($filterStartDate))
                    : date('d M, Y', strtotime($dataBulan->min('start_date')));
                $endDate = $filterEndDate
                    ? date('d M, Y', strtotime($filterEndDate))
                    : date('d M, Y', strtotime($dataBulan->max('end_date')));

                $periodeFormatted = "{$startDate} s/d {$endDate}";
            @endphp

            @foreach ($dataBulan->groupBy('bulan') as $bulan => $dataCabang)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $bulan }}</td>
                    @foreach ($cabangList as $cabang)
                        @php
                            $cabangData = $dataCabang->where('cabang_name', $cabang)->first();
                        @endphp
                        <td>Rp. {{ $cabangData ? number_format($cabangData->total_amount, 0, ',', '.') : '-' }},-</td>
                    @endforeach
                    <td>{{ $periodeFormatted }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

<script>
    $('#table-summary').DataTable({
        scrollX: true,
        responsive: false,
        lengthChange: false,
        paging: true,
        searching: true,
        info: true
    });
</script>
