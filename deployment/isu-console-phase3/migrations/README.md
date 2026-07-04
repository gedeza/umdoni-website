# Migrations

Pre-approved SQL migration files for the ISU console's migration runner
(`/isu/database`). Rules:

- One logical change per file. Name them sortably, e.g.
  `2026-07-10-0001-add-sla-table.sql`.
- Plain SQL statements separated by `;`. Line comments (`-- ...` / `# ...`)
  are stripped. No `DELIMITER` / stored-procedure blocks.
- Files are committed to the repo (reviewed like code) and deployed with the
  rest of the app. The runner executes each file **once** and records it in
  `isu_migrations`.
- MySQL auto-commits DDL, so a failed multi-statement migration can leave
  partial schema changes. Keep migrations small; on failure the runner reports
  which statement failed and does **not** mark the file as run, so you can fix
  and re-run.

There is deliberately **no free-form SQL** in the console — only files here.
