# Use Case Descriptions — ComfortRoom

Diagram: `use-case-diagram.pdf` (source `.mdj` from StarUML in the same folder).

## Actors

| Actor | Type | Description |
|---|---|---|
| User (Student / Teacher) | primary | Looks for the most suitable room. Can search as a guest or log in to use saved preferences. |
| Administrator | primary | Monitors that the system works: sensor data, disconnections, facilities, calendar. |
| Developer | primary | Third-party developer who uses the REST APIs. |
| Arduino | secondary | Reads the room sensors and sends the measurements to the Raspberry Pi. |
| Facilities service | secondary | External source of room facilities (Comm. C). |
| University calendar | secondary | External source of calendar events (Comm. D). |
| Database | secondary | Stores accounts, preferences, measurements, facilities and calendar events. |

## Overview

| # | Use case | Primary actor | Secondary actor | «include» / «extend» |
|---|---|---|---|---|
| 1 | Register | User | Database | |
| 2 | Log In | User, Administrator, Developer | Database | included by 3, 9, 10, 11 |
| 3 | Manage Preferences | User | Database | includes 2, 4, 5; extends 6 |
| 4 | Set Criteria Importance | User | | included by 3 |
| 5 | Adjust Desired Profile | User | | included by 3 |
| 6 | Find Suitable Room | User, Developer | Database | includes 7 |
| 7 | Specify Needs | User, Developer | | included by 6 |
| 8 | View Room Facilities | User, Administrator, Developer | Database | extends 6 |
| 9 | View Sensor Data | Administrator, Developer | Database | includes 2 |
| 10 | View Calendar Events | Administrator, Developer | Database | includes 2 |
| 11 | Monitor Sensor Status | Administrator | Database | includes 2; extends 9 |
| 12 | View API Documentation | Developer | | |
| 13 | Send Sensor Measurements | Arduino | Database | |
| 14 | Synchronize Facilities & Calendar | Facilities service, University calendar | Database | |

## Short descriptions

**1. Register.** A user creates an account (name, e-mail, password) so the system can remember their preferences. Without an account the user can still search as a guest.

**2. Log In.** A user, administrator or developer signs in with e-mail and password and receives a token (JWT). The token is needed to save preferences, to open the admin UI and to call the protected API endpoints.

**3. Manage Preferences.** A logged-in user creates, views or changes their saved preferences: the desired profile and the importance of criteria. The server keeps them and uses them at the next search.

**4. Set Criteria Importance.** The user compares criteria in pairs on the Saaty scale (1–9), e.g. "Temperature 5× more important than Light". The system turns the answers into weights and checks that they are consistent.

**5. Adjust Desired Profile.** The user changes what counts as a good value for a criterion, e.g. 20–25 °C instead of the regulation's range. By default every profile follows EU / national standards.

**6. Find Suitable Room.** The user (through UI1) or a developer (through API2) asks for the best room. The system filters rooms by needs and availability, maps sensor values to the desired profiles, combines them with the weights and returns scores and a ranking. Guests get default profiles and equal weights; logged-in users get their saved preferences.

**7. Specify Needs.** For each search the user gives the time slot and the required facilities: videoprojector, number of seats, computers, robots. Rooms that do not meet the needs or are busy in the slot are left out.

**8. View Room Facilities.** Shows the equipment of a given room. Opened from the ranking, from the admin UI or through API1.

**9. View Sensor Data.** Shows temperature, humidity, CO₂, sound, light, VOC and PM2.5/PM10 of a room over a chosen period, as plots (UI2) or as JSON (API1).

**10. View Calendar Events.** Shows the events booked in the rooms over a chosen period (UI2, API1).

**11. Monitor Sensor Status.** The administrator sees which sensors are online and the periods when a sensor sent no data (disconnected).

**12. View API Documentation.** A developer opens the Swagger documentation of API1 and API2 (second tab of UI1) to learn how to use the system.

**13. Send Sensor Measurements.** The Arduino reads the room sensors (Comm. A) and sends the values to the Raspberry Pi (Comm. B), where they are saved in the database. If no values arrive for a while, the sensor is marked as disconnected.

