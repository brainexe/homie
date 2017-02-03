# Example how to register additionally log handlers

```
<service synthetic="true">
    <tag name="merge_definition" parent="logger"/>
    <call method="pushHandler">
		<argument type="service">
			<service class="Monolog\Handler\HipChatHandler">
				<argument>myToken</argument>
				<argument>myRoom</argument>
				<argument>myName</argument>
				<argument>false</argument>
				<argument>myLogLevel</argument>
			</service>
		</argument>
	</call>
</service>
```

# Elasticsearch/kibana/logstash
```
<service parent="logger">
    <tag name="merge_definition" parent="logger"/>
    <call method="pushHandler">
        <argument type="service">
            <service class="Monolog\Handler\ElasticSearchHandler">
                <argument type="service">
                    <service class="Elastica\Client">
                        <argument type="collection">
                            <argument key="host">myHost</argument>
                            <argument key="port">443</argument>
                        </argument>
                    </service>
                </argument>
                <argument type="collection">
                    <argument key="index">myINdex</argument>
                </argument>
                <argument type="constant">Monolog\Logger::DEBUG</argument>
            </service>
        </argument>
    </call>
</service>
```
