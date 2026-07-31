# Payroll Calculation User Guide

## 1. Overview

This guide explains how the payroll system calculates employee payments based on **pay type**, **pay frequency**, **attendance**, **work patterns**, and **policies** (tax, pension, etc.). It provides clear examples to help you understand and verify payroll results.

All calculations are performed automatically by the system. Manual adjustments (bonuses, commissions, reimbursements, deductions) are added as separate line items and do not affect the base calculation unless policies are configured to use `gross_pay` as their base.

---

## 2. Key Concepts

### 2.1. Pay Type (`pay_type` in `employee_positions`)

| Pay Type | Description | Attendance Required? |
|----------|-------------|----------------------|
| **Salaried Full** | Fixed monthly salary, paid in full each period. | No |
| **Salaried Daily** | Salary prorated based on actual working days attended. | Yes |
| **Hourly** | Pay calculated on actual hours worked (regular, overtime, double‑time). | Yes |

### 2.2. Pay Frequency (`pay_frequency` in `employee_positions`)

This determines the number of pay periods per year used for **tax annualisation**:

| Frequency | Periods per Year | When Used |
|-----------|------------------|-----------|
| Monthly | 12 | Once per month |
| Semi‑monthly | 24 | Twice per month (e.g., 1st & 15th) |
| Bi‑weekly | 26 | Every two weeks |
| Weekly | 52 | Every week |
| Daily | 260 | Every working day (or 365 for calendar daily) |

### 2.3. Work Pattern (`work_patterns`)

Defines which days of the week are considered **working days** (e.g., Monday–Friday). Used by **Salaried Daily** employees to compute the daily rate.

If no work pattern is assigned to the employee, the system falls back to the **company’s default** or **weekdays (Mon–Fri)**.

### 2.4. Attendance Records (`attendances`)

For **Salaried Daily** and **Hourly** employees, the system uses attendance records to determine:

- **Worked Days** – days with `net_hours > 0` or `is_paid_absence = true`.
- **Regular Hours** – standard working hours.
- **Overtime Hours** – hours beyond the daily threshold (default 8 hours).
- **Double‑Time Hours** – hours beyond the double‑time threshold (default 12 hours).
- **Net Hours** – total payable hours (regular + overtime + double‑time minus unpaid breaks).

> **Important:** `net_hours` should always equal `regular_hours + overtime_hours + double_time_hours - unpaid_break_minutes/60`. To avoid manual entry errors, `net_hours` is auto‑calculated from the breakdown fields when saved manually.

### 2.5. Policies

- **Tax Policy** – applies progressive tax bands on the **annualised gross pay** (based on pay frequency).
- **Pension Policy** – applies a percentage of the chosen base (`base_salary` or `gross_pay`).
- **Other Policies** (insurance, benefit, bonus, deduction) – can be fixed or percentage‑based.

Each policy can be configured to apply to either:
- **`base_salary`** – the employee’s fixed salary or attendance‑based base pay.
- **`gross_pay`** – total earnings after adjustments (bonuses, commissions, etc.).

---

## 3. Calculation Formulas

### 3.1. Salaried Full (Monthly)

```
Gross Pay = base_salary + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### 3.2. Salaried Daily

```
Total Working Days = number of days in period that match the work pattern
Daily Rate = base_salary / Total Working Days
Base Pay = Daily Rate × Worked Days (from attendance)
Gross Pay = Base Pay + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### 3.3. Hourly

```
Regular Pay = regular_hours × hourly_rate
Overtime Pay = overtime_hours × hourly_rate × overtime_multiplier
DoubleTime Pay = double_time_hours × hourly_rate × double_time_multiplier
Base Pay = Regular Pay + Overtime Pay + DoubleTime Pay
Gross Pay = Base Pay + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### 3.4. Tax Calculation

```
Period Gross = base/gross amount (depending on policy base)
Periods Per Year = based on pay_frequency
Annualised Gross = Period Gross × Periods Per Year

Tax = sum over bands:
    if Annualised Gross > band_start:
        taxable = min(Annualised Gross, band_end) - band_start
        tax += taxable × band_rate

