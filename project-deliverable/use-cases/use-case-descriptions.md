# Use Case Descriptions — ComfortRoom

## Design decisions behind the use cases

| Topic | Decision |
|---|---|
| Accounts | An account is needed only to **save** preferences (User), to open the admin UI (Administrator) and to use API1 (Developer). A guest can search and change preferences for one search without logging in. |
| REST APIs | **API1** (input data) is protected with JWT. **API2** (ranking) is public, as required by the assignment; preferences and needs are passed as API2 parameters. |
| Account creation | Users and developers register themselves (use case 1). The administrator account is created when the system is installed. |
| Reference time | The latest measurement timestamp stored in the database. Sensor "now" is this timestamp, not the wall clock. The test data is historical, so a wall-clock check would mark every sensor as silent. |
| Two clocks | Sensor rules (the 30-minute score window, the 5-minute disconnection check, "slot is in the past") use the reference time. Sync freshness uses the wall clock: use case 10 warns when `calendarSyncedAt` is more than 30 minutes before the current time. |
| Slot validity | A slot is in the past when **its end is earlier than the reference time**. The wall clock is not used: with the test data every real-world date would look past. |
| Score vs slot (MVP) | **MVP limitation:** the score uses the 30 minutes before the reference time. The slot is used only to decide whether the room is free. Possible improvement: a window of the same hour of day. |
| Sensor values for the score | For each room the system uses the **average of each quantity over the 30 minutes before the reference time**, because the values change a lot from one minute to the next. |
| Saaty vs assignment | The assignment compares **criteria** (example: Temperature 5× more important than Light). This project compares **4 groups** (6 pairs instead of 28). Inside a group the weights are equal, so that PDF example cannot be reproduced exactly. |
| Criteria importance | **Hierarchical AHP**: the user compares 4 groups (6 pairs) on the Saaty scale. Inside a group every criterion has the same weight. The user cannot make temperature matter more than humidity, or CO₂ more than PM10. `Preferences` only stores the comparisons. **AHP** computes the group weights and the consistency ratio. |
| Default weights | Not equal groups. Air quality and Thermal comfort are equal; Acoustic comfort and Visual comfort are equal; each of the first two is **3×** each of the last two (Saaty 3). Group weights are **37.5% / 37.5% / 12.5% / 12.5%**. The matrix is consistent (CR = 0). Sound is then 12.5%, not equal to the whole air-quality group (37.5%). Health-related groups are set above noise and light because the assignment ranks rooms by how healthy they are. |
| Missing measurement | A criterion with no value in that 30-minute window is left out of the score. The remaining weights are **renormalized so they sum to 1**, and the ranking says which criteria were missing. |
| Facilities | Projector, seats, computers and robots are **filters only**. They never enter the score. |
| Disconnected sensor | A sensor is disconnected when its last measurement is **more than 5 minutes before the reference time**. Gaps inside a chosen period on the admin UI are stretches longer than 5 minutes with no stored measurement. These periods are computed from the measurements; they are not stored. |
| Synchronization | Timer **every 15 minutes**. Use case 10 warns when `calendarSyncedAt` is older than **30 minutes** (2 missed cycles). |
| Database | The database is part of the system, so it is not an actor. Steps say "the system stores … in the database". |
| Role | The role **is** the subclass of `Account` (`EndUser`, `Administrator`, `Developer`). There is no separate role attribute. The JWT carries the subclass name. |
| Saved preferences | Registration does not create preferences. A row is stored only when a logged-in user saves. Until then, and for a guest, the search uses the default comparisons and the regulation profiles. Changes made during one search are not saved. |

### Criteria groups (used in use case 4)

| Group | Criteria |
|---|---|
| Thermal comfort | temperature, humidity |
| Air quality | CO₂, VOC, PM2.5, PM10 |
| Acoustic comfort | sound |
| Visual comfort | light |

## Actors

