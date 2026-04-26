<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $employee->first_name }} {{ $employee->last_name }} - Employee Details</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .employee-name h1 {
            font-size: 24pt;
            margin-bottom: 5px;
        }
        .employee-name p {
            color: #555;
        }
        .employee-id {
            text-align: right;
        }
        /* Two-column layout */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        .col {
            flex: 1;
            padding: 0 10px;
        }
        /* Cards (sections) */
        .card {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .card-title {
            font-size: 16pt;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .info-label {
            width: 35%;
            font-weight: bold;
            color: #555;
        }
        .info-value {
            width: 65%;
        }
        /* Table for work patterns, etc. */
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
            background-color: #f2f2f2;
        }
        /* Print-specific */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="employee-name">
                <h1>{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <p>{{ $employee->employeePosition?->jobTitle?->title ?? '—' }} ·
                   {{ $employee->employeePosition?->department?->name ?? $employee->department?->name ?? '—' }}</p>
            </div>
            <div class="employee-id">
                <p><strong>Employee #:</strong> {{ $employee->employee_number }}</p>
                <p><strong>Status:</strong> {{ $employee->status ?? 'Active' }}</p>
            </div>
        </div>

        <div class="row">
            {{-- Left column: Personal, Contact, Emergency --}}
            <div class="col">
                {{-- Personal Information --}}
                <div class="card">
                    <div class="card-title">Personal Information</div>
                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $employee->gender ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Marital Status</div>
                        <div class="info-value">{{ $employee->marital_status ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nationality</div>
                        <div class="info-value">{{ $employee->nationality ?? '—' }}</div>
                    </div>
                </div>

                {{-- Contact Information (from EmployeeProfile) --}}
                @if($employee->employeeProfile)
                <div class="card">
                    <div class="card-title">Contact Information</div>
                    <div class="info-row">
                        <div class="info-label">Personal Email</div>
                        <div class="info-value">{{ $employee->employeeProfile->personal_email ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Work Email</div>
                        <div class="info-value">{{ $employee->email ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Personal Phone</div>
                        <div class="info-value">{{ $employee->employeeProfile->personal_phone ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Work Phone</div>
                        <div class="info-value">{{ $employee->employeeProfile->work_phone ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address</div>
                        <div class="info-value">
                            {{ $employee->employeeProfile->address_street ?? '' }}<br>
                            {{ $employee->employeeProfile->address_city ?? '' }} {{ $employee->employeeProfile->address_state ?? '' }} {{ $employee->employeeProfile->address_postal_code ?? '' }}<br>
                            {{ $employee->employeeProfile->address_country ?? '' }}
                        </div>
                    </div>
                </div>
                @endif

                {{-- Emergency Contact --}}
                @if($employee->employeeProfile && ($employee->employeeProfile->emergency_contact_name || $employee->employeeProfile->emergency_contact_phone))
                <div class="card">
                    <div class="card-title">Emergency Contact</div>
                    <div class="info-row">
                        <div class="info-label">Name</div>
                        <div class="info-value">{{ $employee->employeeProfile->emergency_contact_name ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value">{{ $employee->employeeProfile->emergency_contact_phone ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Relationship</div>
                        <div class="info-value">{{ $employee->employeeProfile->emergency_contact_relationship ?? '—' }}</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right column: Employment, Compensation, Work Patterns --}}
            <div class="col">
                {{-- Employment Details --}}
                <div class="card">
                    <div class="card-title">Employment Details</div>
                    <div class="info-row">
                        <div class="info-label">Hire Date</div>
                        <div class="info-value">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $employee->employeePosition?->department?->name ?? $employee->department?->name ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Job Title</div>
                        <div class="info-value">{{ $employee->employeePosition?->jobTitle?->title ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Manager</div>
                        <div class="info-value">{{ $employee->employeePosition?->manager?->first_name ?? '' }} {{ $employee->employeePosition?->manager?->last_name ?? '—' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Employment Status</div>
                        <div class="info-value">{{ $employee->employeePosition?->employment_status ?? $employee->status ?? 'Active' }}</div>
                    </div>
                </div>

                {{-- Compensation --}}
                @if($employee->employeePosition)
                <div class="card">
                    <div class="card-title">Compensation</div>
                    <div class="info-row">
                        <div class="info-label">Pay Type</div>
                        <div class="info-value">{{ ucfirst($employee->employeePosition->pay_type ?? '—') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Salary / Rate</div>
                        <div class="info-value">
                            @if($employee->employeePosition->pay_type === 'hourly')
                                ${{ number_format($employee->employeePosition->hourly_rate, 2) }}/hour
                            @else
                                ${{ number_format($employee->employeePosition->base_salary, 2) }}/year
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Pay Frequency</div>
                        <div class="info-value">{{ ucfirst($employee->employeePosition->pay_frequency ?? '—') }}</div>
                    </div>
                </div>
                @endif

                {{-- Work Patterns --}}
                @if($employee->employeeWorkPatterns && $employee->employeeWorkPatterns->count())
                <div class="card">
                    <div class="card-title">Work Patterns</div>
                    <table>
                        <thead>
                            <tr><th>Pattern</th><th>Start Date</th><th>End Date</th></tr>
                        </thead>
                        <tbody>
                            @foreach($employee->employeeWorkPatterns as $pattern)
                            <tr>
                                <td>{{ $pattern->workPattern?->name ?? '—' }}</td>
                                <td>{{ $pattern->start_date->format('M d, Y') }}</td>
                                <td>{{ $pattern->end_date ? $pattern->end_date->format('M d, Y') : 'Ongoing' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Print button (only visible on screen) --}}
<div class="no-print" style="text-align: center; margin-top: 30px;">
    <button onclick="window.print();" class="btn btn-primary">🖨️ Print this page</button>
    <button onclick="window.close();" class="btn btn-secondary">✖ Close</button>
</div>
    </div>
</body>
</html>
