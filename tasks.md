<!-- filepath: c:\Users\Administrator\Documents\Dental_And_Billing_System\tasks.md -->
# 📝 Progress Notes Features

#### Planned Enhancements

- [ ] **Visit Table:** Record patient visits and related progress details.
- [ ] **Recall Table:** Schedule and manage patient recalls for follow-up progress notes.
- [ ] **Service and Teeth Provider:** Track services performed, associated teeth providers, and pricing for progress monitoring.
- [ ] **Bills and Bill_items Table:** Store billing information and itemized charges linked to progress notes.
- [ ] **Certificates Table:** Attach and manage certificates relevant to patient progress.
- [ ] **Patient Signature Print Option:** Print patient signatures directly from the progress note entry.

#### Implemented / Present in UI

- [x] Progress Notes panel (partial): resources/views/pages/patients/partials/progress-notes.blade.php
- [x] Add Progress Note modal: resources/views/pages/patients/progress-notes/modals/add.blade.php
  - Form layout, fields for visit/follow-up date, service/teeth, amount display, attachments and signature input are present.

#### Action items / Follow-ups (server & UX)
- Set a concrete route & controller for storing progress notes:
  - Create a POST route like: patients.{patient}.progress-notes.store and a controller method to validate + persist.
- Ensure the modal form sends patient_id (hidden) and service_id, teeth_id, amount, attachments, signature.
- Implement server-side validation for required fields and file uploads (do not rely solely on JS).
- Populate service and teeth <select> options from DB (Blade loop).
- Wire service selection to populate amount and compute total/discount/net on the client side.
- Persist attachments and store signature (prefer image blob or data URI instead of plain text).
- Add DB tables/migrations for Visits / ProgressNotes / Recalls / Bill & BillItems as planned; link to patient.id.
- Add print/export for patient signature in progress note view.

