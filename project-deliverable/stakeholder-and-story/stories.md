# Stakeholder stories — ComfortRoom

Same shape as the stories in the previous project: one story, acceptance criteria, requirements tasks, implementation tasks.
The rules below are the ones already written in the use cases. They are not new decisions.

## Stakeholders

| Stakeholder | What they want from the system |
|---|---|
| User (student or teacher) | A ranking of rooms that fit the time slot, the required facilities and their preferences. |
| Administrator | Proof that the system is working: sensor history, disconnections, facilities, calendar. |
| Developer | A documented way to read the input data and the ranking without using the screens. |

Arduino, the timer, the facilities service and the university calendar are actors in the use cases. They are not people, so they have no story of their own. Their work is listed under the stories that depend on it.

## Story: Find a suitable room

As a student or teacher,  
I want the free rooms ranked by how well they match my needs and my preferences,  
so that I can choose the most suitable room.

### Acceptance Criteria (Definition of Done)

- The user can search with no account.
- The user gives a time slot, or a flexible period, and any required facilities: videoprojector, minimum seats, minimum computers, minimum robots.
- A slot whose end is earlier than the reference time is rejected. The reference time is the latest measurement timestamp in the database, not the wall clock.
- Rooms that miss the facilities or are busy in the slot are left out. Facilities do not enter the score.
- The score of a room uses the average of each quantity over the 30 minutes before the reference time. The chosen slot does not change the score.
- Until the user changes anything, the group weights are 37.5% air quality, 37.5% thermal comfort, 12.5% acoustic comfort and 12.5% visual comfort, and every profile is the regulation trapezoid.
- The user compares the 4 groups in 6 pairs on the Saaty scale (1–9). Inside a group the criteria stay equal. If the consistency ratio is above 0.1, the weights are not accepted.
- The user can move the four points of a criterion's profile. Between the optimum points the compliance is 1. Outside the outer limits it is 0.
- A change made during a search is not saved. To save preferences and reuse them next time, the user registers and logs in (use cases 1 and 2).
- If a quantity has no measurement in that 30-minute window, it is dropped, the remaining weights are renormalized to 1, and the ranking names the missing quantity.
- The result is a list from the highest score to the lowest, with the score and the compliance of each criterion. For a flexible period, each room shows the first free slot that fits.

### Requirements Tasks

Work to define the business logic and rules before or during development.

- Choose the regulation document and the four default trapezoid points for each criterion. The use cases say the default profile comes from an EU or national regulation, and the numbers are not chosen yet.
- Choose the unit of each criterion. The class diagram has a unit field. The test JSON does not name units.

### Implementation Tasks

Technical engineering work required to actually build the feature.

- Build the search screen: time slot, flexible period, facility filters, and the ranked list.
- Build the 6-pair Saaty form, show the consistency ratio, and refuse inconsistent comparisons.
- Build the profile graph with the four points and the regulation profile behind them.
- Compute the 30-minute averages, map each average through its trapezoid, apply the AHP weights, and renormalize when a quantity is missing.
- Store preferences only when a logged-in user saves them.

## Story: Monitor the decision system

As an administrator,  
I want to see the sensor history, the gaps, the room facilities and the calendar,  
so that I can check that the ranking is based on data that is actually arriving.

### Acceptance Criteria (Definition of Done)

- The administrator account is created when the system is installed. It is not created on the register screen.
- The administrator signs in with e-mail and password and receives a JWT. The token's role is the subclass name `Administrator`.
- For a chosen room and period, the screen plots temperature, humidity, CO₂, sound, light, VOC, PM2.5 and PM10.
- A sensor is disconnected when its last measurement is more than 5 minutes before the reference time. The stretches with no data are computed from the stored measurements. They are not stored as their own records.
- The facilities of a room are shown. A facility the room does not list is shown as "No" or 0.
- Calendar events for a period are shown with room, start, end and title. If `calendarSyncedAt` is more than 30 minutes ago, that timestamp is shown as a warning.
- Every 15 minutes the system refreshes facilities and calendar. `facilitiesSyncedAt` changes only when facilities are written. `calendarSyncedAt` changes only when events are written.

### Requirements Tasks

Work to define the business logic and rules before or during development.

- Choose the protocols for Comm. A (sensors to Arduino), Comm. B (Arduino to Raspberry Pi), Comm. C (facilities) and Comm. D (calendar). The use cases name the channels and do not choose the protocols.
- Choose the database for measurements, accounts, facilities and events. The class diagram is the domain model, not the storage choice.
- Decide where calendar events come from. The test repository has sensor files and room facilities, and no calendar file.

### Implementation Tasks

Technical engineering work required to actually build the feature.

- Build the administrator screen with the eight plots, the sensor status, the facilities of a room and the calendar.
- Mark a sensor offline from the reference time, and draw gaps longer than 5 minutes.
- Accept a measurement each minute from the Arduino and store it with its timestamp.
- Run the 15-minute synchronization and update the two timestamps separately.

## Story: Use the ranking from another program

As a developer,  
I want to read the input data and the room ranking through documented REST APIs,  
so that another program can use the system without the screens.

### Acceptance Criteria (Definition of Done)

- The Developers tab of the user interface shows the Swagger documentation of API1 and API2.
- API2 returns the ranking with no token. The caller passes the time slot, the facilities, and optionally the six Saaty comparisons or the four group weights, and the profile points. If they are omitted, the defaults are used.
- Four group weights sent directly are rejected unless each weight is at least 0 and the four weights sum to 1.
- API1 returns measurements for a period, the facilities of a room, and calendar events. It requires a developer JWT.
- A developer registers with account type Developer. That account stores no room preferences.

### Requirements Tasks

Work to define the business logic and rules before or during development.

- Name the API2 parameters for the slot, the facilities, the six comparisons or the four group weights, and the four profile points. The use cases say these are parameters and do not name them.

### Implementation Tasks

Technical engineering work required to actually build the feature.

- Publish Swagger for API1 and API2 on the Developers tab, and mark which operations need a token.
- Implement public API2 and JWT-protected API1.
- On login, put the account subclass name in the JWT.
