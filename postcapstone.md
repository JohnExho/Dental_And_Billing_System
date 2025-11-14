Post-Capstone To-Do List

1. Add Admin Registration Page
   * Make it accessible only through a one-time PIN (create a dedicated table).
   * After registration, require completion of the Organization Registration.

2. Add Organization Registration Page
   * Can only be submitted once per system instance.
   * After registration, redirect to the Admin Dashboard.
   * Ensure the admin account stores an `organization_id`, so any created clinics or related data are properly scoped and not visible to other organizations.

3. Reintegrate Laboratory/Diagnostic/Certificates Logic.

4. Refine Backend.

5. Refine Frontend.

6. Attempt Hosting.

7. Start Monetizing.

8. Implement Role & Permission System
   * Define roles: Super Admin, Organization Admin, Staff, Dentist, Reception, etc.
   * Use Laravel Permissions (spatie/laravel-permission recommended).
   * Ensures multi-org isolation and security.

9. Add Subscription/Billing Module (optional but useful for monetizing)
   * Monthly/annual plans.
   * Feature tiers (e.g., max clinics, storage limits).
   * Stripe integration if you want automated payments.

10. Add Audit Logs / Activity Logs ✓
    * Track actions: record edits, deletions, logins.
    * Required for compliance and real-world accountability.

11. Add Email Setup & Templates
    * Email verification
    * Password reset ✓
    * OTP delivery ✓
    * Appointment reminders (if clinic needs it)

12. Storage & File System Cleanup
    * Implement per-organization storage paths.
    * Handle upload cleanup (X-ray images, patient files, receipts, etc.)
    * Optional: cloud storage support.

13. Add Multi-Tenancy Middleware
    * Ensure every request is scoped by `organization_id`.
    * Prevent cross-organization data leaks.
    * You can choose:
      * Single DB, organization_id scoping (simpler)
      * Multi-DB (more secure, more complex)

14. Add Hashed Fields for Encrypted Data
    * Since all sensitive fields are encrypted, direct searching is not possible.
    * Create additional hashed versions of fields to allow sorting without decrypting all data.

15. Backup & Restore Functionality
    * Scheduled database backups.
    * Export / import patient and clinic data (CSV/Excel).

16. Environment Configuration for Hosting
    * Handle `.env` automation for setup.
    * Queue workers (Redis preferred).
    * Mail config for production.

17. Improve Security (HIPAA-focused)
    * Rate limiting (login, OTP, API)
    * CSRF & CORS checks
    * Strong password policies & multi-factor authentication
    * Field-level encryption for PHI (Protected Health Information)
    * Access logging for all PHI operations
    * Ensure proper backup encryption and retention policies
    * Validation hardening and input sanitization

18. Add Testing
    * Feature tests for key routes
    * Unit tests for logic
    * API tests if you're exposing endpoints

19. Create Setup Wizard (First-Time Installation)
    * Admin Registration
    * Organization Setup
    * Clinic Setup
    * Email configuration test

20. Documentation
    * Admin guide
    * Installation guide
    * API docs (if offering API access)
    * Changelog/versioning

21. Versioning & Release Cycle
    * Add Git tags for releases.
    * Create a “stable” branch for production hosting.
