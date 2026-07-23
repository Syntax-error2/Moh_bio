# MOH HRMS - AI Coding Instructions

## Project Overview
This is a **Human Resource Management System (HRMS)** for the Municipal/Department of Health, built with PHP, MySQL (PDO), Bootstrap, jQuery, and DataTables. It runs on XAMPP.

## Architecture Patterns

### File Organization
- **Page files** (`*.php`): Display pages include `session.php`, `header.php`, `menu_sidebar.php`, `footer.php`
- **Modal files** (`*_modal.php`): Reusable modals included in page files
- **Save files** (`save_*.php`): Process form submissions with validation
- **List files** (`list_*.php`): Display data tables with CRUD operations
- **Print files** (`print_*.php`): Generate printable reports

### Database Connection
Always use `dbcon.php` which provides `$conn` (PDO connection):
```php
include('session.php');  // Includes dbcon.php
// Use prepared statements:
$stmt = $conn->prepare("SELECT * FROM table WHERE id = :id");
$stmt->execute([':id' => $id]);
```

### Key Data Relationships

#### Leave Management (Critical Sync Points)
1. **leave_applications** → **leave_card** (linked via `leave_card_entry_id`)
   - Leave card entry is **only created when status changes to 'approved'**
   - On update, syncs: `less_application_vl` ↔ `vl_with_pay`, `less_application_sl` ↔ `sl_with_pay`
2. **leave_applications** → **leave_applicants** (DTR entries via `leave_application_id`)
3. Field mappings between tables:
   - `less_application_vl` ↔ `vl_with_pay`
   - `less_application_sl` ↔ `sl_with_pay`
   - `less_application_vl_without_pay` ↔ `vl_without_pay`
   - `less_application_sl_without_pay` ↔ `sl_without_pay`

#### Personnel Data
- `personnels` → `dept_offices` (via `do_id`)
- `personnels` → `designation` (via `des_id`)
- `personnels` → `service_record` (via `personnel_id`, use `appointDate_status = 'Active'` for current)

### Special Leave Types (No Credit Deductions)
```php
$special_leave_types = [
    'Maternity Leave', 'Paternity Leave', 'Special Privilege Leave',
    'Solo Parent Leave', 'Study Leave', '10-Day VAWC Leave',
    'Rehabilitation Privilege', 'Special Leave Benefits for Women',
    'Special Emergency (Calamity) Leave', 'Adoption Leave'
];
```

## Code Conventions

### PHP Patterns
- Use PDO prepared statements (never raw SQL interpolation)
- Session check: `if (!isset($_SESSION['id'])) { header("Location: index.php"); exit(); }`
- Return JSON for AJAX: `echo json_encode(['success' => true, 'data' => $data]);`
- Error handling: `try/catch` with `error_log()` for debugging

### JavaScript/jQuery Patterns
- DataTables for all data tables with custom configs
- Bootstrap modals for forms and confirmations
- AJAX for data fetching: `$.ajax({ url: 'endpoint.php', type: 'POST', dataType: 'json', ... })`
- `parseFloat()` for numeric calculations, `toFixed(3)` for leave credits

### URL Parameters
Personnel pages use: `?dept={do_id}&personnel_id={personnel_id}`
```php
$personnel_id = $_GET['personnel_id'] ?? '';
$dept_id = $_GET['dept'] ?? '';
```

## Critical Files for Leave Management
- `save_leave_application.php` - Create/Update/Delete leave applications
- `save_add_leave_card_entry.php` - Manual leave card entries
- `get_leave_application_print_data.php` - Fetch data for CS Form 6 printing
- `get_leave_card_balance.php` - Fetch current leave balances
- `print_leave_application_csform6.php` - CS Form No. 6 template

## Common Gotchas
1. **Leave balance calculation**: Use stored `less_application_*` values, not recalculated from `number_of_days`
2. **is_special_leave column**: Check if column exists before querying (backward compatibility)
3. **Date periods**: `period_from`/`period_to` in leave_card use month boundaries; `date_from`/`date_to` store actual leave dates
4. **Modal IDs**: Include unique identifiers (e.g., `#modal_{$row['id']}`) for multiple modals in loops
