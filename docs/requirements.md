# ComfortRoom — MVP Requirements

## 1. Project purpose and interpretation

ComfortRoom is a website that helps students and teachers choose a suitable room in a university building. It combines room availability, facilities, environmental readings, and the user’s preferences to score and rank rooms. An administrator can monitor the data behind those recommendations, and third-party developers can use it through documented APIs.

This file describes the MVP. The [assignment checklist](assigment_checklist.md) covers the professor’s deliverables and the suggested build order. We’ll revisit the scope for Release 1 after the MVP.

## 2. Stakeholders and needs

### 2.1 Primary stakeholders

**Students**

- Find a free room for individual study or group work.
- Find rooms with enough seats for everyone in the group.
- Prioritize a condition that matters to their activity, such as low noise for studying.

**Teachers**

- Find a room available for the full duration of a class or lab.
- Check that a room has enough seats for the class.
- Find rooms with the equipment the session requires, such as a projector or computers.

**System administrator**

- See how sensor readings changed in a room over a selected period.
- Identify which sensors stopped reporting and when.
- Check the facilities and calendar records the room finder uses.

### 2.2 Secondary stakeholders

**Professor (client and evaluator)**

- Verify that the working website meets the assignment requirements.

**Third-party developers**

- Retrieve sensor history, room facilities, and calendar events through the public input API.
- Request room scores and rankings from another program.
- Find the API parameters and response formats in the documentation.

### 2.3 Hidden stakeholders

**Project team**

- Match sensor, calendar, and facilities records to the same room.
- Tell which readings came from the physical kit and which came from the sample data.
- Reproduce a search or monitoring result when testing or preparing the demo.

### 2.4 External stakeholders

In the demo scenario, the fictional university timetable and facilities staff represent the people who would maintain the original records. The ComfortRoom administrator checks the records loaded into the system. The demo uses the supplied and generated data to represent those records.

**University timetable staff**

- Have existing calendar events reflected accurately in room search results.
- Keep attendee details out of public availability data.

**University facilities staff**

- Have room capacities represented accurately in the website.
- Have equipment records associated with the correct rooms.

## 3. User stories — MVP

Story IDs are stable references for GitHub issues. The stories are grouped by stakeholder.

### 3.1 Students and teachers

- **US-01 — Find rooms for a fixed time.** As a student or teacher, I want to find rooms free for my entire requested time slot so I can use the room without a scheduling clash. (FR-01)
- **US-02 — Compare two criteria.** As a student or teacher, I want to say how much more one criterion matters than another using the Saaty scale so the ranking reflects what matters to me. (FR-02)
- **US-05 — Find a slot in a flexible period.** As a student, I want to see a free one-hour slot within an afternoon so I can plan a group session when a room is available. (FR-01)
- **US-06 — Set a minimum room capacity.** As a student or teacher, I want to enter a minimum number of seats so everyone in my group or class has a place. (FR-01)
- **US-07 — Require specific equipment.** As a teacher, I want to require a projector or another piece of equipment so I can find a room suitable for my lesson. (FR-01)
- **US-08 — Search with default preferences.** As a student or teacher, I want to search with the default settings so I can get recommendations without first adjusting the scoring. (FR-03, FR-12, NFR-01)
- **US-09 — Adjust a desired condition.** As a student or teacher, I want to change my preferred temperature range so the ranking favors rooms closer to that range. (FR-03, FR-11)
- **US-10 — See rooms ranked by score.** As a student or teacher, I want to see eligible rooms ordered by their scores so I can choose the best match. (FR-04)
- **US-11 — See when no rooms qualify.** As a student or teacher, I want a clear message when no rooms meet my requirements so I know to change my search. (FR-04)
- **US-12 — Correct invalid search details.** As a student or teacher, I want to know which input is invalid, such as an end time before the start time, so I can correct it. (NFR-01)

### 3.2 System administrator

- **US-03 — View sensor history.** As an administrator, I want to see a chart of readings for a selected room and period so I can see how conditions changed. The chart covers temperature, CO₂, sound, and humidity. (FR-05)
- **US-13 — Find gaps in sensor reporting.** As an administrator, I want to see when a sensor stopped reporting so I can investigate missing readings. (FR-05, NFR-02)
- **US-14 — Check the facilities recorded for a room.** As an administrator, I want to see the facilities for a selected room so I can verify what the room finder uses. (FR-06)
- **US-15 — Check calendar events.** As an administrator, I want to see calendar events for a selected period so I can check why a room was treated as busy. (FR-06)

