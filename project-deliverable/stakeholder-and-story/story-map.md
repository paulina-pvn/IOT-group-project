# Story map — ComfortRoom

In the previous project the story map was a set of photos. This is the same map in text: activities from left to right, tasks under each activity. Every task is already a use case.

| Find a room | Say what matters | Read the ranking | Keep the data flowing | Monitor | Call the system from code |
|---|---|---|---|---|---|
| Specify the slot and the facilities (7) | Compare the 4 groups on the Saaty scale (4) | See rooms ordered by score (6) | Arduino sends measurements every minute (13) | View sensor plots (9) | Read Swagger (12) |
| Search with no account (6) | Move the four points of a profile (5) | Open a room's facilities from the ranking (8) | Timer refreshes facilities and calendar every 15 minutes (14) | See which sensors are disconnected (11) | Get the ranking from public API2 (6) |
| Register and log in, only to save preferences (1, 2) | Save preferences for the next search (3) | | | View facilities and calendar events (8, 10) | Read measurements, facilities and calendar from API1 (8, 9, 10) |

Walking skeleton, the thin path that ranks a room: specify the slot and the facilities, keep the regulation profiles and the default weights, score the 30 minutes before the reference time, show the list. This path is green only after the regulation profiles have their four numbers. Those numbers are still an open requirements task on "Find a suitable room".

The other tasks make that path personal (Saaty, profile, saved preferences), operable (measurements, sync, admin screen) or callable from outside (Swagger, API1, API2).
