{{-- resources/views/report-cards/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->full_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3c72;
        }
        .report-title {
            font-size: 18px;
            margin: 5px 0;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .grade-excellent {
            background-color: #28a745;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .grade-good {
            background-color: #5cb85c;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .grade-average {
            background-color: #f0ad4e;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .grade-pass {
            background-color: #17a2b8;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .grade-fail {
            background-color: #dc3545;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #000;
            margin-top: 30px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school_name ?? config('app.name', 'School Name') }}</div>
        <div class="report-title">STUDENT PROGRESS REPORT</div>
        <div>Term {{ $term }}, {{ $year }}</div>
        @if(isset($grading_scale))
            <div><small>Grading Scale: {{ $grading_scale->name->value ?? $grading_scale->name }}</small></div>
        @endif
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td width="50%"><strong>Student Name:</strong> {{ $student->full_name }}</td>
                <td width="50%"><strong>Admission No:</strong> {{ $student->admission_number }}</td>
            </tr>
            <tr>
                <td><strong>Class:</strong> {{ $class->name ?? 'N/A' }}</td>
                <td><strong>Gender:</strong> {{ ucfirst($student->gender ?? 'N/A') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Exam (80%)</th>
                <th>CA (20%)</th>
                <th>Final Mark</th>
                <th>Grade</th>
                <th>Descriptor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $index => $subject)
                @php
                    $gradeClass = match(true) {
                        in_array($subject['grade'], ['A', 'D1', 'D2']) => 'grade-excellent',
                        in_array($subject['grade'], ['B', 'C3', 'C4']) => 'grade-good',
                        in_array($subject['grade'], ['C', 'C5', 'C6']) => 'grade-average',
                        in_array($subject['grade'], ['D', 'P7', 'P8']) => 'grade-pass',
                        default => 'grade-fail'
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $subject['subject']->name }}</strong><br><small>{{ $subject['subject']->code }}</small></td>
                    <td class="text-center">{{ number_format($subject['exam_mark'], 2) }}</td>
                    <td class="text-center">{{ number_format($subject['ca_mark'], 2) }}</td>
                    <td class="text-center"><strong>{{ number_format($subject['final_mark'], 2) }}</strong></td>
                    <td class="text-center">
                        <span class="badge {{ $gradeClass }}">{{ $subject['grade'] }}</span>
                    </td>
                    <td>{{ $subject['descriptor'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right"><strong>Average:</strong></td>
                <td class="text-center"><strong>{{ number_format(collect($subjects)->avg('final_mark'), 2) }}</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div>
            <div class="signature-line"></div>
            <small>Class Teacher</small>
        </div>
        <div>
            <div class="signature-line"></div>
            <small>Headteacher</small>
        </div>
        <div>
            <div class="signature-line"></div>
            <small>Date: {{ now()->format('d/m/Y') }}</small>
        </div>
    </div>

    <div class="footer">
        Generated on: {{ $generated_at }}
    </div>
</body>
</html>