**14. Synchronize Facilities & Calendar.** At regular intervals the system fetches room facilities from the facilities service (Comm. C) and events from the university calendar (Comm. D) and updates the database.

---

## Full descriptions

## 1. Register

| | |
|---|---|
| **Use Case Name** | Register |
| **Actors** | User (primary)<br>Database (secondary) |
| **Preconditions** | - User does not have an account yet;<br>- Database is reachable. |
| **Main Success Scenario** | User wants to create an account so the system remembers their preferences.<br>1. User opens "Register".<br>2. User enters name, e-mail and password.<br>3. System validates the data.<br>4. System checks that the e-mail is not used yet.<br>5. System stores the account in the Database with the password hashed.<br>6. System creates default preferences for the account (profiles from regulation, equal weights).<br>7. User receives a confirmation and is logged in. |
| **Extensions (Alternate Flows)** | **3a. Required fields are empty or invalid (e.g. wrong e-mail format, weak password):**<br>1. System highlights the invalid fields;<br>2. User corrects them and resubmits.<br><br>**4a. E-mail is already used:**<br>1. System shows an error;<br>2. System suggests to log in instead.<br><br>**5a. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. No account is created. |
| **Postconditions** | Success: the account exists with default preferences and the user is logged in.<br>Exception: no account is created and the user is informed why. |

## 2. Log In

| | |
|---|---|
| **Use Case Name** | Log In |
| **Actors** | User (primary)<br>Administrator (primary)<br>Developer (primary)<br>Database (secondary) |
| **Preconditions** | - The actor has an account;<br>- Database is reachable. |
| **Main Success Scenario** | The actor wants to sign in to use the functions of their role.<br>1. Actor opens the login page (or calls the login endpoint of the API).<br>2. Actor enters e-mail and password.<br>3. System checks the credentials in the Database.<br>4. Database returns the role of the account (user, administrator or developer).<br>5. System issues a JWT token with the role and an expiry time.<br>6. System opens the requested page with the rights of this role.<br><br>"Log In" is included in "Manage Preferences", "View Sensor Data", "View Calendar Events" and "Monitor Sensor Status". |
| **Extensions (Alternate Flows)** | **3a. Wrong e-mail or password:**<br>1. Login fails;<br>2. System shows an error and asks to try again;<br>3. After 3 failed attempts, login is temporarily blocked.<br><br>**5a. Token has expired during a session:**<br>1. System asks the actor to log in again.<br><br>**3b. Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. Login is not possible. |
| **Postconditions** | Success: the actor holds a valid token with the rights of their role.<br>Exception: the actor is not logged in and is informed why. |

## 3. Manage Preferences

| | |
|---|---|
| **Use Case Name** | Manage Preferences |
| **Actors** | User (primary)<br>Database (secondary) |
| **Preconditions** | - User is logged in ("Log In" is included);<br>- Database is reachable. |
| **Main Success Scenario** | User wants to save or change their preferences for future searches.<br>1. User opens "My preferences".<br>2. System loads the saved preferences from the Database.<br>3. System shows the importance of criteria and the desired profile of each criterion.<br>4. User changes the importance of criteria ("Set Criteria Importance" is included).<br>5. User changes the desired profiles ("Adjust Desired Profile" is included).<br>6. User chooses "Save".<br>7. System validates the preferences.<br>8. System saves them in the Database.<br>9. User receives a confirmation.<br><br>Steps 4 and 5 can be done in any order; the user may change only one of them. |
| **Extensions (Alternate Flows)** | **2a. User has no saved preferences yet:**<br>1. System shows the default preferences (profiles from regulation, equal weights).<br><br>**6a. User chooses "Reset to default":**<br>1. System restores the regulation profiles and equal weights;<br>2. Continue at step 8.<br><br>**7a. Preferences are not valid (e.g. inconsistent comparisons):**<br>1. System shows what is wrong;<br>2. User corrects it; continue at step 4.<br><br>**Opened from "Find Suitable Room":**<br>1. After saving, the system recomputes the ranking with the new preferences. |
| **Postconditions** | Success: the new preferences are stored and used in the next searches.<br>Exception: the saved preferences are unchanged and the user is informed why. |

## 4. Set Criteria Importance

