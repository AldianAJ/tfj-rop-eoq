<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style type="text/css">
        body {
            font-family: "source_sans_proregular", Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
        }

        table tr td {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <center>
        <h3>{{ $title }}</h3>
    </center>
    <table width="100%">
        <tr>
            <td>
                <table>
                    <tr>
                        <td rowspan="3" style="width: 10%"><img src="{{ public_path('assets/images/logo-tfj.png') }}"
                                width="70" height="70"></td>
                        <td style="width: 40%"></td>
                    </tr>
                    <tr>
                        <td style="width: 40%"><b>Toko Fadhil Jaya <span
                                    style="display: inline-block; margin-right: 43%"></span> Jl. Bulak Rukem Timur I
                                No.47, Bulak, Surabaya, Jawa Timur</b></td>
                    </tr>
                    <tr>
                        <td style="width= 40%"></td>
                    </tr>
                </table>
            </td>
            <td style="width:30%"></td>
            <td style="width:30%">
                <table>
                    <tr>
                        <td>Tanggal Cetak</td>
                        <td>: {{ $tanggal }}</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>: Laporan Permintaan</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%" style="border: 1px solid; border-collapse: collapse; margin-right: 10px;">
        <thead>
            <th style="border: 1px solid;">Nama Barang</th>
            <th style="border: 1px solid;">EOQ</th>
            <th style="border: 1px solid;">ROP</th>
            <th style="border: 1px solid;">Jumlah Pemesanan</th>
        </thead>
        <tbody>
            @if (!empty($datas))
                @php
                    $no = 1;
                @endphp
                @foreach ($datas as $data)
                    <tr>
                        <td style="border: 1px solid; text-align: left;">{{ $data->nama_barang }}</td>
                        <td style="border: 1px solid; text-align: center;">{{ $data->eoq }}</td>
                        <td style="border: 1px solid; text-align: center;">{{ $data->rop }}</td>
                        <td style="border: 1px solid; text-align: center;">{{ $data->jumlah_pemesanan }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>

</html>
