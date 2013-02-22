Script calculates a unix timestamp (the amount of seconds that past since 01.01.1970, this format is used for 
storing date) based on user’s entered string, that also contains a message. Based on calculated time, the customer 
receives reminder with message that they put in to initial string.

Script can recognize about 20 time/date format variations.Such as:
1. 6pm call mom
2. 6:30pm call mom
3. 6:19p call mom
4. 6p call mom
5. 6am call mom
6. 6a call mom
7. 2 hours call mom
8. 2 hrs call mom
9. 30 minutes call mom
10. 30 mins call mom
11. today at 6pm call mom – the next 6pm, if 6pm has already passed.
12. Saturday 6pm call mom – The next Saturday, if Saturday has already passed
13. tomorrow at 6pm – calculates tomorrow regardless if it’s after 6pm or not.
