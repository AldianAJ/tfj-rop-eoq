<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style type="text/css">
        table {
            border-style: double;
            border-width: 3px;
            border-color: white;
        }

        table tr .text2 {
            text-align: right;
            font-size: 13px;
        }

        table tr .text {
            text-align: center;
            font-size: 13px;
        }

        table tr td {
            font-size: 13px;
        }
    </style>
</head>

<body>
    <center>
        <table width="100%">
            <tr>
                <td><img src="{{ public_path('assets/images/logo-tfj.png') }}" width="100%" height="130"></td>
                <td style="padding-right: 4rem;">
                    <center>
                        <font size="6"><b>Toko Fadhil Jaya</b></font><br>
                        <font size="3">Jl. Bulak Rukem Timur I No.47, Bulak, Kec. Bulak, Surabaya, Jawa Timur</font>
                    </center>
                </td>
            </tr>
        </table>
        <hr>

        <table style="margin-top: 30px;">
            <tr class="text2">
                <td>Tanggal Cetak</td>
                <td>: {{ $tanggal }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Laporan Pengiriman</td>
            </tr>
        </table>
        <br>
        <h3>{{ $title }}</h3>
    </center>
    <table width="100%" style="border: 1px solid; border-collapse: collapse; margin-right: 10px;">
        <thead>
            <th style="border: 1px solid;">Nama Barang</th>
            <th style="border: 1px solid;">Jumlah Pengiriman</th>
            <th style="border: 1px solid;">Persetujuan</th>
            <th style="border: 1px solid;">Dikirim dari</th>
            <th style="border: 1px solid;">Catatan</th>
        </thead>
        <tbody>
            @if (!empty($details))
                @php
                    $no = 1;
                @endphp
                @foreach ($details as $detail)
                    <tr>
                        <td style="border: 1px solid; text-align: justify;">{{ $detail->nama_barang }}</td>
                        <td style="border: 1px solid; text-align: center;">
                            {{ !empty($detail->jumlah_pengiriman) ? $detail->jumlah_pengiriman : '-' }}
                        </td>
                        <td style="border: 1px solid; text-align: justify;">{{ $detail->persetujuan }}</td>
                        <td style="border: 1px solid; text-align: justify;">
                            {{ !empty($detail->name) ? $detail->name : '-' }}
                        </td>
                        <td style="border: 1px solid; text-align: justify;">
                            {{ $detail->catatan }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</body>

</html>
