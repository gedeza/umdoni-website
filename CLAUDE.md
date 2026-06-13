# CLAUDE.md - Umdoni Municipality Website

## Project Information

### Overview
- **Project:** Umdoni Municipality Official Website
- **Owner:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
- **Company:** ISU Tech
- **Status:** Production - Active Maintenance
- **Repository:** /Users/nhla/Desktop/PROJECTS/2025/umdoni-website
- **Main Branch:** main

### Production Environment
- **Server:** reseller142.aserv.co.za
- **Database:** umdonigov_umdoni
- **cPanel:** https://reseller142.aserv.co.za:2083
- **Website:** https://umdoni.gov.za
- **Timezone:** Africa/Johannesburg (SAST, UTC+2)

---

## Tech Stack

### Backend
- **Framework:** Custom PHP MVC (PHP 5.4+ compatible)
- **Database:** MySQL with PDO
- **Routing:** Core\Router
- **Views:** Custom template system
- **Config:** App\Config

### Directory Structure
```
App/
  ├── Controllers/
  │   ├── Dashboard/     # 24 admin controllers
  │   └── [Public]/      # Public-facing controllers
  ├── Models/            # 29+ data models
  ├── Views/
  └── Config.php
public/
  ├── index.php          # Front controller (timezone set here)
  └── assets/            # CSS, JS, images
DOCS/
  ├── 01-PROJECT/        # Task tracking, status
  ├── 02-DEPLOYMENT/     # SSH guides, deployment packages
  ├── 03-PROPOSALS/      # Client proposals
  └── TASK-TRACKER.md    # Master task list
scripts/
  └── database-backup.php
```

---

## Completed Work (2026)

### June 13, 2026 - Security & UI Session

| Commit | Description |
|--------|-------------|
| `e6ab89d` | Proposal: Website Security & Performance Enhancement |
| `71f6116` | Docs: Update task tracker with SQL injection fixes |
| `f83f428` | Security: Fix 17 SQL injection vulnerabilities |
| `919057d` | Fix: Card heights with smaller description text |
| `b590dde` | Fix: Equal height cards on homepage |
| `091df4d` | Feat: Combined Tenders & Quotations card |

#### SQL Injection Fixes (17 total)
- **ProjectsModel.php:** getById, Save, Delete, Restore
- **CouncillorModel.php:** getCouncillorById, getExcoById, getSeniorManById, Save, Delete, DeleteMan, DeleteExco
- **Profile.php:** Save
- **AgendaModel.php:** GetById, Save, Update, Delete, Restore

#### Homepage Enhancement
- Combined "Tenders" card into "Tenders & Quotations" with dual buttons
- Cards have equal height with smaller description text
- Request from uMdoni technical personnel

### Earlier 2026

| Date | Feature |
|------|---------|
| March 2026 | Dashboard Activity Logging (70 calls across 20 controllers) |
| March 2026 | JS Console Errors & Sidebar fixes |
| Dec 2025 | Automated Database Backup System |
| Dec 2025 | Tender & Quotation Expiry Management |
| Dec 2025 | Admin User Creation & Security Hardening |

---

## Pending Proposals

### 1. Security & Performance Enhancement (NEW)
**File:** `DOCS/03-PROPOSALS/security-performance-enhancement-proposal.md`
**Cost:** R49,600 (62 hours)

| Package | Hours | Cost |
|---------|-------|------|
| A. Critical Security (XSS, CSRF, Session) | 16 | R12,800 |
| B. Security Hardening (RBAC, File Upload) | 12 | R9,600 |
| C. PHP 8.x Compatibility | 4 | R3,200 |
| D. Performance Optimization | 10 | R8,000 |
| E. Audit & Monitoring | 12 | R9,600 |
| F. UX Enhancements | 8 | R6,400 |

### 2. Enhanced Backup & Security Hardening
**File:** `DOCS/03-PROPOSALS/enhanced-backup-system-proposal.md`
- Off-site backups to Hetzner server
- Security incident response (unauthorized access attempts)

### 3. Municipal Policies Management System
**File:** `DOCS/proposals/Municipal-Policies-System-Proposal.md`
**Cost:** R67,200 (84 hours)

---

## Remaining Tasks (from TASK-TRACKER.md)

### Critical/High Priority
- [ ] Task 3: PHP 8.x Compatibility (3 files remaining)
- [ ] Task 4: XSS Output Escaping (7 files)
- [ ] Task 5: CSRF Protection (23+ forms)

### Medium Priority
- [ ] Task 6: Unsafe Deserialization Fix
- [ ] Task 7: File Upload Validation
- [ ] Task 8: Dashboard Performance
- [ ] Task 9: Error Handling
- [ ] Task 10: Session Security
- [ ] Task 11: Authorization / RBAC
- [ ] Task 14: Activity Logs Reporting

### Low Priority
- [ ] Task 13: UX Enhancements

---

## Development Guidelines

### Before Making Changes
1. Always read files before editing
2. Check git status and recent commits
3. Test locally if possible (note: DB connection requires IP whitelist)

### Git Commit Standards
- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
- Include Co-Authored-By for Claude
- Descriptive messages with context

### Security Standards
- Use PDO prepared statements (SQL injection fixed)
- Validate at system boundaries
- Escape output with htmlspecialchars()
- Follow OWASP Top 10 guidelines

### Deployment
- Via cPanel File Manager: https://reseller142.aserv.co.za:2083
- Or SSH: `ssh username@reseller142.aserv.co.za`
- Website root: `public_html/` or `public_html/App/`

---

## Quick Reference

### Local Development
```bash
# Start local server (DB connection will fail - IP not whitelisted)
php -S localhost:8000 -t public/

# Test timezone
php scripts/test-timezone.php
```

### Key Files
- **Task Tracker:** `DOCS/TASK-TRACKER.md`
- **SSH Guide:** `DOCS/02-DEPLOYMENT/SSH-ACCESS-GUIDE.md`
- **Front Controller:** `public/index.php`
- **Database Config:** `App/Config.php`

### Recent Proposals
- Security & Performance: `DOCS/03-PROPOSALS/security-performance-enhancement-proposal.md`
- Backup System: `DOCS/03-PROPOSALS/enhanced-backup-system-proposal.md`
- Policies System: `DOCS/proposals/Municipal-Policies-System-Proposal.md`

---

## Session Notes

### Local Testing Issue
- Production MySQL only allows connections from whitelisted IPs
- Local IP (41.121.39.26) not whitelisted
- For UI-only changes, deploy directly to production

### Homepage Cards (User Preference)
- User kept equal-height cards (`b590dde`) on production
- Smaller text version (`919057d`) is latest in repo
- Both versions work - user can choose which to deploy

---

*Last Updated: 2026-06-13*
