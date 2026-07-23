# Performance Recommendations

## Highest-Priority Indexes

Add or verify indexes on the columns that appear most often in filters, joins, and lookup queries:

- `personnels(personnel_id)`
- `personnels(personnel_id_code)`
- `personnels(RFTag_id)`
- `personnels(do_id, empStat_id, des_id, shift_id)`
- `personnel_logs(RFTag_id, logDate, logFlow)`
- `personnel_logs(logDate, logFlow)`
- `time_schedules(do_id, shift_id, day)`
- `dept_offices(do_id)`
- `designation(des_id)`
- `emp_status(empStat_id)`
- `activity_calendar(completeDate, status)`
- `service_record(personnel_id, appointDate_status)`
- `personnel_educ_bg(personnel_id, degree, school_name, year_grad)`
- `personnel_seminars(personnel_id, event_date)`
- `leave_applicants(lap_code, applicant_id)`
- `lap_dates(lap_code)`
- `news(news_id, ipAddress)`

## Query Shape Improvements

- Replace `SELECT *` with the exact columns needed on list and report pages.
- Prefer `LIMIT 1` for existence checks instead of loading full rows.
- Keep multi-column filters in the same order as the index definition so MySQL can use the index efficiently.
- Avoid repeated lookups inside loops when the same reference data can be fetched once and reused.

## Batch And Transaction Changes

- Keep CSV import and bulk update flows inside transactions.
- Batch inserts or updates where possible instead of executing one statement per row without a transaction.
- For large report pages, fetch a page of rows first, then resolve display data in one additional query instead of per-row lookups.

## Pagination And Reporting

- Add pagination to large lists such as personnel, schedules, news, and log viewers.
- For export endpoints, filter as narrowly as possible before streaming data.
- Avoid rendering full history tables in the browser when a summary view is enough.

## Security And Stability Notes

- Keep using PDO prepared statements for all request-driven queries.
- Remove `or die(...)` patterns in favor of controlled error handling and logging.
- Keep connection setup centralized through `dbcon.php` and `dbcon3.php` so future changes do not fragment again.