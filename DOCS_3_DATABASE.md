# SAFETY WALK AND TALK (SWT) APPLICATION
## PART 3: DATABASE DESIGN

---

## 5. DATABASE DESIGN

**Database Type**: MySQL 8  
**Total Tables**: 6 tables  
**Storage Engine**: InnoDB

### 5.1 Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                               │
│                                                                               │
│   ┌──────────────────────┐                                                   │
│   │   areas              │                                                   │
│   ├──────────────────────┤                                                   │
│   │ PK  id               │                                                   │
│   │     name             │                                                   │
│   │     created_at       │                                                   │
│   │     updated_at       │                                                   │
│   └──────────┬───────────┘                                                   │
│              │                                                               │
│              │ 1                                                             │
│              │                                                               │
│              │ N                                                             │
│   ┌──────────▼───────────┐                                                   │
│   │  penanggung_jawab    │                                                   │
│   ├──────────────────────┤                                                   │
│   │ PK  id               │                                                   │
│   │ FK  area_id          │───────────┐                                       │
│   │     station          │           │                                       │
│   │     name             │           │                                       │
│   │     email            │           │                                       │
│   │     created_at       │           │                                       │
│   │     updated_at       │           │                                       │
│   └──────────┬───────────┘           │                                       │
│              │                       │                                       │
│              │ 1                     │ 1                                     │
│              │                       │                                       │
│              │ N                     │ N                                     │
│   ┌──────────▼───────────────────────▼──────────────────────┐               │
│   │                   laporan                                │               │
│   ├──────────────────────────────────────────────────────────┤               │
│   │ PK  id                                                   │               │
│   │ FK  area_id                                              │               │
│   │ FK  penanggung_jawab_id                                  │               │
│   │ FK  departemen_supervisor_id                             │               │
│   │ FK  problem_category_id                                  │               │
│   │     deskripsi_masalah                                    │               │
│   │     tenggat_waktu                                        │               │
│   │     status (In Progress/Selesai)                         │               │
│   │     Foto (JSON array)                                    │               │
│   │     created_at                                           │               │
│   │     updated_at                                           │               │
│   └──────────┬───────────────────────────────────────────────┘               │
│              │                                                               │
│              │ 1                                                             │
│              │                                                               │
│              │ 1                                                             │
│   ┌──────────▼───────────┐                                                   │
│   │    penyelesaian      │                                                   │
│   ├──────────────────────┤                                                   │
│   │ PK  id               │                                                   │
│   │ FK  laporan_id       │                                                   │
│   │     Tanggal          │                                                   │
│   │     Foto (JSON)      │                                                   │
│   │     deskripsi_       │                                                   │
│   │     penyelesaian     │                                                   │
│   │     created_at       │                                                   │
│   │     updated_at       │                                                   │
│   └──────────────────────┘                                                   │
│                                                                               │
│                                                                               │
│   ┌──────────────────────┐                                                   │
│   │  problem_categories  │                                                   │
│   ├──────────────────────┤                                                   │
│   │ PK  id               │                                                   │
│   │     name             │                                                   │
│   │     description      │                                                   │
│   │     color            │                                                   │
│   │     is_active        │                                                   │
│   │     sort_order       │                                                   │
│   │     created_at       │                                                   │
│   │     updated_at       │                                                   │
│   └──────────┬───────────┘                                                   │
│              │                                                               │
│              │ 1                                                             │
│              │                                                               │
│              │ N                                                             │
│              └───────────────────────┐                                       │
│                                      │                                       │
│                                      │                                       │
│   ┌──────────────────────┐           │                                       │
│   │ departemen_          │           │                                       │
│   │ supervisors          │           │                                       │
│   ├──────────────────────┤           │                                       │
│   │ PK  id               │           │                                       │
│   │     departemen       │           │                                       │
│   │     supervisor       │           │                                       │
│   │     workgroup        │           │                                       │
│   │     created_at       │           │                                       │
│   │     updated_at       │           │                                       │
│   └──────────┬───────────┘           │                                       │
│              │                       │                                       │
│              │ 1                     │                                       │
│              │                       │                                       │
│              │ N                     │                                       │
│              └───────────────────────┘                                       │
│                                                                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

**Figure 5.1: Entity Relationship Diagram - SWT Database**

---

### 5.2 Database Schema

#### Table 5.1: Database Tables Overview

| No | Table Name | Description | Primary Key | Foreign Keys |
|---|---|---|---|---|
| 1 | areas | Production areas/stations | id | - |
| 2 | penanggung_jawab | Person In Charge (PIC) for each area | id | area_id |
| 3 | laporan | Safety/5S issue reports | id | area_id, penanggung_jawab_id, departemen_supervisor_id, problem_category_id |
| 4 | penyelesaian | Report completion records | id | laporan_id |
| 5 | problem_categories | Problem/issue categories | id | - |
| 6 | departemen_supervisors | Department and supervisor information | id | - |

---

### 5.3 Table Schemas Summary

#### Table 5.2: Complete Schema Reference

