<!DOCTYPE html>
<html>
<head>
    <title>Student Absence Notification</title>
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
    <p>Dear Parent,</p>

    <p>This is to inform you about your child's absences for today</p>

    <h5>Student Information:</h5>
    <ul>
        <li>Student Name: {{ $student->name }}</li>
        <li>Class: {{ $student->class->name }}</li>
        <li>Date: {{ now()->format('l, F j, Y') }}</li>
    </ul>

    <p>Below are the details of today's absences:</p>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Session Time</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentTodayAbsences as $absence)
                <tr>
                    <td>{{ $absence->session->subject->name }}</td>
                    <td>{{ $absence->session->session_number }}th Period</td>
                    <td>{{ $absence->session->teacher->teacher->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Please note that regular attendance is crucial for academic success. If you have any questions or concerns, please don't hesitate to contact the school administration.</p>

    <p>
        Best regards,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>