### 3.3 Third-party developers

Both REST APIs must be publicly available and free to call for the MVP. The Developers tab links to Swagger or equivalent open-source documentation. (FR-08–FR-10)

- **US-04 — Request a room ranking.** As a developer, I want to send room requirements and preferences through API2 so my software can display the resulting scores and ranking. (FR-09, NFR-03)
- **US-16 — Retrieve sensor history.** As a developer, I want to retrieve readings for a specified period through API1 so my software can use the room’s environmental history. (FR-08)
- **US-17 — Retrieve a room’s facilities.** As a developer, I want to look up a room’s facilities through API1 so my software can check what equipment and seating are available. (FR-08)
- **US-18 — Retrieve room calendar events.** As a developer, I want to retrieve calendar events for a specified period through API1 so my software can check when rooms are busy. (FR-08, NFR-04)
- **US-19 — Find the API documentation.** As a developer, I want to open the Developers tab so I can see what each API accepts and returns. (FR-10)

### 3.4 Project team

- **US-20 — Check a stored hardware reading.** As a team member testing the kit, I want to verify that a reading from the Arduino was stored on the Raspberry Pi so I can confirm the data path works. (FR-07)
- **US-21 — Supply different facilities data.** As a team member configuring a deployment, I want to load its room facilities so searches reflect the rooms and equipment at that site. (FR-07)
- **US-22 — Supply a different calendar.** As a team member configuring a deployment, I want to load its room calendar so searches reflect its bookings. (FR-07)
- **US-23 — Identify where a reading came from.** As a team member checking test results, I want to tell whether a reading came from the physical kit or the simulated data so I can verify the right source. (NFR-02)
- **US-24 — Match data to the right room.** As a team member, I want readings, facilities, and calendar events to refer to the same room so a search uses the correct records. (FR-07, NFR-02)
- **US-25 — Reproduce a ranking.** As a team member, I want to rerun a known search with the same data and preferences so I can verify the scores and ordering on the website and through API2. (FR-04, NFR-03)
- **US-26 — Score a reading against a desired profile.** As a team member testing the ranking, I want to check how a measured value maps to a score for one criterion so I can verify that changing the desired profile affects the score. (FR-03, FR-11)
- **US-27 — Apply the user’s criterion weights.** As a team member testing the ranking, I want to check how criterion scores and weights combine into one score per room so I can verify that the user’s priorities affect the result. (FR-02, FR-12)

### 3.5 University data owners

- **US-28 — Keep attendee details private.** As a timetable data owner, I want public calendar results to show room and time without attendee details so developers can check availability while keeping personal information private. (FR-08, NFR-04)

US-01 and US-22 cover accurate use of calendar records. US-14, US-21, and US-24 cover accurate room capacities and equipment records.

## 4. Requirements

### 4.1 Functional requirements — MVP

| ID | Requirement |
|---|---|
| **FR-01** | The room finder (UI1) shall accept a date and time slot or a flexible period with a required duration, as illustrated in the brief. It shall use calendar events and facilities to find rooms that meet the requested availability, capacity, and equipment needs. For a flexible search, it shall show a matching free slot for each recommended room. |
| **FR-02** | The room finder shall let users compare the importance of individual criteria in pairs using the Saaty scale, for example temperature versus light. |
| **FR-03** | The room finder shall provide a default desired profile for each environmental scoring criterion and let the user adjust it. A profile defines how well a measured value fits the desired conditions. |
| **FR-04** | The room finder shall show a final score and rank for each eligible room. It shall indicate when no room qualifies. |
| **FR-05** | The separate administrator view (UI2) shall plot sensor readings, including temperature, CO₂, sound, and humidity, for a selected room and period and show periods when sensors stopped reporting. |
| **FR-06** | The administrator view shall show the facilities of a selected room and calendar events over a selected period. |
| **FR-07** | The system shall receive sensor readings from the Arduino at the Raspberry Pi, load facilities and calendar data from configurable sources, and implement a database for sensor readings and room facilities. A deployment shall be able to supply its own compatible facilities and calendar data. |
| **FR-08** | Public REST API1 shall expose the decision system’s input data, including sensor readings for a requested period, facilities for a given room, and calendar events for a requested period. Sensor data shall include temperature, CO₂, sound, and humidity. |
| **FR-09** | Public REST API2 shall accept the user’s room requirements and preferences as request parameters and return room scores and rankings. Both APIs shall be freely accessible in the MVP. |
| **FR-10** | The system shall document both APIs using Swagger or an equivalent open-source tool and make the documentation accessible from a second, developer-facing tab in UI1. |
| **FR-11** | The system shall map raw sensor readings to a score for each environmental criterion using the applicable default or user-adjusted desired profile. |
| **FR-12** | The system shall combine the criterion scores into one final score per eligible room using weights derived from the user’s Saaty comparisons. When the user provides no comparisons, the system shall use default weights. |

