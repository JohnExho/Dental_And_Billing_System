# Clinic Management System – Role‑Based User Manual

## WELCOME

Welcome to the Clinic Management System. This application is a comprehensive dental practice management system designed to streamline clinic operations by automating administrative tasks and maintaining secure electronic patient records.

The system improves efficiency by allowing clinic personnel to focus more on patient care and less on paperwork. It provides role‑based access controls, integrated billing and payments, detailed patient medical records, appointment scheduling, and comprehensive reporting capabilities.

---

## USING THIS GUIDE

This user manual is organized **by role**, following actual clinic workflows. Each section provides task‑based instructions similar to standard healthcare systems.

User roles:

* 🟢 **Administrator** – Full system control and configuration, multi-clinic management, reporting
* 🟡 **Staff** – Daily clinic operations, patient management, appointments, and billing
* 🔴 **Patient** – Self‑registration via QR code access only

---

## GETTING STARTED

### Authentication

The system supports multiple authentication methods:

* **Email & Password Login** – Standard credentials for staff and administrators
* **OTP Verification** – One-Time Password sent to registered email for enhanced security
* **Password Recovery** – Secure password reset via OTP verification
* **Session Management** – Automatic logout after inactivity, multi-device login tracking

### Logging In (🟢🟡)

1. Open the system login page (desktop only; mobile access is restricted).
2. Enter your registered email address and password.
3. Click **Login** to access the dashboard.
4. For first-time setup or password reset, an OTP will be sent to your email.

### Logging Out (🟢🟡)

1. Click the **Profile Account** button in the top-right corner.
2. Select **Logout** to securely end your session.

### Change Password (🟢🟡)

1. Navigate to **Settings**.
2. Click **Change Password**.
3. Enter your current password and new password (twice to confirm).
4. Click **Save**.

## ADMINISTRATOR USER GUIDE 🟢

Administrators have system‑wide access and are responsible for configuration, access control, and reporting.

### Administrator Dashboard

The dashboard provides an overview of clinics, staff, associates, system activity, and storage usage. Navigation menus allow access to all administrative modules.

---

### Account Management

Administrators can manage all system accounts.

#### Add Account

1. Navigate to **Accounts**.
2. Click **Add New Account**.
3. Fill in required user information (name, email, password).
4. Assign role and permissions.
5. Click **Save**.

#### Edit Account

1. Click the **Edit** icon beside an account.
2. Update details as needed.
3. Click **Save**.

#### Activate / Deactivate Account

1. Click the **Status** icon.
2. Confirm activation or deactivation.

#### Delete Account

1. Click the **Delete** icon.
2. Confirm deletion.

#### Change Name

1. Navigate to **Settings**.
2. Click **Change Name**.
3. Enter new name.
4. Click **Save**.

---

### Clinic Management

Administrators maintain clinic records and manage multi-clinic operations.

#### Add Clinic

1. Navigate to **Clinics**.
2. Click **Add New Clinic**.
3. Enter clinic information (name, address, contact details).
4. Click **Save**.

#### Edit Clinic Information

1. Click the **Edit** icon beside a clinic.
2. Update details as needed.
3. Click **Save**.

#### Delete Clinic

1. Click the **Delete** icon.
2. Confirm deletion.

Each clinic serves as a container for patients, staff, appointments, and services. Administrators can select a clinic to view clinic-specific data.

---

### Associate Management

Administrators can:

* Add associate profiles (dentists, specialists, hygienists)
* Edit associate information
* Activate or deactivate associates
* Delete associate records
* Manage associate credentials and specializations

---

### Staff Management

Administrators manage staff access by:

* Creating staff accounts
* Assigning clinic access
* Editing staff information
* Activating or deactivating staff
* Deleting staff accounts

---

### Patient Management

Administrators can fully manage patient records.

#### Add Patient

1. Navigate to **Patients**.
2. Click **Add New Patient**.
3. Fill in personal information (name, contact, address, medical history).
4. Click **Save**.

#### Edit Patient Profile

1. Select a patient from the patient list.
2. Click **Edit**.
3. Update information as needed.
4. Click **Save**.

#### Delete Patient

1. Select a patient.
2. Click **Delete**.
3. Confirm deletion.

#### Archive/Unarchive Patients

1. Navigate to **Patients** or **Patients (Archived)**.
2. Click the **Archive** or **Unarchive** button.
3. Confirm action.

