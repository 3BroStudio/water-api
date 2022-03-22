# water-api
An API for recording how many cups of water you have drunk

## Purpose
1. Storing the number of cups that users have drunk
2. Return the number of cups that users have drunk
3. Upgrade user to "PRO" user
4. Validate user "PRO" identity
5. Return the leaderboard of drinking water
6. Create user
7. Login
8. Logout

## API Endpoint

### GET /leaderboard.php

200 return the top 10 drinking users and their PRO status
500 something wrong on the server side

### POST /drink.php

argument expected: username

200 add 1 cup to the database record, and return the number of cups that user has drunk
400 you haven't provide the "username"
401 username not found
500 something wrong on the server side

### POST /checkpro.php

argument expected: username

200 return true: user is a PRO
400 you haven't provide the "username"
401 return false: user is not a PRO
404 username not found
500 something wrong on the server side

### POST /checkuser.php

argument expected: username

200 return true: user exists
400 you haven't provide the "username"
404 return false: user not exists
404 username not found
500 something wrong on the server side

