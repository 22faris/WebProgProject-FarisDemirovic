# AI assistance log

## Milestone 1 review fixes — 2026-10-25

### Tools and contributions

- **OpenAI Codex (GPT-6)** was used to inspect the repository, read the professor review request, inspect Git status and existing frontend files, add the login and registration SPA views, extend routing and form initialization, write the root `AGENTS.md`, and prepare this log.
- Shell tools (`git`, PowerShell, `rg`, and `node --check`) were used through Codex to inspect project state and verify files. No external AI service or authentication backend was used.

### Important prompts

- The supplied task requested preserving the existing SPA, creating static login/register screens, documenting actual architecture and planned backend technologies in `AGENTS.md`, maintaining this log, verifying changes, and providing safe PR instructions without merging.
- The prior project debugging request asked Codex to inspect the real files and Git changes, diagnose the SPA view-loading issue, make minimal fixes, and report verification accurately.

### AI mistakes and corrections

1. During earlier repository inspection, Codex issued an `rg` command with malformed regular-expression quoting. `rg` returned a parse error instead of searching. This was recognized as a command construction error; subsequent checks used simpler search patterns and direct file/Git inspection. It did not modify project files.
2. During this review, Codex attempted to pass the `backend` directory itself to PowerShell `Get-Content`, which returned an access error because the path is a directory. The directory was then inspected with `Get-ChildItem`; no backend source files were found, only the reserved directories/placeholders. This did not modify project files.
3. A later PowerShell search command used double quotes around patterns containing escaped double quotes. PowerShell interpreted the inner quotes as syntax and attempted to run words from the pattern as commands. The search did not run; the command needs PowerShell single-quoted patterns (or a here-string) and was not needed for the implementation.

These examples document command/tool output mistakes, not application defects. No authentication implementation or backend behavior was generated for this milestone.
