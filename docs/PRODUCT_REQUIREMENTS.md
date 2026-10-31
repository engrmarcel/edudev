> **Source of truth.** This is the full product brief as supplied by the product owner on 2026-10-31 (copied from the "EduDEV — Full Product Brief" doc). Do not edit the requirements here without the product owner's agreement; record deviations in IMPLEMENTATION_ROADMAP.md.

# CLAUDE CODE MASTER DEVELOPMENT PROMPT

# EduDEV — AI-Powered Education & Learning Ecosystem

### A MacoDev Limited Product

**Product:** EduDEV
**Flagship module:** EduDEV AI Tutor
**Parent company:** MacoDev Limited
**Product category:** AI EdTech SaaS, Adaptive Learning, Examination Preparation, CBT and School Learning Management
**Initial market:** Nigeria
**Expansion market:** Africa and international markets
**Primary slogan:** Learn Smarter. Achieve More.
**Product maturity target:** Production-ready, secure, commercially viable, subscription-based platform
**Implementation approach:** Audit first, plan second, build incrementally, test continuously, document everything.

# 1. YOUR ROLE AND MISSION

Act as a principal software architect, senior Laravel engineer, AI systems engineer, database architect, UI/UX designer, QA automation engineer, cybersecurity engineer, SaaS product manager, and DevOps engineer.

Your task is to inspect, architect, develop, test, document, and prepare for deployment a professional, scalable, premium AI-powered education platform named **EduDEV**, owned by MacoDev Limited.

EduDEV must help students understand academic concepts, solve questions with explanations, prepare for examinations, practise under timed conditions, identify learning weaknesses, and follow personalised study plans.

The platform must also provide subscription-based services to schools, teachers, parents or guardians, tutors, and licensed CBT centres.

This is a commercial software product, not a disposable prototype.

Build for real users, real payments, real educational workflows, controlled AI costs, secure student records, operational monitoring, and long-term maintainability.

## Non-negotiable development rules

1. Begin by inspecting the existing repository. Never assume it is empty.
2. Preserve existing working features unless a documented reason justifies changing them.
3. Do not overwrite, delete, or recreate an existing project merely to simplify implementation.
4. If this is an existing application, audit its architecture, database, authentication, routes, dependencies, integrations, and tests before making changes.
5. If no application exists, establish the architecture and initialise the project using the recommended stack.
6. Produce a written implementation roadmap before implementing major features.
7. Build in small, verifiable phases with acceptance criteria.
8. Use database migrations as the source of truth for schema changes.
9. Maintain a complete SQL database export and document how to regenerate it.
10. Never claim a feature is implemented or tested unless you have actually implemented or tested it.
11. Never hard-code secret keys, payment credentials, AI credentials, permanent brand assumptions, or environment-specific configuration.
12. Never expose AI provider keys, payment secrets, privileged operations, or sensitive student information in the browser.
13. Use real integrations where credentials and access are available. Otherwise, implement clearly labelled development adapters and document the remaining configuration.
14. Do not use fake data to represent successful production payments, completed AI operations, successful email delivery, or verified examination results.
15. Do not implement the entire platform in one enormous, untested change. Follow the phased roadmap.
16. Explain important technical decisions in plain English because the product owner may not be a professional programmer.
17. Prioritise a reliable, sellable MVP before implementing advanced AI features that are expensive, risky, or difficult to maintain.
18. Do not stop after producing plans and documentation. Proceed with implementation phase by phase, subject to the available execution environment and any genuine blockers.
# 2. PRODUCT VISION

EduDEV is a premium, AI-powered education ecosystem designed to improve learning outcomes and make quality academic support more accessible.

Its initial focus is the Nigerian education market, including:

- WAEC preparation.
- NECO preparation.
- JAMB/UTME preparation.
- Junior and senior secondary school learning.
- General subject tutoring.
- School assignments and revision.
- CBT mock examinations.
- Personalised study plans.
- Teacher-led learning and school administration.
The architecture must permit future expansion into additional curricula, countries, languages, educational institutions, professional examinations, and international markets.

## Core value proposition

EduDEV enables a learner to ask a question, upload an image of a question, understand the solution, practise related questions, track their progress, and receive recommendations about what to study next.

The system should support learning rather than merely giving answers.

## Educational interaction modes

Implement the following tutor modes:

1. **Teach Me:** Explain a concept from foundational principles.
2. **Guide Me:** Provide hints and progressively reveal steps.
3. **Solve It:** Provide a worked solution with reasoning and verification.
4. **Explain Simply:** Use accessible language and age-appropriate examples.
5. **Exam Mode:** Help the student practise under examination conditions.
6. **Quiz Me:** Generate or select relevant practice questions.
7. **Review My Mistakes:** Analyse incorrect answers and explain misconceptions.
8. **Study Plan:** Recommend a learning sequence based on goals, available time, and demonstrated mastery.
For each mode, establish clear behavioural instructions, validation rules, usage limits, and appropriate AI prompts.

# 3. BRAND IDENTITY AND CONFIGURATION

Use the following brand direction.

## 3.1 Official working identity

- Product name: EduDEV
- Parent company: MacoDev Limited
- Flagship module: EduDEV AI Tutor
- Proposed slogan: Learn Smarter. Achieve More.
- Product description: AI-Powered Learning Platform
- Initial positioning: Premium AI education and examination preparation platform for Nigerian learners and institutions.
The name and identity are approved as the current working direction, but must remain configurable until all applicable domain, company-name, and trademark checks have been completed.

Do not represent legal availability as verified unless documentary evidence is supplied.

## 3.2 Colour system

Use these brand colours consistently.

| Purpose | Colour | Hex |
| --- | --- | --- |
| Primary | Deep Navy | `#0B2545` |
| Secondary | Education Green | `#13A987` |
| Accent | Achievement Gold | `#F2A541` |
| Main background | White | `#FFFFFF` |
| Alternate background | Light neutral | `#F5F7FA` |
| Main text | Dark slate | `#1F2937` |
| Secondary text | Muted slate | `#64748B` |

Define semantic design tokens for primary, secondary, accent, success, warning, danger, information, background, foreground, borders, focus states, and disabled states.

Check colour contrast and keyboard focus visibility. Do not use colour as the only means of communicating status.

## 3.3 Branding requirements

Create a central branding and appearance configuration that controls:

- Product name.
- Parent company name.
- Logo and favicon.
- Slogan.
- Brand colours.
- Contact information.
- Support email.
- Public website URL.
- Terms and privacy policy links.
- Social media links.
- Default locale and timezone.
- Currency and payment settings.
- Optional footer attribution.
- Institution-specific branding where licensed.
- White-label branding options, if enabled by the subscription plan.
Brand configuration must be editable by authorised administrators.

Avoid hard-coding the product name throughout templates, emails, page titles, and API responses.

The default application name is EduDEV, but it must be possible to update the branding without rewriting application code.

Generate or prepare professional logo assets where supported by the development environment. If genuine final assets are unavailable, create a documented placeholder or SVG-based draft that can be replaced later.

Do not use a placeholder logo that imitates another company's trademark.

# 4. RECOMMENDED TECHNICAL STACK

First inspect the existing project and preserve its established stack when technically sound.

