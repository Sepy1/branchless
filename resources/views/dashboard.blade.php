<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #999;
            text-align: center;
        }
        th {
            background-color: #2f2f2f;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Dashboard - Jumlah Perangkat per Kode Kantor</h2>

    <table>
        <thead>
            <tr>
                <th>Kode Kantor</th>
                <th>Jumlah Perangkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->kode_kantor }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
