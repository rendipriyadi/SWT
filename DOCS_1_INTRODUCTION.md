# SAFETY WALK AND TALK (SWT) APPLICATION
## DOCUMENTATION

---

## 1. INTRODUCTION

### 1.1 Background

Workplace safety and 5S standards are critical in manufacturing environments, directly impacting operational efficiency and regulatory compliance. Traditional paper-based safety reporting systems suffer from communication delays, lack of accountability, and difficulty tracking issue resolution. The Safety Walk and Talk (SWT) application was developed to provide Siemens production environment with an efficient digital platform for real-time monitoring, automated notifications, and comprehensive safety issue management.

### 1.2 Purpose

The Safety Walk and Talk (SWT) application is designed to enhance workplace safety and 5S standards in Siemens production environment by providing a structured digital platform for reporting, tracking, and resolving safety hazards and 5S violations.

### 1.3 Scope

The application covers the complete lifecycle of safety issue management including issue reporting with photos, automatic PIC assignment, automated notifications and reminders, real-time dashboard monitoring, historical records, PDF documentation, and master data management. The system is designed for use across all production areas and departments within the Siemens facility.

### 1.4 Expected Outcomes

- **Improved Response Time**: Faster issue resolution through automated assignment and notifications
- **Increased Accountability**: Clear ownership and tracking from reporting to completion
- **Enhanced Safety Culture**: Simplified reporting encouraging proactive safety awareness
- **Better Management Visibility**: Data-driven insights for informed decision-making
- **Reduced Workplace Incidents**: Proactive hazard identification and resolution
- **Comprehensive Documentation**: Complete audit trail for compliance and continuous improvement

### 1.5 Document Overview

This documentation provides comprehensive information about the Safety Walk and Talk application, including:
- System overview and architecture
- Functional requirements and use cases
- Database design and entity relationships
- User interface design and features
- Installation and configuration guidelines
- User manual and operational procedures

---

## 2. SYSTEM OVERVIEW

### 2.1 Application Description

Safety Walk and Talk (SWT) is a web-based application built using Laravel framework that enables employees to report safety and 5S issues digitally. The system automates the workflow from issue identification through resolution, including assignment, notification, tracking, and documentation.

Key characteristics:
- **Web-based**: Accessible through standard web browsers
- **Centralized**: Single source of truth for all safety reports
- **Automated**: Reduces manual intervention through automated workflows
- **Transparent**: Complete visibility of issue status and history
- **Documented**: Maintains comprehensive records with photo evidence

### 2.2 Key Features

- **Report Management**: Create, edit, delete, view, and complete reports with photo uploads
- **Dashboard & Monitoring**: Real-time statistics, monthly trends, area/category distribution charts, and interactive DataTables
- **Email Notifications**: Automated emails for assignment, edits, completion (with PDF), H-2 deadline reminders, and overdue alerts
- **History & Archive**: View completed reports with advanced filtering and PDF export
- **Master Data**: Manage areas, departments, problem categories, and PICs
- **Security**: Encrypted IDs, CSRF protection, XSS prevention, and file validation

### 2.3 Technology Stack

**Backend:**
- Laravel 11 (PHP Framework)
- PHP 8.2
- MySQL 8 (Database)

**Frontend:**
- Bootstrap 5 (CSS Framework)
- jQuery (JavaScript Library)
- DataTables (Yajra) - Server-side table processing
- SweetAlert2 - User notifications
- Chart.js - Data visualization
- Font Awesome - Icons

**Libraries & Packages:**
- DomPDF - PDF generation
- Laravel Mail - Email system
- Carbon - Date/time handling
- Hashids - ID encryption

### 2.4 System Architecture

**Architecture Pattern: MVC (Model-View-Controller)**

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
│  (Views: Blade Templates, JavaScript, CSS)              │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                   CONTROLLER LAYER                       │
│  - ReportController                                      │
│  - HistoryController                                     │
│  - AreaController                                        │
│  - DepartmentController                                  │
│  - ProblemCategoryController                            │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                    SERVICE LAYER                         │
│  - ReportService                                         │
│  - FileUploadService                                     │
│  - Email Notification Services                           │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                  REPOSITORY LAYER                        │
│  - ReportRepository                                      │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                     MODEL LAYER                          │
│  - Laporan (Report)                                      │
│  - Penyelesaian (Completion)                            │
│  - Area                                                  │
│  - PenanggungJawab (PIC)                                │
│  - ProblemCategory                                       │
│  - DepartemenSupervisor                                  │
└────────────────────┬────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────┐
│                   DATABASE LAYER                         │
│                    MySQL 8                               │
└─────────────────────────────────────────────────────────┘
```

**Additional Components:**
- **Scheduled Tasks**: Cron jobs for automated reminders
- **Mail System**: SMTP email notifications
- **File Storage**: Local storage for uploaded images
- **Session Management**: User session handling

---