Archived patients are retained for record purposes but no longer appear in active patient lists.

#### Manage Patient Documentation

Administrators can manage:

* **Progress Notes** – Clinical observations and treatment progress
* **Prescriptions** – Medication orders with dosage and instructions
* **Treatment Plans** – Detailed treatment plans with associated teeth and costs
* **Recalls** – Follow-up appointments and recall schedules
* **Diagnostics** – Diagnostic findings and referrals

---

### Waitlist & Appointment Management

#### Manage Waitlist

Administrators can:

1. Navigate to **Waitlist**.
2. Add patients to the daily waitlist
3. Edit or remove waitlist entries
4. View all pending waitlist requests

#### Schedule Appointments

1. Navigate to **Appointments**.
2. Click **Add New Appointment**.
3. Select patient, associate, date, and time.
4. Add appointment notes if needed.
5. Click **Save**.

#### Edit Appointments

1. Click the **Edit** icon beside an appointment.
2. Update details (date, time, associate, notes).
3. Click **Save**.

#### Cancel Appointments

1. Click the **Delete** icon beside an appointment.
2. Confirm cancellation.

#### Mark Appointments as Completed

1. Click the appointment record.
2. Click **Mark as Finished**.
3. Confirm completion.

---

### Billing & Payments

Administrators manage comprehensive billing operations.

#### Create Patient Bills

1. Navigate to **Billing**.
2. Click **Process Bill**.
3. Select a patient and treatment.
4. Add bill items (services, procedures, products).
5. Review total amount due.
6. Click **Save**.

#### Record Payments

1. Open a bill.
2. Click **Process Payment**.
3. Enter payment details:
   * Payment method (cash, online, credit card)
   * Amount paid
   * Discount percentage (if applicable)
   * Payment reference
4. Click **Save**.

#### Update Billing Records

1. Select a bill from the billing list.
2. Click **Edit**.
3. Update items or amounts as needed.
4. Click **Save**.

#### Delete Bills

1. Click the **Delete** icon beside a bill.
2. Confirm deletion.

Bills can be filtered by patient, date range, or payment status for easy management.

---

### Reports

Administrators can generate comprehensive reports for analytics and auditing:

#### Billing Reports

* View revenue by clinic and date range
* Track payment methods and collection status
* Monitor outstanding balances

#### Collection Reports

* View payment collection history
* Track collection rates by time period
* Identify overdue payments

#### Recall Reports

* View pending patient recalls
* Track recall completion rates
* Schedule follow-up appointments

#### Geographic Reports

* View patient distribution by province, city, and barangay
* Analyze patient demographics

#### Treatment Reports

* View completed treatments by date range
* Track treatment types and associates
* Monitor treatment outcomes

Reports support auditing and financial monitoring. Data can be exported for further analysis.

---

### Master Files & System Setup

Administrators configure core system data:

#### Manage Medicines

1. Navigate to **Medicines**.
2. Click **Add New Medicine**.
3. Enter medicine name, dosage, and clinic availability.
4. Click **Save**.

Edit or delete medicines as needed.

#### Manage Services / Treatments

1. Navigate to **Services**.
2. Click **Add New Service**.
3. Enter service name, description, and base price.
4. Click **Save**.

Services can be linked to specific teeth for pricing variations.

#### Manage Tooth Records

1. Navigate to **Teeth**.
2. Click **Add New Tooth**.
3. Enter tooth number, name, and pricing information.
4. Click **Save**.

Each clinic can have tooth-specific pricing through **Tooth Pricing**.

#### System Settings

1. Navigate to **Settings**.
2. Update system preferences:
   * Clinic-wide settings
   * Display preferences
   * Default values

#### QR Codes for Patient Self‑Registration

1. Navigate to **Tools**.
2. Click **Generate QR Code**.
3. Select clinic and set password (optional).
4. QR code is generated and displayed.
5. Share QR code with patients for self-registration.

---

### Tools & Logs

#### Export Patient Data

1. Navigate to **Tools**.
2. Click **Export Patients**.
3. Select filters (date range, clinic).
4. Click **Download**.
5. Patient data exports to Excel file.

Export includes patient demographics, contact information, and visit history. A cooldown of 30 seconds is enforced between exports to prevent abuse.

#### View Activity Logs

1. Navigate to **Tools** or **Reports**.
2. View system activity log.
3. Filter by user, action, date, or resource type.

Logs record all user actions for security auditing and compliance.

