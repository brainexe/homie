# Supported switches
- 443 MHz  radio switch (using rc-switch command by default)
- Raspberry GPIO output (e.g. control relay)
- Arduino GPIO output (e.g. control relay)
- Particle device

# Internal steps to add a new switch type
 - add type to js/controllers/switch.js
 - add templates/switch/addForm/XXX.html
 - implement SwitchVO in PHP
 - implement SwitchInterface in PHP
 - add to SwitchVO::TYPES
 - add creation of new VO to Switches/Controller/Controller.php
 - add creation of new VO to Switches/Switches.php
