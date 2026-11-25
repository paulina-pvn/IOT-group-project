# ComfortRoom — Assignment checklist

Use this checklist to track the design decisions, evidence, and report required by the IoT project brief. The [MVP requirements](requirements.md) describe what ComfortRoom should do. Keep owners, sub-issues, and progress in GitHub using the IDs below.

## 1. Design decisions

### AR-01 — Communication protocols

Brief items 1 and 5. Recommend and justify a protocol for each of the following connections.

- [ ] **US-29 — Comm A.** Sensors to the Arduino.
- [ ] **US-30 — Comm B.** Arduino to the Raspberry Pi.
- [ ] **US-31 — Comm C.** Room facilities source to the Raspberry Pi.
- [ ] **US-32 — Comm D.** Calendar source to the Raspberry Pi.

For Comm E, the brief specifies REST. The two public API groups are defined in FR-08 and FR-09.

### AR-02 — Database choice

- [ ] **US-33 — Justify the database.** Explain why the implemented database is suitable for storing sensor readings and room facilities. See FR-07 and brief item 2.

### AR-03 — Decision tree

- [ ] **US-34 — Define and justify the decision tree.** Explain why each ranking criterion belongs in the tree. Support the choices with relevant European or national regulations or standards where possible. See brief item 3.

### AR-04 — Default scoring profiles

- [ ] **US-35 — Justify the default profiles.** Document what supports each default profile and cite relevant European or national standards, regulations, or recommendations where possible. See FR-03 and brief items 3 and 4.

The brief suggests AHP or a similar multi-criteria method. FR-02 requires Saaty-scale input.

## 2. Report and submission

- [ ] **US-36 (AR-05) — Explain the implemented architecture.** Submit the group report explaining the design choices and how they address the brief. Document the Arduino-to-Pi test in the report. The brief encourages screenshots of working features.
- [ ] **US-37 (AR-06) — Record team responsibilities.** Explain who was responsible for each area of work.
- [ ] **US-38 (AR-06) — Report the milestones.** Record what the team delivered at each milestone.
- [ ] **US-39 (AR-07) — Compare planned and actual dates.** Include a Gantt chart that shows the original plan and what actually happened.

For the presentation, pitch ComfortRoom to the professor using the supplied and generated demo data.

## 3. Check the software against the brief

Use the [functional requirements](requirements.md#41-functional-requirements--mvp) to verify these parts of the software before submission.

| Brief item | What to check | Requirements |
|---|---|---|
| 2 | The database stores sensor readings and room facilities. | FR-07 |
| 4 | The system scores and ranks rooms using desired profiles and criterion weights. | FR-03, FR-04, FR-11, FR-12 |
| 5 | Both REST APIs are publicly available and documented. | FR-08, FR-09, FR-10 |
| 6 | The room finder shows scores and rankings, supports Saaty comparisons and adjustable profiles, and links to the API docs. The administrator view shows sensor history, disconnection periods, facilities, and calendar events. | FR-01–FR-06, FR-10 |

## 4. Suggested build order for GitHub

### 4.1 Start with two groups of three

These are suggested ownership splits for the first phase. Both groups can start at the same time.

| Group | Suggested ownership | First milestone |
|---|---|---|
| **Project setup and database (3 people)** | One person reviews the MVP requirements and drafts the planned Gantt chart. One sets up the GitHub repo, board, and first issues. One sets up the database and imports a small sample of the sensor and facilities data. | MVP issues have owners, the original Gantt dates are saved, and the database can return a reading for a known room. (US-21, US-24, US-33, US-37–US-39) |
| **Hardware (3 people)** | One identifies the kit components and checks how to connect the temperature and humidity sensor. One gets the Arduino to print readings. One sets up the Raspberry Pi and tests receiving a reading from the Arduino. | Record a live temperature and humidity reading from the kit on the Pi with a room ID and timestamp. (US-20, US-23, US-29, US-30) |

Agree early on what a sensor reading contains: room ID, measurement, value, unit, timestamp, and source. Once the Pi receives a reading and the database is ready, connect the two and verify that the database stores the live reading under the right room. The website can use the sample data while this is in progress.

### 4.2 Build the frontend and backend in parallel

Before splitting up, agree on one example request and response for a fixed-time room search. Define which fields the search sends (time, seats, and equipment) and what the backend returns (eligible rooms and scores, or an empty result). Give both sides the same example. The frontend can build with placeholder responses while the backend implements the actual search.

| Milestone | Frontend | Backend and data | Done when |
|---|---|---|---|
| **First working room search** | Build the fixed-time search form and results page using the agreed example response. (US-01, US-06–US-07, US-10) | Load the demo calendar, check overlapping bookings and room requirements, and return a ranking using default profiles and weights. Define the decision tree and justify its defaults as the scoring is implemented. (US-01, US-08, US-22, US-26–US-27, US-31–US-32, US-34–US-35) | The website can run a search against the real backend using the demo data. Check the order with a known example. (US-25) |
| **Complete the room finder** | Add flexible time searches, Saaty comparisons, adjustable desired conditions, and messages for invalid inputs or no matches. (US-02, US-05, US-09, US-11–US-12) | Implement flexible slot matching, validate the inputs, and apply user preferences to the ranking. Publish the ranking as API2. (US-02, US-04–US-05, US-09, US-11–US-12, US-26–US-27) | The website and API2 return the same ranking for the same request. (US-25) |
| **Admin view and public data API** | Build the sensor charts, gaps in reporting, facilities and calendar views, and Developers tab. Use agreed example responses while the backend is being built. (US-03, US-13–US-15, US-19) | Implement the data endpoints for sensor history, facilities, and calendar events. Keep attendee details out of the public response and document both APIs. (US-16–US-19, US-28) | The admin view and both public APIs work, and the Developers tab opens their documentation. |

Once the first search works, the admin view and data API can progress alongside the remaining room finder work. In GitHub, use frontend, backend, and test sub-issues under a shared story when different people own them. Keep one integration check on the parent story.

### 4.3 Finish the report and demo

Write down decisions as the work happens, especially the communication protocols, database choice, and scoring criteria. Before submission, check the website against the brief, rehearse the demo, and finish the report. Update the Gantt chart with actual dates while keeping the original plan for comparison. (US-36–US-39)
