<!DOCTYPE html>
<html>
<head>
    <title>Contact Us Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            color: #2c3e50;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <h2>Contact Us Message</h2>

    <ul>
        <li><strong>Name:</strong> {{ $data['first_name'] }} {{ $data['last_name'] }}</li>
        <li><strong>Email:</strong> {{ $data['email'] }}</li>
        <li><strong>Phone:</strong> {{ $data['phone'] }}</li>
    </ul>

    <h2>Message Details:</h2>
    <table>
        <tbody>
            <tr>
                <td>{{ $data['message'] }}</td>
            </tr>
        </tbody>
    </table>

    <p>
        Best regards,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>