For a new implementation, use the following preferred architecture.

## 4.1 Core stack

- Backend: Laravel, using a current stable release compatible with the environment.
- Frontend: Laravel Livewire.
- UI framework: Tailwind CSS.
- Database: MySQL.
- Authentication: Laravel-supported authentication.
- Authorisation: Laravel Policies and Gates, with a maintained roles-and-permissions package if appropriate.
- Background processing: Laravel Queues.
- Cache: Laravel cache, with Redis when available.
- File storage: Laravel Storage abstraction, local in development and private S3-compatible storage in production.
- Testing: Pest or PHPUnit, with browser-level testing where available.
- API: Laravel API routes and versioned endpoints where external integrations or mobile clients require them.
- API authentication: Laravel Sanctum where appropriate.
- Frontend build: Vite.
- Deployment: A documented environment-appropriate deployment workflow.
Use Composer and npm dependencies responsibly. Check compatibility before introducing packages.

## 4.2 Development and production environments

Support local development on Windows/XAMPP where compatible with the chosen Laravel version.

Also document a recommended production deployment using a supported PHP hosting environment, VPS, or managed cloud infrastructure.

Do not assume that shared hosting supports Redis, persistent workers, unrestricted scheduled tasks, WebSockets, or every required PHP extension.

Where a hosting environment lacks a capability, provide a supported alternative and document its limitations.

## 4.3 Mobile architecture

Design the backend and service layer so that a dedicated Flutter mobile application can be added without duplicating business logic.

The first release should prioritise a responsive web experience.

Provide API contracts and authentication patterns that support future Android and iOS clients.

Do not attempt to build a separate mobile application before the core web platform is stable unless the existing repository already includes a mobile project.

## 4.4 Architecture principles

- Modular monolith initially.
- Clear domain boundaries.
- Reusable services and components.
- Dependency injection.
- Form Requests or equivalent validation boundaries.
- Policies for resource access.
- Events and listeners where they provide genuine value.
- Queue-based execution for expensive or slow operations.
- Database transactions for related financial or academic state changes.
- Explicit API versioning when public APIs are introduced.
- Centralised error handling and structured logging.
- Configuration-driven integrations.
- Avoid unnecessary microservices during the MVP.
Do not introduce architectural complexity solely to appear enterprise-ready.

# 5. REQUIRED PRODUCT MODULES

Design the platform as an integrated product with the following modules.

## Module A: Public website and acquisition

Build a professional public website with:

- Homepage.
- Product features.
- AI Tutor overview.
- WAEC, NECO and JAMB preparation pages.
- School and institutional offerings.
- CBT centre offerings.
- Pricing and subscription plans.
- How it works.
- Frequently asked questions.
- Contact and support.
- Privacy policy.
- Terms of service.
- Acceptable use policy.
- Refund and cancellation policy.
- Parent/guardian information.
- Educational safety and AI limitations.
- Sign-up and login.
- SEO metadata and sitemap.
- Analytics integration controlled by privacy settings.
Homepage messaging should communicate educational value rather than promising guaranteed examination success.

## Module B: Authentication and onboarding

Implement:

- Registration.
- Login and logout.
- Email verification where configured.
- Password reset.
- Session management.
- Optional social login through explicitly configured providers.
- Rate limiting.
- Secure account recovery.
- Role-aware redirects.
- Terms acceptance and recorded policy versions.
- Account suspension and reactivation workflows.
- Account deletion requests.
- Optional multi-factor authentication for administrators and institutional users.
Support appropriate onboarding flows for:

- Students.
- Teachers.
- Parents or guardians.
- Schools.
- CBT centres.
- Tutors.
- Platform administrators.
Collect only information needed for the relevant account type.

Do not assume every student has an email address or personal payment card. Design suitable guardian-sponsored or institution-sponsored account workflows.

## Module C: Student dashboard

The dashboard should display:

- Welcome message.
- Current learning goals.
- Selected curriculum and subjects.
- Recent tutor sessions.
- Daily or weekly study activity.
- Upcoming study tasks.
- Practice questions completed.
- Recent mock-exam results.
- Subject-level progress.
- Weak topics.
- Recommended next lessons.
- Current subscription and usage.
- Notifications.
- Saved explanations and bookmarks.
All statistics must come from actual application records.

Provide meaningful empty states for new learners.

## Module D: EduDEV AI Tutor

This is the flagship module.

Implement:

- Text-based tutoring.
- Image upload for questions.
- OCR or vision-based extraction of question content.
- Subject and topic classification.
- Step-by-step explanations.
- Follow-up questions.
- Conversation history.
- Saved conversations.
- Bookmarks.
- Feedback on AI responses.
- Report-an-answer functionality.
- Practice question generation.
- Explanation difficulty selection.
- Tutor mode selection.
- Usage limits and subscription checks.
- AI request status and failure recovery.
### Image-question workflow

1. Student uploads an image.
2. Validate file type, file size, image dimensions, and account permissions.
3. Scan uploaded content where supported.
4. Store the image securely.
5. Extract the text and relevant mathematical notation using an appropriate OCR or vision provider.
6. Display extracted content for confirmation when confidence is low.
7. Identify the subject and likely topic.
8. Generate an educational explanation.
9. Validate the output format and applicable safety rules.
10. Store the conversation and usage record.
11. Offer related practice questions.
12. Allow the student to report an inaccurate answer.
Support diagrams and mathematical notation where the selected provider can handle them.

Do not claim perfect OCR or guaranteed correctness.

### Tutor response structure

Where suitable, responses should contain:

1. Question interpretation.
2. Relevant concept or formula.
3. Step-by-step solution.
4. Explanation of why each step is taken.
5. Final answer.
6. Verification or reasonableness check.
7. Common mistake to avoid.
8. One or more related practice questions.
9. Source references when reliable curriculum content is retrieved.
For subjective questions, essays, language learning, or questions where this structure is unsuitable, use a more appropriate response format.

### AI correctness and grounding

The AI Tutor must not blindly invent curriculum facts, examination rules, official marking schemes, or past-paper sources.

Use a retrieval-augmented generation architecture where appropriate:

- Approved educational resources.
- Curriculum and syllabus content.
- Admin-managed learning materials.
- Licensed question banks.
- Institution-approved content.
- Relevant source metadata.
- Retrieval ranking and relevance thresholds.
- Source citations when the underlying material supports them.
Record content provenance and licensing information.

If authoritative content is unavailable, the tutor should communicate uncertainty rather than inventing citations.

# 6. AI PROVIDER ARCHITECTURE AND COST CONTROL

Create an AI provider abstraction so the platform is not permanently tied to one model provider.

## 6.1 Provider interface

Define a consistent service contract for supported operations such as:

- Text tutoring.
- Image or multimodal analysis.
- Structured output generation.
- Embedding generation where required.
- Content classification.
- Question generation.
- Response evaluation where justified.
Create provider adapters for the providers actually selected and configured.

Potential providers include OpenAI, Anthropic, and Google, subject to current capabilities, pricing, terms, and available credentials.

Do not assume all providers support identical image inputs, structured outputs, token limits, or capabilities.

## 6.2 Model selection

Allow authorised administrators to configure:

