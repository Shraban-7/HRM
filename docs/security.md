# Security and Privacy

HR systems contain personal, employment, attendance, and compensation data. Apply least privilege throughout the request and background-job paths.

## Application controls

- Authentication routes and protected routes are declared in `routes/web.php`; custom session authentication is implemented under `app/Services/Auth/` and `app/Middleware/`.
- Authorization uses role/permission middleware and policies. Check access on every server-side action, including downloads, exports, and asynchronous jobs.
- Login, registration, and password reset POST routes apply Laravel throttling. Preserve throttles when modifying those endpoints.
- Request validation should constrain accepted fields and formats. Use mass-assignment allowlists on models and escape output through Blade's normal escaped rendering.
- Security headers and audit logging are implemented in custom middleware. Keep audit records useful for investigations while excluding passwords, tokens, and unnecessary sensitive values.
- Database-backed sessions, queue, and cache are development defaults. Production operators should configure secure storage, backups, access control, TLS, and queue supervision.

## Sensitive data handling

- Do not place credentials, real employee data, or production configuration in source control, test fixtures, logs, screenshots, or documentation.
- Restrict access to bank details, compensation, health/benefit records, and employee identifiers. Encrypt data at rest where the deployment's threat model and applicable requirements call for it; protect encryption keys outside the database and repository.
- Use HTTPS in production and set `APP_DEBUG=false`. Keep `APP_KEY` secret and back it up securely; rotating it can invalidate encrypted data and sessions.
- Use least-privilege database and mail credentials. Avoid using demo credentials or seeded sample accounts in deployed environments.
- Define retention and deletion rules for employee records, applicant data, audit logs, and backups according to organizational policy and applicable law.

## Operational review

Before production use, review current route authorization, role/permission seed data, file upload validation and storage visibility, queue retry behavior, backup restoration, audit retention, and dependency updates. This guide is engineering guidance, not a statement of legal or regulatory compliance.
