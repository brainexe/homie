## Supported switches
- 443 MHz  radio switch
- Raspberry GPIO output (e.g. relay)

## Add a new switch type
 - add type to js/controllers/switch/switch.js
 - add templates/switch/addForm/XXX.html
 - implement SwitchVO in PHP
 - implement SwitchInterface in PHP
 - add creation of new VO to Controller/Add.php