| Actor | Type | Description |
|---|---|---|
| User (Student / Teacher) | primary | Looks for the most suitable room. Can search as a guest or log in to use and save preferences. |
| Administrator | primary | Monitors that the system works: sensor data, disconnections, facilities, calendar. |
| Developer | primary | Third-party developer who uses the REST APIs. Needs an account only for API1. |
| Arduino | primary | Reads the room sensors and sends the measurements to the Raspberry Pi. |
| Timer | primary | Starts the synchronization of facilities and calendar every 15 minutes. |
| Facilities service | secondary | External source of room facilities (Comm. C). |
| University calendar | secondary | External source of calendar events (Comm. D). |

## Overview

| # | Use case | Primary actor | Secondary actor | «include» / «extend» |
|---|---|---|---|---|
| 1 | Register | User, Developer | | |
| 2 | Log In | User, Administrator, Developer | | included by 3, 9, 10, 11; used by the API1 path of 8 and by 12 |
| 3 | Manage Preferences | User | | includes 2; extended by 4, 5 |
| 4 | Set Criteria Importance | User, Developer | | extends 3 and 6 |
| 5 | Adjust Desired Profile | User, Developer | | extends 3 and 6 |
| 6 | Find Suitable Room | User, Developer | | includes 7 |
| 7 | Specify Needs | User, Developer | | included by 6 |
| 8 | View Room Facilities | User, Administrator, Developer | | extends 6 |
| 9 | View Sensor Data | Administrator, Developer | | includes 2 |
| 10 | View Calendar Events | Administrator, Developer | | includes 2 |
| 11 | Monitor Sensor Status | Administrator | | includes 2; extends 9 |
| 12 | View API Documentation | Developer | | |
| 13 | Send Sensor Measurements | Arduino | | |
| 14 | Synchronize Facilities & Calendar | Timer | Facilities service, University calendar | |

## Short descriptions

**1. Register.** A user or a developer creates an account (name, e-mail, password, account type). A user needs it to save preferences; a developer needs it to use API1. Registration does not store preferences. Without an account a user can still search as a guest.

**2. Log In.** A user, administrator or developer signs in with e-mail and password and receives a JWT. The role is the subclass of `Account`; the token carries that subclass name. The token is needed to save preferences, to open the admin UI and to call API1.

**3. Manage Preferences.** A logged-in user creates, views or changes their saved preferences: the importance of criteria groups and the desired profiles. The server keeps them and uses them at the next search.

**4. Set Criteria Importance.** The user compares the 4 criteria groups in pairs on the Saaty scale (1–9), e.g. "Air quality 3× more important than Visual comfort". The comparisons are stored with the preferences. AHP turns them into group weights, checks the consistency ratio, and each group weight is split equally among its criteria. Available while searching (for one search, not saved) and from "Manage Preferences" (saved). The default is 37.5% / 37.5% / 12.5% / 12.5% for air, thermal, acoustic and visual.

**5. Adjust Desired Profile.** The user changes what counts as a good value for a criterion, e.g. 20–25 °C instead of the regulation's range. By default every profile follows EU / national standards. Available while searching (for one search) and in "Manage Preferences" (saved).

**6. Find Suitable Room.** The user (through UI1) or a developer (through API2) asks for the best room. The system filters rooms by needs and availability, averages each quantity over the 30 minutes before the reference time, maps the averages to the desired profiles, combines them with the weights and returns scores and a ranking. MVP limitation: the time slot is used only to test occupancy; it does not change the score. Guests and users who never saved preferences get the default group weights (37.5% / 37.5% / 12.5% / 12.5%) and the regulation profiles. A logged-in user with saved preferences gets those. Preferences can also be changed for one search, without saving.

**7. Specify Needs.** For each search the user gives the time slot (or a flexible period) and the required facilities: videoprojector, number of seats, computers, robots. Rooms that do not meet the needs or are busy are left out. Weights and desired profiles are not needs; they are set in use cases 4 and 5.

**8. View Room Facilities.** Shows the equipment of a given room. Opened from the ranking, from the admin UI or through API1.

**9. View Sensor Data.** Shows temperature, humidity, CO₂, sound, light, VOC and PM2.5/PM10 of a room over a chosen period, as plots (UI2) or as JSON (API1).

