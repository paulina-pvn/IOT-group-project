# Fitness Class & Membership System — Requirements

## 1. Project purpose and interpretation

The Fitness Class & Membership System is a platform for managing fitness classes, bookings, and memberships. The team imagines it being used by a yoga or Pilates studio.

Members can browse and book classes. Trainers can view and manage their sessions. Administrators can manage schedules, memberships, class capacity, and trainer requests. The system aims to make bookings and membership management easier, reduce manual administrative work, and keep schedules and member information up to date.

## 2. Stakeholders and needs

### 2.1 Primary stakeholders

**Members**  
Role: requirement provider and validator.

- Browse available classes and book or cancel a place online.
- See available places and find classes that fit their schedule.
- Check membership status and expiry date.

**Trainers**  
Role: requirement provider and validator.

- View upcoming classes, including time, room, and registered participants.
- Request a substitute or schedule change when needed.
- Use member features and trainer features with one account.

**Administrators**  
Role: requirement provider, decision-maker, validator, and operator.

- Create, edit, reschedule, and cancel classes while managing capacity and trainer assignments.
- Manage memberships, including creation, renewal, expiry, freezing, and termination.
- Review trainer requests and keep schedules and member access accurate.

### 2.2 Secondary stakeholders

**Gym owners or management**  
Role: decision-maker and requirement provider.

- Compare class popularity and booking trends to decide which classes to expand or reduce.
- View membership growth, renewals, and cancellations to understand whether the business is growing.
- View class and room utilization by day and time to understand the statisics. 

### 2.3 Hidden stakeholders

**IT support and developers**  
Role: maintainer/operator and requirement provider.

- Maintain and update the system without disrupting its use.
- Identify and diagnose technical problems.
- Keep the system secure, reliable, and maintainable.

### 2.4 External stakeholders

**Data protection authorities and regulators**  
Role: source of constraints and potential validators.

- Personal information must be handled according to applicable data-protection rules.
- Only authorized users should access personal data.
- The system should protect personal information from unauthorized access.

## 3. User stories

### 3.1 All roles

- **US-17:** As a potential user, I want to see a home page that introduces the studio and gives me clear links to register or log in, so that I know how to get started.
- **US-14:** As a new user, I want to register an account so that I can access the platform.
- **US-01:** As a user, I want to log in with my own account so that I can access the features and data allowed for my role.

### 3.2 Members

- **US-15:** As a future member, I want to apply for a membership so that I can become a member.
- **US-16:** As a member, I want to pay for my membership so that my membership becomes active.
- **US-02:** As a member, I want to browse the class schedule by day, type, and trainer so that I can find a class that fits my availability.
- **US-03:** As a member, I want to see the number of available places in each class so that I know whether I can still book it.
- **US-04:** As a member, I want to book a class online so that I do not have to call or visit the front desk.
- **US-05:** As a member, I want to cancel a class online so that I do not have to call or visit the front desk.
- **US-06:** As a member, I want to see my membership status and expiry date so that I know whether I can book and when I may need to renew.


### 3.3 Trainers

- **US-07:** As a trainer, I want to view my upcoming sessions and their time, room, class type, and registered participants so that I can prepare for each class.
- **US-08:** As a trainer, I want to request a substitute or schedule change so that an administrator can review and respond to the request in time.
- **US-09:** As a trainer who is also a member, I want to use member and trainer features with one account so that I do not need separate accounts.

### 3.4 Administrators

- **US-10:** As an administrator, I want to create, edit, and cancel classes with a set capacity, so that the schedule stays accurate.
- **US-11:** As an administrator, I want to be able to manage memberships both automatically and manually (create, renew, freeze, terminate), so that member access is always correct.
- **US-12:** As an administrator, I want to review and approve or reject trainer requests, so that I can keep the schedule organized.
- **US-13:** As an administrator, I want to view the list of members registered for a class, so that I can keep track of engagement.

## 4. Requirements

### 4.1 Functional requirements — MVP

- **FR-01:** The system shall allow users to log in using an email address and password.
- **FR-02:** The system shall provide users with features according to their assigned role(s).
- **FR-03:** The system shall allow members to view the weekly class schedule and filter classes by day, type, and trainer.
- **FR-04:** The system shall display the number of places available in each class.
- **FR-05:** The system shall allow a member to book a class only if their membership is valid on the date of the class and a place is available.
- **FR-06:** The system shall allow members to view and cancel their bookings.
- **FR-07:** When a booking is cancelled, the system shall update the booking list and the class's available-place count.
- **FR-08:** The system shall allow members to view their membership status and expiry date.
- **FR-09:** The system shall allow trainers to view their upcoming sessions, including class details and registered participants.
- **FR-10:** The system shall allow trainers to submit substitute-trainer and schedule-change requests.
- **FR-11:** The system shall allow a user with trainer and member roles to access the features of both roles with one account.
- **FR-12:** The system shall allow administrators to create classes and set each class's date, time, type, capacity, and trainer.
- **FR-13:** The system shall allow administrators to edit, reschedule, and cancel classes.
- **FR-14:** The system shall allow administrators to approve or reject trainer requests and assign a substitute trainer when appropriate.
- **FR-15:** The system shall allow administrators to create and renew memberships.
- **FR-16:** The system shall prevent a member from booking a class if their membership is not valid on the date of the class.
- **FR-25:** The system shall allow new users to register an account using an email address and password.
- **FR-26:** The system shall allow registered users to submit a membership application.
- **FR-27:** The system shall allow users to pay for their membership.
- **FR-28:** The system shall provide a public home page that introduces the studio and links to the registration and login pages. (US-17)