- Default provider.
- Default model.
- Provider-specific model selection.
- Fallback model.
- Supported operations.
- Maximum output tokens.
- Timeout limits.
- Retry policy.
- Cost limits.
- Feature-level model selection.
Never expose secrets through frontend configuration.

## 6.3 Usage metering

Track AI usage by:

- User.
- Institution.
- Subscription.
- Feature.
- Provider.
- Model.
- Request.
- Input tokens or equivalent provider usage.
- Output tokens or equivalent provider usage.
- Image or other billed units where available.
- Estimated cost.
- Actual cost where reported.
- Request status.
- Latency.
- Timestamp.
Use idempotency and unique request identifiers where appropriate.

Prevent users from bypassing usage limits through concurrent requests, repeated retries, or manipulated client-side state.

## 6.4 AI reliability

Implement:

- Request timeouts.
- Controlled retries.
- Exponential backoff where appropriate.
- Provider error normalisation.
- Graceful failure messages.
- Queue processing for long-running operations.
- Duplicate request protection.
- Usage reservation or other safe quota accounting.
- Cancellation where supported.
- AI service health monitoring.
- Configurable spending alerts.
Do not charge for a failed request unless the configured policy and actual provider cost justify the charge.

Do not promise unlimited AI usage without a sustainable cost model.

# 7. CURRICULUM AND EDUCATIONAL CONTENT MANAGEMENT

Create an admin-managed curriculum structure.

Suggested hierarchy:

- Country.
- Education system.
- Examination or curriculum.
- Academic level.
- Class or year.
- Subject.
- Topic.
- Subtopic.
- Learning objective.
- Lesson or explanatory resource.
- Associated practice questions.
Support initial Nigerian curricula and examinations, including WAEC, NECO and JAMB/UTME.

Allow administrators to add additional curricula without changing the database schema.

Support subject-specific metadata and configurable academic terminology.

## Content management requirements

- Curriculum creation and editing.
- Topic hierarchy management.
- Learning resource uploads.
- Draft, review, published, archived states.
- Content versioning where useful.
- Author and reviewer metadata.
- Approval workflow.
- Search and filtering.
- Source references.
- Licence and usage-rights metadata.
- Import and export tools.
- Change history for important content.
- Visibility controls.
- Content status monitoring.
Do not seed copyrighted past examination papers or proprietary textbooks unless the relevant rights permit their use.

Use original demonstration questions or verified, appropriately licensed materials for initial test data.

# 8. QUESTION BANK AND PRACTICE ENGINE

Build a reusable question bank.

Each question should support relevant fields such as:

- Subject.
- Topic and subtopic.
- Curriculum.
- Academic level.
- Examination type.
- Question type.
- Question text.
- Optional image or diagram.
- Answer choices.
- Correct answer or accepted answers.
- Explanation.
- Difficulty level.
- Marks.
- Estimated completion time.
- Source and licensing metadata.
- Review status.
- Author and reviewer.
- Publication status.
Support multiple-choice, true/false, short-answer, structured-answer, and other question types where appropriate.

Use a flexible schema for question types without making every field unstructured JSON.

## Practice engine features

- Topic-based practice.
- Subject-based practice.
- Difficulty-based practice.
- Mixed quizzes.
- Timed practice.
- Untimed learning mode.
- Immediate feedback.
- Delayed feedback.
- Explanations.
- Retry incorrect questions.
- Bookmarks.
- Practice history.
- Progress statistics.
- Adaptive question selection.
- Question quality reporting.
Question selection must be explainable and testable.

Prevent students from viewing protected answer keys or unpublished question data before the configured reveal time.

# 9. MASTERY, WEAKNESS DETECTION AND PERSONAL STUDY PLANS

Develop a transparent learning analytics service.

Do not treat a single incorrect answer as proof that a student has permanently failed to understand a topic.

Use multiple observations and account for the quality and quantity of available evidence.

## 9.1 Mastery tracking

Track relevant signals such as:

- Attempts.
- Correct answers.
- Incorrect answers.
- Time spent.
- Difficulty level.
- Repeated errors.
- Hint usage.
- Improvement over time.
- Confidence feedback, when available.
- Recency of practice.
Start with a simple, explainable scoring algorithm.

Keep the algorithm configurable and versioned so it can be improved later.

Do not claim that a heuristic score is a scientifically validated measure of knowledge.

## 9.2 Weakness identification

Identify topics where evidence indicates that the learner may need additional practice.

Display:

- Topic.
- Evidence summary.
- Recent performance.
- Recommended revision.
- Suggested practice.
- Confidence or evidence sufficiency.
Avoid discouraging labels such as “poor student.”

Use supportive wording, for example, “This topic may benefit from more practice.”

## 9.3 Personalised study plans

Allow students to specify:

- Examination goal.
- Target examination date.
- Subjects.
- Available study days.
- Approximate daily study time.
- Priority topics.
- Preferred learning mode.
Generate plans using curriculum structures, learning history, and available time.

Support:

- Daily tasks.
- Weekly goals.
- Completion tracking.
- Missed-task recovery.
- Plan adjustments.
- Progress reviews.
- Reminders.
- Study streaks where appropriate.
Do not penalise learners excessively for missed sessions or use manipulative engagement mechanics.

# 10. CBT MOCK EXAMINATION SYSTEM

Develop a robust computer-based testing module for individual students, schools, and authorised CBT centres.

## Features

- Exam creation.
- Exam instructions.
- Exam categories.
- Subject and topic selection.
- Duration.
- Question count.
- Marks and scoring rules.
- Randomisation where appropriate.
- Scheduled examinations.
- Access codes.
- Candidate enrolment.
- Timed exam interface.
- Question navigation.
- Answer selection.
- Autosave.
- Submission.
- Automatic scoring for supported objective questions.
- Manual marking for applicable subjective questions.
- Results and review.
- Performance analysis.
- Result exports.
- Exam attempt history.
- Institution-level reporting.
## Examination integrity

- Enforce time limits on the server.
- Do not rely exclusively on browser timers.
- Protect answer keys.
- Validate all submissions server-side.
- Prevent duplicate submissions.
- Use database transactions for submission and scoring.
- Handle network interruptions gracefully.
- Make autosave and recovery behaviour explicit.
- Log significant exam events without invasive or unjustified surveillance.
- Implement appropriate access controls for exam authors and candidates.
Do not advertise proctoring, plagiarism detection, or sophisticated cheating prevention unless those capabilities have genuinely been implemented and evaluated.

## Scoring

Define scoring rules explicitly.

Support configurable negative marking only when enabled for the particular exam.

Store raw answers, marks awarded, scoring-rule versions, and result status so results can be audited.

Allow authorised reviewers to correct manual marks with a recorded reason and audit trail.

# 11. TEACHER, SCHOOL AND CBT CENTRE PORTALS

## 11.1 Teacher portal

Provide:

- Teacher profile.
- Assigned classes.
- Student lists.
- Learning material management.
- Assignment creation.
- Quiz creation.
- Assignment deadlines.
- Student submission review.
- Manual marking where applicable.
- Class-level performance analytics.
- Subject-level progress.
- Weak-topic summaries.
- Student feedback.
- Announcements.
- Teacher access permissions.
Teachers must only access the students and classes they are authorised to manage.

