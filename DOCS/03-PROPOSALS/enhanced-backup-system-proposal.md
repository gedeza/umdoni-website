# Proposal: Enhanced Backup System & Security Hardening
## uMdoni Municipality Website

**Prepared by:** ISU Tech
**Date:** 23 December 2025
**Version:** 1.0
**Status:** Pending Client Approval

---

## Executive Summary

This proposal addresses two critical areas for the uMdoni Municipality website:

### 1. Enhanced Off-Site Backup System
An improved backup system that provides **off-site data protection** by storing backups on a secure remote server. This addresses the current risk where backups are stored on the same server as the website, and extends coverage to include uploaded documents (tenders, quotations, etc.).

### 2. Security Incident Response & Hardening
Documentation of **unauthorized access attempts** detected in December 2025, along with recommended security measures to protect the website from ongoing threats. ISU Tech has identified and analyzed malicious login attempts from foreign IP addresses targeting the municipality's website.

---

## Current Automated Backup System (Already In Place)

ISU Tech has implemented an automated database backup system for the uMdoni Municipality website. This section details what is currently covered.

### System Overview

| Component | Details |
|-----------|---------|
| **Type** | Automated Database Backup |
| **Server** | reseller142.aserv.co.za |
| **Schedule** | Daily at 2:00 AM SAST |
| **Method** | MySQL dump with gzip compression |
| **Management** | Dashboard interface at `/dashboard/backups` |

### What's Currently Backed Up

The automated backup covers the **MySQL database** which includes:

| Data Category | Description |
|---------------|-------------|
| **User Accounts** | Administrator and staff login credentials |
| **Content Management** | News articles, events, announcements |
| **Tenders & Quotations** | Metadata (titles, dates, descriptions, status) |
| **Documents** | Document listings and metadata |
| **Council Information** | Councillor profiles, ward information |
| **Service Requests** | Public service request submissions |
| **Activity Logs** | System audit trail and user actions |
| **Site Settings** | Configuration and preferences |

### Retention Policy

The system uses an intelligent 3-tier retention policy:

| Retention Type | Duration | Description |
|----------------|----------|-------------|
| **Daily** | 7 days | Every backup from the last week |
| **Weekly** | 4 weeks | Sunday backups kept for a month |
| **Monthly** | 3 months | 1st of month backups kept quarterly |

This ensures recent backups are readily available while older backups are preserved at key intervals.

### Dashboard Features

Administrators can access backup management via the dashboard:

- **View all backups** - List with dates, sizes, and ages
- **Manual backup** - Trigger immediate backup if needed
- **Download backups** - Download any backup file
- **Delete old backups** - Remove unnecessary backups
- **Statistics** - Total count, size, oldest/newest dates

### Activity Logging

All backup operations are recorded in the Activity Logs:
- Backup start and completion times
- Success or failure status
- File sizes and compression ratios
- Error messages if failures occur

### Current Storage Location

```
Production Server (reseller142.aserv.co.za)
└── backups/database/
    └── YYYY/MM/
        └── umdoni_backup_2025-12-23_02-00-00.sql.gz
```

---

## Current Limitations & Risks

### What's NOT Currently Backed Up

The current system does **not** backup uploaded files:

| File Type | Location | Risk |
|-----------|----------|------|
| Tender PDFs | `public/files/tenders/` | Lost if server fails |
| Quotation PDFs | `public/files/quotations/` | Lost if server fails |
| Official Documents | `public/files/documents/` | Lost if server fails |
| Public Notices | `public/files/notices/` | Lost if server fails |
| Vacancy Documents | `public/files/vacancies/` | Lost if server fails |
| RFP Documents | `public/files/rfps/` | Lost if server fails |

**Note:** The database contains metadata (titles, descriptions, dates) for these documents, but the actual PDF files are stored separately and are not included in database backups.

### Risk Assessment

| Risk | Impact | Likelihood |
|------|--------|------------|
| Server failure | Complete data loss (website + backups) | Medium |
| Disk corruption | All backups lost | Low |
| Security breach | Backups compromised with website | Medium |
| Uploaded files not backed up | Tender/quotation documents lost | High |

### Key Concern: Single Point of Failure

Currently, both the website and its backups reside on the **same server**. If that server experiences:
- Hardware failure
- Data center issues
- Security compromise
- Catastrophic disk failure

**Both the live website AND all backups would be lost simultaneously.**

---

## Proposed Enhancement: Off-Site Backup System