**10. View Calendar Events.** Shows the events booked in the rooms over a chosen period (UI2, API1).

**11. Monitor Sensor Status.** The administrator sees which sensors are online and the periods when a sensor sent no data for more than 5 minutes. A sensor is disconnected when its last measurement is more than 5 minutes before the reference time. The periods are computed from the stored measurements.

**12. View API Documentation.** A developer opens the Swagger documentation of API1 and API2 (second tab of UI1) to learn how to use the system.

**13. Send Sensor Measurements.** The Arduino reads the room sensors (Comm. A) and sends the values to the Raspberry Pi (Comm. B), where they are stored in the database.

**14. Synchronize Facilities & Calendar.** Every 15 minutes a timer starts the synchronization. The system fetches room facilities from the facilities service (Comm. C) and events from the university calendar (Comm. D) and updates the database.

---

## Full descriptions

## 1. Register

| | |
|---|---|
| **Use Case Name** | Register |
| **Actors** | User (primary)<br>Developer (primary) |
| **Preconditions** | - The actor does not have an account yet. |
| **Main Success Scenario** | The actor wants to create an account.<br>1. Actor opens "Register".<br>2. Actor enters name, e-mail and password and chooses the account type (User or Developer).<br>3. System validates the data.<br>4. System checks that the e-mail is not used yet.<br>5. System stores the account in the database with the password hashed.<br>6. Actor receives a confirmation and is logged in.<br><br>The administrator account is not created here: it is created when the system is installed. No preferences are stored at registration. |
| **Extensions (Alternate Flows)** | **3a. Required fields are empty or invalid (e.g. wrong e-mail format, password shorter than 8 characters):**<br>1. System highlights the invalid fields;<br>2. Actor corrects them and resubmits.<br><br>**4a. E-mail is already used:**<br>1. System shows an error;<br>2. System suggests to log in instead.<br><br>**5a. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. No account is created. |
| **Postconditions** | Success: the account exists and the actor is logged in. A User account has no saved preferences yet.<br>Exception: no account is created and the actor is informed why. |

## 2. Log In

| | |
|---|---|
| **Use Case Name** | Log In |
| **Actors** | User (primary)<br>Administrator (primary)<br>Developer (primary) |
| **Preconditions** | - The actor has an account. |
| **Main Success Scenario** | The actor wants to sign in to use the functions of their role.<br>1. Actor opens the login page (or calls the login endpoint of the API).<br>2. Actor enters e-mail and password.<br>3. System checks the credentials in the database.<br>4. System reads the subclass of the account. That subclass is the role: `EndUser`, `Administrator` or `Developer`. There is no separate role field.<br>5. System issues a JWT whose role claim is that subclass name, with an expiry time.<br>6. System opens the requested page or endpoint with the rights of this subclass.<br><br>"Log In" is included in "Manage Preferences", "View Sensor Data", "View Calendar Events" and "Monitor Sensor Status". The API1 path of "View Room Facilities" and "View API Documentation" use it when a token is required. |
| **Extensions (Alternate Flows)** | **3a. Wrong e-mail or password:**<br>1. Login fails;<br>2. System shows an error and asks to try again.<br><br>**3b. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. Login is not possible.<br><br>**5a. Token has expired:**<br>1. System rejects the request;<br>2. System asks the actor to log in again. |
| **Postconditions** | Success: the actor holds a valid token with the rights of their role.<br>Exception: the actor is not logged in and is informed why. |

## 3. Manage Preferences