## 11.2 School portal

Support institutional subscriptions and multi-user management.

Features:

- School profile.
- School administrator.
- Teacher invitations.
- Class and subject management.
- Student enrolment.
- Bulk student import.
- Seat allocation.
- Subscription status.
- Usage and cost reports.
- Learning analytics.
- Mock exam management.
- Institutional announcements.
- Data export.
- Staff permissions.
- Audit history.
- Renewal and billing records.
Implement tenant isolation where institutions have separate data boundaries.

Every institution-owned query and resource operation must enforce tenant scope server-side.

## 11.3 CBT centre portal

Support:

- CBT centre registration.
- Centre verification.
- Centre administrators.
- Candidate management.
- Mock exam scheduling.
- Exam sessions.
- Candidate access codes.
- Result reporting.
- Licence and subscription management.
- Usage monitoring.
- Audit logs.
Distinguish EduDEV practice exams from official examinations administered by external examination bodies.

Do not imply official WAEC, NECO or JAMB affiliation or endorsement without documented authorisation.

## 11.4 Parents and guardians

Where appropriate, allow authorised guardians to:

- Link to a student's account through a controlled consent process.
- View permitted progress summaries.
- Manage sponsored subscriptions.
- Receive selected learning updates.
- Manage communication preferences.
- Submit account or privacy requests.
Prevent unauthorised access to student conversations and sensitive educational records.

Provide age-appropriate privacy controls and account workflows for minors.

# 12. SAAS SUBSCRIPTIONS, PAYMENTS AND REVENUE

EduDEV must support recurring revenue from individual learners and institutions.

Create a configurable commercial system rather than hard-coding prices into application logic.

## 12.1 Subscription types

Support plans for:

- Free users.
- Individual learners.
- Premium AI Tutor users.
- Examination preparation users.
- Schools.
- Teachers or tutors where appropriate.
- CBT centres.
- Enterprise customers.
The product owner must be able to create, edit, activate, deactivate, and retire plans.

## 12.2 Subscription features

- Plan name and description.
- Billing interval.
- Currency.
- Price.
- Trial configuration.
- Usage limits.
- Feature entitlements.
- Student or seat limits.
- AI usage allowances.
- Renewal status.
- Upgrade and downgrade.
- Cancellation.
- Grace period.
- Expiry.
- Invoice records.
- Payment history.
- Receipts.
- Refund tracking.
- Institutional billing.
- Sponsored accounts.
Example initial pricing for commercial validation—not final pricing:

| Customer | Illustrative pricing |
| --- | --- |
| Free learner | ₦0 |
| Individual learner | ₦1,000–₦3,000/month |
| Premium learner | Configurable |
| School | Annual or per-seat pricing |
| CBT centre | Licence or usage-based pricing |

Do not hard-code these example amounts as permanent commercial policy.

Allow pricing in NGN initially and support additional currencies when needed.

## 12.3 Payment architecture

Create a payment-provider abstraction.

Select and integrate an appropriate provider, potentially Paystack or Flutterwave, based on current supported recurring-billing capabilities, fees, business requirements, and account availability.

Implement:

- Checkout initiation.
- Server-side payment verification.
- Signed webhook verification.
- Idempotent webhook handling.
- Transaction records.
- Subscription activation.
- Renewal handling.
- Failed payment handling.
- Cancellation.
- Refund recording.
- Reconciliation tools.
- Payment status history.
- Invoice or receipt generation.
Never activate a paid subscription solely because a browser redirects to a success page.

Never store raw payment card details.

Keep test and live environments separate.

If provider credentials are unavailable, create a sandbox adapter and document the exact steps required to complete the live integration.

# 13. SUPER ADMIN AND PLATFORM OPERATIONS

Build a protected Super Admin dashboard.

The dashboard must include:

## Platform overview

- Total registered users.
- Active users.
- New registrations.
- Active subscriptions.
- Revenue and payment summaries.
- Failed payments.
- Subscription renewals.
- AI usage.
- Estimated AI expenditure.
- AI service failures.
- Active schools.
- Active CBT centres.
- Question bank size.
- Mock examination activity.
- Storage usage.
- Operational health indicators.
Use actual database-backed data. Clearly identify estimates and reporting periods.

## User management

- Search users.
- Filter by role and status.
- View account details subject to permissions.
- Suspend and reactivate accounts.
- Manage roles.
- Review account-related audit history.
- Process account deletion requests.
- Inspect usage and subscription status.
Do not provide unrestricted access to private student conversations as a routine administrative feature.

Where support access to sensitive data is necessary, require authorised permissions, a legitimate purpose, audit logging, and suitable disclosure and privacy safeguards.

## Administration modules

- Curriculum management.
- Question bank management.
- Learning resource management.
- AI provider settings.
- AI usage limits.
- Subscription plans.
- Payment configuration.
- Schools.
- CBT centres.
- Teachers and tutors.
- Notifications.
- Support tickets.
- Feature flags.
- Brand configuration.
- System settings.
- Security audit logs.
- Data export.
- Backup monitoring.
- Failed job monitoring.
- Application health.
## Administrative safeguards

- Enforce least privilege.
- Require stronger authentication for privileged roles where possible.
- Log sensitive administrative changes.
- Prevent unauthorised role escalation.
- Apply Policies and server-side permission checks.
- Protect administrative routes from direct access by ordinary users.
# 14. DATABASE ARCHITECTURE AND REQUIRED SQL EXPORT

Design a relational MySQL database that supports transactional integrity, reporting, tenant isolation, and future growth.

Do not attempt to create every table before analysing the repository and finalising the domain model.

Produce an ERD and document relationships, cardinality, indexes, constraints, and important business rules.

The following are domain groups, not a requirement to use these exact table names if an existing schema already provides sound equivalents.

## 14.1 Identity and access

Potential tables:

- users
- user_profiles
- roles and permissions, or package-managed equivalents
- user_role assignments
- sessions, where database-backed sessions are used
- account_verifications
- policy_acceptances
- user_preferences
- guardian_student_links
- audit_logs
Avoid storing redundant profile information across multiple tables.

## 14.2 Institutions and tenancy

Potential tables:

- institutions
- institution_memberships
- institution_settings
- institution_classes
- class_memberships
- institution_subject_assignments
- institution_invitations
- institutional_licences
- institutional_seat_allocations
## 14.3 Curriculum and content

Potential tables:

- curricula
- examinations
- academic_levels
- subjects
- topics
- subtopics
- learning_objectives
- learning_resources
- resource_versions
- resource_sources
- content_licences
- content_reviews
## 14.4 AI Tutor

Potential tables:

- tutor_conversations
- tutor_messages
- tutor_attachments
- ai_requests
- ai_usage_records
- ai_provider_settings or appropriately protected configuration records
- ai_feedback
- reported_ai_responses
- ai_request_failures
Never store API secrets in plaintext database fields without an appropriate secret-management design. Prefer environment or managed secret configuration for provider credentials.

## 14.5 Question bank and learning

Potential tables:

- questions
- question_options
- question_answers
- question_tags
- question_sources
- question_reviews
- quizzes
- quiz_questions
- quiz_attempts
- quiz_responses
- bookmarks
- topic_mastery_records
- study_plans
- study_plan_tasks
- study_activity_records
## 14.6 CBT

