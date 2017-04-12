# requestron

Retrieve SQL requests from an XML file and execute them.

## Usage

### 1) Create a XML file
The XML file should follow the structure described below.

```xml
<?xml version="1.0" encoding="UTF-8"?>  
<requests>  
	<request name="query_name">  
	<![CDATA[  
		SQL query  
	]]>  
	</request>  
</requests>
```

### 2) Edit requestron settings
Edit the database connection string, database user and database password in Settings.php.
Also edit the path to the XML file.

### 3) Execute the query
```php
require_once("requestron/Requester.php");  

$req = new Requester();  
$results = $req->select("query_name", array(":field1" => "value1", ":field2" => "value2"));  
```