# Salesforce Rest SDK

This API supports the following areas of the Salesforce API:
* Limits
* Global Describe
* SObject Describe
* SObject CRUD
* SObject Get Updated/Deleted
* Composite API
    * Batch
    * Tree
    * SObject Collections
* Bulk API
* Streaming API

## Instantiate a Rest Client

```php
<?php

use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;

$client = new Client(
  new OAuthProvider(
      "SF_CLIENT_ID",
      "SF_CLIENT_SECRET",
      "https://login.salesforce.com",
      "SF_USER",
      "SF_PASS"
  )
);
```

If you have an authorization code returned to your redirectUrl and wish to use it, you can do so like this:

```php
<?php
use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;

$client = new Client(
  new OAuthProvider(
      "SF_CLIENT_ID",
      "SF_CLIENT_SECRET",
      "https://login.salesforce.com",
      null,
      null,
      OAuthProvider::GRANT_CODE,
      "https://your.redirect.uri",
      "THE_CODE_FROM_SALESFORCE"
  )
);
```

### Cached Auth Providers

Cached Auth Providers provide a way to hold onto valid credentials across requests, otherwise the client will authenticate
with Salesforce every time it's instantiated.

```php
<?php
use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\AuthProvider\CachedOAuthProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Any adapter that uses Psr\Cache\CacheItemPoolInterface will work with the Cached Providers
$adapter = new FilesystemAdapter();

$client = new Client(
  new CachedOAuthProvider(
      $adapter,
      "SF_CLIENT_ID",
      "SF_CLIENT_SECRET",
      "https://login.salesforce.com",
      null,
      null,
      CachedOAuthProvider::GRANT_CODE,
      "https://your.redirect.uri",
      "THE_CODE_FROM_SALESFORCE"
  )
);

```

#### Composer autoloading without a framework

If you happen to not be using a PHP Framework that handles annotation registration for you, like Symfony, then you must do it yourself:

```PHP
<?php

use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require_once 'vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, "loadClass"));

$client = new Client(
   // ...
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
use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\Bayeux\Transport\LongPollingTransport;

$client = new BayeuxClient(
      new LongPollingTransport(),
      new OAuthProvider(
          "SF_CLIENT_ID",
          "SF_CLIENT_SECRET",
          "https://login.salesforce.com",
          "SF_USER",
          "SF_PASS"
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
use AE\SalesforceRestSdk\Bayeux\Extension\ReplayExtension;
use AE\SalesforceRestSdk\Bayeux\Extension\CachedReplayExtension;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/** @var BayeuxClient $client */

// Getting a channel tells the client you want to subscribe to a topic
$channel = $client->getChannel('/topic/[YOUR_PUSH_TOPIC_NAME]');

// Give some durability to the messages on this channel by adding the ReplayExtension
$channel->addExtension(new ReplayExtension(ReplayExtension::REPLAY_SAVED));

// Using the CachedReplayExtension give greater durability in that it will remember the replay Id of the last
// message received and pick up where it left off, even if the process stops and restarts. If no messages for the
// channel topic have been received, it will use the value provided in the constructor.
// Again, Any Psr\Cache\CacheItemPoolInterface will do for the adapter parameter
$channel->addExtension(new CachedReplayExtension(new FilesystemAdapter(), CachedReplayExtension::REPLAY_SAVED));

// You can also apply extensions at the Client level, rather than the channel.
// In the case of the ReplayExtension, the last replayId will be remembered for each channel,
// however, if no messages have been received on the channel, the constructor argument is used
$client->addExtension(new CachedReplayExtension(new FilesystemAdapter(), CachedReplayExtension::REPLAY_SAVED));

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
