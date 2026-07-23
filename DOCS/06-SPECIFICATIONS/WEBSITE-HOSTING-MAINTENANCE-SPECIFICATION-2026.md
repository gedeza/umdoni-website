# WEBSITE HOSTING AND MAINTENANCE SPECIFICATIONS
## UMDONI MUNICIPALITY

### Appointment of a Service Provider for Website Hosting and Maintenance Services for a Period of 12 Months

**Document status:** Draft technical & service specification, prepared for uMdoni Municipality's internal Supply Chain Management (SCM) unit to incorporate into a formal public Request for Quotation / Tender. This document covers scope, service levels, and technical requirements only; SCM-specific sections (evaluation criteria, B-BBEE scoring, mandatory compliance documents, submission instructions, closing date and venue) are to be added by the Municipality's SCM unit prior to public issue.

---

## 1. PURPOSE

Umdoni Municipality invites suitably qualified and experienced service providers to provide website hosting, maintenance, technical support, security monitoring, backup services, and content management support for the Municipality's official website (https://umdoni.gov.za) for the contract period specified in Section 2.

This appointment follows a short-term (one-month) interim hosting and maintenance appointment put in place to bridge the period between the expiry of the Municipality's previous service provider agreement and the appointment of a long-term service provider. The purpose of this appointment is to secure a stable, accountable service provider for the medium term, ensuring uninterrupted availability, security, and optimal performance of the municipal website.

---

## 2. CONTRACT PERIOD

The appointment shall be for a period of:

**12 (twelve) months**, from date of award, with the option, at the sole discretion of the Municipality, to renew for a further period of **[RENEWAL PERIOD — TBD]**, subject to satisfactory performance and availability of budget.

The Municipality reserves the right to terminate the appointment on [NOTICE PERIOD — TBD] days' written notice, or with immediate effect for material breach, security negligence, or non-performance against the service levels in Section 4.

---

## 3. SYSTEM BASELINE (CONTEXT FOR BIDDERS)

To enable bidders to price and resource this appointment accurately, the current system is described below. The successful service provider will be maintaining and hosting this system as a going concern, not building a new website.

| Item | Detail |
|---|---|
| **Platform** | Custom PHP MVC application (PHP 8.x compatible), MySQL/MariaDB database |
| **Public site** | News, events, council notices, tenders, quotations, RFPs, vacancies, councillor/ward information, service request submissions, document library |
| **Admin dashboard** | Content management for all public modules; user and role management; activity/audit logging; automated backup management interface |
| **Authentication** | Session-based login with bcrypt password hashing, session timeout, activity logging of authentication events |
| **Automated backups (current)** | Daily database backup (2:00 AM SAST) with tiered retention (7 daily / 4 weekly / 3 monthly); managed via `/dashboard/backups` |
| **File storage** | Uploaded documents (tender PDFs, quotation PDFs, official documents, notices, vacancy documents, RFP documents) stored on the web server file system |
| **Domain / DNS** | umdoni.gov.za (current registrar/DNS management to be confirmed and handed over to the incoming provider — see Section 10) |
| **Timezone** | Africa/Johannesburg (SAST, UTC+2) — must be preserved in any migration |

Bidders should note the following known conditions of the current system, which the successful service provider will be expected to remediate or manage under this appointment:

- Uploaded document files (tenders, quotations, notices, vacancies, RFPs) are **not currently included in off-site backups** — only database records are backed up off the live server. The off-site backup requirement in Section 3.4 below explicitly requires file-level backup to close this gap.
- The site has previously experienced automated brute-force/credential-stuffing login attempts from foreign IP addresses. Security monitoring, rate limiting, and account lockout are therefore explicit requirements, not optional extras (see Sections 3.3 and 4).

---

## 4. SCOPE OF WORK

The successful service provider shall provide the following services:

### 4.1 Website Hosting

- Secure cloud or dedicated website hosting, sized for a PHP/MySQL municipal website with the modules described in Section 3.
- Minimum 99.9% website uptime.
- SSL Certificate provisioning, renewal, and management (auto-renewal preferred).
- Domain and DNS management, including safe handover of DNS records from the outgoing provider without service interruption.
- Sufficient storage and bandwidth to support the Municipality's website, including document uploads, with headroom for growth.
- Daily website and service (uptime) monitoring, with proactive alerting.
- PHP version and server software kept current and supported (no end-of-life PHP versions), with compatibility testing before any upgrade.

### 4.2 Website Maintenance

- Application, dependency, and server software updates.
- Bug fixes and troubleshooting across the public site and admin dashboard.
- Website performance optimisation (query performance, page load times, caching).
- Repair of broken links and website functionality.
- Database optimisation and integrity checks.
- No structural changes to functionality, workflows, or design without prior written approval from the Municipality (see Section 9).

### 4.3 Security Management

- Malware scanning on a scheduled basis.
- Firewall / Web Application Firewall (WAF) protection.
- Security updates and patches applied promptly upon release (critical patches within 48 hours of release).
- Continuous monitoring against cyber threats, including brute-force and credential-stuffing login attempts.
- Login rate limiting and account lockout after repeated failed authentication attempts.
- Immediate response to security incidents, including written notification to the Municipality within 4 hours of detection of any confirmed or suspected breach.
- Maintenance of an authentication/security event log, reviewable by the Municipality on request.

### 4.4 Backup and Recovery

