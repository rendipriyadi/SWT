<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Safety Walk and Talk | Siemens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .status-badge {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
        }
        .foto-masalah {
            max-width: 70px; /* Foto lebih kecil untuk mengakomodasi lebih banyak foto */
            height: auto;
            margin: 2px; /* Margin lebih kecil */
        }
        .no-image {
            font-style: italic;
            color: #666;
        }
        .foto-container {
            display: flex;
            flex-wrap: wrap;
            border-top: 1px dashed #ccc;
            margin-top: 8px;
            padding-top: 5px;
        }
        /* Memperbaiki tampilan untuk deskripsi panjang */
        .deskripsi-cell {
            font-size: 11px;
            line-height: 1.4;
        }
        .deskripsi-text {
            display: block;
            margin-bottom: 8px; /* Jarak antara teks dan foto */
            word-wrap: break-word;
        }
        /* Grid layout untuk banyak foto */
        .foto-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, 70px);
            gap: 2px;
            margin-top: 5px;
        }
    </style>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo">
        <div class="title">LAPORAN SAFETY WALK AND TALK</div>
        <div class="subtitle">Periode: {{ $periode }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Tanggal Laporan</th>
                <th width="7%">Tanggal Selesai</th>
                <th width="15%">Area/Station</th>
                <th width="13%">Kategori</th>
                <th width="27%">Masalah</th>
                <th width="27%">Penyelesaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
                <td>
                    @if($item->penyelesaian && $item->penyelesaian->Tanggal)
                        {{ \Carbon\Carbon::parse($item->penyelesaian->Tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($item->area)
                        @if($item->penanggungJawab && strtolower($item->area->name) !== strtolower($item->penanggungJawab->station))
                            {{ $item->area->name }} ({{ $item->penanggungJawab->station }})
                        @else
                            {{ $item->area->name }}
                        @endif
                        @if($item->penanggungJawab)
                        <br><small>{{ $item->penanggungJawab->name }}</small>
                        @endif
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->kategori_masalah }}</td>
                <td class="deskripsi-cell">
                    <span class="deskripsi-text">{{ $item->deskripsi_masalah }}</span>
                    @if(!empty($item->validPhotos))
                        <div class="foto-grid">
                            @foreach($item->validPhotos as $foto)
                                <img src="{{ public_path('images/' . $foto) }}" class="foto-masalah">
                            @endforeach
                        </div>
                    @else
                        <div class="no-image">Tidak ada foto</div>
                    @endif
                </td>
                <td class="deskripsi-cell">
                    @if($item->penyelesaian)
                        <span class="deskripsi-text">{{ $item->penyelesaian->deskripsi_penyelesaian }}</span>
                        @if($item->penyelesaian && !empty($item->penyelesaian->validPhotos))
                            <div class="foto-grid">
                                @foreach($item->penyelesaian->validPhotos as $foto)
                                    <img src="{{ public_path('images/' . $foto) }}" class="foto-masalah">
                                @endforeach
                            </div>
                        @else
                            <div class="no-image">Tidak ada foto</div>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>