# 5th Brew — Final Project (CCS0043)

Coffee e-commerce site. Plain procedural PHP + MySQLi. No frameworks.

## Structure
```
fifthbrew/
├── database.sql              <- import this first
├── config/db.php             <- EDIT with your host's DB credentials
├── includes/                 <- buyer session, header, footer, functions
├── admin/                    <- seller side (login, dashboard, manage_users,
│                                 manage_stocks, reports, setup_admin)
├── index.php, register.php, verify.php, login.php, logout.php
├── store.php, cart.php, checkout.php, payment.php, about.php
└── assets/logo.png
```

## Requirement coverage
- **Seller part**: admin/manage_users.php (add/edit admins), admin/manage_stocks.php
  (add products, edit price/stock), admin/reports.php (inventory + audit log
  filtered to the currently logged-in admin).
- **Buyer part**: register.php (full name, email, password+confirm, address,
  contact number, sends confirmation email via verify.php), store.php
  (categorized products, add to cart), cart.php, checkout.php, payment.php
  (no payment API — simulation only), about.php.
- Footer disclaimer + group name/logo appear on every page (includes/footer.php
  and admin/includes/admin_footer.php, includes/header.php).

## Deploying to a free host (InfinityFree example)
1. **GitHub**: push this whole folder to your repo (for submission #1).
2. **Sign up** at infinityfree.net, create a hosting order — you'll get an
   FTP login and a MySQL panel.
3. **Create the database**: hosting panel → MySQL Databases → create one.
   Note the DB host (often `sqlXXX.infinityfree.com`, not `localhost`),
   DB name, username, password.
4. **Import the schema**: open phpMyAdmin from the panel → Import →
   upload `database.sql`.
5. **Edit `config/db.php`**: fill in the 4 values from step 3.
6. **Upload via FTP** (FileZilla): upload everything inside this `fifthbrew`
   folder into `htdocs/` (upload the contents, not the folder itself).
7. **Create your first admin**: visit `https://yoursite.com/admin/setup_admin.php`,
   fill the form once, then delete that file from the server via FTP.
8. **Test registration email**: InfinityFree's `mail()` support can be
   unreliable — if the confirmation email doesn't arrive, use the manual
   SQL verification command in `sample_accounts.txt` and note it in your
   submission PDF.
9. Submit: GitHub repo link + live hosted URL (as separate links) +
   screenshots PDF + `sample_accounts.txt`.

## Notes
- No CSS yet — plain HTML structure only, per current project stage.
- Uses `mysqli_*` (the modern successor to the removed `mysql_*` extension
  shown in older course material) with the exact procedural style and
  `validate_email()` regex pattern from the M5 slides.
