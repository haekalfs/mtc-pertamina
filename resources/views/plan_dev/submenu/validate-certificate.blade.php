<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f9f9f9;
        }

        .document {
            width: 700px;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 300px;
        }

        .header h2 {
            margin: 10px 0 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .content {
            margin-top: 20px;
            margin-left: 25px;
            margin-bottom: 70px;
        }

        .content table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; /* Adds 10px vertical gap between rows */
        }

        .content td {
            vertical-align: top;
            padding: 5px 0;
        }

        .content td.value {
            width: 300px;
        }

        .content td.separator {
            width: 10px;
            text-align: center;
        }

        .content td.label {
            padding-left: 10px; /* Controls gap between separator and value */
        }

        .content td.value .english {
            display: block;
            font-style: italic;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <img src="{{ asset('img/logo-certificate.png') }}" alt="Dummy Logo">
            <p>Jl. Pemuda No.44, RT.2/RW.4, Jati, Kec. Pulo Gadung,<br>
            Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13220</p>
        </div>
        <div class="content">
            <table>
                <tr>
                    <td class="value">
                        Nama
                        <span class="english">Name</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">{{ $data->peserta->nama_peserta }}</td>
                </tr>
                <tr>
                    <td class="value">
                        Tempat, Tgl. Lahir
                        <span class="english">Place and date of birth</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">{{ $data->peserta->birth_place }}, {{ \Carbon\Carbon::parse($data->peserta->birth_date)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td class="value">
                        Sertifikat Keterampilan
                        <span class="english">Certificate of Proficiency</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">{{ $data->penlatCertificate->batch->penlat->description }}</td>
                </tr>
                <tr>
                    <td class="value">
                        Nomor Sertifikat Keterampilan
                        <span class="english">No Certificate of Proficiency</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">
                        @if (empty($data->certificate_number))
                            <span style="font-style: italic; color: red;">Missing Number</span> / {{ explode('/', $data->penlatCertificate->batch->batch)[0] }} / PMTC / {{ explode('/', $data->penlatCertificate->batch->batch)[2] }} / {{ explode('/', $data->penlatCertificate->batch->batch)[3] }}
                        @else
                            {{ $data->certificate_number }} / {{ explode('/', $data->penlatCertificate->batch->batch)[0] }} / PMTC / {{ explode('/', $data->penlatCertificate->batch->batch)[2] }} / {{ explode('/', $data->penlatCertificate->batch->batch)[3] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="value">
                        Terdaftar di
                        <span class="english">Register at</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">Pertamina Maritime Training Center</td>
                </tr>
                <tr>
                    <td class="value">
                        Tanggal Terbit
                        <span class="english">Issued Date</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">{{ $data->issued_date ? \Carbon\Carbon::parse($data->issued_date)->format('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="value">
                        Penanda Tangan
                        <span class="english">Approved By</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">Capt. BRAHMA ADEYANTO, M.Tr.M</td>
                </tr>
                <tr>
                    <td class="value">
                        Status Sertifikat
                        <span class="english">Certificate Status</span>
                    </td>
                    <td class="separator">:</td>
                    <td class="label">
                        {{ is_null($data->expire_date) || \Carbon\Carbon::parse($data->expire_date)->format('Y-m-d') >= now()->format('Y-m-d') ? 'Valid' : 'Expire' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
