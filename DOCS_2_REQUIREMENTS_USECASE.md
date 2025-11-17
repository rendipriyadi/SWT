# SAFETY WALK AND TALK (SWT) APPLICATION
## PART 2: REQUIREMENTS & USE CASE

---

## 3. REQUIREMENTS ANALYSIS

### 3.1 Functional Requirements

#### Table 3.1: Functional Requirements Specification

| ID | Category | Requirement Description | Priority | Status |
|---|---|---|---|---|
| **FR-01** | **Report Management** | | | |
| FR-01.1 | Report Management | System shall allow users to create new safety/5S reports | High | ✓ Implemented |
| FR-01.2 | Report Management | System shall support uploading multiple photos (up to 5) per report | High | ✓ Implemented |
| FR-01.3 | Report Management | System shall automatically assign reports to designated PIC based on area | High | ✓ Implemented |
| FR-01.4 | Report Management | System shall allow editing of report details (area, PIC, category, description, deadline) | High | ✓ Implemented |
| FR-01.5 | Report Management | System shall allow deletion of reports with proper validation | Medium | ✓ Implemented |
| FR-01.6 | Report Management | System shall allow viewing detailed report information | High | ✓ Implemented |
| FR-01.7 | Report Management | System shall allow PIC to complete reports with resolution details and photos | High | ✓ Implemented |
| **FR-02** | **Dashboard & Monitoring** | | | |
| FR-02.1 | Dashboard & Monitoring | System shall display real-time statistics (total, in progress, completed reports) | High | ✓ Implemented |
| FR-02.2 | Dashboard & Monitoring | System shall display monthly report trends in chart format | Medium | ✓ Implemented |
| FR-02.3 | Dashboard & Monitoring | System shall display area distribution charts | Medium | ✓ Implemented |
| FR-02.4 | Dashboard & Monitoring | System shall display problem category distribution charts | Medium | ✓ Implemented |
| FR-02.5 | Dashboard & Monitoring | System shall provide filtering capabilities by date, area, PIC, and category | High | ✓ Implemented |
| FR-02.6 | Dashboard & Monitoring | System shall support searching and sorting in data tables | Medium | ✓ Implemented |
| **FR-03** | **Email Notification System** | | | |
| FR-03.1 | Email Notification | System shall send email notification when report is assigned to PIC | High | ✓ Implemented |
| FR-03.2 | Email Notification | System shall send email notification when report is edited | Medium | ✓ Implemented |
| FR-03.3 | Email Notification | System shall send email notification with PDF attachment when report is completed | High | ✓ Implemented |
| FR-03.4 | Email Notification | System shall send reminder email 2 working days before deadline (H-2) | High | ✓ Implemented |
| FR-03.5 | Email Notification | System shall send daily reminder email for overdue reports | High | ✓ Implemented |
| **FR-04** | **History & Archive** | | | |
| FR-04.1 | History & Archive | System shall maintain history of all completed reports | High | ✓ Implemented |
| FR-04.2 | History & Archive | System shall allow filtering of historical data | Medium | ✓ Implemented |
| FR-04.3 | History & Archive | System shall support exporting completed reports to PDF format | Medium | ✓ Implemented |
| **FR-05** | **Master Data Management** | | | |
| FR-05.1 | Master Data | System shall allow CRUD operations for production areas | High | ✓ Implemented |
| FR-05.2 | Master Data | System shall allow CRUD operations for departments and supervisors | High | ✓ Implemented |
| FR-05.3 | Master Data | System shall allow CRUD operations for problem categories | High | ✓ Implemented |
| FR-05.4 | Master Data | System shall allow CRUD operations for PIC (Person In Charge) | High | ✓ Implemented |
| FR-05.5 | Master Data | System shall validate data integrity before deletion | Medium | ✓ Implemented |

**Priority Levels:**
- **High**: Critical functionality, must be implemented
- **Medium**: Important functionality, should be implemented
- **Low**: Nice to have, can be implemented later

