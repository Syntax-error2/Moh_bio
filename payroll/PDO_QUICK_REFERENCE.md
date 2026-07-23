# Quick Reference: PDO Database Queries in Payroll Module

## ✅ CORRECT Pattern (Use This)

```php
// 1. Prepare the query
$query = $conn->prepare("SELECT * FROM table WHERE column = :value");

// 2. Execute with parameters
$query->execute([':value' => $input]);

// 3. Fetch results
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// 4. Loop through results
foreach ($results as $row) {
    echo $row['column_name'];
}
```

## ❌ WRONG Pattern (Don't Use This)

```php
// MySQLi style - DO NOT USE IN PAYROLL MODULE
$query = "SELECT * FROM table WHERE column = '$input'";
$result = $db->query($query);
while ($row = $result->fetch_assoc()) {
    echo $row['column_name'];
}
```

## Common Operations

### Insert
```php
$query = $conn->prepare("INSERT INTO table (col1, col2) VALUES (:val1, :val2)");
$query->execute([':val1' => $value1, ':val2' => $value2]);
$lastId = $conn->lastInsertId();
```

### Update
```php
$query = $conn->prepare("UPDATE table SET col1 = :val1 WHERE id = :id");
$query->execute([':val1' => $value1, ':id' => $id]);
$rowCount = $query->rowCount();
```

### Delete
```php
$query = $conn->prepare("DELETE FROM table WHERE id = :id");
$query->execute([':id' => $id]);
```

### Count
```php
$query = $conn->prepare("SELECT COUNT(*) FROM table WHERE column = :value");
$query->execute([':value' => $input]);
$count = $query->fetchColumn();
```

### Single Row
```php
$query = $conn->prepare("SELECT * FROM table WHERE id = :id");
$query->execute([':id' => $id]);
$row = $query->fetch(PDO::FETCH_ASSOC);
```

## Variables

- **Connection:** `$conn` (from dbcon.php via session.php)
- **Never use:** `$db` (MySQLi - not available)

## Files Fixed

1. ✅ view_payroll_profile.php - Line 677 (Department query)
2. ✅ view_payroll_profile.php - Line 826 (Income query)
3. ✅ view_payroll_profile.php - Line 954 (Deduction query)

All queries now use PDO correctly!