| Table | Key Columns | Relationships | Notes |
|---|---|---|---|
| **areas** | id, name | → penanggung_jawab (1:N)<br>→ laporan (1:N) | Production areas/stations |
| **penanggung_jawab** | id, area_id, name, email | ← areas (N:1)<br>→ laporan (1:N) | Person In Charge for each area |
| **laporan** | id, area_id, penanggung_jawab_id, problem_category_id, deskripsi_masalah, tenggat_waktu, status, Foto (JSON) | ← areas (N:1)<br>← penanggung_jawab (N:1)<br>← problem_categories (N:1)<br>← departemen_supervisors (N:1)<br>→ penyelesaian (1:1) | Main reports table<br>Status: In Progress / Selesai |
| **penyelesaian** | id, laporan_id, Tanggal, Foto (JSON), deskripsi_penyelesaian | ← laporan (1:1) | Completion records<br>One per report |
| **problem_categories** | id, name, color, is_active, sort_order | → laporan (1:N) | Issue categories with color coding |
| **departemen_supervisors** | id, departemen, supervisor, workgroup | → laporan (1:N) | Department information |

---

### 5.4 Key Business Rules

1. **Report Lifecycle**:
   - Status: `In Progress` → `Selesai`
   - One report = one completion record (1:1)
   - Photos: Multiple photos supported, stored as JSON array

2. **Data Constraints**:
   - Report must have: area, description, deadline
   - Completion must have: date, description
   - Photo storage: `public/images/reports/` and `public/images/completions/`
   - Supported formats: JPG, JPEG, PNG (max 2MB each)

3. **Cascade Rules**:
   - Delete area → delete all PICs and reports (CASCADE)
   - Delete report → delete completion (CASCADE)
   - Delete PIC/Category → set FK to NULL in reports (SET NULL)

---

### 5.5 Production Recommendations

#### 5.5.1 Database Backup and Maintenance

**Note**: The following strategies are recommended for production deployment with multiple concurrent users.

**Backup Strategy:**

| Aspect | Recommendation | Rationale |
|---|---|---|
| **Backup Frequency** | Daily at 02:00 AM (off-peak hours) | Minimize impact on active users |
| **Backup Type** | Full database backup + Transaction logs | Complete recovery capability |
| **Retention Period** | 30 days rolling retention | Balance storage cost and recovery needs |
| **Backup Location** | Off-site secure storage + Cloud backup | Disaster recovery protection |
| **Encryption** | AES-256 encryption for backup files | Data security compliance |
| **Testing** | Monthly restore test | Verify backup integrity |

**Recovery Objectives:**
- **RTO (Recovery Time Objective)**: 4 hours maximum
- **RPO (Recovery Point Objective)**: 24 hours maximum (last backup)

**Implementation Tools:**
- MySQL Enterprise Backup
- Percona XtraBackup
- AWS RDS Automated Backups (if using cloud)
- Custom backup scripts with `mysqldump`

---

**Maintenance Schedule:**

| Frequency | Task | Purpose | Estimated Downtime |
|---|---|---|---|
| **Daily** | Monitor disk space and query logs | Prevent storage issues | 0 minutes |
| **Weekly** | Optimize tables (`OPTIMIZE TABLE`) | Defragment and reclaim space | 5-10 minutes |
| **Weekly** | Rebuild indexes (`ANALYZE TABLE`) | Update index statistics | 5 minutes |
| **Monthly** | Review slow query log | Identify performance bottlenecks | 0 minutes |
| **Monthly** | Update database statistics | Improve query optimizer | 2 minutes |
| **Quarterly** | Archive old completed reports (>1 year) | Reduce active database size | 15-30 minutes |
| **Quarterly** | Security audit and user access review | Compliance and security | 0 minutes |
| **Yearly** | Full database audit and cleanup | Remove orphaned data | 30-60 minutes |
| **Yearly** | Disaster recovery drill | Test backup/restore procedures | Planned maintenance window |

---

#### 5.5.2 Performance Monitoring

**Key Metrics to Monitor:**
- Query response time (target: <100ms for simple queries)
- Concurrent connections (monitor for connection pool exhaustion)
- Table lock wait time
- Disk I/O utilization
- Memory usage (buffer pool hit ratio)
- Slow query count (queries >1 second)

**Monitoring Tools:**
- MySQL Performance Schema
- Prometheus + Grafana
- New Relic / DataDog (APM)
- Custom Laravel logging

---

#### 5.5.3 Scaling Considerations

**When to Scale:**
- Response time consistently >500ms
- CPU usage >80% sustained
- Concurrent users >500
- Database size >50GB

**Scaling Options:**
1. **Vertical Scaling**: Upgrade server resources (CPU, RAM, SSD)
2. **Read Replicas**: Separate read and write operations
3. **Database Sharding**: Partition data by area or date
4. **Caching Layer**: Implement Redis/Memcached for frequent queries
5. **CDN for Images**: Offload photo storage to S3/CloudFront

---
