# GitHub Secrets Setup for CI/CD

## Required Secrets

Add these secrets to your GitHub repository for automated deployment:

**Location:** Repository → Settings → Secrets and variables → Actions → New repository secret

### 1. SERVER_IP
- **Description:** Production server hostname or IP address
- **Example:** `srv1045082.hosting.com` or `123.45.67.89`
- **How to find:** Check your hosting provider dashboard or SSH connection command

### 2. SERVER_USER
- **Description:** SSH username for the production server
- **Example:** `root` or `sisaraya`
- **How to find:** Use the username you normally use to SSH into the server

### 3. SSH_PRIVATE_KEY
- **Description:** SSH private key for authentication (entire key including headers)
- **Format:**
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtzc2gtZW
...
(multiple lines)
...
-----END OPENSSH PRIVATE KEY-----
```

## How to Generate SSH Key Pair

### On Windows (PowerShell or Git Bash):

```powershell
# Open PowerShell or Git Bash
ssh-keygen -t ed25519 -C "github-actions@sisaraya" -f ~/.ssh/sisaraya_deploy

# When prompted:
# - Enter passphrase: Leave empty (press Enter)
# - Confirm passphrase: Leave empty (press Enter)
```

### On Linux/Mac (Terminal):

```bash
# Open Terminal
ssh-keygen -t ed25519 -C "github-actions@sisaraya" -f ~/.ssh/sisaraya_deploy

# When prompted:
# - Enter passphrase: Leave empty (press Enter)
# - Confirm passphrase: Leave empty (press Enter)
```

This will create two files:
- `~/.ssh/sisaraya_deploy` - Private key (for GitHub Secret)
- `~/.ssh/sisaraya_deploy.pub` - Public key (for server)

## Setup Steps

### Step 1: Copy Public Key to Server

**Option A: Using ssh-copy-id (Recommended):**
```bash
ssh-copy-id -i ~/.ssh/sisaraya_deploy.pub root@srv1045082
```

**Option B: Manual copy:**
```bash
# Display public key
cat ~/.ssh/sisaraya_deploy.pub

# SSH to server
ssh root@srv1045082

# Add to authorized_keys
echo "YOUR_PUBLIC_KEY_CONTENT" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
exit
```

### Step 2: Test SSH Connection

```bash
# Test connection with the new key
ssh -i ~/.ssh/sisaraya_deploy root@srv1045082

# If successful, you should be logged into the server
```

### Step 3: Copy Private Key

**Windows (PowerShell):**
```powershell
# Display private key
Get-Content ~/.ssh/sisaraya_deploy | Set-Clipboard
# Now paste into GitHub Secret
```

**Linux/Mac:**
```bash
# Display private key
cat ~/.ssh/sisaraya_deploy

# Copy the entire output (including headers)
```

### Step 4: Add Secrets to GitHub

1. Go to your GitHub repository: `https://github.com/Bhimonw/sisaraya-workspace`
2. Click **Settings** tab
3. Click **Secrets and variables** → **Actions** (left sidebar)
4. Click **New repository secret** button
5. Add each secret:

   **SERVER_IP:**
   - Name: `SERVER_IP`
   - Value: `srv1045082.hosting.com` (your server hostname/IP)
   - Click **Add secret**

   **SERVER_USER:**
   - Name: `SERVER_USER`
   - Value: `root` (your SSH username)
   - Click **Add secret**

   **SSH_PRIVATE_KEY:**
   - Name: `SSH_PRIVATE_KEY`
   - Value: Paste entire private key including headers
   - Click **Add secret**

## Verification

### Check Secrets Are Added

1. Go to Repository → Settings → Secrets and variables → Actions
2. You should see all secrets listed (values are hidden)
3. Secrets should show:
   - ✅ SERVER_IP
   - ✅ SERVER_USER
   - ✅ SSH_PRIVATE_KEY

### Test Deployment

**Option 1: Push to main branch**
```bash
git push origin main
```

**Option 2: Manual trigger**
1. Go to Actions tab
2. Click "Deploy to Production" workflow
3. Click "Run workflow"
4. Select `main` branch
5. Click "Run workflow" button

### Monitor Deployment

1. Go to Actions tab
2. Click on the running workflow
3. Click on "deploy" job
4. Watch the logs for each step

## Troubleshooting

### Error: Permission denied (publickey)

**Cause:** Public key not added to server or wrong key format

**Solution:**
1. Verify public key is in server's `~/.ssh/authorized_keys`
2. Check file permissions on server:
   ```bash
   chmod 700 ~/.ssh
   chmod 600 ~/.ssh/authorized_keys
   ```

### Error: Host key verification failed

**Cause:** Server's host key not in known_hosts

**Solution:** Add to workflow (already included in `.github/workflows/deploy.yml`):
```yaml
script: |
  ssh-keyscan -H ${{ secrets.SSH_HOST }} >> ~/.ssh/known_hosts
  # ... rest of script
```

### Error: Connection timeout

**Cause:** Wrong hostname, port, or firewall blocking

**Solution:**
1. Verify `SERVER_IP` secret is correct
2. Check server firewall allows GitHub Actions IPs

### Error: bash: line 1: cd: ~/projects/sisaraya-workspace: No such file or directory

**Cause:** Project directory doesn't exist on server

**Solution:** SSH to server and clone repository:
```bash
ssh root@srv1045082
mkdir -p ~/projects
cd ~/projects
git clone https://github.com/Bhimonw/sisaraya-workspace.git
```

## Security Best Practices

1. ✅ **Never commit private keys** to repository
2. ✅ **Use separate SSH key** for GitHub Actions (don't reuse personal key)
3. ✅ **Rotate keys regularly** (every 3-6 months)
4. ✅ **Use strong passphrase** for personal keys (but not for CI/CD keys)
5. ✅ **Limit key permissions** on server (only necessary directories)
6. ✅ **Enable 2FA** on GitHub account
7. ✅ **Review deployment logs** regularly

## Key Rotation

When rotating keys (recommended every 3-6 months):

1. Generate new key pair
2. Add new public key to server
3. Update `SSH_PRIVATE_KEY` secret in GitHub
4. Test deployment
5. Remove old public key from server
6. Delete old private key from local machine

## Support

If you need help:
1. Check workflow logs in GitHub Actions
2. Review this documentation
3. Test SSH connection manually
4. Contact DevOps team

---

**Last Updated:** 2025-10-22  
**Document Version:** 1.0
