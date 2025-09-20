<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TODO: add this table and reference it on other table for visit

        // Independent Tables
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('account_id')->primary();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->text('email')->unique();
            $table->string('email_hash')->unique()->index();
            $table->text('last_name');
            $table->text('last_name_hash')->index();
            $table->text('middle_name')->nullable();
            $table->text('first_name');
            $table->text('mobile_no')->nullable();
            $table->text('contact_no')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'staff'])->default('staff')->index();
            $table->boolean('can_act_as_staff')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->string('otp_hash', 255)->nullable();
            $table->timestamp('otp_expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Dependent Tables
        Schema::create('clinics', function (Blueprint $table) {
            $table->uuid('clinic_id')->primary();
            $table->uuid('account_id')->nullable();
            $table->text('name');
            $table->text('name_hash')->index();
            $table->text('description')->nullable();
            $table->text('schedule_summary')->nullable();
            $table->text('speciality')->nullable();
            $table->text('mobile_no')->nullable();
            $table->text('contact_no')->nullable();
            $table->text('email')->nullable();
            $table->string('email_hash')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });

        Schema::create('clinic_schedules', function (Blueprint $table) {
            $table->uuid('clinic_schedule_id')->primary();
            $table->uuid('clinic_id')->nullable();
            $table->text('day_of_week');
            $table->text('start_time');
            $table->text('end_time');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('clinic_id')
                ->references('clinic_id')->on('clinics')
                ->onDelete('set null');
        });

        Schema::create('laboratories', function (Blueprint $table) {
            $table->uuid('laboratory_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that owns/added the laboratory
            $table->text('name');
            $table->text('name_hash')->index();
            $table->text('description')->nullable();
            $table->text('speciality')->nullable();
            $table->text('contact_person');
            $table->text('mobile_no')->nullable();
            $table->text('contact_no')->nullable();
            $table->text('email')->nullable();
            $table->string('email_hash')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });

        Schema::create('associates', function (Blueprint $table) {
            $table->uuid('associate_id')->primary();
            $table->uuid('account_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->text('first_name');
            $table->text('middle_name')->nullable();
            $table->text('last_name');
            $table->text('last_name_hash')->index();
            $table->text('speciality')->nullable();
            $table->text('mobile_no')->nullable();
            $table->text('contact_no')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('email')->unique();
            $table->string('email_hash')->unique()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
        });

        Schema::create('patient_qr_codes', function (Blueprint $table) {
            $table->uuid('qr_id')->primary();
            $table->string('qr_code'); // could store string or path to QR image
            $table->string('qr_password')->nullable(); // optional password for QR code access
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('patient_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the patient record
            $table->uuid('qr_id')->nullable(); // Nullable if patient is not linked to a QR code
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('sex');
            $table->text('mobile_no')->nullable();
            $table->text('contact_no')->nullable();
            $table->date('date_of_birth');
            $table->string('email')->nullable();
            $table->boolean('is_validated')->default(false); // Indicates if the patient has been validated in case of qr code input
            $table->index('account_id');    // join with accounts
            $table->index('last_name');     // search by name
            $table->index('contact_no');    // search by phone
            $table->index('email');         // search by email

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('qr_id')->references('qr_id')->on('patient_qr_codes')->onDelete('set null');
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('appointment_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the appointment
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->dateTime('appointment_date');
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->index('patient_id');     // join
            $table->index('associate_id');   // join
            $table->index('clinic_id');      // join
            $table->index('laboratory_id');  // join
            $table->index('appointment_date'); // upcoming appointments
            $table->index('status');         // scheduled/completed/cancelled

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
        });

        Schema::create('waitlist', function (Blueprint $table) {
            $table->uuid('waitlist_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the waitlist entry
            $table->uuid('patient_id')->nullable(); // Patient on the waitlist
            $table->uuid('associate_id')->nullable(); // Associate handling the waitlist
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->dateTime('requested_at');
            $table->integer('queue_position')->nullable(); // Position in the waitlist
            $table->string('status')->default('waiting'); // waiting, finished, removed
            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');
            $table->index('requested_at');   // queue by request date
            $table->index('status');         // waiting/finished/removed

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
        });

        // new table for machine learning

        Schema::create('services', function (Blueprint $table) {
            $table->uuid('service_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the service
            // clinic and laboratory added for where the treatment is performed and available
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->string('service_type'); // e.g., cleaning, extraction, filling
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->index('account_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');
            $table->index('name');           // search by service

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->uuid('medicine_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the medicine
            $table->text('name');
            $table->text('description')->nullable();
            $table->text('name_hash')->index();           // search by medicine

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
        });

        Schema::create('medicine_clinics', function (Blueprint $table) {
            $table->uuid('medicine_clinic_id')->primary();
            $table->uuid('medicine_id');
            $table->uuid('clinic_id');
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('medicine_id')->references('medicine_id')->on('medicines')->onDelete('cascade');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('cascade');

            $table->unique(['medicine_id', 'clinic_id']); // prevent duplicate entries
        });

        Schema::create('tooth_list', function (Blueprint $table) {
            $table->uuid('tooth_list_id')->primary();
            $table->text('number')->unique(); // e.g., 11, 12, 13
            $table->text('name');             // e.g., upper right central incisor
            $table->text('name_hash')->index();
            $table->decimal('price', 10, 2)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teeth', function (Blueprint $table) {
            $table->uuid('tooth_id')->primary();
            $table->uuid('account_id')->nullable();
            $table->uuid('patient_id'); // should usually NOT be nullable
            $table->uuid('tooth_list_id'); // link to reference tooth slot
            $table->text('condition')->nullable(); // e.g., healthy, decayed, missing

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->foreign('tooth_list_id')->references('tooth_list_id')->on('tooth_list')->onDelete('cascade');

            $table->unique(['patient_id', 'tooth_list_id']); // enforce one slot per patient
        });

        Schema::create('logs', function (Blueprint $table) {
            $table->uuid('log_id')->primary();
            $table->string('account_id')->nullable();
            $table->text('account_name_snapshot')->nullable(); // store name at time of action for audit trail
            $table->uuid('patient_id')->nullable(); // Nullable if log is not patient-specific
            $table->uuid('associate_id')->nullable(); // Nullable if log is not associate-specific
            $table->uuid('clinic_id')->nullable(); // Nullable if log is not clinic-specific
            $table->uuid('laboratory_id')->nullable(); // Nullable if log is not laboratory
            $table->string('log_type'); // e.g., 'appointment', 'bill', 'note'
            $table->string('action'); // e.g., 'created', 'updated', 'deleted'
            $table->text('description')->nullable();
            $table->text('private_description')->nullable(); // shown only for audit trail
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 512)->nullable();

            $table->index('account_id');
            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');
            $table->index('log_type');
            $table->index('action');
            $table->index('created_at');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('address_id')->primary();
            $table->uuid('account_id')->nullable(); // Nullable if address is not linked to an account
            $table->uuid('qr_id')->nullable(); // Nullable if address is not linked to a QR code
            $table->boolean('is_staff')->default(false); // based on account id true if address is for staff, false if for just admin
            $table->uuid('associate_id')->nullable();
            $table->uuid('patient_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            // Encrypt
            $table->text('house_no')->nullable();
            $table->text('street')->nullable();
            $table->text('barangay_name')->nullable();
            $table->text('city_name')->nullable();
            $table->text('province_name')->nullable();

            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();

            // Indexes
            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('qr_id')->references('qr_id')->on('patient_qr_codes')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
        });

        // Table: patient_visits
        Schema::create('patient_visits', function (Blueprint $table) {
            $table->uuid('visit_id')->primary();
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->dateTime('visit_date')->nullable();
            $table->text('visit_summary')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');

            // Optional indexes
            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('visit_date');
        });

        Schema::create('bills', function (Blueprint $table) {
            $table->uuid('bill_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the bill
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);

            $table->string('status')->default('unpaid'); // unpaid, paid, partially_paid, cancelled
            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');
            $table->index('status');         // unpaid/paid/partially_paid
            $table->index('created_at');     // reporting by date

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('progress_notes', function (Blueprint $table) {
            $table->uuid('progress_note_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the progress note
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who added the note
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->text('progress_note');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->uuid('bill_item_id')->primary();
            $table->uuid('bill_id')->nullable();
            $table->uuid('account_id')->nullable(); // Account that created the bill item
            $table->string('item_type'); // service, medicine, other
            $table->uuid('medicine_id')->nullable(); // Nullable if item_type is service
            $table->uuid('service_id')->nullable(); // Nullable if item_type is medicine
            $table->uuid('tooth_id')->nullable(); // Nullable if item_type is not tooth-related
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bill_id')->references('bill_id')->on('bills')->onDelete('set null');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('medicine_id')->references('medicine_id')->on('medicines')->onDelete('set null');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
            $table->foreign('tooth_id')->references('tooth_id')->on('teeth')->onDelete('set null');
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('payment_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the payment
            $table->uuid('bill_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // cash, credit_card, online
            $table->dateTime('paid_at');
            $table->index('bill_id');        // join
            $table->index('account_id');     // who processed
            $table->index('paid_at');        // reporting/filtering

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('bill_id')->references('bill_id')->on('bills')->onDelete('set null');
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('prescription_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the prescription
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who prescribed
            $table->uuid('clinic_id')->nullable(); // Clinic where the prescription is issued
            $table->uuid('laboratory_id')->nullable(); // Laboratory if applicable
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->string('prescription_type'); // e.g., medicine, service
            $table->uuid('medicine_id')->nullable(); // Nullable if prescription_type is service
            $table->uuid('service_id')->nullable(); // Nullable if prescription_type is medicine
            $table->uuid('tooth_id')->nullable(); // Nullable if prescription is not tooth
            $table->text('prescription_details'); // JSON or text format for details
            $table->dateTime('prescribed_at');

            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('laboratory_id');
            $table->index('prescribed_at');  // filter by date

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('medicine_id')->references('medicine_id')->on('medicines')->onDelete('set null');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
            $table->foreign('tooth_id')->references('tooth_id')->on('teeth')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('diagnostic_requests', function (Blueprint $table) {
            $table->uuid('request_id')->primary();
            $table->uuid('account_id')->nullable(); // staff who sent the request
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who asked the request
            $table->uuid('laboratory_id')->nullable(); // target laboratory
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->string('test_type'); // e.g., X-Ray, Blood Test, etc.
            $table->text('request_details')->nullable();
            $table->enum('status', ['pending', 'in-progress', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('requested_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('result_file_path')->nullable(); // store diagnostics result file or external link

            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('laboratory_id');
            $table->index('requested_at');
            $table->index('status');         // pending/in-progress/etc

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('certificate_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the certificate
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who issued the certificate
            $table->uuid('clinic_id')->nullable(); // Clinic where the certificate is issued
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->string('certificate_type'); // e.g., Medical Certificate, Dental Certificate
            $table->text('certificate_details'); // JSON or text format for details
            $table->dateTime('issued_at');
            $table->string('file_path')->nullable(); // store certificate file or external link

            $table->index('patient_id');
            $table->index('associate_id');
            $table->index('clinic_id');
            $table->index('issued_at');      // filtering by date

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('general_notes', function (Blueprint $table) {
            $table->uuid('note_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the note
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who added the note
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->text('note_content');
            $table->index('patient_id')->nullable();
            $table->index('associate_id')->nullable();
            $table->index('created_at');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        Schema::create('recalls', function (Blueprint $table) {
            $table->uuid('recall_id')->primary();
            $table->uuid('account_id')->nullable(); // Account that created the recall
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable(); // Associate who set the recall
            $table->uuid('visit_id')->nullable(); // Nullable if historically logged with no visit
            $table->dateTime('recall_date');
            $table->text('recall_reason')->nullable();
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->index('patient_id')->nullable();
            $table->index('associate_id')->nullable();
            $table->index('recall_date');    // upcoming recalls
            $table->index('status');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
        });

        // Table: patient_treatments
        Schema::create('patient_treatments', function (Blueprint $table) {
            $table->uuid('patient_treatment_id')->primary();
            $table->uuid('visit_id')->nullable();   // nullable if historically logged with no visit
            $table->uuid('patient_id')->nullable();
            $table->uuid('associate_id')->nullable();
            $table->uuid('service_id')->nullable();
            $table->uuid('clinic_id')->nullable();
            $table->uuid('laboratory_id')->nullable();
            $table->uuid('tooth_id')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->dateTime('treatment_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('visit_id')->references('visit_id')->on('patient_visits')->onDelete('set null');
            $table->foreign('patient_id')->references('patient_id')->on('patients')->onDelete('set null');
            $table->foreign('associate_id')->references('associate_id')->on('associates')->onDelete('set null');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
            $table->foreign('clinic_id')->references('clinic_id')->on('clinics')->onDelete('set null');
            $table->foreign('laboratory_id')->references('laboratory_id')->on('laboratories')->onDelete('set null');
            $table->foreign('tooth_id')->references('tooth_id')->on('teeth')->onDelete('set null');

            // Optional indexes
            $table->index('visit_id');
            $table->index('patient_id');
            $table->index('service_id');
            $table->index('associate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_treatments');
        Schema::dropIfExists('recalls');
        Schema::dropIfExists('general_notes');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('diagnostic_requests');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('progress_notes');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('patient_visits');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('logs');
        Schema::dropIfExists('tooth_list');
        Schema::dropIfExists('teeth');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('medicine_clinics');
        Schema::dropIfExists('services');
        Schema::dropIfExists('waitlist');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('patient_qr_codes');
        Schema::dropIfExists('associates');
        Schema::dropIfExists('laboratories');
        Schema::dropIfExists('clinic_schedules');
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('accounts');
    }
};