| | |
|---|---|
| **Use Case Name** | Manage Preferences |
| **Actors** | User (primary) |
| **Preconditions** | - User is logged in ("Log In" is included). |
| **Main Success Scenario** | User wants to save or change their preferences for future searches.<br>1. User opens "My preferences".<br>2. System loads the saved preferences from the database.<br>3. System shows the importance of the criteria groups and the desired profile of each criterion.<br>4. User may change the importance of the groups ("Set Criteria Importance" extends this use case).<br>5. User may change the desired profiles ("Adjust Desired Profile" extends this use case).<br>6. User chooses "Save".<br>7. System saves the preferences in the database.<br>8. User receives a confirmation.<br><br>Steps 4 and 5 can be done in any order; the user may change only one of them. |
| **Extensions (Alternate Flows)** | **2a. User has no saved preferences yet:**<br>1. System shows the defaults: group weights 37.5% / 37.5% / 12.5% / 12.5% (air, thermal, acoustic, visual), and every profile is the regulation profile.<br><br>**6a. User chooses "Reset to default":**<br>1. System restores those default comparisons and the regulation profiles;<br>2. Continue at step 7.<br><br>**7a. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. The saved preferences stay unchanged. |
| **Postconditions** | Success: the new preferences are stored and used in the next searches.<br>Exception: the saved preferences are unchanged and the user is informed why. |

## 4. Set Criteria Importance

| | |
|---|---|
| **Use Case Name** | Set Criteria Importance |
| **Actors** | User (primary)<br>Developer (primary, through API2) |
| **Preconditions** | - "Find Suitable Room" or "Manage Preferences" is in progress. |
| **Main Success Scenario** | User wants to say which groups matter more to them.<br>1. System shows the 6 pairs of criteria groups (Thermal comfort, Air quality, Acoustic comfort, Visual comfort) with a Saaty scale from 1 to 9 in both directions. A new profile shows the default comparisons (air and thermal are 3× acoustic and 3× visual).<br>2. For each pair, user chooses which group is more important and how much (e.g. "Air quality 3× more important than Visual comfort").<br>3. System stores the pairwise comparisons with the preferences. It does not compute weights itself.<br>4. AHP computes the weight of each group from those comparisons.<br>5. AHP computes the consistency ratio.<br>6. System splits the weight of each group equally among its criteria.<br>7. System shows the resulting weights to the user.<br><br>Inside a group the criteria stay equal: temperature weighs the same as humidity, and CO₂, VOC, PM2.5 and PM10 weigh the same. A developer passes the 6 comparisons, or the 4 group weights, as API2 parameters. |
| **Extensions (Alternate Flows)** | **2a. User leaves a pair unchanged:**<br>1. System keeps the value already shown for that pair. On a profile that was never edited, that value is the default comparison, not 1.<br><br>**5a. Consistency ratio is above 0.1:**<br>1. System warns that the answers contradict each other and shows the most inconsistent pair;<br>2. User changes the comparisons; continue at step 3.<br><br>**Developer passes the 4 group weights directly:**<br>1. System checks that every weight is ≥ 0 and that the four weights sum to 1;<br>2. If they do, system splits each group weight equally among its criteria and skips the consistency check;<br>3. If they do not, system rejects the weights and does not rank. |
| **Postconditions** | Success: a consistent set of weights is used in the current search or saved in "Manage Preferences".<br>Exception: weights are not accepted until the comparisons are consistent, or until the direct group weights are valid. |

## 5. Adjust Desired Profile

| | |
|---|---|
| **Use Case Name** | Adjust Desired Profile |
| **Actors** | User (primary)<br>Developer (primary, through API2) |
| **Preconditions** | - "Find Suitable Room" or "Manage Preferences" is in progress. |
| **Main Success Scenario** | User wants to change what counts as a good value for a criterion.<br>1. User selects a criterion (e.g. Temperature).<br>2. System shows the current profile as a trapezoid, with the regulation profile for reference. The trapezoid has four points: lower limit, start of the optimum, end of the optimum, upper limit.<br>3. User moves these points (e.g. optimum 20–25 °C instead of the regulation optimum).<br>4. System validates the limits: lower ≤ optimum start ≤ optimum end ≤ upper.<br>5. System updates the graph.<br><br>Between the two optimum points the compliance is 1. It falls to 0 at the lower and upper limits, and stays 0 outside them. User may repeat steps 1–5 for other criteria. A developer passes the four points per criterion as API2 parameters. |
| **Extensions (Alternate Flows)** | **3a. User chooses "Use regulation profile":**<br>1. System restores the default profile for this criterion.<br><br>**4a. Limits are not valid (e.g. the points are out of order, or a point is outside the sensor range):**<br>1. System highlights the invalid limit;<br>2. User corrects it. |
| **Postconditions** | Success: the changed profiles are used in the current search or saved in "Manage Preferences".<br>Exception: the profile is unchanged. |

