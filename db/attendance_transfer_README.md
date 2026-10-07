# Attendance transfer setup

Install the same application version on the offline laptop and the server. Run `attendance_transfer_migration.sql` against `moh_bio` on **each** device. The migration only adds two tracking tables. PHP's `zip` extension and write access to the application folder and `upload/` are required.

Open **Attendance Transfer** from Other Settings or Attendance Reports as an administrator. On the laptop, export the dates recorded during the trip. On the server, upload the ZIP, review the personnel matches and record counts, then confirm. The server can export in the same way and the laptop can import in the same way. Personnel are matched by both RFID and personnel code; missing or conflicting matches block the whole import. No personnel roster, biometric template, or leave record is transferred.

The first visit creates `attendance_device_id.php` locally. This file is excluded from Git. **Do not copy it between devices**: each installation needs its own identity even when both use the same code and roster. The generated identity is also bound to the computer name to protect against an accidental folder copy. Deploy the migration and application files to each device, but let each device generate this file itself.

Repeated ZIP imports and overlapping date exports skip matching records. An open biometric row exported before its time-out can later receive the completed time-out from a newer export. Other changes to a previously imported source row are shown as conflicts for review; the importer does not silently overwrite attendance. Captured attendance photos are transferred when present. Keep the ZIP private because it contains attendance data and may contain photos.

Each ZIP must be at most 30 MB; use a shorter date range if needed. Export stops if a selected attendance row has no unique personnel match or if the same person/date/AM-PM slot contains different clock times. These records must be corrected before transfer so no attendance is silently omitted or overwritten.