Annual Tax = sum of all band taxes
Period Tax = Annual Tax / Periods Per Year
```

### 3.5. Pension (Percentage‑Based)

```
If policy base = base_salary:
    Effective Base = base_salary (or daily/hourly base pay)
If policy base = gross_pay:
    Effective Base = Gross Pay (after adjustments)

Employee Contribution = Effective Base × (employee_percentage / 100)
Employer Contribution = Effective Base × (employer_percentage / 100)  (informational)
```

---

## 4. Detailed Examples

All examples assume the following employee data:

| Field | Value |
|-------|-------|
| Employee | Kaela Ferry (EMP0001) |
| Base Salary | ₦100,000 per month |
| Hourly Rate | ₦2,000 |
| Attendance | 1 day, 2 hours worked (for daily/hourly tests) |
| Period | 1–31 July 2026 (23 working days, Mon–Fri) |
| Tax Policy | Progressive bands: 0–10,000 @5%, 10,000–50,000 @10%, 50,000+ @15% |
| Pension Policy | 8% employee, 10% employer, based on gross pay |
| Adjustments | ₦5 each for Bonus, Commission, Correction, Reimbursement (total +₦20), and a ₦5 deduction |

---

### 4.1. Salaried Full

#### 4.1.1. Monthly (12 periods/year)

| Component | Amount |
|-----------|--------|
| Base Salary | ₦100,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦100,020.00** |
| Pension (8% of gross) | ₦8,001.60 |
| Tax (annualised gross: 100,020 × 12 = 1,200,240) | ₦14,753.00 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦22,759.60** |
| **Net Pay** | **₦77,260.40** |

#### 4.1.2. Bi‑Weekly (26 periods/year)

| Component | Amount |
|-----------|--------|
| Base Salary | ₦100,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦100,020.00** |
| Pension (8% of gross) | ₦8,001.60 |
| Tax (annualised gross: 100,020 × 26 = 2,600,520) | ₦14,887.62 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦22,894.22** |
| **Net Pay** | **₦77,125.78** |

#### 4.1.3. Weekly (52 periods/year)

| Component | Amount |
|-----------|--------|
| Base Salary | ₦100,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦100,020.00** |
| Pension (8% of gross) | ₦8,001.60 |
| Tax (annualised gross: 100,020 × 52 = 5,201,040) | ₦14,945.31 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦22,951.91** |
| **Net Pay** | **₦77,068.09** |

#### 4.1.4. Daily (260 periods/year)

| Component | Amount |
|-----------|--------|
| Base Salary | ₦100,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦100,020.00** |
| Pension (8% of gross) | ₦8,001.60 |
| Tax (annualised gross: 100,020 × 260 = 26,005,200) | ₦14,991.46 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦22,998.06** |
| **Net Pay** | **₦77,021.94** |

#### 4.1.5. Summary – Salaried Full Across Frequencies

| Frequency | Periods/Year | Net Pay |
|-----------|-------------|---------|
| Monthly | 12 | ₦77,260.40 |
| Semi‑monthly | 24 | ₦77,135.40 |
| Bi‑weekly | 26 | ₦77,125.78 |
| Weekly | 52 | ₦77,068.09 |
| Daily | 260 | ₦77,021.94 |

---

### 4.2. Salaried Daily (1 day worked, 23 working days in period)

- **Daily Rate** = ₦100,000 ÷ 23 = **₦4,347.83**
- **Base Pay** = 1 × ₦4,347.83 = **₦4,347.83**

#### 4.2.1. Monthly (12 periods/year)

| Component | Amount |
|-----------|--------|
| Base Pay | ₦4,347.83 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦4,367.83** |
| Pension (8% of gross) | ₦349.43 |
| Tax (annualised gross: 4,367.83 × 12 = 52,413.96) | ₦405.17 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦759.60** |
| **Net Pay** | **₦3,608.23** |

#### 4.2.2. Weekly (52 periods/year)

| Component | Amount |
|-----------|--------|
| Base Pay | ₦4,347.83 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦4,367.83** |
| Pension (8% of gross) | ₦349.43 |
| Tax (annualised gross: 4,367.83 × 52 = 227,127.16) | ₦597.48 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦951.91** |
| **Net Pay** | **₦3,415.92** |

#### 4.2.3. Summary – Salaried Daily Across Frequencies

| Frequency | Periods/Year | Net Pay |
|-----------|-------------|---------|
| Monthly | 12 | ₦3,608.23 |
| Semi‑monthly | 24 | ₦3,483.23 |
| Bi‑weekly | 26 | ₦3,473.61 |
| Weekly | 52 | ₦3,415.92 |
| Daily | 260 | ₦3,369.76 |

---

### 4.3. Hourly (2 regular hours worked, no overtime/double‑time)

- **Base Pay** = 2 × ₦2,000 = **₦4,000.00**

#### 4.3.1. Monthly (12 periods/year)

| Component | Amount |
|-----------|--------|
| Base Pay | ₦4,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦4,020.00** |
| Pension (8% of gross) | ₦321.60 |
| Tax (annualised gross: 4,020 × 12 = 48,240) | ₦360.33 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦686.93** |
| **Net Pay** | **₦3,333.07** |

#### 4.3.2. Weekly (52 periods/year)

| Component | Amount |
|-----------|--------|
| Base Pay | ₦4,000.00 |
| Adjustments | + ₦20.00 |
| **Gross Pay** | **₦4,020.00** |
| Pension (8% of gross) | ₦321.60 |
| Tax (annualised gross: 4,020 × 52 = 209,040) | ₦545.31 |
| Deduction Adjustment | ₦5.00 |
| **Total Deductions** | **₦871.91** |
| **Net Pay** | **₦3,148.09** |

#### 4.3.3. Summary – Hourly Across Frequencies

| Frequency | Periods/Year | Net Pay |
|-----------|-------------|---------|
| Monthly | 12 | ₦3,333.07 |
| Semi‑monthly | 24 | ₦3,215.40 |
| Bi‑weekly | 26 | ₦3,205.78 |
| Weekly | 52 | ₦3,148.09 |
| Daily | 260 | ₦3,101.94 |

---

## 5. How Overtime & Double‑Time Work (Hourly Employees)

When an hourly employee works beyond the daily threshold (default 8 hours), the attendance system splits hours into:

- `regular_hours` – up to 8 hours
- `overtime_hours` – between 8 and the double‑time threshold (default 12 hours)
- `double_time_hours` – beyond 12 hours

Payroll then applies multipliers from the **attendance policy** (default: 1.5× for overtime, 2× for double‑time).

**Example:**

| Hours Worked | regular_hours | overtime_hours | double_time_hours |
|--------------|---------------|----------------|-------------------|
| 9 | 8 | 1 | 0 |
| 12 | 8 | 2 | 2 |
| 14 | 8 | 4 | 2 |

**Calculation (hourly_rate = ₦2,000, overtime_multiplier = 1.5, double_time_multiplier = 2.0):**

For 12 hours:
```
Regular Pay = 8 × 2,000 = ₦16,000
Overtime Pay = 2 × 2,000 × 1.5 = ₦6,000
DoubleTime Pay = 2 × 2,000 × 2.0 = ₦8,000
Base Pay = ₦16,000 + ₦6,000 + ₦8,000 = ₦30,000
```

---

## 6. How Work Patterns Affect Salaried Daily Employees

For a **Salaried Daily** employee, the **total working days** in the period determine the daily rate.

| Work Pattern | Working Days in July 2026 (31 days) |
|--------------|-------------------------------------|
| Mon–Fri | 23 |
| Mon–Sat | 27 |
| Sun–Thu | 26 |

**Impact on Daily Rate:**

| Work Pattern | Daily Rate (₦100,000 / days) |
|--------------|------------------------------|
| Mon–Fri | ₦4,347.83 |
| Mon–Sat | ₦3,703.70 |
| Sun–Thu | ₦3,846.15 |

> **Note:** Changing the work pattern changes the daily rate, and therefore the gross pay for the same number of attended days.

---

## 7. How Policy Base (`base_salary` vs `gross_pay`) Affects Deductions

Each policy can be configured to apply to **base salary** or **gross pay**.

| Policy Base | Effect |
|-------------|--------|
| `base_salary` | Deduction is based on the employee's fixed salary (or daily/hourly base), **excluding** adjustments (bonuses, commissions, etc.) |
| `gross_pay` | Deduction is based on **total earnings**, including adjustments |

**Example (Salaried Full, Monthly):**

- Base Salary = ₦100,000
- Adjustments = +₦20
- Gross Pay = ₦100,020

| Policy | Base | Calculation | Amount |
|--------|------|-------------|--------|
| Pension | `gross_pay` | 8% × 100,020 | ₦8,001.60 |
| Pension | `base_salary` | 8% × 100,000 | ₦8,000.00 |

---

## 8. Important Notes

### 8.1. Attendance Consistency

- `net_hours` must equal `regular_hours + overtime_hours + double_time_hours - unpaid_break_minutes/60`.
- To prevent manual entry errors, `net_hours` is **auto‑calculated** from the breakdown fields when saved via the UI.
- The system trusts the breakdown fields when they sum to `net_hours`; otherwise, it falls back to `net_hours`.

### 8.2. Work Pattern Fallback

- If an employee has no `EmployeeWorkPattern` assignment, the system uses:
  1. The **company’s default** work pattern (if set).
  2. **Weekdays (Mon–Fri)** as a system fallback.

### 8.3. Overtime Multipliers

- Overtime multipliers (1.5×, 2×) are read from the **attendance policy** assigned to the employee.
- If the attendance policy does not define them, the system uses config defaults.

### 8.4. Pay Schedule vs Pay Frequency

- **Pay Schedule** determines the period (e.g., 1–31 July) and when runs are generated.
- **Pay Frequency** (employee‑level) determines the **number of periods per year** for **tax annualisation**.
- These are independent: a monthly schedule can still process employees with different pay frequencies.

### 8.5. Proration

- Policies are prorated based on the **number of active days** within the payroll period.
- For example, if a policy becomes effective on 15 July, only the remaining days are used in the proration factor.

---

## 9. Quick Reference Tables

### 9.1. Periods per Year by Frequency

| Frequency | Periods/Year |
|-----------|-------------|
| Monthly | 12 |
| Semi‑monthly | 24 |
| Bi‑weekly | 26 |
| Weekly | 52 |
| Daily (working days) | 260 |

### 9.2. Net Pay Examples (Same Base, Different Frequency)

**Salaried Full (₦100,000 base, +₦20 adjustments, 8% pension, tax bands)**

| Frequency | Net Pay |
|-----------|---------|
| Monthly | ₦77,260.40 |
| Semi‑monthly | ₦77,135.40 |
| Bi‑weekly | ₦77,125.78 |
| Weekly | ₦77,068.09 |
| Daily | ₦77,021.94 |

**Salaried Daily (1 day worked, ₦100,000 base, +₦20 adjustments)**

| Frequency | Net Pay |
|-----------|---------|
| Monthly | ₦3,608.23 |
| Semi‑monthly | ₦3,483.23 |
| Bi‑weekly | ₦3,473.61 |
| Weekly | ₦3,415.92 |
| Daily | ₦3,369.76 |

**Hourly (2 hours worked, ₦2,000/hr, +₦20 adjustments)**

| Frequency | Net Pay |
|-----------|---------|
| Monthly | ₦3,333.07 |
| Semi‑monthly | ₦3,215.40 |
| Bi‑weekly | ₦3,205.78 |
| Weekly | ₦3,148.09 |
| Daily | ₦3,101.94 |

---

## 10. Conclusion

This guide covers the core payroll calculation logic and provides concrete examples for all supported pay types and frequencies. The system is **flexible**, **accurate**, and designed to handle diverse employment and tax scenarios.

For further assistance, refer to the system documentation or contact your payroll administrator.