### Comparison: Current vs Enhanced

| Feature | Current System | Enhanced System |
|---------|----------------|-----------------|
| Database backup | ✅ Yes | ✅ Yes |
| Uploaded files backup | ❌ No | ✅ Yes |
| Storage location | Same server | Off-site (Hetzner) |
| Survives server failure | ❌ No | ✅ Yes |
| Storage monitoring | ❌ No | ✅ Yes (5GB limit) |
| Automated alerts | ❌ No | ✅ Yes (email at 80%) |
| Dashboard visibility | ✅ Local only | ✅ Local + Remote |

### Overview

Implement an **off-site backup system** that:
1. Backs up both database AND all uploaded files
2. Transfers backups to a secure remote server (Hetzner)
3. Monitors storage usage with automated alerts
4. Provides redundant data protection

### Architecture

```
uMdoni Website Server              ISU Tech Hetzner Server
(reseller142.aserv.co.za)          (46.224.40.5)
         │                                  │
         │  Encrypted SFTP Transfer         │
         │  (Daily at 2:00 AM)              │
         ├─────────────────────────────────►│
         │                                  │
    [Database]                         [Remote Backups]
    [Files]                            ├── Database backups
    [Local Backups]                    └── Files backups
                                            │
                                       5GB Storage Limit
                                            │
                                       Email Alert at 80%
```

### What Will Be Backed Up

| Category | Contents | Estimated Size |
|----------|----------|----------------|
| Database | All tables, user data, content | ~10-50 MB |
| Tenders | Tender document PDFs | Variable |
| Quotations | Quotation document PDFs | Variable |
| Documents | Official municipality documents | Variable |
| Notices | Public notices | Variable |
| Vacancies | Job posting documents | Variable |

### Storage & Retention

| Parameter | Value |
|-----------|-------|
| **Remote Storage Limit** | 5 GB |
| **Alert Threshold** | 80% (4 GB) |
| **Retention Policy** | 7 daily, 4 weekly, 3 monthly |
| **Backup Frequency** | Daily at 2:00 AM SAST |
| **Transfer Security** | SSH Key encrypted SFTP |

### Alert System

When remote storage reaches 80% capacity:
1. Email notification sent to ISU Tech (nhlanhla@isutech.co.za)
2. ISU Tech downloads older backups
3. Archived backups provided to uMdoni Municipality
4. Remote storage cleared for new backups

---

## Benefits

### Data Protection
- **Off-site redundancy** - Backups survive server failure
- **Complete coverage** - Database + all uploaded files
- **Encrypted transfer** - Secure data transmission

### Compliance & Governance
- **Audit trail** - All backup activities logged
- **Document preservation** - Tender/quotation records protected
- **Disaster recovery** - Ability to restore from remote backup

### Visibility
- **Dashboard monitoring** - View backup status in admin panel
- **Storage tracking** - See how much space is used
- **Automated alerts** - No manual monitoring required

---

## Security Incident Report

### Overview

During routine monitoring of the Activity Logs, ISU Tech identified **unauthorized access attempts** against the uMdoni Municipality website. These attempts occurred over multiple weeks, including the weekend of December 2025.

**Action Taken:** ISU Tech immediately notified the uMdoni IT Administrator of these security events.

### Documented Incidents

| Date | Time | IP Address | Email Used | Event |
|------|------|------------|------------|-------|
| 2025-12-20 | 14:11:28 | 88.210.3.196 | merlin.oleg@web.de | Authentication Failed |
| 2025-12-16 | 06:13:24 | 37.114.48.221 | dsdhhh414141in@loancalculator.world | Authentication Failed |

**Key Observations:**
- Neither email address uses the legitimate `@umdoni.gov.za` domain
- Attacks originate from multiple countries (Russia, Germany)
- One email (`dsdhhh414141in@loancalculator.world`) is clearly bot-generated
- Attack times include early morning hours (06:13 AM) suggesting automated scripts

### Threat Intelligence Analysis

#### IP Address 1: 88.210.3.196

| Attribute | Value |
|-----------|-------|
| **Country** | Netherlands (server location) |
| **Organization** | VDSina - Hosting technology LTD |
| **Headquarters** | Moscow, Russia |
| **Type** | Virtual Private Server (VPS) Provider |
| **ASN** | AS207651 |
| **Abuse Contact** | abuse@vdsina.ru |

**Assessment:** Russian VPS hosting provider. These services are commonly used by attackers due to anonymity and low cost.

