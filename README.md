Script calculates a unix timestamp (the amount of seconds that past since 01.01.1970, this format is used for 
storing date) based on user’s entered string, that also contains a message. Based on calculated time, the customer 
receives reminder with message that they put in to initial string.

Script can recognize about 20 time/date format variations.
Such as:
<ol>
  <li>6pm call mom</li>
  <li>6:30pm call mom</li>
  <li>6:19p call mom</li>
  <li>6p call mom</li>
  <li>6am call mom</li>
  <li>6a call mom</li>
  <li>2 hours call mom</li>
  <li>2 hrs call mom</li>
  <li>30 minutes call mom</li>
  <li>30 mins call mom</li>
  <li>today at 6pm call mom – the next 6pm, if 6pm has already passed.</li>
  <li>Saturday 6pm call mom – The next Saturday, if Saturday has already passed</li>
  <li>tomorrow at 6pm – calculates tomorrow regardless if it’s after 6pm or not.</li>
</ol>
