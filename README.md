# Salesforce Rest SDK

## Instantiate a Rest Client

```php
<?php

use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\AuthProvider\LoginProvider;

$client = new Client(
  getenv("SF_URL"),
  new LoginProvider(
      "SF_CLIENT_ID",
      "SF_CLIENT_SECRET",
      "SF_USER",
      "SF_PASS",
      "https://login.salesforce.com"
  )
);
```

### Work with SObjects with the SObject Client

[Reference](https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_list.htm)

```php
<?php

//...

/** @var \AE\SalesforceRestSdk\Rest\SObject\Client $sObjectClient */
$sObjectClient = $client->getSObjectClient();

// Get basic metadata and recently used records for an object
$info = $sObjectClient->info("Account");

// Get very detailed metadata about an object
$describe = $sObjectClient->describe("Account");

// Get basic metadata info about all objects in SF
$globalDescribe = $sObjectClient->describeGlobal();

// Let's CRUD it up
$account = new \AE\SalesforceRestSdk\Model\SObject();

$sObjectClient->persist("Account", $account); // returns true if success

echo $account->Id; // outputs the SFID of the account
echo $account->Type; // Type is populated after persistence, this will output "Account"

$account->MyCustomField__c = "Some Value I want to Save";

$sObjectClient->persist("Account", $account); // returns true on success

// Let's get new info from out account, pretend it was updated in SF
$account = $sObjectClient->get("Account", $account->Id, ["Name", "AnotherCoolField__c"]);

// Kill the account
$sObjectClient->remove("Account", $account);

// Query for more stuff
$result = $sObjectClient->query("SELECT Id, Name FROM Account");

echo $result->getTotalSize(); // OUtputs the total number of records for the query

var_dump($result->getRecords()); // SObject[]

while (!$result->isDone()) {
    // There are more records to be returned!
     // Just pass in the last result set and get the next batch
     // Lather, rinse, repeat until $result->isDone() === true;
    $result = $sObjectClient->query($result);
    
    var_dump($result->getRecords()); // CompositeSObject[]
}

// Query deleted and merged records, too
$result = $sObjectClient->queryAll("SELECT Id, Name FROM Account");

// Search for something
$result = $sObjectClient->search("FIND {Some Query} IN ALL FIELDS");

var_dump($result->getSearchRecords()); // CompositeSObject[]
```

## Instantiate the Streaming Client
[Reference](https://developer.salesforce.com/docs/atlas.en-us.api_streaming.meta/api_streaming/intro_stream.htm)

```php
<?php
use AE\SalesforceRestSdk\Bayeux\BayeuxClient;
use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use AE\SalesforceRestSdk\Bayeux\Transport\LongPollingTransport;

$client = new BayeuxClient(
      "https://my-salesforce.my.salesforce.com",
      new LongPollingTransport(),
      new LoginProvider(
          "SF_CLIENT_ID",
          "SF_CLIENT_SECRET",
          "SF_USER",
          "SF_PASS",
          "https://login.salesforce.com"
      )
 );

```

### Subscribe to a PushTopic
* [Reference: Create a PushTopic](https://developer.salesforce.com/docs/atlas.en-us.api_streaming.meta/api_streaming/create_a_pushtopic.htm)
* [Reference: Supported PushTopic Queries](https://developer.salesforce.com/docs/atlas.en-us.api_streaming.meta/api_streaming/supported_soql.htm)

You can create a new PushTopic using the Rest Client above. The topic only needs created once in a Salesforce Org. All custom objects are supported
by the Streaming API, however, not all standard objects supported.

Supported Standard Objects:
* Account
* Campaign
* Case
* Contact
* ContractLineItem
* Entitlement
* Lead
* LiveChatTranscript
* Opportunity
* Quote
* QuoteLineItem
* ServiceContract
* Task

> Tasks that are created or updated using the following methods don’t appear in task object topics in the streaming API.
>  
> * Lead conversion
> * Entity merge
> * Mass email contacts/leads

```php
<?php
use AE\SalesforceRestSdk\Bayeux\BayeuxClient;
use AE\SalesforceRestSdk\Bayeux\Consumer;
use AE\SalesforceRestSdk\Bayeux\ConsumerInterface;
use AE\SalesforceRestSdk\Bayeux\Message;

/** @var BayeuxClient $client */

// Getting a channel tells the client you want to subscribe to a topic
$channel = $client->getChannel('/topic/[YOUR_PUSH_TOPIC_NAME]');
// Register topic consumers prior to starting the client
$channel->subscribe(
    Consumer::create(function (ConsumerInterface $consumer, Message $message) {
        // This will be fired when the client receives a topic notification
        
        $payload = $message->getData();
        
        // The payload has information about the event that occurred
        $event = $payload->getEvent();
        
        echo $event->getType(); // "created", "updated", "undeleted", "deleted"
        echo $event->getCreatedDate()->format(\DATE_ISO8601); // outputs the datetime the event was created
        echo $event->getReplayId(); // This ia n ID used by the replay extension so it can pick up the feed where it left off
        
        $sobject = $payload->getSobject();
        
        echo $sobject->Id; // Get the Id
        echo $sobject->getFields(); // this outputs all the fields and their values that were in the create or update request
    })
);

// Start the client to begin receiving notifications;
$client->start();
```

> The `$client->start();` is a blocking call and no code after it will execute
> until an error occurs in the client, causing it to disconnect.
> 
> For instance, the client must reconnect to the streaming server within 40 seconds after each
> notification is received. If it fails to do so, it will attempt to re-handshake
> to create a new connection. If that fails, then the client will disconnect,
> which will allow the rest of the script to execute.
>
> It's recommended that the streaming client be run in it's own thread

## Future Additions

* Tooling API
* Metadata API
* Bulk API