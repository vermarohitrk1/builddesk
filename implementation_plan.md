# Multi-Tenant SaaS Architecture Implementation Plan

This document outlines the architecture for a multi-tenant SaaS application designed for business management.

## 1. Core Architecture Principles
- **Multi-tenancy**: Single database with `organisation_id` isolation.
- **Security**: Automatic filtering via Global Scopes.
- **Scalability**: Service Layer and Repository Pattern for future API and Mobile support.
- **Auditability**: Track all major changes (Created By, Updated By, Deleted By).

## 2. Database Schema Design

### Core Tables
- **`organisations`**:
    - `id`, `name`, `logo`, `gst_number`, `address`, `phone`, `email`, `website`, `status` (active/suspended)
    - `terms_and_conditions`, `quotation_footer`, `pdf_branding` (JSON)
    - `subscription_plan_id`, `subscription_start_date`, `subscription_end_date`
- **`subscription_plans`**:
    - `id`, `name` (Starter, Professional, Enterprise), `slug`, `price`, `features_json`
- **`users`**:
    - `id`, `organisation_id` (nullable for Super Admin), `name`, `email`, `password`, `role` (super_admin, org_admin, employee)
- **`audit_logs`**:
    - `id`, `organisation_id`, `user_id`, `event` (created/updated/deleted), `auditable_type`, `auditable_id`, `old_values`, `new_values`

### Business Tables (All with `organisation_id` and `created_by/updated_by`)
- `customers`
- `leads` (source: web, facebook, google, whatsapp, manual)
- `followups`
- `quotations`
- `projects`
- `employees` (Additional details for users)
- `attendance`
- `leave_requests`
- `measurements`
- `installations`

## 3. Implementation Modules

### Module A: Multi-Tenancy Engine
- **`HasTenant` Trait**: Automatically adds `organisation_id` to queries and sets it on save.
- **`TenantMiddleware`**: Identifies the current organisation context from the authenticated user.
- **`TenantScope`**: Global Laravel scope to ensure `organisation_id` is always applied.

### Module B: Access Control & Hierarchy
- **Super Admin**: Access to all organisations, management of plans and roles.
- **Organisation Admin**: Access only to their own organization.
- **Employee**: Record-level permissions (e.g., assigned leads only).

### Module C: Logic & Data Layer
- **Repositories**: Standardized data access (e.g., `CustomerRepositoryInterface`).
- **Services**: Business logic (e.g., `QuotationService`, `LeadService`).
- **API Resources**: Proper JSON transformation for future mobile apps.

### Module D: Branding & Notifications
- **Branding Engine**: Helper to retrieve organization-specific logos and settings for PDFs.
- **Notification Manager**: Abstraction layer for Email, SMS, WhatsApp, and In-App notifications.

## 4. Progress Update
- [x] Initial core migrations created (Organisations, Users, Plans, Audit).
- [x] Multi-tenancy Engine implemented (`HasTenant`, `TenantScope`).
- [x] Audit Logging trait implemented (`HasAudit`).
- [x] Base Service and Repository patterns established.
- [x] Initial Business tables created (Leads, Customers, Quotations, Projects, etc.).
- [x] Future-proof services for Notifications and Lead Imports added.

## 5. Next Steps for Development
1. Run migrations to set up the database.
2. Create Seeders for Subscription Plans and a Super Admin user.
3. Implement the Super Admin dashboard for Organisation management.
4. Develop the lead management module using the new architecture.

