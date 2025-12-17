<!DOCTYPE html>
<html>
<head>
    <title>Export PDF</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
    <tr>
        <th>Nama Pegawai</th>
        <th>Jenis</th>
        <th>Keterangan</th>
        <th>Waktu Akses</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
    <tr>
        <td>{{ $row->nampeg }}</td>
        <td>{{ $row->jenis }}</td>
        <td>{{ $row->keterangan }}</td>
        <td>{{ $row->created_at }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