| | |
|---|---|
| **Use Case Name** | Set Criteria Importance |
| **Actors** | User (primary) |
| **Preconditions** | - "Manage Preferences" is in progress. |
| **Main Success Scenario** | User wants to say which criteria matter more to them.<br>1. System shows pairs of criteria (e.g. Temperature – Light) with a Saaty scale from 1 to 9 in both directions.<br>2. For each pair, user chooses which criterion is more important and how much (e.g. "Temperature 5× more important than Light").<br>3. System builds the pairwise comparison matrix.<br>4. System computes the weight of each criterion.<br>5. System computes the consistency ratio.<br>6. System shows the resulting weights to the user. |
| **Extensions (Alternate Flows)** | **2a. User leaves a pair unchanged:**<br>1. System uses 1 (equally important) for that pair.<br><br>**5a. Consistency ratio is above 0.1:**<br>1. System warns that the answers contradict each other and shows the most inconsistent pair;<br>2. User changes the comparisons; continue at step 3. |
| **Postconditions** | Success: a consistent set of weights is ready to be saved.<br>Exception: weights are not accepted until the comparisons are consistent. |

## 5. Adjust Desired Profile

| | |
|---|---|
| **Use Case Name** | Adjust Desired Profile |
| **Actors** | User (primary) |
| **Preconditions** | - "Manage Preferences" is in progress. |
| **Main Success Scenario** | User wants to change what counts as a good value for a criterion.<br>1. User selects a criterion (e.g. Temperature).<br>2. System shows the current profile as a graph, with the regulation profile for reference.<br>3. User moves the limits of the profile (e.g. comfort range 20–25 °C instead of 19–23 °C).<br>4. System validates the limits.<br>5. System updates the graph.<br><br>User may repeat steps 1–5 for other criteria. |
| **Extensions (Alternate Flows)** | **4a. Limits are not valid (e.g. lower limit above upper limit, value outside the sensor range):**<br>1. System highlights the invalid limit;<br>2. User corrects it.<br><br>**3a. User chooses "Use regulation profile":**<br>1. System restores the default profile for this criterion. |
| **Postconditions** | Success: the changed profiles are ready to be saved.<br>Exception: the profile is unchanged. |

## 6. Find Suitable Room

| | |
|---|---|
| **Use Case Name** | Find Suitable Room |
| **Actors** | User (primary)<br>Developer (primary)<br>Database (secondary) |
| **Preconditions** | - Database is reachable;<br>- Sensor data, room facilities and calendar events are stored. |
| **Main Success Scenario** | User wants to find the most suitable room for a time slot.<br>1. User opens the search (UI1) or a developer calls API2.<br>2. User specifies the needs ("Specify Needs" is included).<br>3. System loads the user's saved preferences; for a guest it uses the default preferences.<br>4. System keeps only the rooms that have the required facilities and are free in the time slot.<br>5. System retrieves the latest sensor values of these rooms.<br>6. System maps each value to a compliance value (0–1) using the desired profile.<br>7. System combines the compliance values with the weights into a final score per room.<br>8. System sorts the rooms by score.<br>9. System displays the ranking with the score of each room and its compliance per criterion. |
| **Extensions (Alternate Flows)** | **4a. No room meets the needs or is free:**<br>1. System shows "No room found";<br>2. System suggests to change the time slot or the needs.<br><br>**5a. A sensor of a room is disconnected or has no recent data:**<br>1. System marks this criterion of the room as "No data";<br>2. System computes the score without it and shows a warning.<br><br>**9a. User wants to change preferences:**<br>1. User opens "Manage Preferences" (extends this use case);<br>2. System recomputes the ranking.<br><br>**9b. User wants to see a room's equipment:**<br>1. User opens "View Room Facilities" (extends this use case).<br><br>**Database is not reachable:**<br>1. System shows "Service unavailable";<br>2. No ranking is computed. |
| **Postconditions** | Success: the user sees the rooms ranked from the most to the least suitable.<br>Exception: no ranking is shown and the user is informed why. |

## 7. Specify Needs

