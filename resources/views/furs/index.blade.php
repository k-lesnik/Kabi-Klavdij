<!DOCTYPE html>
<html>
<head>
    <title>Furs Data</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Furs Data</h1>

@if (session('error'))
    <p style="color: red;">{{ session('error') }}</p>
@endif

@if (count($fursData) > 0)
    <table>
        <thead>
        <tr>
            <th>Omejen obseg identifikacije</th>
            <th>Zavezanost za DDV</th>
            <th>Davčna številka</th>
            <th>Matična številka</th>
            <th>Datum registracije za DDV</th>
            <th>Šifra dejavnosti</th>
            <th>Ime zavezanca</th>
            <th>Naslov zavezanca</th>
            <th>Finančni urad</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($fursData as $row)
            <tr>
                @foreach ($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p>No furs data available.</p>
@endif
</body>
</html>
