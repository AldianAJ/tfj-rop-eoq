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
                <td><img src="{{ public_path('assets/images/logo-yms.png') }}" width="100%" height="130"></td>
                <td style="padding-right: 4rem;">
                    <center>
                        <font size="6"><b>PT Young Multi Sarana</b></font><br>
                        <font size="3">Jl. Kyai Tambak Deres No.229, Bulak, Kec. Bulak, Surabaya, Jawa Timur 60124
                        </font>
                    </center>
                </td>
                {{-- <td></td> --}}
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
                <td>: Laporan Pemesanan</td>
            </tr>
        </table>
        <br>
        <h3>{{ $title }}</h3>
    </center>
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
                        <td style="border: 1px solid; text-align: justify;">{{ $data->nama_barang }}</td>
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