| | |
|---|---|
| **Use Case Name** | Specify Needs |
| **Actors** | User (primary)<br>Developer (primary) |
| **Preconditions** | - "Find Suitable Room" is in progress. |
| **Main Success Scenario** | User wants to say what the room must have for this search.<br>1. User selects the date and time slot.<br>2. User selects the required facilities: videoprojector, minimum number of seats, minimum number of computers, minimum number of robots.<br>3. System validates the needs.<br>4. System passes the needs to "Find Suitable Room".<br><br>A developer gives the same needs as API2 parameters. |
| **Extensions (Alternate Flows)** | **1a. User gives a flexible period (e.g. "any 1-hour slot in the next 2 days"):**<br>1. System checks every slot of that length in the period.<br><br>**2a. User selects no facilities:**<br>1. All rooms are kept.<br><br>**3a. Needs are not valid (e.g. slot in the past, negative number):**<br>1. System highlights the invalid field;<br>2. User corrects it. |
| **Postconditions** | Success: valid needs are passed to the search.<br>Exception: the search is not started until the needs are valid. |

## 8. View Room Facilities

| | |
|---|---|
| **Use Case Name** | View Room Facilities |
| **Actors** | User (primary)<br>Administrator (primary)<br>Developer (primary)<br>Database (secondary) |
| **Preconditions** | - Database is reachable. |
| **Main Success Scenario** | The actor wants to see the equipment of a room.<br>1. Actor selects a room (from the ranking, from UI2, or through API1).<br>2. System retrieves the room facilities from the Database.<br>3. System displays the facilities: videoprojector, seating capacity, computers, robots for training.<br><br>Facilities that the room does not have are shown as "No" or 0. |
| **Extensions (Alternate Flows)** | **2a. Room does not exist:**<br>1. System shows "Room not found".<br><br>**2b. Database is not reachable:**<br>1. System shows "Service unavailable". |
| **Postconditions** | Success: the actor sees the facilities of the room.<br>Exception: the actor is informed why they cannot be shown. |

## 9. View Sensor Data

| | |
|---|---|
| **Use Case Name** | View Sensor Data |
| **Actors** | Administrator (primary)<br>Developer (primary)<br>Database (secondary) |
| **Preconditions** | - Actor is logged in ("Log In" is included);<br>- Database is reachable. |
| **Main Success Scenario** | The actor wants to see how the indoor conditions of a room changed over time.<br>1. Actor selects a room.<br>2. Actor selects one or more quantities (temperature, humidity, CO₂, sound, light, VOC, PM2.5, PM10).<br>3. Actor selects a period.<br>4. System retrieves the measurements from the Database.<br>5. System displays them as plots over time (UI2) or returns them as JSON (API1). |
| **Extensions (Alternate Flows)** | **3a. Period is not valid (end before start, too long):**<br>1. System shows an error;<br>2. Actor changes the period.<br><br>**4a. No measurements in the period:**<br>1. System shows "No data for this period".<br><br>**5a. There are gaps in the data:**<br>1. System shows the gaps on the plot;<br>2. Administrator may open "Monitor Sensor Status" (extends this use case). |
| **Postconditions** | Success: the actor sees the measurements of the period.<br>Exception: the actor is informed that no data is available. |

## 10. View Calendar Events

| | |
|---|---|
| **Use Case Name** | View Calendar Events |
| **Actors** | Administrator (primary)<br>Developer (primary)<br>Database (secondary) |
| **Preconditions** | - Actor is logged in ("Log In" is included);<br>- Database is reachable. |
| **Main Success Scenario** | The actor wants to see when rooms are booked.<br>1. Actor selects a period.<br>2. Actor may select a room.<br>3. System retrieves the calendar events from the Database.<br>4. System displays the events with room, start, end and title (UI2) or returns them as JSON (API1). |
| **Extensions (Alternate Flows)** | **1a. Period is not valid:**<br>1. System shows an error;<br>2. Actor changes the period.<br><br>**3a. No events in the period:**<br>1. System shows "No events".<br><br>**3b. Calendar was not synchronized recently:**<br>1. System shows the time of the last synchronization as a warning. |
| **Postconditions** | Success: the actor sees the events of the period.<br>Exception: the actor is informed that no events are available. |

## 11. Monitor Sensor Status

