<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ data_get($reportData, 'meta.report_code', 'report') }}</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px;">
    @include('reports.partials.header', ['meta' => data_get($reportData, 'meta', [])])

    <h3 style="margin: 0 0 12px 0;">Sample Village Profile</h3>

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Title</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Area ID</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Level</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Generated At</th>
                <th style="border:1px solid #444; text-align:left; padding:6px;">Requested By</th>
            </tr>
        </thead>
        <tbody>
            @foreach ((array) data_get($reportData, 'rows', []) as $row)
                <tr>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'title') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'area_id') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'level') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'generated_at') }}</td>
                    <td style="border:1px solid #444; padding:6px;">{{ data_get($row, 'requested_by') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