Potential tables:

- exams
- exam_sections
- exam_questions
- exam_schedules
- exam_enrolments
- exam_attempts
- exam_responses
- exam_results
- manual_marking_records
- exam_access_codes
- exam_event_logs
Ensure exam answers, answer keys, marks, and result states are correctly separated and protected.

## 14.7 Billing and payments

Potential tables:

- subscription_plans
- plan_entitlements
- subscriptions
- subscription_items where needed
- payment_transactions
- invoices
- refunds
- payment_webhook_events
- billing_adjustments
- usage_ledger_entries
- institutional_billing_records
Use integer minor currency units where appropriate, with a clearly documented currency representation. Do not rely on floating-point arithmetic for financial calculations.

## 14.8 Notifications and support

Potential tables:

- notifications, or Laravel's equivalent
- notification_preferences
- email_delivery_logs where useful
- support_tickets
- support_messages
- support_attachments
- announcements
## 14.9 Operations

Potential tables:

- feature_flags
- system_settings
- scheduled_jobs or job metadata where justified
- data_export_requests
- account_deletion_requests
- backup_audit_records
Use framework conventions and existing package schemas where appropriate.

## 14.10 Database quality requirements

- Correct foreign keys.
- Appropriate indexes.
- Unique constraints for genuine uniqueness requirements.
- Referential integrity.
- Explicit deletion and retention rules.
- Transactions for multi-step state changes.
- Consistent timestamps.
- Appropriate decimal or integer financial fields.
- Pagination for large result sets.
- No unnecessary duplication of sensitive information.
- Documented tenant-scoping rules.
- Safe migrations for existing data.
Do not indiscriminately cascade-delete financial records, exam history, or audit trails.

## 14.11 Required SQL export

Create and maintain:

`database/sql/edudev.sql`

The export should contain the current schema and, if explicitly intended, controlled reference data required to initialise the platform.

Do not include:

- Production user data.
- Passwords or authentication tokens.
- API keys.
- Payment secrets.
- Private student conversations.
- Real payment records.
- Sensitive uploaded files.
Document whether the SQL file contains schema only or schema plus reference data.

Ensure the export can be regenerated from the migrations using a documented process.

If the local database is unavailable, provide a reproducible schema-export script or command and document the limitation. Do not create a misleading or empty SQL dump and claim it represents the complete application.

For production data backups, use a separate encrypted backup procedure.

# 15. API DESIGN AND MOBILE READINESS

Design authenticated, permission-protected APIs for future mobile applications and approved external integrations.

Potential API groups:

- Authentication.
- Student profile.
- Curriculum.
- Tutor sessions.
- Image-question submission.
- AI request status.
- Question bank.
- Practice attempts.
- Mastery and study plans.
- CBT exams.
- Subscription status.
- Payment status.
- Notifications.
- Institution and teacher workflows.
Requirements:

- API versioning.
- Request validation.
- Consistent error responses.
- Pagination.
- Rate limiting.
- Authentication and authorisation.
- Tenant isolation.
- Request identifiers.
- Idempotency for relevant state-changing operations.
- Documentation.
- API tests.
- File upload restrictions.
- Safe handling of expired sessions and revoked tokens.
Do not expose unrestricted administrative APIs to mobile clients.

Do not duplicate AI, payment, or subscription business logic in the mobile application.

# 16. SECURITY, PRIVACY AND SAFEGUARDING

Security and student privacy are release requirements.

Implement protections proportionate to the data and risks involved.

## Application security

- Validate all user inputs.
- Use parameterised database access through framework facilities.
- Protect against SQL injection, XSS, CSRF, and insecure direct object references.
- Enforce authentication and authorisation server-side.
- Use secure session settings in production.
- Apply rate limiting to sensitive endpoints.
- Restrict uploaded file types and sizes.
- Use private storage for confidential files.
- Prevent executable uploads.
- Protect download endpoints.
- Use signed temporary URLs where appropriate.
- Avoid logging secrets.
- Keep dependencies updated.
- Disable debug mode in production.
- Protect environment files.
- Document TLS and secure-cookie requirements.
- Monitor suspicious authentication and account activity.
- Use safe exception handling.
- Validate webhook signatures.
- Apply least privilege to database and infrastructure credentials.
## Student privacy

Design for children and young people who may use the platform.

- Collect only necessary personal data.
- Provide understandable privacy notices.
- Support appropriate parental or guardian consent where required.
- Establish lawful processing and retention policies.
- Restrict access to student records.
- Provide account data access and deletion workflows where legally applicable.
- Minimise collection of sensitive personal information.
- Establish a data breach response procedure.
- Use appropriate contractual and technical safeguards with third-party AI providers.
- Avoid sending unnecessary student identifiers or personal information to AI providers.
- Clearly disclose relevant third-party processing.
- Avoid using identifiable student conversations to train models without a lawful basis and appropriate consent.
- Do not expose student analytics to other students.
- Provide appropriate protections for institution-managed accounts.
Review applicable Nigerian data protection requirements, including the Nigeria Data Protection Act 2023, and obtain qualified legal advice for compliance decisions.

Do not claim certification or complete legal compliance merely because basic privacy features exist.

## AI safeguarding

- Use age-appropriate explanations.
- Avoid presenting AI output as infallible.
- Make it possible to report harmful or inaccurate responses.
- Apply safeguards to inappropriate content.
- Do not provide cheating assistance for a live, restricted examination when the context clearly indicates prohibited assistance.
- Offer learning-oriented alternatives.
- Treat uploaded documents and retrieved material as untrusted input.
- Defend against prompt injection from documents and external content.
- Do not allow retrieved content to override system security rules.
- Avoid unsupported medical, legal, financial, or other high-stakes advice in educational conversations.
- Provide human review paths for disputed results where appropriate.
# 17. UI/UX AND ACCESSIBILITY

Create a polished, premium, mobile-responsive interface consistent with the EduDEV brand.

Avoid a generic admin-template appearance.

Use a clear visual hierarchy, readable typography, consistent spacing, high-quality empty states, accessible forms, and predictable navigation.

## Student experience

The student interface should feel encouraging, modern, and easy to use.

Prioritise:

- Fast access to the AI Tutor.
- Simple question upload.
- Readable mathematical explanations.
- Clear practice flows.
- Progress visualisation.
- Low-friction onboarding.
- Accessible navigation.
- Helpful error messages.
- Responsive mobile layouts.
## Institutional experience

Provide structured dashboards suitable for teachers, school administrators, and CBT operators.

Avoid overwhelming users with irrelevant statistics.

## Accessibility

- Semantic HTML.
- Keyboard navigation.
- Visible focus states.
- Accessible labels.
- Sufficient contrast.
- Screen-reader-friendly controls.
- Appropriate form errors.
- Reduced-motion considerations.
- Responsive layout testing.
## Nigerian usage considerations

- Optimise for mobile-first access.
- Keep pages lightweight.
- Reduce unnecessary network requests.
- Handle intermittent connectivity gracefully.
- Avoid requiring large uploads where smaller alternatives are possible.
- Use NGN formatting correctly.
- Make timezone behaviour explicit.
- Use clear, accessible English.
- Consider Nigerian education terminology and local subject structures.
Do not assume all users have unlimited mobile data, modern devices, or stable electricity.