## 6. Find Suitable Room

| | |
|---|---|
| **Use Case Name** | Find Suitable Room |
| **Actors** | User (primary)<br>Developer (primary, through API2) |
| **Preconditions** | - Sensor data, room facilities and calendar events are stored in the database. |
| **Main Success Scenario** | User wants to find the most suitable room for a time slot.<br>1. User opens the search (UI1) or a developer calls API2 (public, no login).<br>2. User specifies the needs ("Specify Needs" is included).<br>3. System takes the preferences: the ones given for this search, else the user's saved preferences, else the defaults (group weights 37.5% / 37.5% / 12.5% / 12.5%, regulation profiles).<br>4. System keeps only the rooms that have the required facilities and are free in the time slot. The slot is an occupancy filter only.<br>5. System reads the reference time (the latest measurement timestamp in the database) and computes, for each remaining room, the average of each quantity over the 30 minutes before it.<br>6. System maps each average to a compliance value (0–1) using the desired profile.<br>7. AHP supplies the weights from the stored comparisons. System combines the compliance values with those weights into a final score per room.<br>8. System sorts the rooms by score.<br>9. System displays the ranking (UI1) or returns it as JSON (API2), with the score of each room and its compliance per criterion. |
| **Extensions (Alternate Flows)** | **4a. No room meets the needs or is free:**<br>1. System shows "No room found";<br>2. System suggests to change the time slot or the needs.<br><br>**5a. A criterion has no measurement in that 30-minute window:**<br>1. System marks this criterion of the room as "No data";<br>2. System drops its weight and renormalizes the remaining weights so they sum to 1;<br>3. System shows a warning listing the missing criteria.<br><br>**5b. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. No ranking is computed.<br><br>**9a. User wants to change preferences only for this search (no login needed):**<br>1. User opens "Set Criteria Importance" and/or "Adjust Desired Profile" (extend this use case);<br>2. System recomputes the ranking. The preferences are not saved.<br><br>**9b. User wants to see a room's equipment:**<br>1. User opens "View Room Facilities" (extends this use case). |
| **Postconditions** | Success: the user sees the rooms ranked from the most to the least suitable.<br>Exception: no ranking is shown and the user is informed why. |

## 7. Specify Needs

| | |
|---|---|
| **Use Case Name** | Specify Needs |
| **Actors** | User (primary)<br>Developer (primary, through API2) |
| **Preconditions** | - "Find Suitable Room" is in progress. |
| **Main Success Scenario** | User wants to say what the room must have for this search.<br>1. User selects the date and time slot.<br>2. User selects the required facilities: videoprojector, minimum number of seats, minimum number of computers, minimum number of robots.<br>3. System validates the needs. A slot is in the past when its end is earlier than the reference time. The wall clock is not used.<br>4. System passes the needs to "Find Suitable Room".<br><br>A developer gives the same needs as API2 parameters. Weights and desired profiles are parameters of use cases 4 and 5, not of this one. |
| **Extensions (Alternate Flows)** | **1a. User gives a flexible period (e.g. "any 1-hour slot in the next 2 days"):**<br>1. System rejects the period when its end is earlier than the reference time;<br>2. Otherwise system checks every slot of that length whose end is at or after the reference time;<br>3. For each room, the ranking shows the first free slot that fits. The score is the same for every slot.<br><br>**2a. User selects no facilities:**<br>1. All rooms are kept.<br><br>**3a. Needs are not valid (slot ends before the reference time, or a number is negative):**<br>1. System highlights the invalid field (UI1) or returns an error (API2);<br>2. User corrects it. |
| **Postconditions** | Success: valid needs are passed to the search.<br>Exception: the search is not started until the needs are valid. |

## 8. View Room Facilities

