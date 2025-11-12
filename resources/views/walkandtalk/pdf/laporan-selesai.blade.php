<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Safety Walk and Talk | Siemens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
            text-align: right;
        }
        .logo {
            max-width: 200px;
            height: auto;
        }
        .title {
            font-family: Arial, sans-serif;
            font-size: 18px;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 5px;
            color: #000;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Fixed layout untuk kontrol lebar kolom */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        th {
            background-color: #f4f4f4;
            vertical-align: middle;
            text-align: center;
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
            max-width: 100%;
            max-height: 200px;
            width: auto;
            height: auto;
            display: block;
            object-fit: contain;
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
            font-size: 14px;
            line-height: 1.3;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        .deskripsi-text {
            display: block;
            margin-bottom: 10px; /* Jarak antara teks dan foto */
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }
        /* Grid layout untuk foto */
        .foto-grid {
            margin-top: 5px;
        }
        .foto-grid table {
            width: 100%;
            border-collapse: collapse;
        }
        .foto-grid td {
            width: 50%;
            padding: 2px;
            border: none;
            vertical-align: top;
        }
        /* Jika hanya 1 foto */
        .foto-grid.single td {
            width: 100%;
        }
        /* Badge Before/After */
        .badge-container {
            text-align: right;
            margin-top: 8px;
        }
        .before-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
        }
        .after-badge {
            display: inline-block;
            background-color: #009999;
            color: white;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/static/siemens-logo.png') }}" alt="Siemens Logo" class="logo">
        </div>
        <div class="header-right">
            <div class="title">SAFETY WALK AND TALK</div>
            <div class="subtitle">Period: {{ $periode }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="7%">No</th>
                <th width="12%">Report Date</th>
                <th width="12%">Completion Date</th>
                <th width="15%">Area/Station</th>
                <th width="12%">Category</th>
                <th width="22%">Observation</th>
                <th width="22%">Resolution</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('l, j-n-Y') }}</td>
                <td>
                    @if($item->penyelesaian && $item->penyelesaian->Tanggal)
                        {{ \Carbon\Carbon::parse($item->penyelesaian->Tanggal)->format('l, j-n-Y') }}
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
                <td>{{ $item->problemCategory->name ?? '-' }}</td>
                <td class="deskripsi-cell">
                    <span class="deskripsi-text">{{ $item->deskripsi_masalah }}</span>
                    @if(!empty($item->Foto) && is_array($item->Foto))
                        @php
                            $fotoCount = count($item->Foto);
                            $fotoClass = $fotoCount == 1 ? 'single' : '';
                        @endphp
                        <div class="foto-grid {{ $fotoClass }}">
                            <table>
                                @foreach($item->Foto as $index => $foto)
                                    @php
                                        // Try storage path first, fallback to public path for old images
                                        $imagePath = storage_path('app/public/images/reports/' . $foto);
                                        if (!file_exists($imagePath)) {
                                            $imagePath = public_path('images/reports/' . $foto);
                                        }
                                        if (file_exists($imagePath)) {
                                            $imageData = base64_encode(file_get_contents($imagePath));
                                            $imageSrc = 'data:image/' . pathinfo($foto, PATHINFO_EXTENSION) . ';base64,' . $imageData;
                                        } else {
                                            $imageSrc = '';
                                        }
                                    @endphp
                                    @if($index % 2 == 0)
                                        <tr>
                                    @endif
                                    @if($imageSrc)
                                        <td><img src="{{ $imageSrc }}" class="foto-masalah"></td>
                                    @endif
                                    @if($index % 2 == 1 || $index == $fotoCount - 1)
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </div>
                    @else
                        <div class="no-image">Tidak ada foto</div>
                    @endif
                    <div class="badge-container">
                        <span class="before-badge">BEFORE</span>
                    </div>
                </td>
                <td class="deskripsi-cell">
                    @if($item->penyelesaian)
                        <span class="deskripsi-text">{{ $item->penyelesaian->deskripsi_penyelesaian }}</span>
                        @if($item->penyelesaian && !empty($item->penyelesaian->Foto) && is_array($item->penyelesaian->Foto))
                            @php
                                $fotoCount = count($item->penyelesaian->Foto);
                                $fotoClass = $fotoCount == 1 ? 'single' : '';
                            @endphp
                            <div class="foto-grid {{ $fotoClass }}">
                                <table>
                                    @foreach($item->penyelesaian->Foto as $index => $foto)
                                        @php
                                            // Try storage path first, fallback to public path for old images
                                            $imagePath = storage_path('app/public/images/completions/' . $foto);
                                            if (!file_exists($imagePath)) {
                                                $imagePath = public_path('images/completions/' . $foto);
                                            }
                                            if (file_exists($imagePath)) {
                                                $imageData = base64_encode(file_get_contents($imagePath));
                                                $imageSrc = 'data:image/' . pathinfo($foto, PATHINFO_EXTENSION) . ';base64,' . $imageData;
                                            } else {
                                                $imageSrc = '';
                                            }
                                        @endphp
                                        @if($index % 2 == 0)
                                            <tr>
                                        @endif
                                        @if($imageSrc)
                                            <td><img src="{{ $imageSrc }}" class="foto-masalah"></td>
                                        @endif
                                        @if($index % 2 == 1 || $index == $fotoCount - 1)
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </div>
                        @else
                            <div class="no-image">Tidak ada foto</div>
                        @endif
                        <div class="badge-container">
                            <span class="after-badge">AFTER</span>
                        </div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>