### 3.2 Non-Functional Requirements

#### Table 3.2: Non-Functional Requirements Specification

| ID | Category | Requirement Description | Priority | Status |
|---|---|---|---|---|
| **NFR-01** | **Performance** | | | |
| NFR-01.1 | Performance | System shall load dashboard within 3 seconds | High | ✓ Implemented |
| NFR-01.2 | Performance | System shall support concurrent users without performance degradation | High | ✓ Implemented |
| NFR-01.3 | Performance | DataTables shall load efficiently using server-side processing | High | ✓ Implemented |
| **NFR-02** | **Security** | | | |
| NFR-02.1 | Security | All IDs in URLs shall be encrypted | High | ✓ Implemented |
| NFR-02.2 | Security | All forms shall implement CSRF protection | High | ✓ Implemented |
| NFR-02.3 | Security | All user inputs shall be validated and sanitized | High | ✓ Implemented |
| NFR-02.4 | Security | File uploads shall be validated for type and size | High | ✓ Implemented |
| NFR-02.5 | Security | System shall prevent XSS attacks | High | ✓ Implemented |
| **NFR-03** | **Usability** | | | |
| NFR-03.1 | Usability | Interface shall be responsive and mobile-friendly | High | ✓ Implemented |
| NFR-03.2 | Usability | System shall provide clear error messages | Medium | ✓ Implemented |
| NFR-03.3 | Usability | System shall provide confirmation dialogs for destructive actions | Medium | ✓ Implemented |
| NFR-03.4 | Usability | Navigation shall be intuitive and consistent | Medium | ✓ Implemented |
| **NFR-04** | **Reliability** | | | |
| NFR-04.1 | Reliability | System shall maintain 99% uptime | High | ✓ Implemented |
| NFR-04.2 | Reliability | Email notifications shall be queued and retry on failure | Medium | ✓ Implemented |
| NFR-04.3 | Reliability | Database transactions shall ensure data integrity | High | ✓ Implemented |
| NFR-04.4 | Reliability | System shall maintain audit logs for critical operations | Low | ⚠ Partial |

**Priority Levels:**
- **High**: Critical requirement, must be met
- **Medium**: Important requirement, should be met
- **Low**: Desirable requirement, can be implemented later

**Status Legend:**
- **✓ Implemented**: Fully implemented and tested
- **⚠ Partial**: Partially implemented
- **✗ Not Implemented**: Not yet implemented

---

## 4. USE CASE DIAGRAM & DESCRIPTION

### 4.1 Use Case Diagram

**Note**: Current implementation has no authentication system. All features are publicly accessible.

```
                        Safety Walk and Talk System
┌───────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│                                                                             │
│  User ──────────┬──────► (View Dashboard)                                  │
│                 │              │                                           │
│                 │              │ <<include>>                               │
│                 │              ▼                                           │
│                 │        (View Statistics &                                │
│                 │         Charts)                                          │
│                 │                                                          │
│                 ├──────► (Create Report)                                   │
│                 │              │                                           │
│                 │              │ <<include>>                               │
│                 │              ▼                                           │
│                 │        (Upload Photos)                                   │
│                 │                                                          │
│                 ├──────► (View Report List)                                │
│                 │              │                                           │
│                 │              │ <<include>>                               │
│                 │              ▼                                           │
│                 │        (Filter & Search)                                 │
│                 │                                                          │
│                 ├──────► (View Report Detail)                              │
│                 │                                                          │
│                 ├──────► (Edit Report)                                     │
│                 │                                                          │
│                 ├──────► (Complete Report)                                 │
│                 │              │                                           │
│                 │              │ <<include>>                               │
│                 │              ▼                                           │
│                 │        (Upload Completion                                │
│                 │         Photos)                                          │
│                 │                                                          │
│                 ├──────► (View History)                                    │
│                 │              │                                           │
│                 │              │ <<extend>>                                │
│                 │              ▼                                           │
│                 │        (Export PDF)                                      │
│                 │                                                          │
│                 └──────► (Delete Report)                                   │
│                                                                             │
│                                                                             │
│  Admin ─────────┬──────► (Manage Areas)                                    │
│                 │              │                                           │
│                 │              │ <<include>>                               │
│                 │              ▼                                           │
│                 │        (Manage PICs)                                     │
│                 │                                                          │
│                 ├──────► (Manage Departments)                              │
│                 │                                                          │
│                 └──────► (Manage Problem                                   │
│                          Categories)                                       │
│                                                                             │
│                                                                             │
│  System ────────┬──────► (Send Email                                       │
│  (Automated)    │         Notifications)                                   │
│                 │              │                                           │
│                 │              ├──► Assignment Email                       │
│                 │              ├──► Edit Email                             │
│                 │              └──► Completion Email                       │
│                 │                                                          │
│                 └──────► (Send Reminders)                                  │
│                                │                                           │
│                                ├──► H-2 Deadline Reminder                  │
│                                └──► Overdue Reminder                       │
│                                                                             │
└───────────────────────────────────────────────────────────────────────────┘
```

