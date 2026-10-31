# Known Limitations (end of Phase 2)

1. **Learning features are not built yet.** The AI Tutor, practice, exams, study plans and payments come in Phases 3–9. The student dashboard shows "Coming soon" cards rather than invented numbers.
2. **The homepage describes the planned product.** Review the wording before public launch so it matches what is live.
3. **Legal pages are drafts.** All five carry a visible "Draft for legal review" notice. Bracketed sections need decisions from the business and counsel.
4. **Two-step login is built but off in development.** It turns on automatically in production. It uses authenticator apps only; SMS codes aren't offered, because SMS is weaker and costs money per message.
5. **Guardian–student linking, and student accounts without an email address**, need the institution and guardian workflows in Phase 8. Guardians can register and onboard, but can't link to a child yet.
6. **School and CBT centre verification is manual.** Onboarding records the organisation; nothing institutional is switched on until Phase 8.
6. **Email verification is on**, but local mail is written to the log file. A real mail provider is needed before launch.
7. **The logo is a draft SVG.** Final artwork, trademark and domain checks are outstanding.
8. **Source control:** Git is set up, and the code is on GitHub at https://github.com/engrmarcel/edudev (branch `main`).
9. **No browser or visual tests yet.** Pages were checked by HTTP status, not screenshots.
10. **Mail and social links are placeholders** (`support@example.com`). Set the real values in Admin → Branding.