# 18. EMAILS, NOTIFICATIONS AND SUPPORT

Implement notification services with configurable channels.

Potential notifications include:

- Welcome and verification.
- Password recovery.
- Subscription activation.
- Payment confirmation.
- Renewal reminders.
- Failed payment notifications.
- Study reminders.
- Assignment deadlines.
- Exam schedules.
- Results available.
- Account security alerts.
- Support ticket updates.
Use queued delivery where appropriate.

Provide templates using configurable branding.

Do not claim an email was delivered merely because the application queued it.

Track relevant delivery states when the chosen provider makes those states available.

Respect notification preferences and applicable consent requirements.

Provide a support ticket workflow with priority, status, assignment, replies, and audit history.

# 19. REPORTING AND BUSINESS ANALYTICS

Build dashboards for three distinct audiences.

## Student analytics

- Practice accuracy.
- Progress over time.
- Topic-level performance.
- Study-plan completion.
- Examination readiness indicators based on available evidence.
- Time spent learning.
- Improvement areas.
Label predictive indicators appropriately. Do not promise a particular examination grade.

## Institutional analytics

- Student activity.
- Class performance.
- Subject-level outcomes.
- Assignment completion.
- Mock exam outcomes.
- Weak-topic patterns.
- Teacher and class activity.
- Subscription and seat usage.
Protect student privacy and limit reports to authorised personnel.

## Business analytics

- Registrations.
- Activation rates.
- Free-to-paid conversion.
- Active subscriptions.
- Monthly recurring revenue where billing supports reliable calculation.
- Churn.
- Renewal rates.
- Failed payments.
- Revenue by plan.
- AI cost per user.
- AI cost per paid subscriber.
- Contribution margin estimates.
- Institution acquisition.
- Product usage by feature.
- Support volume.
Document the definition of every financial KPI. Distinguish cash collected, recognised revenue, outstanding invoices, refunds, and estimated recurring revenue.

Do not present projections as actual revenue.

# 20. TESTING AND QUALITY ASSURANCE

Write automated tests throughout implementation, not only at the end.

## Required test categories

### Unit tests

Test important business rules such as:

- Subscription entitlement evaluation.
- AI usage accounting.
- Question scoring.
- Study-plan task generation.
- Mastery calculation.
- Payment state transitions.
- Exam time-limit calculations.
- Tenant access rules.
### Feature tests

Test:

- Registration and login.
- Role-based access.
- Student dashboard.
- AI Tutor requests.
- Upload validation.
- Conversation persistence.
- Question bank workflows.
- Practice attempts.
- Exam creation and submission.
- Subscription checks.
- Payment verification.
- Webhook idempotency.
- Institutional isolation.
- Administrative actions.
- Account deletion workflows.
### Security tests

Test that:

- Students cannot access other students' private records.
- Teachers cannot access unauthorised classes.
- Institution users cannot cross tenant boundaries.
- Ordinary users cannot invoke privileged endpoints.
- Unpublished answers remain protected.
- AI keys and payment secrets never appear in browser responses.
- Forged payment callbacks cannot activate subscriptions.
- Replayed webhooks do not duplicate transactions.
- Malicious uploads are rejected.
- Rate limits work as intended.
### UI and browser tests

Where the environment supports browser automation, test key user journeys on desktop and mobile-sized viewports.

Include:

- New student registration.
- Asking the AI Tutor a question.
- Uploading a question image.
- Completing a practice quiz.
- Taking a mock exam.
- Purchasing or upgrading a plan in test mode.
- School administrator inviting a teacher.
- Admin managing a curriculum topic.
### Performance tests

Evaluate:

- Slow database queries.
- N+1 query problems.
- Pagination.
- Queue throughput.
- Large question banks.
- Concurrent exam submissions.
- AI provider timeouts.
- File upload limits.
- Dashboard query performance.
Do not claim the platform supports a particular concurrency or daily-active-user target unless it has been measured under a documented test setup.

## Definition of done

A feature is complete only when:

1. Its requirements are documented.
2. Its implementation is present.
3. Its authorisation rules are enforced.
4. Its validation is implemented.
5. Its relevant tests pass.
6. Its error states are handled.
7. Its UI has been reviewed.
8. Its documentation is updated.
9. Any external configuration requirements are recorded.
10. Known limitations are disclosed.
# 21. PERFORMANCE, SCALABILITY AND OPERATIONS

Start with an appropriately designed Laravel application and MySQL database.

Implement:

- Database indexes.
- Query optimisation.
- Pagination.
- Caching where safe.
- Queue processing for slow operations.
- Private object storage for uploads.
- Structured logs.
- Health checks.
- Scheduled maintenance tasks.
- Failed job monitoring.
- Database backup procedures.
- Restore procedures.
- Error reporting.
- Environment-specific configuration.
Keep AI provider calls out of long-running web requests where the operation is expensive or unpredictable.

Design for eventual growth in student accounts, questions, institutions, exams, and AI requests.

Do not introduce premature sharding or microservices.

Provide a documented scale-up path covering:

- Database resource upgrades.
- Connection management.
- Queue workers.
- Redis deployment.
- Object storage.
- Read-heavy query optimisation.
- Database backup and restore.
- Monitoring and alerting.
- Horizontal application scaling when needed.
Any performance benchmark must identify the environment, workload, dataset, and results.

# 22. REQUIRED REPOSITORY STRUCTURE AND DOCUMENTATION

Adapt this structure to the actual application rather than forcing unnecessary folders into an existing project.

Create or maintain the following documentation:

- `/docs/PRODUCT_REQUIREMENTS.md`
- `/docs/ARCHITECTURE.md`
- `/docs/IMPLEMENTATION_ROADMAP.md`
- `/docs/DATABASE_DESIGN.md`
- `/docs/ERD.md`
- `/docs/AI_ARCHITECTURE.md`
- `/docs/AI_SAFETY_AND_GROUNDING.md`
- `/docs/BRANDING_GUIDE.md`
- `/docs/SECURITY_AND_PRIVACY.md`
- `/docs/API_DOCUMENTATION.md`
- `/docs/PAYMENTS_AND_SUBSCRIPTIONS.md`
- `/docs/TESTING_GUIDE.md`
- `/docs/DEPLOYMENT_GUIDE.md`
- `/docs/BACKUP_AND_RESTORE.md`
- `/docs/ADMIN_GUIDE.md`
- `/docs/USER_GUIDE.md`
- `/docs/KNOWN_LIMITATIONS.md`
- `/docs/CHANGELOG.md`
Maintain:

- `.env.example` without real credentials.
- Appropriate database migrations.
- Seeders for safe reference and demonstration data.
- Automated tests.
- Database export instructions.
- API specifications where applicable.
- Queue and scheduler configuration.
- Deployment configuration examples.
- Dependency installation instructions.
Create the directory below for the database export:

`database/sql/edudev.sql`

Never commit production secrets or private user data to source control.

# 23. PHASED IMPLEMENTATION ROADMAP

Execute the work in the following phases. Update the roadmap if the repository audit reveals a better sequence, but document and justify changes.

## Phase 0 — Repository audit and project definition