**Figure 4.1: Use Case Diagram - Safety Walk and Talk System**

### 4.2 Actor Description

| Actor | Description | Responsibilities |
|-------|-------------|------------------|
| **User** | Any employee who uses the system (no authentication required) | - View dashboard and statistics<br>- Create, edit, and complete reports<br>- Upload photos<br>- View history and export PDF<br>- Delete reports |
| **Admin** | Administrator who manages master data | - Manage production areas<br>- Manage PICs (Person In Charge)<br>- Manage departments and supervisors<br>- Manage problem categories |
| **System** | Automated system processes | - Send email notifications (assignment, edit, completion)<br>- Execute scheduled tasks (cron jobs)<br>- Generate reminders (H-2 deadline, overdue) |

### 4.3 Use Case Descriptions

#### UC-01: Create Report

**Actor**: User  
**Precondition**: User accesses the application  
**Postcondition**: New report is created and PIC is notified via email

**Main Flow**:
1. User navigates to "Create Report" page
2. System displays report creation form
3. User selects production area
4. System loads available PICs for selected area
5. User selects PIC (or leaves blank for all PICs in area)
6. User selects problem category
7. User enters issue description
8. User sets deadline date
9. User uploads photos (optional, multiple photos supported)
10. User submits the form
11. System validates input data
12. System saves report to database
13. System sends email notification to assigned PIC(s)
14. System displays success message
15. System redirects to report list page

**Alternative Flow**:
- 11a. Validation fails: System displays error messages and returns to form
- 13a. Email sending fails: System logs error but continues with report creation

---

#### UC-02: Edit Report

**Actor**: Reporter, PIC, Admin  
**Precondition**: Report exists in the system  
**Postcondition**: Report is updated and changes are notified

**Main Flow**:
1. User navigates to report list
2. User clicks "Edit" button on specific report
3. System displays edit form with current data
4. User modifies report details (area, PIC, category, description, deadline)
5. User can add new photos or remove existing photos
6. User submits the form
7. System validates input data
8. System detects changes from original data
9. System updates report in database
10. If significant changes detected, system sends email notification to PIC
11. System displays success message
12. System redirects to previous page

**Alternative Flow**:
- 7a. Validation fails: System displays error messages
- 10a. No significant changes: Skip email notification

---

#### UC-03: Complete Report

**Actor**: PIC  
**Precondition**: Report is assigned and not yet completed  
**Postcondition**: Report status is "Completed" and moved to history

