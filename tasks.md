### ✅ To-Do List

- Recalls
  - Only these fields are editable: Next Recall, Note, Status

- Billing
  - Only the Status field is editable.
  - View Shows all Bill Items.
  - Do not create bills immediately. Use a bill status such as "Not recorded" and set it to "Unpaid" only when the treatment status is "Ongoing" or "Finished".

- All statuses must be enums.

- Treatment provider should be "associate"
  - Provider, procedure, tooth, notes, and status must be editable.
  - In table view the Tooth cell is clickable and opens a Tooth modal.
  - The Tooth modal must include a "Condition" field implemented as an enum dropdown to enforce consistent values:
    - Unknown, Healthy, Decay, Filled, Missing, Crown, Root Canal, Fracture, Abscess, Other
    - If "Other" is selected, allow a short free-text note.
  - Persist the condition as a standardized enum value in the database and display its label in the UI.
  - The Condition dropdown should be searchable and default to "Unknown".
