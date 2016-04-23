# IFTTT
"[Maker|https://ifttt.com/maker]" is the IFTTT channel for any 3rd party application using HTTP calls to trigger actions.

## Configuration of "[Maker|https://ifttt.com/maker]"
 - Create a key on [Maker|https://ifttt.com/maker]
 - add '<parameter key="ifttt.key">mygeneratediftttkey</parameter>' to you app/config.xml
 - 'php console cc'
 - In IFTTT you can add add an action now, which goes to "http://youpublicdoma.in/ifttt/"
 - Possible parameters are: event, value1, value2, value3
 - Now you can create an action which trigger on "isEvent('ifttt.action')"