**Main Flow**:
1. User navigates to report detail or list
2. User clicks "Action/Tindakan" button
3. System displays completion form
4. User enters completion date
5. User enters resolution description
6. User uploads completion photos (optional)
7. User selects status as "Completed"
8. User submits the form
9. System validates input data
10. System creates completion record
11. System updates report status to "Completed"
12. System generates PDF report
13. System sends email notification with PDF attachment to PIC
14. System displays success message
15. System redirects to history page

**Alternative Flow**:
- 7a. User selects status other than "Completed": System only updates status without creating completion record
- 9a. Validation fails: System displays error messages

---

#### UC-04: View Dashboard

**Actor**: All Users  
**Precondition**: User has access to the system  
**Postcondition**: Dashboard statistics and charts are displayed

**Main Flow**:
1. User navigates to dashboard page
2. System retrieves statistics (total, in progress, completed reports)
3. System retrieves monthly report data (12 months)
4. System retrieves area distribution data
5. System retrieves category distribution data (current month)
6. System displays statistics cards
7. System renders line chart for monthly trends
8. System renders bar chart for area distribution
9. System renders pie chart for category distribution
10. System displays active reports in DataTable
11. User can apply filters (date range, area, PIC, category)
12. System updates table based on filters

---

#### UC-05: View History

**Actor**: All Users  
**Precondition**: User has access to the system  
**Postcondition**: Completed reports are displayed

**Main Flow**:
1. User navigates to history page
2. System retrieves all completed reports
3. System displays reports in DataTable
4. User can apply filters (date range, area, PIC, category)
5. System updates table based on filters
6. User can view completion details by clicking "View" button
7. System displays completion information in modal

---

#### UC-06: Export PDF

**Actor**: Manager, Admin  
**Precondition**: Completed reports exist  
**Postcondition**: PDF file is downloaded

**Main Flow**:
1. User is on history page
2. User applies desired filters (optional)
3. User clicks "Download PDF" button
4. System retrieves filtered completed reports
5. System generates PDF document with report data
6. System formats PDF in landscape A4
7. System initiates file download
8. User receives PDF file

---

#### UC-07: Manage Master Data (Areas)

**Actor**: Administrator  
**Precondition**: User has admin privileges  
**Postcondition**: Master data is created/updated/deleted

**Main Flow (Create)**:
1. User navigates to Master Data > Areas
2. User clicks "Add New Area" button
3. System displays area creation form
4. User enters area name
5. User adds PICs with station names and emails
6. User submits the form
7. System validates input data
8. System saves area and PICs to database
9. System displays success message

**Main Flow (Edit)**:
1. User clicks "Edit" button on specific area
2. System displays edit form with current data
3. User modifies area name or PIC details
4. User submits the form
5. System validates and updates data
6. System displays success message

**Main Flow (Delete)**:
1. User clicks "Delete" button on specific area
2. System checks if area is used in any reports
3. If not used, system displays confirmation dialog
4. User confirms deletion
5. System deletes PICs associated with area
6. System deletes area
7. System displays success message

**Alternative Flow**:
- 2a. Area is used in reports: System displays error message and prevents deletion

---

#### UC-08: Send Email Notifications (Automated)

**Actor**: System  
**Precondition**: Triggering event occurs  
**Postcondition**: Email is sent to recipient(s)

**Main Flow (Assignment Notification)**:
1. System detects new report creation
2. System retrieves PIC email(s) from report
3. System prepares email content with report details
4. System sends email to PIC(s)
5. System logs email sending status

**Main Flow (Deadline Reminder - H-2)**:
1. Cron job executes daily at 08:00 WIB
2. System calculates date 2 working days ahead
3. System retrieves reports with deadline matching that date
4. System filters reports with status != "Completed"
5. For each report, system sends reminder email to PIC
6. System logs reminder sending status

**Main Flow (Overdue Reminder)**:
1. Cron job executes daily at 08:00 WIB
2. System retrieves reports with deadline < today
3. System filters reports with status != "Completed"
4. For each report, system calculates days overdue
5. System sends overdue reminder email to PIC
6. System logs reminder sending status

---
