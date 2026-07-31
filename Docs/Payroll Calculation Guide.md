# Payroll Calculation Guide

## 1. Overview

This guide explains how payroll calculations work for employees based on their **pay type**, **work pattern**, **attendance records**, and configured **policies**. It covers the three supported pay types:

- **Salaried Full (Monthly)** – fixed monthly salary, no attendance required.
- **Salaried Daily** – salary prorated based on actual working days attended.
- **Hourly** – pay calculated on actual hours worked (regular, overtime, double‑time).

All calculations are performed by the `PayrollCalculator` service, which respects the `base` setting (`base_salary` or `gross_pay`) of each policy.

---

## 2. Salaried Full (Monthly)

### 2.1. Calculation

- **Gross Pay** = `base_salary` (from `employee_positions`)
- **Adjustments** (bonuses, commissions, reimbursements, corrections) are added or subtracted as separate line items.
- **Policies** (pension, tax, etc.) apply to the `base_salary` or `gross_pay` depending on the policy’s `base` setting.

### 2.2. Example

| Field | Value |
|-------|-------|
| `base_salary` | ₦100,000 |
| Adjustments | +₦20 (4 × ₦5) |
| **Gross Pay** | **₦100,020** |
| Pension (8% of gross, `base: gross_pay`) | ₦8,001.60 |
| Tax (using annualised gross, `base: gross_pay`) | ₦14,753.00 |
| Net Pay | ₦77,265.40 |

> **Note:** If pension uses `base_salary`, the deduction would be ₦8,000 (8% of ₦100,000). This is configurable per policy.

---

## 3. Salaried Daily

### 3.1. Calculation

- **Daily Rate** = `base_salary` ÷ **total working days** in the payroll period.
- **Total working days** are derived from the employee’s **work pattern** (e.g., Mon–Fri) for the given period.
- **Worked Days** = number of days where `net_hours > 0` or `is_paid_absence = true` (attendance records).
- **Base Pay** = `Daily Rate × Worked Days`.
- **Adjustments** and **policies** apply to the `Base Pay` (or `gross_pay` after adjustments) depending on policy base.

### 3.2. Example

| Field | Value |
|-------|-------|
| `base_salary` | ₦100,000 |
| Period | 1–31 July 2026 (31 days) |
| Work pattern | Monday–Friday → 23 working days |
| Worked days | 1 (attended 1 day) |
| Daily Rate | ₦100,000 ÷ 23 = ₦4,347.83 |
| **Base Pay** | **₦4,347.83** |
| Adjustments | +₦20 |
| **Gross Pay** | **₦4,367.83** |
| Pension (8% of gross) | ₦349.43 |
| Tax (annualised gross) | ₦405.17 |
| Net Pay | ₦3,608.23 |

> **Important:** If no work pattern is assigned, the system defaults to **weekdays (Mon–Fri)**. If an employee has no attendance on a working day, that day is not counted.

---

## 4. Hourly

### 4.1. Calculation

- **Regular Pay** = `regular_hours × hourly_rate`
- **Overtime Pay** = `overtime_hours × hourly_rate × overtime_multiplier`
- **Double‑Time Pay** = `double_time_hours × hourly_rate × double_time_multiplier`
- **Gross Pay** = Regular + Overtime + Double‑Time + Adjustments

The multipliers are fetched from the **attendance policy** assigned to the employee (or fallback to config defaults).

### 4.2. Example

| Field | Value |
|-------|-------|
| `hourly_rate` | ₦2,000 |
| `regular_hours` (from attendance) | 2.00 |
| `overtime_hours` | 0.00 |
| `double_time_hours` | 0.00 |
| **Regular Pay** | 2 × 2,000 = ₦4,000 |
| Adjustments | +₦20 |
| **Gross Pay** | **₦4,020.00** |
| Pension (8% of gross) | ₦321.60 |
| Tax (annualised gross) | ₦360.33 |
| Net Pay | ₦3,333.07 |

> **Tip:** The attendance calculator ensures `regular_hours` reflects actual regular hours worked (not the shift duration). If `regular_hours` is mistakenly set to shift duration (e.g., 8 hours), the payroll will overpay.

---

## 5. Role of Policies

Policies (pension, tax, benefit, deduction, etc.) are defined in `payroll_policies` and assigned via `payroll_policy_assignments`. Each policy can be configured to apply to either:

- **`base_salary`** (the employee’s fixed salary or attendance‑based base pay)
- **`gross_pay`** (total earnings after all adjustments)

