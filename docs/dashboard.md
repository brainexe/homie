
# Dashboard

## Widgets
```
ls src/Homie/Dashboard/Widgets/*.php | awk -F "/" '{print $5}'
```

## Add a new widget
 - implement AbstractWidget in PHP (@Widget annotation)
 - assets/templates/widgets/NAME.html
 - assets/js/controller/dashboard/widgets/NAME.js