### 4.2 Functional requirements — Release 1

- **FR-17:** The system shall allow users to reset their passwords.
- **FR-18:** The system shall allow members to join a waiting list for a fully booked class.
- **FR-19:** The system shall notify waiting-list members when a place becomes available.
- **FR-20:** The system shall remind members before their membership expires.
- **FR-21:** The system shall allow members to request membership renewal online.
- **FR-22:** The system shall allow trainers to view whether their requests are pending, approved, or rejected.
- **FR-23:** The system shall notify booked members when a class is changed or cancelled.
- **FR-24:** The system shall allow administrators to freeze or terminate memberships.

### 4.3 Non-functional requirements

- **NFR-01 (Security):** Passwords shall be stored using a secure password-hashing method.
- **NFR-02 (Authorization):** Users shall only be able to access data and features permitted by their assigned roles.
- **NFR-03 (Performance):** Under typical usage, the weekly class schedule and booking views shall load within 3 seconds.
- **NFR-04 (Usability):** Members shall be able to find and book a class without requiring technical knowledge.
- **NFR-05 (Capacity and concurrency):** The system shall not confirm more bookings than a class's capacity, including when multiple members attempt to book the last available place concurrently.
- **NFR-06 (Maintainability):** The system should be organized so that a feature can be updated without unnecessary changes to unrelated features.
- **NFR-07 (Privacy):** Personal information shall be protected from unauthorized access.
- **NFR-08 (Compatibility):** The interface shall work on the desktop and mobile browsers supported by the project.

## 5. Potential stakeholder conflicts

| Stakeholders | Conflict | Possible resolution |
|---|---|---|
| Trainers and members | Trainers want manageable class sizes, while members want more available places. | Set capacity based on room size and class type, with trainer input. Offer a waiting list when a class is full. |
| Members, trainers, and administrators | Members want flexible cancellations, while trainers and administrators want predictable attendance. | Set a cancellation deadline. Late cancellations may incur a penalty. |
| Trainers and members | Trainers want schedule flexibility, while members need a dependable timetable. | Seek a substitute trainer first. Reschedule or cancel only when necessary, with administrator approval, and notify affected members. |

## 6. Assumptions and open questions

### Current assumptions

1. A person may hold more than one role, such as trainer and member.
2. The system is intended for a yoga or Pilates studio.
3. Membership is checked the day of the class.
4. Attendance is defined as those registered for a class.


## User story mapping

### All users

| **Activity** | **Discover the studio** | **Register an account** | **Access the platform** |
|---|---|---|---|
| **Tasks** | Visit the public home page | Create an account | Log in |
| **MVP** | Read the studio introduction | Enter registration details | Enter email and password |
| | Open the registration or login page | Create account | Open role-based home page |
| **Release 1** | | | Reset password |

### Member

| **Activity** | **Apply for membership** | **Pay for membership** | **Browse & book classes** | | | **Check my membership** |
|---|---|---|---|---|---|---|
| **Tasks** | Submit application | Complete payment | Browse schedule | Book a class | Manage my bookings | View membership |
| **MVP** | Submit a one-time membership application | Pay for membership | View weekly schedule | Book a spot/Block when membership expired | View my bookings | View expiry date |
| | | | Filter by day / type / trainer | | Cancel a booking | |
| | | | View free spots per class | View free spots per class | | |
| **Release 1** | | | | Join waiting list | | Get reminder before expiry |
| | | | | Get notified when a spot frees up | | Request renewal online |

### Trainer

| **Activity** | **Manage my sessions** | | **Use member features** |
|---|---|---|---|
| **Tasks** | View sessions | Request changes | Switch to member role |
| **MVP** | View upcoming classes | Request a substitute | Book classes with the same account |
| | View class details (time, room, type) | Request a schedule change | Toggle trainer / member view |
| | View participants | | |
| **Release 1** | | See request status (pending / approved/ rejected) | |

### Administrator

| **Activity** | **Manage class schedule** | | | **Manage memberships** | |
|---|---|---|---|---|---|
| **Tasks** | Create class | Edit / cancel class | Handle trainer requests | Manage individual membership | Automate statuses |
| **MVP** | Set date / time | Reschedule a class | Approve / reject request | Create membership | Automatic expiry |
|  | Set capacity | Cancel a class | Assign substitute trainer | Renew membership | Block booking when membership is invalid on the class date |
|  | Assign trainer | View list of registered members |  |  |  |
| **Release 1** | Notify booked members about class changes or cancellations |  |  | Freeze membership | Terminate membership |