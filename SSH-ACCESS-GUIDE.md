# SSH Access Guide - uMdoni Production Server

**Server:** reseller142.aserv.co.za
**Author:** Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
**Date:** December 8, 2025

---

## 🔐 SSH Connection Overview

This guide explains how to SSH into the uMdoni production server from your local machine.

---

## 📋 Prerequisites

### What You'll Need
1. **SSH Username** - Usually your cPanel username
2. **SSH Password** - Usually your cPanel password (or SSH key)
3. **Server Address** - reseller142.aserv.co.za
4. **SSH Port** - Usually 22 (default) or check with hosting provider

### Where to Find SSH Credentials

**Option 1: Check Hosting Welcome Email**
- Look for email from hosting provider (aServ)
- Subject might be: "Welcome to aServ" or "cPanel Account Information"
- Contains: Username, Password, Server details

**Option 2: cPanel Dashboard**
1. Login to cPanel: https://reseller142.aserv.co.za:2083
2. Look for "SSH Access" icon (under Security section)
3. Enable SSH access if not already enabled
4. Note your SSH username (usually same as cPanel username)

**Option 3: Contact Hosting Provider**
- Email/Call aServ support
- Ask for SSH access credentials
- They might need to enable SSH for your account first

---

## 🚀 Method 1: Direct SSH Connection (macOS/Linux)

### Basic SSH Command

```bash
ssh username@reseller142.aserv.co.za
```

**Replace `username` with your cPanel username**

### Step-by-Step

1. **Open Terminal** (on macOS: Cmd+Space, type "Terminal")

2. **Connect to server:**
   ```bash
   ssh your-cpanel-username@reseller142.aserv.co.za
   ```

3. **Accept fingerprint** (first time only):
   ```
   The authenticity of host 'reseller142.aserv.co.za' can't be established.
   ECDSA key fingerprint is SHA256:...
   Are you sure you want to continue connecting (yes/no)? yes
   ```
   Type `yes` and press Enter.