### 4.2 Non-functional requirements

- **NFR-01 (Usability).** Users shall be able to search with defaults, see which fields are required, and understand how to correct invalid input.
- **NFR-02 (Data quality).** Readings shall be identifiable by room, measurement, timestamp, unit, and source so live and simulated data can be distinguished. Sensor, facilities, and calendar records shall use a consistent way to identify the same room. Missing readings shall be visible and never treated as measured zeroes.
- **NFR-03 (Consistency).** Given the same data and preferences, UI1 and API2 shall give the same scores and ranking.
- **NFR-04 (Privacy).** Public calendar data shall expose the information needed to understand room availability, such as room and time. Personal details such as attendee names shall stay private.

### 4.3 Room eligibility and ranking

- **Eligibility.** The requested time, minimum capacity, and required equipment determine which rooms qualify. For a one-hour class that needs 20 seats and a projector, a room qualifies only if it is free for the full hour, seats at least 20, and has a projector.
- **Ranking.** The system scores the eligible rooms using the desired conditions and the importance of each criterion. For example, a teacher can prefer a temperature near 21°C and say that quiet matters more than brightness. Giving quiet more weight may move a quieter room higher in the results. A room without the required projector stays ineligible regardless of its comfort score.

## 5. Potential stakeholder conflicts

| Stakeholders | Conflict | Proposed resolution |
|---|---|---|
| Teachers and students | Both may want the same room and time. Scheduled teaching may have priority under university rules. | Use existing calendar bookings to determine availability. Confirm whether any teaching-priority rule should also affect which rooms are recommended. |
| End users and professor | Users want to adjust their preferences, while the client expects the ranking to start from evidence-based defaults. | Show the default profile alongside the user’s settings and confirm which limits, if any, must apply to every search. |
| End users and administrator | Users want a recommendation even when data is missing, while the administrator needs rankings to be trustworthy. | Show which readings are missing and agree on when coverage is too limited to score a room. |
| Third-party developers and timetable staff | Developers need public calendar data, while timetable staff may manage events with private details. | Expose availability data in the public API, with room and time sufficient for the MVP. |

## 6. Assumptions and open questions

### Current assumptions

1. ComfortRoom uses calendar events to check whether rooms are free. The demo uses a generated calendar. Users make or change bookings in the calendar that supplies the data.
2. One physical room has the Arduino sensor kit. Its available sensors provide live readings for that room. Other rooms use the supplied or generated simulated readings. The demo calendar and facilities data use the same room IDs and cover the demo searches.
3. The first hardware check is to read temperature and humidity in the physical room and verify that the Raspberry Pi stores those readings.

### Open questions

- **Hardware:** Does the assignment require specific sensors or a specific transport for Comm B?
- **Room access:** Does the university give teachers priority for certain rooms or times? If so, is it already reflected in the calendar, or should it affect recommendations?
- **Data:** How will a deployment supply its facilities and calendar data, and what units do the supplied readings use?
- **Calendar coverage:** How will the room finder distinguish an available slot from one where the calendar data is missing or does not cover the requested dates?
- **Ranking:** Which criteria, sources, desired ranges, and default weights will we use? For a future time slot, should a room’s comfort score use recent readings or historical readings from a comparable time? How much data do we need to score it?
- **Monitoring:** How long should a sensor be silent before the system shows a disconnection? Which reference time should be used when replaying historical data?
