<table style="width:100%; border-bottom:1px solid #222; margin-bottom:16px; padding-bottom:8px;">
    <tr>
        <td style="font-weight:700; font-size:16px;">{{ strtoupper((string) data_get($meta, 'report_code', 'unknown.report')) }}</td>
        <td style="text-align:right; font-size:12px;">{{ strtoupper((string) data_get($meta, 'orientation', 'landscape')) }}</td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            Wilayah: {{ data_get($meta, 'area_name', '-') }} ({{ data_get($meta, 'area_level', '-') }})
        </td>
        <td style="text-align:right; font-size:12px;">Area ID: {{ data_get($meta, 'area_id', '-') }}</td>
    </tr>
    <tr>
        <td style="font-size:12px;">Dicetak oleh: {{ data_get($meta, 'printed_by', '-') }}</td>
        <td style="text-align:right; font-size:12px;">Tanggal cetak: {{ data_get($meta, 'printed_at', '-') }}</td>
    </tr>
</table>