Tasks:

1. Inspect the repository and identify the application structure.
2. Check the framework and runtime versions.
3. Review dependencies and environment configuration.
4. Inspect existing migrations and schema.
5. Inspect authentication and authorisation.
6. Identify existing modules and reusable services.
7. Run existing tests and record the baseline.
8. Identify incomplete, unsafe, duplicated, or broken features.
9. Confirm the target architecture.
10. Create the product requirements and implementation roadmap.
11. Record blockers and assumptions.
Deliverables:

- Repository audit report.
- Architecture document.
- Implementation roadmap.
- Initial risk register.
- Existing-feature preservation plan.
- Baseline test report.
Do not perform destructive refactoring in this phase.

## Phase 1 — Application foundation and brand system

Implement:

- Framework and environment configuration.
- Base application layout.
- EduDEV design tokens.
- Configurable branding.
- Navigation.
- Shared UI components.
- Error pages.
- Logging foundations.
- Health check.
- Base database setup.
- Initial automated tests.
Acceptance criteria:

- The application starts using documented instructions.
- Brand identity can be changed centrally.
- Shared components use consistent design tokens.
- Existing functionality remains intact.
- Tests pass or inherited failures are documented.
## Phase 2 — Authentication, permissions and onboarding

Implement:

- Authentication.
- User profiles.
- Role and permission management.
- Onboarding.
- Account verification.
- Password recovery.
- Account suspension.
- Initial student, teacher, school, CBT centre, and administrator workflows.
Acceptance criteria:

- Users can register and authenticate.
- Unauthorised access is rejected.
- Role redirects work.
- Validation and recovery flows work.
- Security tests cover privileged operations.
## Phase 3 — Curriculum, resources and question bank

Implement:

- Curriculum hierarchy.
- Subjects and topics.
- Learning resource management.
- Question bank.
- Question review and publication.
- Search and filtering.
- Safe reference data.
Acceptance criteria:

- Administrators can create and manage curriculum content.
- Questions can be authored, reviewed, published, and archived.
- Protected answer keys remain inaccessible to unauthorised users.
- Content provenance is retained.
## Phase 4 — Student dashboard and practice engine

Implement:

- Student dashboard.
- Practice sessions.
- Question selection.
- Answer submission.
- Scoring.
- Explanations.
- Bookmarks.
- Practice history.
- Basic progress statistics.
Acceptance criteria:

- Students can practise and review results.
- Scores are calculated server-side.
- Attempts persist correctly.
- Dashboard statistics match stored records.
## Phase 5 — AI provider foundation and EduDEV AI Tutor

Implement:

- Provider abstraction.
- AI configuration.
- Tutor conversations.
- Prompt management.
- Text tutoring.
- Tutor modes.
- Usage metering.
- Rate limits.
- Cost controls.
- Failure handling.
- Feedback and answer reporting.
Then implement:

- Image upload.
- OCR or vision processing.
- Extracted-question confirmation.
- Image-based tutoring.
- Related practice generation.
Acceptance criteria:

- A configured provider can answer supported tutor requests.
- Provider errors are handled safely.
- Secrets are not exposed.
- Usage is recorded.
- Failed requests do not create false success states.
- Image processing validates files and handles low-confidence extraction.
## Phase 6 — Mastery engine and study plans

Implement:

- Learning activity aggregation.
- Topic mastery scoring.
- Weakness detection.
- Study plan generation.
- Task completion.
- Progress updates.
- Plan adjustment.
Acceptance criteria:

- Results are based on stored learning activity.
- Scores are explainable.
- Insufficient evidence is handled honestly.
- Students can update and track plans.
## Phase 7 — CBT examinations

Implement:

- Exam authoring.
- Question allocation.
- Schedules.
- Candidate access.
- Timed exam interface.
- Autosave.
- Submission.
- Scoring.
- Results.
- Reports.
Acceptance criteria:

- Server-side timing is enforced.
- Submissions are idempotent.
- Answer keys are protected.
- Interrupted sessions follow documented recovery rules.
- Scoring is covered by automated tests.
## Phase 8 — Schools, teachers, parents and CBT centres

Implement the required institutional workflows, invitations, enrolments, class management, reporting, guardian relationships, and tenant isolation.

Acceptance criteria:

- Institutional users can manage authorised resources.
- Cross-institution data access is blocked.
- Institutional seat and licence limits are enforced.
- Student privacy is preserved.
## Phase 9 — Subscriptions, billing and payments

Implement:

- Subscription plans.
- Entitlements.
- Usage limits.
- Payment provider abstraction.
- Test-mode checkout.
- Payment verification.
- Webhooks.
- Renewals.
- Cancellation.
- Failed payment handling.
- Invoices or receipts.
- Reconciliation tools.
Acceptance criteria:

- Subscription entitlements are enforced server-side.
- Forged callbacks cannot activate paid plans.
- Webhook replays do not duplicate financial state changes.
- Test-mode and live-mode configuration are separated.
- Unconfigured production integrations are clearly documented.
## Phase 10 — Super Admin, reporting and support

Implement:

- Administrative dashboards.
- User management.
- Content administration.
- AI configuration.
- Usage analytics.
- Billing administration.
- Institutional management.
- Support tickets.
- Notifications.
- Audit trails.
- Feature flags.
- Operational monitoring.
Acceptance criteria:

- All privileged operations require the correct permissions.
- Dashboards use real data.
- Sensitive administrative access is audited.
- Reporting definitions are documented.
## Phase 11 — Hardening, performance and release preparation

Implement and verify:

- Full regression testing.
- Security review.
- Performance review.
- Accessibility review.
- Database index review.
- Backup and restore tests.
- Queue and scheduler verification.
- Production configuration review.
- Error monitoring.
- Deployment documentation.
- SQL export.
- User and administrator documentation.
Acceptance criteria:

- Critical user journeys pass.
- No known critical security issue remains unresolved.
- Backup restoration has been tested or its untested status is explicitly disclosed.
- Deployment instructions are reproducible.
- Known limitations and outstanding configuration are documented.
- The product owner receives a final release-readiness report.
## Phase 12 — Mobile readiness and future expansion

After the web platform is stable:

- Finalise API contracts.
- Verify API authentication.
- Test mobile-oriented endpoints.
- Document Flutter integration.
- Review offline and intermittent-connectivity requirements.
- Assess additional curricula and country localisation.
- Assess advanced AI features against cost and educational value.
Do not start a separate mobile implementation until the core platform and required API contracts are sufficiently stable.

# 24. MVP PRIORITISATION

Do not attempt to release every advanced capability at once.

The minimum sellable release should include:

1. Public website and EduDEV branding.
2. Secure registration and login.
3. Student dashboard.
4. Curriculum and subject selection.
5. AI Tutor with text questions.
6. Image-based question assistance.
7. Practice question bank.
8. Timed CBT mock examinations.
9. Basic learning progress and study plans.
10. Subscription plans and one verified payment integration.
11. Super Admin.
12. Usage metering and AI cost controls.
13. Privacy and security essentials.
14. Automated tests.
15. Deployment documentation and SQL export.
School administration, guardian features, advanced analytics, additional payment providers, mobile applications, sophisticated adaptive learning, and enterprise white-labelling can follow according to the validated roadmap.