---

## STAFF USER GUIDE 🟡

Staff users perform daily clinic operations. Staff access is restricted to their assigned clinic.

### Staff Dashboard

The staff dashboard provides quick access to:

* **Daily Dashboard** – Overview of the day's activities
* **Waitlist** – Today's patient waitlist and queue
* **Calendar** – Scheduled appointments and availability
* **Patients** – Patient list and profiles
* **Archived Patients** – Historical patient records

---

### Waitlist Management

#### View Daily Waitlist

1. Click **Waitlist** from the main menu.
2. View all patients waiting today.
3. Sort by check-in time.

#### Add Patient to Waitlist

1. Click **Add to Waitlist**.
2. Select a patient or create new patient entry.
3. Enter check-in time and reason for visit.
4. Click **Save**.

#### Update Waitlist Entry

1. Click the **Edit** icon beside a waitlist entry.
2. Update details (reason, status).
3. Click **Save**.

#### Remove from Waitlist

1. Click the **Delete** icon beside a waitlist entry.
2. Confirm removal.

---

### Patient Handling

Staff can:

* **View Patient List** – Access all clinic patients
* **Add New Patient** – Create new patient profiles quickly
* **Edit Patient Information** – Update contact, address, or medical history
* **View Patient Profile** – Access complete patient record including history

---

### Appointment Management

#### View Appointments

1. Click **Appointments** from the menu.
2. View calendar or list view of appointments.
3. Filter by date, associate, or status.

#### Schedule Appointments

1. Click **Add New Appointment**.
2. Select patient, associate, date, and time.
3. Add visit notes (optional).
4. Click **Save**.

#### Edit Appointments

1. Click the **Edit** icon beside an appointment.
2. Update date, time, associate, or notes.
3. Click **Save**.

#### Mark as Completed

1. Select an appointment.
2. Click **Mark as Finished** after the visit.
3. System records completion.

#### Cancel Appointments

1. Click the **Delete** icon beside an appointment.
2. Confirm cancellation.

---

### Patient Documentation

Staff can manage comprehensive patient records:

#### Add Progress Notes

1. Open patient profile.
2. Click **Add Progress Note**.
3. Enter clinical observations, symptoms, and findings.
4. Click **Save**.

Progress notes track each patient visit and treatment progression.

#### Create Prescriptions

1. Open patient profile.
2. Click **Add Prescription**.
3. Select medicine and enter:
   * Dosage
   * Frequency
   * Duration
   * Instructions
4. Click **Save**.

#### Create Treatment Plans

1. Open patient profile.
2. Click **Add Treatment**.
3. Select treatment type and affected teeth.
4. Enter description and estimated cost.
5. Click **Save**.

#### Manage Recalls

1. Open patient profile.
2. Click **Add Recall**.
3. Set recall date and type (cleaning, checkup, etc.).
4. Click **Save**.

Recall records help track preventive care and follow-up needs.

---

### Billing & Payments

Staff can process patient billing:

#### Create Patient Bills

1. Navigate to **Billing** (if accessible to staff).
2. Click **Process Bill**.
3. Select patient and treatment items.
4. Add bill line items:
   * Services
   * Medications
   * Materials
5. Review total amount.
6. Click **Save**.

#### Record Payments

1. Open a patient bill.
2. Click **Process Payment**.
3. Enter:
   * Payment method (cash, online, credit card)
   * Amount paid
   * Discount (if applicable)
   * Payment date and time
4. Click **Save**.

Payment confirmation is displayed and recorded.

#### View Billing History

1. Select a patient.
2. View all bills and payments.
3. Check outstanding balances.

---

## PATIENT USER GUIDE 🔴

Patients have limited self‑service access for registration and basic profile management through secure QR code access.

### QR Code Access

Clinic staff provides patients with a QR code for registration.

#### Step 1: Scan QR Code

1. Scan the clinic‑provided QR code using a mobile device or QR code reader.
2. You will be directed to the registration page.

#### Step 2: Verify Password (Optional)

If the clinic has set a security password:

1. Enter the password provided by clinic staff.
2. Click **Verify** to proceed.

---

### Self‑Registration

Patients can create their profile through QR code access:

#### Create Patient Profile

1. After scanning QR code and verifying password (if required):
2. Enter your personal information:
   * Full name
   * Email address
   * Phone number
   * Date of birth
   * Gender
3. Enter your address:
   * Province
   * City
   * Barangay
   * Street address