| | |
|---|---|
| **Use Case Name** | Monitor Sensor Status |
| **Actors** | Administrator (primary)<br>Database (secondary) |
| **Preconditions** | - Administrator is logged in ("Log In" is included);<br>- Database is reachable. |
| **Main Success Scenario** | Administrator wants to check that all sensors work.<br>1. Administrator opens "Sensor status" in UI2.<br>2. System retrieves the time of the last measurement of each sensor.<br>3. System marks each sensor as online or disconnected.<br>4. System displays the status of every sensor per room.<br>5. Administrator selects a sensor and a period.<br>6. System shows the periods in which the sensor sent no data. |
| **Extensions (Alternate Flows)** | **3a. A sensor is disconnected now:**<br>1. System highlights it and shows since when it is disconnected.<br><br>**6a. No disconnections in the period:**<br>1. System shows "No disconnections". |
| **Postconditions** | Success: the administrator knows which sensors work and when they were disconnected.<br>Exception: the administrator is informed that the status cannot be shown. |

## 12. View API Documentation

| | |
|---|---|
| **Use Case Name** | View API Documentation |
| **Actors** | Developer (primary) |
| **Preconditions** | - The system is running. |
| **Main Success Scenario** | Developer wants to learn how to use the system's APIs.<br>1. Developer opens the "Developers" tab of UI1.<br>2. System shows the Swagger documentation of API1 and API2.<br>3. Developer selects an endpoint.<br>4. System shows its parameters, responses and examples.<br>5. Developer may try the endpoint from the documentation. |
| **Extensions (Alternate Flows)** | **5a. Endpoint is protected:**<br>1. Developer logs in ("Log In") and enters the token in Swagger;<br>2. Developer tries the endpoint again. |
| **Postconditions** | Success: the developer knows how to call the APIs.<br>Exception: — |

## 13. Send Sensor Measurements

| | |
|---|---|
| **Use Case Name** | Send Sensor Measurements |
| **Actors** | Arduino (primary)<br>Database (secondary) |
| **Preconditions** | - Sensors are connected to the Arduino;<br>- The Raspberry Pi is running. |
| **Main Success Scenario** | Arduino sends the current indoor conditions of its room.<br>1. Arduino reads every sensor of the room (Comm. A).<br>2. Arduino builds a message with room, quantity, value and timestamp.<br>3. Arduino sends the message to the Raspberry Pi (Comm. B).<br>4. System validates the message.<br>5. System stores the measurement in the Database.<br>6. System updates the time of the last measurement of the sensor.<br><br>Steps 1–6 repeat every minute. |
| **Extensions (Alternate Flows)** | **1a. A sensor does not answer:**<br>1. Arduino skips this sensor and sends the others.<br><br>**4a. Value is outside the sensor's physical range:**<br>1. System rejects the value and logs it.<br><br>**5a. Database is not reachable:**<br>1. System keeps the messages in a buffer;<br>2. System stores them when the Database is back.<br><br>**No message from a sensor for more than [X] minutes:**<br>1. System marks the sensor as disconnected. |
| **Postconditions** | Success: the new measurements are stored and the sensor is marked online.<br>Exception: the sensor is marked disconnected or the value is rejected. |

## 14. Synchronize Facilities & Calendar

| | |
|---|---|
| **Use Case Name** | Synchronize Facilities & Calendar |
| **Actors** | Facilities service (primary)<br>University calendar (primary)<br>Database (secondary) |
| **Preconditions** | - The external services are configured;<br>- Database is reachable. |
| **Main Success Scenario** | System keeps room facilities and calendar events up to date.<br>1. At a regular interval, system requests the room facilities from the facilities service (Comm. C).<br>2. System validates the data.<br>3. System updates the facilities in the Database.<br>4. System requests the events of the coming period from the university calendar (Comm. D).<br>5. System updates the calendar events in the Database.<br>6. System stores the time of the synchronization. |
| **Extensions (Alternate Flows)** | **1a / 4a. External service is not reachable:**<br>1. System keeps the previous data;<br>2. System retries at the next interval and logs the error.<br><br>**2a. Data is not valid (e.g. missing field, broken format):**<br>1. System fills missing facilities with 0 / "No";<br>2. System rejects records it cannot read and logs them. |
| **Postconditions** | Success: facilities and calendar in the Database match the external services.<br>Exception: the previous data is kept and the error is logged. |