| | |
|---|---|
| **Use Case Name** | View Room Facilities |
| **Actors** | User (primary)<br>Administrator (primary)<br>Developer (primary, through API1) |
| **Preconditions** | - A developer using API1 is logged in.<br>- An administrator opening facilities from UI2 is logged in.<br>- A user opening facilities from the ranking does not need an account. |
| **Main Success Scenario** | The actor wants to see the equipment of a room.<br>1. Actor selects a room (from the ranking, from UI2, or through API1).<br>2. System retrieves the room facilities from the database.<br>3. System displays the facilities (videoprojector, seating capacity, computers, robots for training) or returns them as JSON (API1).<br><br>Facilities that the room does not have are shown as "No" or 0. |
| **Extensions (Alternate Flows)** | **2a. Room does not exist:**<br>1. System shows "Room not found".<br><br>**2b. Database is not reachable:**<br>1. System shows "Service unavailable". |
| **Postconditions** | Success: the actor sees the facilities of the room.<br>Exception: the actor is informed why they cannot be shown. |

## 9. View Sensor Data

| | |
|---|---|
| **Use Case Name** | View Sensor Data |
| **Actors** | Administrator (primary)<br>Developer (primary, through API1) |
| **Preconditions** | - Actor is logged in ("Log In" is included). |
| **Main Success Scenario** | The actor wants to see how the indoor conditions of a room changed over time.<br>1. Actor selects a room.<br>2. Actor selects one or more quantities (temperature, humidity, CO₂, sound, light, VOC, PM2.5, PM10).<br>3. Actor selects a period.<br>4. System retrieves the measurements from the database.<br>5. System displays them as plots over time (UI2) or returns them as JSON (API1). |
| **Extensions (Alternate Flows)** | **3a. Period is not valid (end before start, too long):**<br>1. System shows an error;<br>2. Actor changes the period.<br><br>**4a. No measurements in the period:**<br>1. System shows "No data for this period".<br><br>**5a. There are gaps in the data:**<br>1. System shows the gaps on the plot;<br>2. Administrator may open "Monitor Sensor Status" (extends this use case). |
| **Postconditions** | Success: the actor sees the measurements of the period.<br>Exception: the actor is informed that no data is available. |

## 10. View Calendar Events

| | |
|---|---|
| **Use Case Name** | View Calendar Events |
| **Actors** | Administrator (primary)<br>Developer (primary, through API1) |
| **Preconditions** | - Actor is logged in ("Log In" is included). |
| **Main Success Scenario** | The actor wants to see when rooms are booked.<br>1. Actor selects a period.<br>2. Actor may select a room.<br>3. System retrieves the calendar events from the database.<br>4. System displays the events with room, start, end and title (UI2) or returns them as JSON (API1). |
| **Extensions (Alternate Flows)** | **1a. Period is not valid:**<br>1. System shows an error;<br>2. Actor changes the period.<br><br>**3a. No events in the period:**<br>1. System shows "No events".<br><br>**3b. `calendarSyncedAt` is more than 30 minutes before the current time (2 missed 15-minute cycles):**<br>1. System shows `calendarSyncedAt` as a warning. |
| **Postconditions** | Success: the actor sees the events of the period.<br>Exception: the actor is informed that no events are available. |

## 11. Monitor Sensor Status

| | |
|---|---|
| **Use Case Name** | Monitor Sensor Status |
| **Actors** | Administrator (primary) |
| **Preconditions** | - Administrator is logged in ("Log In" is included). |
| **Main Success Scenario** | Administrator wants to check that all sensors work.<br>1. Administrator opens "Sensor status" in UI2.<br>2. System retrieves the time of the last measurement of each sensor.<br>3. System marks a sensor as disconnected if that time is more than 5 minutes before the reference time, otherwise as online.<br>4. System displays the status of every sensor per room.<br>5. Administrator selects a sensor and a period.<br>6. System computes, from the stored measurements, the stretches of that period longer than 5 minutes with no measurement, and shows them. |
| **Extensions (Alternate Flows)** | **3a. A sensor is disconnected now:**<br>1. System highlights it and shows since when it is disconnected.<br><br>**6a. No disconnections in the period:**<br>1. System shows "No disconnections". |
| **Postconditions** | Success: the administrator knows which sensors work and when they were disconnected.<br>Exception: the administrator is informed that the status cannot be shown. |

