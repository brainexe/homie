
# Dashboard

## Widgets
- Command
- Display
- EggTimer
- ExecuteExpression
- Expression
- SensorGraph
- SensorInput
- SensorWidget
- ShoppingList
- Speak
- SpeechRecognition
- Status
- SwitchWidget
- Time
- TodoList
- Webcam

```
ls src/Homie/Dashboard/Widgets/*.php | awk -F "/" '{print "- " substr($5, 0, length($5)-3)}' | grep -v WidgetMetadataVo
```

## Add a new widget
 - implement AbstractWidget in PHP (@Widget annotation)
 - assets/templates/widgets/NAME.html
 - assets/js/controller/modules/dashboard/widgets/NAME.js
