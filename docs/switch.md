# Supported switches
- 443 MHz  radio switch (using rc-switch command by default)
- Raspberry GPIO output (e.g. control relay)
- Arduino GPIO output (e.g. control relay)

# Internal steps to add a new switch type
 - add type to js/controllers/switch/switch.js
 - add templates/switch/addForm/XXX.html
 - implement SwitchVO in PHP
 - implement SwitchInterface in PHP
 - add creation of new VO to Switches/Controller/Add.php
 - add creation of new VO to Switches/Switches.php
