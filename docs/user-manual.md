# User Manual

## Document Tracker System

A comprehensive document management system for tracking documents through their lifecycle.

## Getting Started

### Login

1. Navigate to the application URL
2. Enter your email and password
3. Click **Login**

### First Time Setup

If this is your first login, you may need to:
- Complete your profile information
- Set up security questions
- Configure notification preferences

## Dashboard

The dashboard provides an overview of:
- **My Documents** - Documents you created
- **Pending Documents** - Documents awaiting your action
- **Statistics** - Document counts by status
- **Recent Activity** - Latest system actions

## Document Management

### Creating a Document

1. Navigate to **Documents** → **Create New**
2. Fill in required fields:
   - **Title** - Document name
   - **Document Type** - Category (Memorandum, Letter, Report, etc.)
   - **Processing Hours** - Expected processing time
   - **ARTA Setting** - Processing time category
   - **ARTA Category** - Simple, Complex, or Highly Technical
   - **Notes** - Additional information
3. Optionally mark as **Private** (restricted access)
4. Click **Generate Document**

The system automatically generates:
- **QR Code** - For quick scanning
- **Barcode** - For barcode scanners
- **Document Code** - Unique identifier

### Document Statuses

| Status | Description |
|--------|-------------|
| **Pending** | Document created, not yet received |
| **In Transit | Received by an office, being processed |
| **Finished** | Processing completed |
| **Terminated** | Document cancelled/ended |
| **Reopened** | Previously finished/terminated, now active |

### Document Actions

#### Receive Document
1. Go to **Documents** → **Receive**
2. Scan QR code or enter document code
3. Click **Receive**
4. Document status changes to **In Transit**

#### Finish Document
1. Go to **Documents** → **Finish**
2. Scan QR code or enter document code
3. Click **Finish**
4. Document status changes to **Finished**

#### Terminate Document
1. Go to **Documents** → **Terminate**
2. Scan QR code or enter document code
3. Enter termination reason
4. Click **Terminate**
5. Document status changes to **Terminated**

#### Reopen Document
1. View a **Finished** or **Terminated** document
2. Click **Reopen**
3. Document returns to **Pending** status

### My Documents

View documents you created:
- Filter by status
- Search by title/code
- Export to PDF

### My Scanned Documents

View documents you received:
- Track processing history
- View current holder

## Document Tracking

### View Document Details

Click on any document to see:
- **Basic Info** - Title, type, creator, dates
- **QR Code & Barcode** - For scanning
- **Processing Timeline** - Complete history
- **Current Holder** - Who has it now
- **Past Holders** - Previous offices/users
- **ARTA Info** - Processing time limits

### Document History

Each document tracks:
- **Created** - When and by whom
- **Received** - Each office receipt
- **Released** - Each office release
- **Finished** - Completion timestamp
- **Terminated** - If applicable, with reason
- **Reopened** - If applicable

### Printing Documents

1. Open document details
2. Click **Print**
3. Generates printable version with QR/barcode

## User Management (Admin)

### Create User

1. Go to **System** → **Users** → **Create**
2. Fill in:
   - **First Name, Last Name**
   - **Email** (unique)
   - **ID Number** (unique)
   - **Password** (min 12 chars, complex)
   - **Department** and **Office**
   - **Role** (permissions)
3. Click **Save**

### User Actions

| Action | Description |
|--------|-------------|
| **View** | See user details |
| **Edit** | Update profile |
| **Change Password** | Reset user password |
| **Ban** | Disable account |
| **Unban** | Re-enable account |
| **Lock** | Temporary lock |
| **Unlock** | Remove lock |
| **Force Logout** | End all sessions |
| **Delete** | Permanent removal |

### Bulk Actions

Select multiple users → Choose action:
- Ban
- Unban
- Lock
- Unlock
- Delete

## Role & Permission Management (Admin)

### Roles

Predefined roles:
- **Administrator** - Full system access
- **Staff** - Document processing
- **Viewer** - Read-only access

### Permissions

Granular permissions per module:
- `documents.list`, `documents.create`, `documents.view`, `documents.edit`, `documents.delete`
- `users.list`, `users.create`, `users.view`, `users.edit`, `users.delete`
- `roles.list`, `roles.create`, `roles.edit`, `roles.delete`
- `permissions.manage`
- `settings.access`
- `arta.list`, `arta.create`, `arta.edit`, `arta.delete`

