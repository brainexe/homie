
# Dashboard

## Widgets
- Display
- EggTimer
- Expression
- SensorGraph
- SensorInput
- SensorWidget
- ShoppingList
- Speak
- Status
- SwitchWidget
- Time
- TodoList
- Webcam
- WidgetMetadataVo

```
ls src/Homie/Dashboard/Widgets/*.php | awk -F "/" '{print "- " substr($5, 0, length($5)-3)}'
```

## Add a new widget
 - implement AbstractWidget in PHP (@Widget annotation)
 - assets/templates/widgets/NAME.html
 - assets/js/controller/dashboard/widgets/NAME.js