However, do not omit architectural foundations that would make those features unsafe or prohibitively expensive later.

# 25. IMPLEMENTATION WORKFLOW FOR CLAUDE CODE

Follow this exact workflow.

### Step 1: Inspect

Read the relevant project files, configuration, migrations, routes, models, services, views, policies, and tests.

Do not begin by replacing the project structure.

### Step 2: Report

Provide a concise summary of:

- Existing stack.
- Current application condition.
- Reusable components.
- Missing functionality.
- Critical risks.
- Proposed implementation order.
Write the detailed findings into the documentation.

### Step 3: Plan

Break the selected phase into concrete tasks.

For each task, identify:

- Files to create or modify.
- Database changes.
- Business rules.
- Permissions.
- Tests.
- Dependencies.
- Acceptance criteria.
### Step 4: Implement

Implement one coherent feature or small group of related features at a time.

Follow framework conventions and existing project patterns.

### Step 5: Validate

Run relevant automated tests, formatting checks, static analysis, migrations in an appropriate test environment, and available UI checks.

Do not destroy or reset a database containing user data.

### Step 6: Review

Review the changes for:

- Security.
- Tenant isolation.
- Data integrity.
- Performance.
- Accessibility.
- Brand consistency.
- Error handling.
- Regression risks.
### Step 7: Document

Update architecture, schema, configuration, and user documentation.

Record any manual configuration or external credentials required.

### Step 8: Summarise

At the end of each phase, report:

- Completed features.
- Files and components changed.
- Migrations created.
- Tests executed and their results.
- Commands executed.
- Remaining issues.
- External configuration still required.
- Recommended next phase.
Then continue with the next phase when safe and practical.

Do not repeatedly stop to ask for approval for routine, reversible implementation decisions already covered by this specification. Ask only when a genuine blocker, destructive action, significant cost, external commitment, or unresolved business decision requires the product owner's input.

# 26. ENVIRONMENT AND CONFIGURATION MANAGEMENT

Create a clear `.env.example` containing placeholders for relevant settings, including:

- Application environment.
- Application URL.
- Database connection.
- Session and cache configuration.
- Queue configuration.
- Mail configuration.
- AI provider credentials.
- AI model selection.
- Payment provider credentials.
- Object storage credentials.
- Logging and monitoring.
- Optional Redis.
- Optional external integrations.
Do not put actual secrets in the example file.

Validate required environment variables at startup or at the relevant service boundary.

If an optional service is not configured, show a clear, safe status and avoid pretending the service is working.

Separate development, testing, staging, and production settings.

Document safe key rotation and credential replacement procedures.

# 27. DATA MIGRATION, SEEDING AND SQL EXPORT WORKFLOW

For each schema change:

1. Create a migration.
2. Check foreign keys and indexes.
3. Review compatibility with existing records.
4. Test the migration on a disposable or test database.
5. Test rollback where feasible.
6. Seed only appropriate reference or demonstration data.
7. Verify application behaviour.
8. Update the database design document.
9. Regenerate or document regeneration of `database/sql/edudev.sql`.
Never run a destructive migration against production data without explicit authorisation, a verified backup, and a documented recovery plan.

Do not use destructive database commands as a routine troubleshooting shortcut.

Do not store production data in seeders.

# 28. PRODUCTION DEPLOYMENT PREPARATION

Prepare deployment instructions that cover:

- Server and PHP requirements.
- Required PHP extensions.
- Composer installation.
- Frontend dependency installation and asset compilation.
- Environment configuration.
- Database creation.
- Database migrations.
- Initial administrative account creation.
- File storage configuration.
- File permissions.
- Queue workers.
- Scheduled tasks.
- Mail configuration.
- AI provider configuration.
- Payment integration.
- TLS/HTTPS.
- Application caching.
- Log monitoring.
- Backup configuration.
- Database restoration.
- Deployment rollback.
- Smoke testing.
- Troubleshooting.
Keep all secrets outside public directories and source control.

Ensure the web server points to the Laravel public directory rather than exposing application internals.

Document separate instructions for local XAMPP development and the selected production hosting environment.

Do not assume that deployment has succeeded simply because the application builds locally.

# 29. RELEASE READINESS AND HANDOVER

At the end of the implementation, prepare a final handover package containing:

1. Working application source.
2. Updated architecture documentation.
3. Product requirements.
4. Database design and ERD.
5. Database migrations.
6. SQL export at `database/sql/edudev.sql`.
7. Installation guide.
8. Deployment guide.
9. Environment variable reference.
10. Administrator guide.
11. Student and teacher user guides.
12. API documentation.
13. AI provider configuration guide.
14. Payment setup guide.
15. Security and privacy notes.
16. Backup and restore instructions.
17. Automated test report.
18. Known limitations.
19. External services requiring credentials.
20. Production launch checklist.
21. Suggested next-phase improvements.
Include a feature-status matrix:

| Feature | Status | Evidence or limitation |
| --- | --- | --- |
| Authentication | Verified / Partial / Not implemented | Tests and notes |
| AI Tutor | Verified / Partial / Not implemented | Provider and test evidence |
| Image questions | Verified / Partial / Not implemented | OCR/vision provider status |
| Question bank | Verified / Partial / Not implemented | Test evidence |
| CBT | Verified / Partial / Not implemented | Exam-flow test evidence |
| Subscriptions | Verified / Partial / Not implemented | Entitlement tests |
| Payments | Verified / Partial / Not implemented | Provider and webhook tests |
| Schools | Verified / Partial / Not implemented | Tenant isolation evidence |
| Super Admin | Verified / Partial / Not implemented | Permission tests |
| Security | Verified / Partial / Not implemented | Review findings |
| SQL export | Verified / Partial / Not implemented | Export validation |
| Deployment | Verified / Partial / Not implemented | Environment tested |

Use only statuses supported by actual implementation evidence.

# 30. FINAL EXECUTION INSTRUCTIONS

Begin now.

**First, audit the repository. Do not immediately generate a large quantity of new code.**

Then:

1. Create or update the documentation and phased roadmap.
2. Identify the smallest safe implementation phase.
3. Implement the application foundation and brand system.
4. Run the relevant tests.
5. Report the actual results.
6. Continue through the roadmap in order.
7. Preserve existing working features.
8. Maintain database migrations and the SQL export workflow.
9. Keep the EduDEV identity configurable.
10. Deliver a secure, maintainable, commercially viable application.
When making design decisions, favour simplicity, correctness, educational usefulness, affordability, security, and maintainability.

When making commercial decisions, consider recurring revenue, AI inference costs, subscription entitlements, Nigerian payment realities, institutional sales, and the path to international expansion.

When making AI decisions, favour grounded educational explanations, reliable source material, transparent uncertainty, privacy, and measurable value to learners.

When making technical decisions, prefer proven Laravel conventions and testable business rules over unnecessary complexity.

**Do not finish after the initial audit or roadmap. Execute the development phases incrementally, verifying each phase before progressing.**

The final product must be recognisably **EduDEV by MacoDev Limited**: a premium AI-powered education platform built to help learners learn smarter and achieve more.

Begin with Phase 0 and provide the repository audit findings and implementation roadmap before making significant changes.

