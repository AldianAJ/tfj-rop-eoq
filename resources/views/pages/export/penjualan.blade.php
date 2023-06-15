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
            {{-- <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr> --}}
            {{-- <table width="500">
                <tr>
                    <td class="text2">Jember, 16 mei 2019</td>
                </tr>
            </table> --}}
        </table>
        <hr>

        <table style="margin-top: 30px;">
            <tr class="text2">
                <td>Tanggal Cetak</td>
                <td>: {{ $tanggal }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Laporan Penjualan</td>
            </tr>
        </table>
        <br>
        <h3>{{ $title }}</h3>
    </center>
    <table width="100%" style="border: 1px solid; border-collapse: collapse; margin-right: 10px;">
        <thead>
            <th style="border: 1px solid;">Tanggal Penjualan</th>
            <th style="border: 1px solid;">Nama Barang</th>
            <th style="border: 1px solid;">Total Terjual</th>
        </thead>
        <tbody>
            @if (!empty($penjualans))
                @php
                    $no = 1;
                @endphp
                @foreach ($penjualans as $penjualan)
                    <tr>
                        {{-- <td style="border: 1px solid; text-align: center;">{{ $no++ }}</td> --}}
                        <td style="border: 1px solid; text-align: center;">
                            {{ $month[substr($penjualan->tanggal_penjualan, 5)] . ' ' . substr($penjualan->tanggal_penjualan, 0, 4) }}
                        </td>
                        <td style="border: 1px solid; text-align: center;">{{ $penjualan->nama_barang }}</td>
                        <td style="border: 1px solid; text-align: center;">{{ $penjualan->total_penjualan }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{-- <table width="500">
            <tr>
                <td>
                    <font size="2">Kpd yth.<br>Siswa Smk Baitul Hikmah kelas x<br>Di tempat</font>
                </td>
            </tr>
        </table>
        <br> --}}
    {{-- <table width="520">
            <tr>
                <td>
                    <font size="2">Assalamu'alaikum wr.wb<br>Dalam rangka praktikum simulasi digital yg jatuh pada
                        tanggal 16 mei 2019
                        Siswa smk baitul hikmah <b>kelas X</b> akan mengadakan peraktikum, jadi di harapkan siswa di
                        minta hadir
                        pada tempat yang sudah di siapkan.</font>
                </td>
            </tr>
        </table>
        <br>
        </table>
        <table>
            <tr class="text2">
                <td>Hari Tanggal</td>
                <td width="520">: <b>Selasa/16 mei 2019</b></td>
            </tr>
            <tr>
                <td>Jam</td>
                <td width="520">: 08:30</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td width="520">: Ruang lap komputer</td>
            </tr>
        </table>
        <br>
        <table width="520">
            <tr>
                <td>
                    <font size="2">Diharapkan atas kehadiranya, Demikian surat ini di sampaikan, atas perhatian
                        dan kerjasamanya kami
                        ucapkan terima kasih.<br><br>Wassalamu'alaikum wr.wb.
                    </font>
                </td>
            </tr>
        </table>
        <br> --}}
    {{-- <table width="520">
            <tr>
                <td width="430"><br><br><br><br></td>
                <td class="text" align="center">Wali kelas<br><br><br><br>Bpk Fauzy.s.kom</td>
            </tr>
        </table> --}}

</body>

</html>