### Managing Permissions

1. Go to **System** → **Permissions**
2. Select a role
3. Toggle permissions on/off
4. Changes apply immediately

## Department & Office Management (Admin)

### Departments

1. Go to **System** → **Departments**
2. **Create** - Add new department
3. **Edit** - Update name/description
4. **Toggle Status** - Active/Inactive
5. **Delete** - Remove (if no users)

### Offices

1. Go to **System** → **Offices**
2. **Create** - Add office with department
3. **Edit** - Update details
4. **Toggle Status** - Active/Inactive
4. **Delete** - Remove (if no users)

## Document Types & ARTA Settings (Admin)

### Document Types

1. Go to **System** → **Document Types**
2. **Create** - Add type (e.g., Memorandum, Letter)
3. **Edit** - Update name/description
4. **Toggle Status** - Active/Inactive
5. **Delete** - Remove

### ARTA Settings

Configure processing times per ARTA category:

| Category | Default Days | Description |
|----------|--------------|-------------|
| Simple | 3 | Straightforward transactions |
| Complex | 7 | Multi-step processes |
| Highly Technical | 20 | Specialized expertise needed |

1. Go to **System** → **ARTA Settings**
2. **Create** - Add new setting
3. **Edit** - Modify days/description
4. **Toggle Status** - Active/Inactive

## System Settings (Admin)

Access via **System** → **Settings**

### General
- Site name, short name
- Timezone, date format
- Items per page

### Appearance
- Logo, favicon
- Color theme
- Document header/logo

### Email
- SMTP configuration
- From address/name
- Test email

### Security
- Password policies
- Session settings
- Rate limiting

## Profile & Settings (All Users)

### Profile

Access via **Profile** in sidebar:
- Update personal info
- Change password
- Upload profile picture
- Set department/office

### Notifications

Configure notification preferences:
- Email on document received
- Email on document finished
- In-app notifications

## Messaging

### Internal Messages

1. Go to **Messages**
2. **Compose** - Send to any user
3. **Inbox** - Received messages
4. **Sent** - Sent messages
5. **Read/Unread** - Mark status

## Activity & Security Logs (Admin)

### Activity Logs

Go to **System** → **Activity Logs** to view:
- User actions (create, edit, delete)
- Document events
- Login/logout
- Filter by user, date, action

### Security Logs

Go to **System** → **Security Logs** to view:
- Login attempts (success/failed)
- Password changes
- Brute force detection
- Suspicious activity
- Session events
- Filter by severity, user, date

## Statistics (Admin)

Go to **System** → **Statistics** for:
- Document counts by status
- Processing time averages
- Department performance
- User activity
- Date range filters

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + N` | New document |
| `Ctrl + F` | Search documents |
| `Esc` | Close modal |
| `Tab` | Next field |
| `Shift + Tab` | Previous field |

## Troubleshooting

### Can't Login

1. Check email/password
2. Try **Forgot Password**
3. Contact admin if account locked/banned

### Document Not Found

1. Verify document code/QR
2. Check correct scanner page (Receive/Finish/Terminate)
3. Contact admin if document deleted

### Permission Denied

1. Check your role permissions
2. Contact admin for access request

### Slow Performance

1. Clear browser cache
2. Check internet connection
3. Contact admin if persistent

## FAQ

**Q: How do I scan a document QR code?**
A: Use the camera on Receive/Finish/Terminate pages or enter code manually.

**Q: Can I edit a finished document?**
A: No, finished documents are read-only. Reopen first if changes needed.

**Q: What happens when I terminate a document?**
A: Document status becomes "Terminated", cannot be processed further unless reopened.

**Q: How are ARTA days calculated?**
A: Based on document's ARTA category and assigned ARTA setting.

**Q: Can I export document data?**
A: Yes, use the export button on document lists (PDF/CSV).

**Q: Who can see private documents?**
A: Only creator and users with `documents.view` permission.

## Support

For technical issues:
1. Check this manual
2. Contact system administrator
3. Report bugs via issue tracker

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-07-14 | Initial release |