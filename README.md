# Open Civilization Online

## requirements
 * webserver with PHP
 * PHP CLI
 * REDIS

## build instructions

### set up the game
 **Note:** this will reset an exisiting game without prompting or warning run with caution.
```
php initialize-game.php
```

### start build monitor
```
php builder.php
```

### connect to the site to signup and view the map
```
 chromium-browser http://localhost:80
```