#### IP Address 2: 37.114.48.221

| Attribute | Value |
|-----------|-------|
| **Country** | Germany |
| **City** | Gelnhausen, Hesse |
| **Organization** | Röth & Beck GbR \| IT:Solutions |
| **Type** | Hosting Provider |
| **ASN** | AS44486 (synlinq.de) |
| **Threat Status** | **FLAGGED AS ABUSIVE** |
| **Abuse Contact** | abuse@roeth-und-beck.de |

**Assessment:** This IP has been reported to threat intelligence databases for malicious activity. It is a known bad actor infrastructure.

### Attack Classification

| Factor | Finding | Implication |
|--------|---------|-------------|
| IP Type | Both are datacenter/hosting IPs | Not legitimate users |
| Email Domains | Foreign domains, not @umdoni.gov.za | Attackers, not staff |
| Pattern | Multiple IPs, multiple days | Persistent automated attack |
| Timing | Off-hours (early morning) | Bot/script activity |
| Threat Database | One IP flagged as abusive | Known malicious infrastructure |

**Conclusion:** These are **credential stuffing** or **brute force attacks** from automated bots operating on rented/compromised VPS infrastructure. This is NOT accidental failed logins from legitimate users.

---

## Security Hardening Recommendations

Based on the identified threats, ISU Tech recommends implementing the following security measures:

### Priority 1: Immediate Actions (Critical)

#### 1.1 IP Blocking
Block known malicious IP addresses at the server level:
```
Block: 88.210.3.196
Block: 37.114.48.221
```
Consider blocking entire IP ranges from known malicious hosting providers.

#### 1.2 Rate Limiting
Implement login attempt restrictions:
- Maximum 5 failed attempts per IP address per 15 minutes
- Temporary IP ban (30 minutes) after exceeding limit
- Permanent ban after repeated violations

#### 1.3 Account Lockout Policy
Protect user accounts from brute force:
- Lock account after 5 consecutive failed login attempts
- Require administrator intervention or time-based unlock (30 minutes)
- Notify administrator of locked accounts

### Priority 2: Short-Term Improvements

#### 2.1 Enhanced Logging
Improve security monitoring:
- Log all authentication attempts (success and failure)
- Capture IP address, user agent, and timestamp
- Flag suspicious patterns (multiple failures, unusual IPs)
- Dashboard alert for security events

#### 2.2 CAPTCHA Implementation
Add bot protection to login form:
- Implement CAPTCHA after 3 failed attempts
- Prevents automated credential stuffing
- Minimal impact on legitimate users

#### 2.3 Session Security
Strengthen session management:
- Reduce session timeout (already implemented: 10 minutes)
- Regenerate session ID on login
- Invalidate sessions on password change

### Priority 3: Long-Term Enhancements

#### 3.1 Two-Factor Authentication (2FA)
Add optional 2FA for administrator accounts:
- Email-based one-time codes
- Significantly reduces account compromise risk
- Recommended for all admin users

#### 3.2 Geographic Restrictions
Limit login attempts by location:
- Allow logins only from South African IP addresses
- Require administrator approval for foreign access
- Whitelist specific IPs for remote administrators

#### 3.3 Web Application Firewall (WAF)
Consider server-level protection:
- ModSecurity or similar WAF
- Block common attack patterns
- Protection against SQL injection, XSS, etc.

### Security Measures Summary

| Measure | Priority | Complexity | Impact |
|---------|----------|------------|--------|
| IP Blocking | Critical | Low | Immediate threat mitigation |
| Rate Limiting | Critical | Medium | Stops brute force attacks |
| Account Lockout | Critical | Medium | Protects user accounts |
| Enhanced Logging | High | Medium | Improves visibility |
| CAPTCHA | High | Medium | Blocks automated bots |
| Session Security | High | Low | Reduces session hijacking |
| Two-Factor Auth | Medium | High | Strong account protection |
| Geographic Restrictions | Medium | Medium | Reduces attack surface |
| WAF | Low | High | Comprehensive protection |

---

## Implementation

### Timeline

| Phase | Description | Duration |
|-------|-------------|----------|
| 1 | Development & testing | 3-5 days |
| 2 | Deployment to production | 1 day |
| 3 | Monitoring & verification | 1 week |

### Technical Requirements

- SSH key access to Hetzner server (already configured)
- SMTP configuration for email alerts
- No changes required on uMdoni's end

---

