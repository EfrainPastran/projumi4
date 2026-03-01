<?php

require_once __DIR__ . '/vendor/autoload.php';

class TestLinkAPIClient 
{
    private $client;
    private $devKey;

    public function __construct()
    {
        $url = "http://127.0.0.1/testlink/lib/api/xmlrpc/v1/xmlrpc.php";
        $this->client = new PhpXmlRpc\Client($url);

        $this->devKey = "3d8abad2f729384a69eb6abfa1bdec2c";
    }

    public function reportTCResult($testCaseExternalId, $testPlanId, $buildId, $status, $notes)
    {
        $params = [
            "devKey"                => new PhpXmlRpc\Value($this->devKey, "string"),
            "testcaseexternalid"    => new PhpXmlRpc\Value($testCaseExternalId, "string"),
            "testplanid"            => new PhpXmlRpc\Value($testPlanId, "int"),
            "buildid"               => new PhpXmlRpc\Value($buildId, "int"),
            "status"                => new PhpXmlRpc\Value($status, "string"),
            "notes"                 => new PhpXmlRpc\Value($notes, "string"),
        ];

        $request = new PhpXmlRpc\Request(
            "tl.reportTCResult",
            [ new PhpXmlRpc\Value($params, "struct") ]
        );

        return $this->client->send($request);
    }
}
