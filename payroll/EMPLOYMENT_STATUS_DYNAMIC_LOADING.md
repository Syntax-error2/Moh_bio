# Employment Status Dynamic Loading Update

**Date:** October 20, 2025  
**File:** `view_payroll_profile.php`  
**Change:** Updated Employment Status filter to fetch from database

## Change Summary

The Employment Status dropdown in the "Add Personnel Filter" modal now dynamically loads values from the `emp_status` table instead of using hardcoded values.

## What Changed

### Before (Hardcoded Values)
```php
<select class="form-control" name="employment_status[]" multiple size="6">
    <option value="Permanent">Permanent</option>
    <option value="Casual">Casual</option>
    <option value="Job Order">Job Order</option>
    <option value="Contract of Service">Contract of Service</option>
    <option value="Temporary">Temporary</option>
    <option value="Probationary">Probationary</option>
</select>
```

**Issues:**
- ❌ Not dynamic - can't add new statuses without code changes
- ❌ Might not match actual database values
- ❌ No sync with emp_status table

### After (Database-Driven)
```php
<select class="form-control" name="employment_status[]" multiple size="8">
    <?php
    $emp_status_query = $conn->prepare("SELECT empStat_id, emp_stat_name FROM emp_status ORDER BY emp_stat_name");
    $emp_status_query->execute();
    $emp_status_results = $emp_status_query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($emp_status_results as $emp_stat) {
        echo "<option value='{$emp_stat['empStat_id']}'>{$emp_stat['emp_stat_name']}</option>";
    }
    ?>
</select>
```

**Benefits:**
- ✅ Dynamic - automatically shows all employment statuses from database
- ✅ Uses empStat_id as value (proper foreign key reference)
- ✅ Displays emp_stat_name as label (user-friendly)
- ✅ Always in sync with emp_status table
- ✅ Alphabetically sorted
- ✅ Increased size to 8 rows for better visibility

## Database Table Structure

```sql
TABLE `emp_status` (
  `empStat_id` int(11) NOT NULL,           -- Used as <option value>
  `emp_stat_name` varchar(255) NOT NULL,   -- Displayed to user
  `position_class` varchar(55) NOT NULL,
  `status` varchar(55) NOT NULL
)
```

## Query Details

**SQL:** `SELECT empStat_id, emp_stat_name FROM emp_status ORDER BY emp_stat_name`

**Method:** PDO prepared statement (secure)

**Sorting:** Alphabetical by employment status name

**Output Example:**
```html
<option value="1">Casual</option>
<option value="2">Contract of Service</option>
<option value="3">Job Order</option>
<option value="4">Permanent</option>
<option value="5">Probationary</option>
<option value="6">Temporary</option>
```

## Implementation Details

### Location
- **File:** `view_payroll_profile.php`
- **Line:** ~689-703
- **Modal:** Add Personnel Filter Modal
- **Section:** Employment Status Filter Options

### PDO Pattern Used
```php
// 1. Prepare query
$emp_status_query = $conn->prepare("SELECT empStat_id, emp_stat_name FROM emp_status ORDER BY emp_stat_name");

// 2. Execute
$emp_status_query->execute();

// 3. Fetch all results
$emp_status_results = $emp_status_query->fetchAll(PDO::FETCH_ASSOC);

// 4. Loop and output
foreach ($emp_status_results as $emp_stat) {
    echo "<option value='{$emp_stat['empStat_id']}'>{$emp_stat['emp_stat_name']}</option>";
}
```

## Consistency

This change makes the Employment Status filter consistent with other dynamic dropdowns in the same modal:

1. ✅ **Department Filter** - Loads from `dept_offices` table
2. ✅ **Employment Status Filter** - Loads from `emp_status` table (NEW)
3. ❌ **Position Filter** - Text input (manual entry)
4. ❌ **Salary Grade Filter** - Number input (manual entry)
5. ❌ **Gender Filter** - Static options (Male/Female - doesn't need table)
6. ❌ **Age Range Filter** - Number input (manual entry)
7. ❌ **Custom Filter** - SQL textarea (manual entry)

## Testing Checklist

After this change, verify:

- [ ] Page loads without errors
- [ ] Add Personnel Filter modal opens
- [ ] Click "Filter Type" dropdown and select "Employment Status"
- [ ] Employment Status options div appears
- [ ] Dropdown shows all employment statuses from database
- [ ] Options are sorted alphabetically
- [ ] Can select multiple statuses (Ctrl/Cmd + click)
- [ ] Selected values are empStat_id (check form submission)

## Future Enhancements

Consider adding similar dynamic loading for:

1. **Position/Designation Filter** - Could load from `designation` table
2. **Salary Grade Filter** - Could load from a salary grade table if available

## Related Files

These files also use the `emp_status` table and should reference `empStat_id`:
- Backend filter processing (when created)
- Payroll generation logic
- Personnel filtering queries

## Impact

**Backend Compatibility:**
When the filter is saved and later applied during payroll generation, the backend PHP code should:

1. **Store:** `empStat_id` values (e.g., "1,2,3")
2. **Query:** JOIN with emp_status table or use IN clause:
   ```sql
   SELECT * FROM personnels 
   WHERE empStat_id IN (1,2,3)
   ```

**Data Type:**
- Value stored: Integer (`empStat_id`)
- Not a string anymore (previously "Permanent", "Casual", etc.)

## Migration Notes

If existing filters were saved with old string values (e.g., "Permanent"):
- Old filters may need data migration
- Consider adding a conversion script if needed
- Or accept that new filters use ID, old filters use names

## Summary

✅ **Updated:** Employment Status filter to use database values  
✅ **Uses:** `empStat_id` as value, `emp_stat_name` as display  
✅ **Method:** PDO prepared statement (secure)  
✅ **Benefits:** Dynamic, always in sync, proper foreign key reference  
✅ **Tested:** No PHP errors  

This change improves data integrity and makes the system more maintainable!
