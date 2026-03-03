<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Kecamatan Village Recaps</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px;">
    @include('reports.partials.header', ['meta' => data_get($reportData, 'meta', [])])

    <h3 style="margin:0 0 12px 0;">Rekap Desa per Kecamatan</h3>

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #444; text-align:left; padding:6px;">ID Desa</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Nama Desa</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Jumlah RT</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Jumlah Warga</th>
            </tr>
        </thead>
        <tbody>
            @forelse ((array) data_get($reportData, 'rows', []) as $row)
                <tr>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'desa_id', 0) }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'desa_name', '-') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'household_count', 0) }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'resident_count', 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="border:1px solid #444; padding:6px;">Data tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
