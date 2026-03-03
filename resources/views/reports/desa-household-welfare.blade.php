<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Desa Household Welfare</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px;">
    @include('reports.partials.header', ['meta' => data_get($reportData, 'meta', [])])

    <h3 style="margin:0 0 12px 0;">Distribusi Kesejahteraan Rumah Tangga Desa</h3>

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Status Kesejahteraan</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Jumlah Rumah Tangga</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Total Anggota</th>
            </tr>
        </thead>
        <tbody>
            @forelse ((array) data_get($reportData, 'rows', []) as $row)
                <tr>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'welfare_status', '-') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'household_count', 0) }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'total_members', 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="border:1px solid #444; padding:6px;">Data tidak tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
