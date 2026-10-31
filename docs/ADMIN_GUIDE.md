# Admin Guide

## Signing in

Staff sign in at **`/admin/login`**. In production this is `/<your ADMIN_PATH>/login`. Keep that address private.

The normal `/login` page is for students and other users. It will not let staff accounts in: staff are told to use the staff portal, and the page doesn't reveal its address.

## Creating the first administrator

```
php artisan edudev:create-admin owner@yourdomain.com --name="Owner Name"
```

You'll be asked for a password (at least 12 characters, with mixed case, numbers and symbols). Running the command for an existing email promotes that account instead. Use `--role=platform_admin` for a less-privileged administrator.

## Roles

| Role | Can do (Phase 1) |
|---|---|
| Super Admin | Everything, including managing roles and AI settings |
| Platform Admin | Everything except managing roles and AI settings |
| Content Manager | Admin overview; curriculum and questions (when built) |
| Support Agent | Admin overview; support tickets (when built) |
| Student, Guardian, Teacher, Tutor, School Admin, CBT Centre Admin | No admin access |

## Two-step login

It's on in production. Staff must set it up on first sign-in: scan the QR code with an authenticator app, enter the 6-digit code, and **save the 8 recovery codes**.

If someone loses their phone and their recovery codes, open their account in Admin → Users and choose **Reset two-step login**. Confirm who they are first, for example by phone.

## Managing users (Phase 2)

- **Users:** search by name or email and filter by role or status. Open a user to see their account type, sign-in history and audit trail. Their private learning content is not shown.
- **Suspend:** a reason is required. The person is signed out immediately and can't sign in until reactivated.
- **Roles:** only super admins can change roles. Nobody can change their own roles or suspend themselves. Only a super admin can act on another super admin. The last active super admin can't be removed.
- **Deletion requests:** users ask for deletion in Settings. Before choosing "Delete account", check for records that must be kept (payments, institutional exam results). Rejecting needs a note. Staff accounts must have their staff roles removed first.

## Pages

- **Overview:** live account counts and recent sensitive activity.
- **Branding:** name, slogan, colours, contacts, links. Changes are audited.
- **Audit log:** a searchable list of sensitive actions. Entries can't be edited or deleted.