4. **Enter password** when prompted:
   ```
   your-cpanel-username@reseller142.aserv.co.za's password:
   ```
   Type your cPanel password (won't show on screen - this is normal)

5. **Success!** You should see a prompt like:
   ```
   [your-username@reseller142 ~]$
   ```

### Common SSH Usernames for cPanel

If you don't know your username, try these patterns:
- `umdonigov` (based on database user: umdonigov_admin)
- Check your cPanel login username
- Check hosting welcome email

---

## 🔑 Method 2: SSH with Custom Port (If Default Doesn't Work)

Some shared hosting servers use non-standard SSH ports.

### Try These Ports

```bash
# Try default port (22)
ssh username@reseller142.aserv.co.za

# Try alternate port (2222)
ssh -p 2222 username@reseller142.aserv.co.za

# Try cPanel SSH port (sometimes 22022)
ssh -p 22022 username@reseller142.aserv.co.za
```

### Find Your SSH Port

**In cPanel:**
1. Login to cPanel
2. Go to "SSH Access" (Security section)
3. Port number shown at top

---

## 🔐 Method 3: SSH Key Authentication (More Secure)

Instead of password, use SSH key for automatic login.

### Generate SSH Key (If You Don't Have One)

```bash
# On your local machine (macOS)
ssh-keygen -t rsa -b 4096 -C "nhlanhla@isutech.co.za"

# Press Enter to accept default location: /Users/nhla/.ssh/id_rsa
# Enter passphrase (optional but recommended)
```

### Copy SSH Key to Server

```bash
# Method 1: Using ssh-copy-id (easiest)
ssh-copy-id username@reseller142.aserv.co.za

# Method 2: Manual copy
cat ~/.ssh/id_rsa.pub | ssh username@reseller142.aserv.co.za "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```

### Connect with SSH Key

```bash
ssh username@reseller142.aserv.co.za
# No password needed!
```

---

## 🖥️ Method 4: cPanel SSH Terminal (Browser-Based)

If command-line SSH doesn't work, use cPanel's built-in terminal.

### Steps

1. **Login to cPanel:**
   - URL: https://reseller142.aserv.co.za:2083
   - Username: Your cPanel username
   - Password: Your cPanel password

2. **Find "Terminal" or "SSH Access":**
   - Under "Advanced" or "Security" section
   - Click "Terminal" icon

3. **Browser terminal opens:**
   - Same as SSH connection
   - Run commands directly in browser

**Pros:**
- No local SSH setup needed
- Works from any device
- Good for quick tasks

**Cons:**
- Less convenient for file transfers
- Can't use local tools (scp, rsync)

---

## 📁 Finding Your Website Directory

Once connected via SSH:

### Navigate to Website Root

```bash
# Check current directory
pwd

# Usually you'll be in home directory: /home/username

# Website files typically in:
cd ~/public_html
# or
cd ~/public_html/umdoni
# or
cd ~/domains/umdoni.gov.za/public_html

# List files
ls -la

# Look for familiar files:
ls index.php App/ public/
```

### Common Directory Structures

**Pattern 1: Direct in public_html**
```
/home/username/public_html/
├── App/
├── public/
├── index.php
└── ...
```

**Pattern 2: Subdomain/addon domain**
```
/home/username/public_html/umdoni.gov.za/
├── App/
├── public/
└── ...
```

**Pattern 3: Under domains folder**
```
/home/username/domains/umdoni.gov.za/public_html/
```

### Quick Check Command

```bash
# Find where index.php is located
find ~ -name "index.php" -path "*/public/index.php" 2>/dev/null

# Or find App/Config.php
find ~ -name "Config.php" -path "*/App/Config.php" 2>/dev/null
```

---

## 📦 Deploying Files via SSH

### Method 1: SCP (Secure Copy) from Local Machine

```bash
# From your local machine (NOT on SSH)
cd /Users/nhla/Desktop/PROJECTS/2025/umdoni-website

# Copy single file
scp public/assets/js/session-timeout.js username@reseller142.aserv.co.za:~/public_html/public/assets/js/

# Copy directory
scp -r deployment/session-timeout-20251208/ username@reseller142.aserv.co.za:~/backups/

# Copy tarball and extract on server
scp deployment/session-timeout-20251208.tar.gz username@reseller142.aserv.co.za:~/
```

### Method 2: SFTP (Interactive File Transfer)

```bash
# Start SFTP session
sftp username@reseller142.aserv.co.za

# Navigate on remote server
cd public_html/public/assets/js

# Upload file
put /Users/nhla/Desktop/PROJECTS/2025/umdoni-website/public/assets/js/session-timeout.js

# Upload directory
put -r /path/to/local/directory

# Download file
get remote-file.php

# Exit SFTP
exit
```

### Method 3: Upload Tarball & Extract on Server

```bash
# 1. On local machine - upload tarball
scp deployment/session-timeout-20251208.tar.gz username@reseller142.aserv.co.za:~/

# 2. SSH into server
ssh username@reseller142.aserv.co.za

# 3. Extract tarball
cd ~/
tar -xzf session-timeout-20251208.tar.gz

# 4. Copy files to correct locations
cp session-timeout-20251208/files/session-timeout.js ~/public_html/public/assets/js/
cp session-timeout-20251208/files/Index.php ~/public_html/App/Controllers/Dashboard/
cp session-timeout-20251208/files/dashboardLayout.php ~/public_html/public/layouts/

# 5. Set permissions
chmod 644 ~/public_html/public/assets/js/session-timeout.js
```

---

## 🛠️ Useful SSH Commands After Connecting

### Navigation
```bash
# Show current directory
pwd

# List files
ls -la

# Change directory
cd public_html/

# Go back one level
cd ..

# Go to home directory
cd ~
```

### File Operations
```bash
# View file contents
cat App/Config.php

# Edit file (nano editor)
nano public/assets/js/session-timeout.js

# Copy file
cp file.php file.php.backup

# Move/rename file
mv oldname.php newname.php

# Delete file (careful!)
rm file.php

# Create directory
mkdir backups
```

### File Permissions
```bash
# View permissions
ls -la file.php

# Set file permissions (644 for PHP files)
chmod 644 file.php

# Set directory permissions (755 for directories)
chmod 755 directory/

# Recursive permission change
chmod -R 644 public/assets/js/*.js
```

### Check Running Processes
```bash
# Show running PHP processes
ps aux | grep php

# Check cron jobs
crontab -l

# Edit cron jobs
crontab -e
```

### Database Operations
```bash
# Access MySQL
mysql -u umdonigov_admin -p umdonigov_umdoni

# Run SQL file
mysql -u umdonigov_admin -p umdonigov_umdoni < backup.sql

# Export database
mysqldump -u umdonigov_admin -p umdonigov_umdoni > backup_$(date +%Y%m%d).sql
```

### Logs
```bash
# View PHP error log
tail -f ~/logs/error_log

# View Apache error log (if accessible)
tail -f ~/logs/apache_error_log

# View last 100 lines
tail -n 100 ~/logs/error_log
```

---

## 🚨 Troubleshooting SSH Issues

### Issue 1: "Permission denied (publickey)"
**Cause:** SSH key authentication required, password auth disabled

**Solutions:**
1. Use cPanel Terminal instead
2. Contact hosting provider to enable password authentication
3. Set up SSH key (see Method 3 above)

---

### Issue 2: "Connection refused"
**Cause:** SSH not enabled or wrong port

**Solutions:**
```bash
# Try alternate port
ssh -p 2222 username@reseller142.aserv.co.za

# Check if SSH is enabled in cPanel
# Go to: cPanel > Security > SSH Access > Enable
```

---

### Issue 3: "Host key verification failed"
**Cause:** Server fingerprint changed (security concern)

**Solutions:**
```bash
# Remove old fingerprint
ssh-keygen -R reseller142.aserv.co.za

# Reconnect (will ask to accept new fingerprint)
ssh username@reseller142.aserv.co.za
```

---

### Issue 4: "Connection timed out"
**Cause:** Firewall, wrong server, or SSH not running

**Solutions:**
1. Check server address is correct: `reseller142.aserv.co.za`
2. Try alternate ports (2222, 22022)
3. Check your firewall allows SSH (port 22)
4. Contact hosting provider

---

### Issue 5: "Too many authentication failures"
**Cause:** Multiple failed password attempts

**Solutions:**
```bash
# Wait 5 minutes, then try again
ssh username@reseller142.aserv.co.za

# Or disable SSH key auth temporarily
ssh -o PubkeyAuthentication=no username@reseller142.aserv.co.za
```

---

## 💡 Quick Deployment Workflow

Here's the complete workflow for deploying session timeout feature:

### Step 1: Upload Files

```bash
# From local machine
cd /Users/nhla/Desktop/PROJECTS/2025/umdoni-website

# Upload tarball
scp deployment/session-timeout-20251208.tar.gz username@reseller142.aserv.co.za:~/
```

### Step 2: SSH and Extract

```bash
# SSH into server
ssh username@reseller142.aserv.co.za

# Extract
tar -xzf session-timeout-20251208.tar.gz
cd session-timeout-20251208/files
```

### Step 3: Backup Existing Files

```bash
# Create backup directory
mkdir -p ~/backups/session-timeout-backup-$(date +%Y%m%d)

# Backup files we're about to modify
cp ~/public_html/App/Controllers/Dashboard/Index.php ~/backups/session-timeout-backup-$(date +%Y%m%d)/
cp ~/public_html/public/layouts/dashboardLayout.php ~/backups/session-timeout-backup-$(date +%Y%m%d)/
```

### Step 4: Deploy Files

```bash
# Copy new files
cp session-timeout.js ~/public_html/public/assets/js/
cp Index.php ~/public_html/App/Controllers/Dashboard/
cp dashboardLayout.php ~/public_html/public/layouts/

# Set permissions
chmod 644 ~/public_html/public/assets/js/session-timeout.js
chmod 644 ~/public_html/App/Controllers/Dashboard/Index.php
chmod 644 ~/public_html/public/layouts/dashboardLayout.php
```

### Step 5: Verify Deployment

```bash
# Check files exist
ls -la ~/public_html/public/assets/js/session-timeout.js
ls -la ~/public_html/App/Controllers/Dashboard/Index.php

# Check file sizes
du -h ~/public_html/public/assets/js/session-timeout.js
```

### Step 6: Test

```bash
# Visit website
# Open: https://umdoni.gov.za/dashboard
# Check browser console for: "Session timeout initialized: 30 minutes"
```

---

## 🔒 Security Best Practices

### Keep Credentials Safe
- ✅ Never commit SSH credentials to git
- ✅ Use SSH keys instead of passwords
- ✅ Keep private keys secure (chmod 600 ~/.ssh/id_rsa)
- ✅ Use different passwords for each service

### Secure Connection
```bash
# Verify you're on correct server
hostname
# Should show: reseller142 or similar

# Check who you're logged in as
whoami

# Check current directory
pwd
```

### Before Running Commands
- ✅ Always verify you're in correct directory
- ✅ Backup files before modifying
- ✅ Test in development first
- ✅ Double-check file paths in commands

---

## 📞 Support & Resources

### Hosting Provider (aServ)
- **Website:** https://aserv.co.za
- **Support:** Check your hosting welcome email
- **Documentation:** Usually available in cPanel

### If SSH Doesn't Work
1. Use cPanel File Manager (browser-based)
2. Use cPanel Terminal (browser-based)
3. Contact hosting provider to enable SSH
4. Request SSH credentials

### Developer Contact
**Nhlanhla Mnyandu**
- Email: nhlanhla@isutech.co.za
- Company: ISU Tech

---

## 📚 Additional Resources

### SSH Command Cheat Sheet
```bash
# Connect
ssh user@host

# Connect with port
ssh -p 2222 user@host

# Connect with verbose output (debugging)
ssh -v user@host

# Copy file to server
scp local-file user@host:~/remote-path/

# Copy file from server
scp user@host:~/remote-file local-path/

# Copy directory
scp -r local-dir/ user@host:~/remote-path/

# SFTP connection
sftp user@host

# Execute command without login
ssh user@host "ls -la ~/public_html"

# Keep connection alive
ssh -o ServerAliveInterval=60 user@host
```

### Common Paths
```bash
# Home directory
cd ~
# Same as: /home/username

# Website root (common locations)
~/public_html/
~/public_html/umdoni.gov.za/
~/domains/umdoni.gov.za/public_html/

# Logs
~/logs/
~/public_html/logs/

# Backups
~/backups/
~/public_html/backups/

# Cron jobs
crontab -l
```

---

## ✅ Quick Start Checklist

Before attempting SSH:
- [ ] Know your cPanel username
- [ ] Know your cPanel password
- [ ] Know server address (reseller142.aserv.co.za)
- [ ] Know SSH port (usually 22, check with provider)
- [ ] Have terminal/command prompt open
- [ ] Files ready to upload (if deploying)

First SSH attempt:
- [ ] Run: `ssh username@reseller142.aserv.co.za`
- [ ] Accept fingerprint (first time)
- [ ] Enter password
- [ ] Verify correct server: `hostname`
- [ ] Find website directory: `pwd`, `ls`
- [ ] Test basic commands: `ls`, `cd`, `pwd`

---

**Guide Version:** 1.0
**Created:** December 8, 2025
**Last Updated:** December 8, 2025

---

*End of SSH Access Guide*