## 12. View API Documentation

| | |
|---|---|
| **Use Case Name** | View API Documentation |
| **Actors** | Developer (primary) |
| **Preconditions** | - The system is running. |
| **Main Success Scenario** | Developer wants to learn how to use the system's APIs.<br>1. Developer opens the "Developers" tab of UI1.<br>2. System shows the Swagger documentation of API1 and API2.<br>3. Developer selects an endpoint.<br>4. System shows its parameters, responses, examples and whether a token is needed (API1).<br>5. Developer may try the endpoint from the documentation. |
| **Extensions (Alternate Flows)** | **5a. Endpoint belongs to API1 (protected):**<br>1. Developer logs in ("Log In") and enters the token in Swagger;<br>2. Developer tries the endpoint again. |
| **Postconditions** | Success: the developer knows how to call the APIs.<br>Exception: — |

## 13. Send Sensor Measurements

| | |
|---|---|
| **Use Case Name** | Send Sensor Measurements |
| **Actors** | Arduino (primary) |
| **Preconditions** | - Sensors are connected to the Arduino;<br>- The Raspberry Pi is running. |
| **Main Success Scenario** | Arduino sends the current indoor conditions of its room.<br>1. Arduino reads every sensor of the room (Comm. A).<br>2. Arduino builds a message with room, quantity, value and timestamp.<br>3. Arduino sends the message to the Raspberry Pi (Comm. B).<br>4. System validates the message.<br>5. System stores the measurement in the database.<br>6. System updates the time of the last measurement of the sensor.<br><br>Steps 1–6 repeat every minute. |
| **Extensions (Alternate Flows)** | **1a. A sensor does not answer:**<br>1. Arduino skips this sensor and sends the others.<br><br>**4a. Value is outside the sensor's physical range:**<br>1. System rejects the value and logs it.<br><br>**5a. Database is not reachable:**<br>1. System keeps the messages in a buffer;<br>2. System stores them when the database is back. |
| **Postconditions** | Success: the new measurements are stored and the time of the last measurement is updated.<br>Exception: the value is rejected and logged. |

## 14. Synchronize Facilities & Calendar

| | |
|---|---|
| **Use Case Name** | Synchronize Facilities & Calendar |
| **Actors** | Timer (primary)<br>Facilities service (secondary)<br>University calendar (secondary) |
| **Trigger** | Timer, every 15 minutes. |
| **Preconditions** | - The external services are configured. |
| **Main Success Scenario** | System keeps room facilities and calendar events up to date.<br>1. System requests the room facilities from the facilities service (Comm. C).<br>2. System validates the data.<br>3. System updates the facilities in the database and sets `SyncStatus.facilitiesSyncedAt` to the current time.<br>4. System requests the events of the coming period from the university calendar (Comm. D).<br>5. System updates the calendar events in the database and sets `SyncStatus.calendarSyncedAt` to the current time.<br><br>The two timestamps are written separately. One source can succeed while the other fails. |
| **Extensions (Alternate Flows)** | **1a. Facilities service is not reachable:**<br>1. System keeps the previous facilities;<br>2. System does not change `facilitiesSyncedAt`;<br>3. System logs the error and continues at step 4.<br><br>**2a. Data is not valid (e.g. missing field, broken format):**<br>1. System fills missing facilities with 0 / "No";<br>2. System rejects records it cannot read and logs them.<br><br>**4a. University calendar is not reachable:**<br>1. System keeps the previous events;<br>2. System does not change `calendarSyncedAt`;<br>3. System logs the error and retries at the next interval. |
| **Postconditions** | Success: each source that was written matches the external service, and only that source's timestamp has moved.<br>Exception: the previous data of the failed source is kept, and its timestamp is unchanged. |