4. Provide basic medical information:
   * Allergies
   * Medical history
   * Current medications
5. Click **Register** to complete registration.

#### After Registration

* Your profile is created in the clinic system
* Clinic staff can now schedule appointments for you
* Your information is secure and encrypted
* You can view your registration confirmation

---

### Important Notes for Patients

* **No Direct System Access** – Patients cannot log in directly; access is only through QR codes
* **Data Privacy** – All personal and medical information is secure and confidential
* **Appointment Scheduling** – Contact the clinic directly to schedule appointments
* **Billing & Records** – Clinic staff manages billing and access to medical records

---

## SECURITY & BEST PRACTICES

### Account Security

* **Protect Login Credentials** – Keep your email and password confidential
* **Use Strong Passwords** – Create unique passwords with numbers, letters, and special characters
* **Change Password Regularly** – Update your password at least quarterly
* **Verify OTP Requests** – Only respond to OTP requests you initiated
* **Multi-Device Awareness** – Be aware of active sessions on other devices
* **Logout After Use** – Always log out, especially on shared computers

### Data Privacy

* **Patient Confidentiality** – Never share patient information with unauthorized personnel
* **Secure Communication** – Use secure channels when discussing patient details
* **Access Control** – Only access data necessary for your role
* **Document Handling** – Securely dispose of printed patient records
* **Mobile Devices** – Use password-protected devices if accessing the system remotely

### Best Practices

* **Regular Backups** – System maintains automatic backups; verify critical data regularly
* **Activity Monitoring** – Administrators should review activity logs monthly
* **Audit Trail Review** – Check logs for any unauthorized access attempts
* **Session Management** – System logs you out after inactivity for security
* **Report Issues** – Report any security concerns or suspicious activity to administrators
* **Training** – Ensure all staff receive initial and refresher training on system usage

### Compliance & Auditing

* **Logging** – All user actions are recorded for auditing and compliance
* **Data Retention** – Patient records are retained per healthcare regulations
* **Access Rights** – Review user access rights quarterly and remove unnecessary permissions
* **Error Handling** – Report any system errors or data discrepancies to administrators
* **Change Documentation** – Document significant system changes for audit purposes

---

## TROUBLESHOOTING & SUPPORT

### Common Issues

#### Forgot Password

1. Click **Forgot Password** on login page.
2. Enter your email address.
3. An OTP will be sent to your email.
4. Use the OTP to reset your password.
5. Log in with your new password.

#### OTP Not Received

1. Check your email spam folder.
2. Click **Resend OTP** to request a new one.
3. Wait 2-3 minutes for the email to arrive.
4. Contact your administrator if problem persists.

#### Session Timeout

The system automatically logs you out after 30 minutes of inactivity for security. Simply log in again to continue.

#### Cannot Access System

* Verify you have an active account and correct role
* Check your internet connection
* Ensure you're using a supported desktop browser
* Mobile devices cannot access the system
* Contact your administrator if access is restricted

#### Data Not Saving

* Verify all required fields are completed
* Check for error messages in the form
* Try again after confirming your internet connection
* Contact administrator if problem persists

### Contact Support

For technical support or system issues:

* Contact your **Clinic Administrator**
* Reference the specific feature or action when reporting issues
* Provide details about any error messages received
* Include the date and time the issue occurred

---

## SYSTEM FEATURES SUMMARY

| Feature | Administrator | Staff | Patient |
|---------|---|---|---|
| Account Management | ✓ | ✗ | ✗ |
| Clinic Management | ✓ | ✗ | ✗ |
| Staff Management | ✓ | ✗ | ✗ |
| Associate Management | ✓ | ✗ | ✗ |
| Waitlist Management | ✓ | ✓ | ✗ |
| Patient Management | ✓ | ✓ | Self-registration only |
| Appointments | ✓ | ✓ | ✗ |
| Progress Notes | ✓ | ✓ | ✗ |
| Prescriptions | ✓ | ✓ | ✗ |
| Treatments | ✓ | ✓ | ✗ |
| Recalls | ✓ | ✓ | ✗ |
| Billing & Payments | ✓ | ✓ | ✗ |
| Reports | ✓ | ✗ | ✗ |
| Master Files | ✓ | ✗ | ✗ |
| Tools & Export | ✓ | ✗ | ✗ |
| Activity Logs | ✓ | ✗ | ✗ |

---

End of User Manual