## Cost Considerations

### ISU Tech Resources Required

| Resource | Details |
|----------|---------|
| Hetzner Storage | 5 GB allocation from ISU Tech infrastructure |
| Development | Enhancement to existing backup system |
| Maintenance | Monitoring and archive management |

### Notes

- This enhancement uses ISU Tech's existing Hetzner server infrastructure
- Storage is limited to 5 GB to manage resource allocation
- When storage limit is reached, archived backups will be provided to uMdoni Municipality for their records

---

## Scope Clarification

This proposal includes two components that are **outside the current support SLA**:

### Component A: Off-Site Backup System
- ISU Tech server resources (Hetzner storage - 5 GB)
- Development of enhanced backup system
- Ongoing monitoring and archive management
- Delivery of archived backups when storage limit reached

### Component B: Security Hardening
- Implementation of recommended security measures
- IP blocking and rate limiting
- Account lockout policies
- Enhanced security logging

### Resource Commitment
| Resource | Backup System | Security Hardening |
|----------|---------------|-------------------|
| Development | 3-5 days | 2-3 days |
| ISU Tech Infrastructure | Hetzner storage | None |
| Ongoing Maintenance | Archive management | Monitoring |

**Client approval required before implementation of either component.**

---

## Next Steps

1. **Client Review** - uMdoni Municipality reviews this proposal
2. **Security Discussion** - Review documented security incidents with IT Administrator
3. **Component Selection** - Decide which components to approve:
   - [ ] Component A: Off-Site Backup System
   - [ ] Component B: Security Hardening (Priority 1 measures)
   - [ ] Component B: Security Hardening (Priority 2 measures)
   - [ ] Component B: Security Hardening (Priority 3 measures)
4. **Scope Agreement** - Confirm if added to SLA or separate agreement
5. **Written Approval** - Formal approval to proceed
6. **Implementation** - ISU Tech implements approved components
7. **Handover** - Training on new features and security measures

---

## Appendix A: Technical Specifications

### Backup File Naming
- Database: `umdoni_db_YYYY-MM-DD_HH-ii-ss.sql.gz`
- Files: `umdoni_files_YYYY-MM-DD_HH-ii-ss.tar.gz`

### Remote Directory Structure
```
/backups/umdoni/
├── db/
│   └── YYYY/MM/
│       └── umdoni_db_2025-12-23_02-00-00.sql.gz
└── files/
    └── YYYY/MM/
        └── umdoni_files_2025-12-23_02-00-00.tar.gz
```

### Security Measures
- SSH key authentication (no password transmission)
- Encrypted SFTP transfer
- Restricted server access
- Backup files compressed (gzip)

---

## Appendix B: Dashboard Preview

The admin dashboard will display:

```
┌─────────────────────────────────────────────────────┐
│  Remote Backup Storage                              │
│  ═══════════════════════════════════                │
│  [████████████░░░░░░░░░░░░] 2.1 GB / 5.0 GB (42%)  │
│                                                     │
│  ✓ Last backup: 23 Dec 2025, 02:00 AM              │
│  ✓ Connection: Secure (SFTP)                       │
│  ✓ Status: Healthy                                 │
└─────────────────────────────────────────────────────┘
```

---

## Appendix C: Security Incident Evidence

### Threat Intelligence Sources

The IP address analysis was conducted using the following threat intelligence resources:

- **IPinfo.io** - IP geolocation and organization data
- **AbuseIPDB** - IP reputation and abuse reports database
- **RIPE Database** - European IP registry information

### IP 37.114.48.221 Threat Status

This IP address has been **flagged as abusive** in threat intelligence databases, indicating it has been previously reported for malicious activity by other organizations.

### Recommended Reporting

If attacks persist, ISU Tech recommends filing abuse reports with:

| IP Address | Abuse Contact |
|------------|---------------|
| 88.210.3.196 | abuse@vdsina.ru |
| 37.114.48.221 | abuse@roeth-und-beck.de |

### Ongoing Monitoring

ISU Tech will continue to monitor Activity Logs for:
- Additional failed authentication attempts
- New malicious IP addresses
- Pattern changes in attack methods
- Any successful unauthorized access (none detected to date)

---

**Prepared by:**
Nhlanhla Mnyandu
ISU Tech
nhlanhla@isutech.co.za

---

*This proposal requires client approval before implementation can begin.*

*Security incidents documented herein have been reported to the uMdoni Municipality IT Administrator.*
