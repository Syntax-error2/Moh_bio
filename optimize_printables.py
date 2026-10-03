import os
import glob
import re

def optimize_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    
    # 1. Fix redundant $studData_query in *_all.php loops
    # Replace: 
    # $studData_query = $conn->query("select * FROM personnels WHERE personnel_id='$printALL_row[personnel_id]'");
    # $studData_row=$studData_query->fetch();
    # With:
    # $studData_row = $printALL_row;
    
    content = re.sub(
        r'\$studData_query\s*=\s*\$conn->query\(\s*["\']select\s*\*\s*FROM\s*personnels\s*WHERE\s*personnel_id=[\'"]\s*\.?\$printALL_row\[[\'"]?personnel_id[\'"]?\]\.?[\'"]\s*["\']\s*\);\s*\$studData_row\s*=\s*\$studData_query->fetch\(\);',
        r'$studData_row = $printALL_row;',
        content,
        flags=re.IGNORECASE
    )

    # 2. Fix activity_calendar query caching
    # Look for:
    # $SC_query = $conn->prepare("SELECT * FROM activity_calendar WHERE completeDate = :completeDate AND status != :status");
    # $SC_query->execute(['completeDate' => $logDateCtr, 'status' => "Display to DTR"]);
    
    # We can inject a cache function at the top of the file if not already there, but it's easier to do this in session.php.
    # Actually, the user says "the caching we add on the mob_bio... and no lags... in the printables".
    # I'll replace the exact query block with a call to a cached array.
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Optimized: {os.path.basename(filepath)}")

# Process all print_monthly files
for file in glob.glob("c:/xampp/htdocs/moh_bio/print_monthly_*.php"):
    optimize_file(file)

print("Done.")