This is set in the `calculation_logic` JSON under the `"base"` key. If not set, it defaults to `base_salary`.

**Example:** A tax policy with `"base": "gross_pay"` uses the gross amount (including bonuses, commissions, etc.) as the taxable base. A pension policy with `"base": "base_salary"` uses only the employee’s base pay.

---

## 6. Role of Work Patterns

Work patterns (`work_patterns`) define which days of the week are considered working days (e.g., Monday–Friday). They are used by **salaried_daily** employees to compute the total number of working days in the period, which determines the daily rate.

- If an employee has an `EmployeeWorkPattern` assignment with specific dates, that takes precedence.
- Otherwise, the system looks for a default work pattern for the employee’s company.
- If none exists, it falls back to **weekdays (Mon–Fri)**.

> **Impact:** If the work pattern does not match the employee’s actual schedule, the daily rate and thus gross pay will be incorrect.

---

## 7. Role of Attendance Policies

Attendance policies (`attendance_policies`) define:
- **Overtime thresholds** (daily/weekly hours)
- **Overtime multipliers** (e.g., 1.5× for overtime, 2× for double‑time)
- **Grace periods** for lateness/early departure
- **Break rules** and **unpaid break minutes**

These are used by the **attendance calculator** (`AttendanceCalculator`) to populate `regular_hours`, `overtime_hours`, `double_time_hours`, and `net_hours`.

The payroll calculator reads the **overtime multiplier** and **double‑time multiplier** from the employee’s attendance policy (or fallback to config) to compute pay for hourly employees.

---

## 8. Impact of Clocking Out Early / Missing Events

The **attendance status** (present, late, early departure, half‑day, incomplete, absent) is determined by the attendance calculator based on clock‑in/out times, expected shift duration, and grace periods.

- **Early departure** (clock‑out before shift end minus grace period) may still be counted as a working day, but the hours are the actual time worked.
- **Half‑day** (worked <50% of expected shift) is flagged for review but still counts as a worked day (with actual hours).
- **Incomplete** (between 50% and 90%) also counts, with actual hours.
- **Absent** (no clock events) is counted as 0 hours and does **not** contribute to worked days.

**For salaried_daily**:
- Only days with `net_hours > 0` or `is_paid_absence = true` count as worked days.
- Early departure or half‑day still count as a worked day (the hours worked are still >0).

**For hourly**:
- Pay is based **only on actual hours** (`regular_hours`, `overtime_hours`, `double_time_hours`).
- There is no concept of a "daily rate"; hours are summed across the period.

---

## 9. Summary of Key Formulas

### Salaried Full
```
Gross Pay = base_salary + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### Salaried Daily
```
Total Working Days = count of days in period that match work pattern
Daily Rate = base_salary / Total Working Days
Base Pay = Daily Rate × Worked Days (from attendance)
Gross Pay = Base Pay + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### Hourly
```
Regular Pay = regular_hours × hourly_rate
Overtime Pay = overtime_hours × hourly_rate × overtime_multiplier
DoubleTime Pay = double_time_hours × hourly_rate × double_time_multiplier
Base Pay = Regular Pay + Overtime Pay + DoubleTime Pay
Gross Pay = Base Pay + adjustments
Deductions = sum(policy amounts based on base/gross)
Net Pay = Gross Pay - Deductions
```

### Policies (Tax, Pension, etc.)
```
If policy uses base_salary:
   Effective Base = base_salary (or daily/hourly base pay)
If policy uses gross_pay:
   Effective Base = Gross Pay (after adjustments)
Policy Amount = percentage × Effective Base  (or fixed amount)
```

---

## 10. Important Notes for Users

- **Manual entries:** Always ensure `regular_hours`, `overtime_hours`, `double_time_hours` and `net_hours` are consistent. It is recommended to **hide `net_hours`** on forms and auto‑calculate it as the sum of the breakdown fields.
- **Attendance records:** Only attendance with `net_hours > 0` or `is_paid_absence = true` counts as a worked day for salaried_daily.
- **Work pattern:** Employees without an explicit `EmployeeWorkPattern` will use the company’s default or weekdays.
- **Policies:** Check the `base` setting of each policy (in `calculation_logic`) to ensure correct application.
- **Overtime multipliers:** Are defined in the attendance policy; if missing, the system uses config defaults (1.5× and 2×).

---

This guide should help users understand how their payroll is calculated and what data influences it. For any further questions, refer to the system documentation or contact your payroll administrator.