- Daily automated backups of **both the database and all uploaded/stored files** (tenders, quotations, notices, vacancies, RFPs, and any other document uploads).
- Secure **off-site** backup storage, physically/logically separate from the live hosting environment, so that a failure of the live server does not also destroy the backups.
- Tiered retention policy (minimum: 7 daily, 4 weekly, 3 monthly, or better).
- Restoration of website, database, and files on request, tested periodically to confirm recoverability.
- Documented disaster recovery procedure, with a target Recovery Time Objective (RTO) of no more than 4 hours for full site restoration.

### 4.5 Content Management Support

The service provider shall assist with publishing the following content types via the existing dashboard modules:

- Council Notices
- Tender Advertisements
- Quotations
- Vacancies
- Public Notices
- Council Documents
- News Articles
- Municipal Announcements
- Documents supplied by the Municipality

Standard content updates shall be completed within one (1) business day. Urgent updates shall be completed within two (2) hours during support hours.

**Support hours:** Monday to Friday, 07:30 – 16:30 (SAST). Emergency support shall be available after hours and on weekends for critical incidents (site down, security breach, data loss risk).

---

## 5. SERVICE LEVEL REQUIREMENTS

| Service | Requirement |
|---|---|
| Website Availability | Minimum 99.9% monthly uptime |
| Critical Incident Response Time | Within 30 minutes |
| Critical Incident Resolution | Within 4 hours |
| Standard Support Requests | Within 1 business day |
| Urgent Content Updates | Within 2 hours (support hours) |
| Daily Backups (database + files) | Required, off-site |
| Backup Restoration Test | At least quarterly |
| Security Monitoring | Continuous |
| Critical Security Patch Application | Within 48 hours of release |
| Security Incident Notification to Municipality | Within 4 hours of detection |

The Municipality reserves the right to levy service credits or penalties for repeated or material failure to meet these service levels, as set out in the final contract terms.

---

## 6. DELIVERABLES

The successful service provider shall provide:

- Secure website hosting meeting the requirements of Section 4.1.
- Website maintenance per Section 4.2.
- Daily off-site backups of database and files per Section 4.4.
- Continuous website security monitoring per Section 4.3.
- Technical support within the response times in Section 5.
- Content publishing support per Section 4.5.
- Monthly maintenance and service report (see Section 7).
- Ongoing performance monitoring and reporting.
- Website and data recovery services if and when required.
- An up-to-date register of all administrative/production access credentials, held securely and available to the Municipality at all times (see Section 10).

---

## 7. REPORTING

The service provider shall submit a **monthly report** (and a final consolidated report at contract end or termination) detailing:

- Website uptime for the period, with any downtime incidents and root cause.
- Maintenance activities performed.
- Security incidents (if any), including login/brute-force attempt patterns.
- Backup status (database and files), including confirmation of off-site storage and any restoration tests performed.
- Website performance metrics.
- Technical and content support requests received and attended to, against the SLA targets in Section 5.
- Recommendations for future improvements.

---

## 8. SERVICE PROVIDER REQUIREMENTS

The bidder must:

- Have demonstrable experience in website hosting and maintenance for PHP/MySQL web applications.
- Demonstrate knowledge of website security, including OWASP Top 10 risk areas.
- Provide qualified technical support personnel, with named contacts and escalation paths.
- Be able to respond promptly to service requests within the defined support hours, plus after-hours emergency response.
- Have experience working with government or municipal websites (recommended, not mandatory).
- Demonstrate an existing off-site/secondary backup capability independent of the primary hosting environment.
- Confirm data sovereignty/hosting location (South African hosting preferred/required per Municipality policy, to be confirmed by SCM).

---

## 9. GENERAL CONDITIONS

The successful service provider shall:

- Maintain confidentiality of all municipal information, data, and credentials.
- Ensure that no website downtime occurs due to negligence.
- Notify the Municipality at least 24 hours before any scheduled maintenance likely to affect availability.
- Not make any structural, functional, or design changes to the website without prior written approval from the Municipality.
- Ensure that all website data, content, and uploaded documents remain the sole property of Umdoni Municipality at all times.
- Not withhold access, backups, source code, or credentials from the Municipality at contract end, suspension, or termination, for any reason (see Section 10).

---

## 10. HANDOVER AND TRANSITION

Because this appointment follows on from a previous service provider arrangement, and to protect continuity should the appointment change hands again in future, the successful service provider shall, within 10 business days of contract commencement:

- Confirm receipt of (or arrange transfer of) the website source code repository, database, and all uploaded files from the outgoing provider/environment.
- Provide the Municipality with a written register of all hosting, domain, DNS, SSL, and administrative access credentials created or used under this appointment.

At contract end, suspension, or termination (for any reason), the service provider shall, within 5 business days:

- Hand over full, current copies of the source code, database, and all uploaded files to the Municipality or its nominated incoming provider.
- Hand over or transfer all domain, DNS, SSL, and hosting account credentials.
- Provide a final backup export in a portable, non-proprietary format.

No transition or exit fees shall apply to this handover.

---

## 11. PAYMENT

Payment shall be made monthly in arrears, upon successful delivery of that month's hosting and maintenance services, submission of the required monthly report (Section 7), and certification by the Municipality that the services have been rendered satisfactorily against the service levels in Section 5.

---

*This document is a technical and service specification only. Procurement-process sections — evaluation and scoring criteria, functionality thresholds, B-BBEE requirements, mandatory compliance documentation (tax clearance, CSD registration, etc.), submission instructions, and closing date/venue — are to be completed by Umdoni Municipality's Supply Chain Management unit before public issue.*

