<?php

namespace Nickcheek\McKesson;

class McKesson extends Builder
{
    protected string $identity;
    protected string $secret;
    protected string $b2bkey;
    protected int $account;
    protected iterable $items;

    public function __construct(string $identity, string $secret, int $account, string $b2bKey, string $deployment_mode = 'production')
    {
        $this->identity = $identity;
        $this->secret = $secret;
        $this->account = $account;
        $this->b2bkey = $b2bKey;
        $this->mode = $deployment_mode;
    }

    public function setupItemXML(string $itemId, ?string $type): string
    {
        $xml  = $this->credentials('Lookup');
        (!is_null($type)) ? $xml->addAttribute('itemType', $type) : null;
        $xml->addChild('ItemId', $itemId);
        return $xml->asXML();
    }

    function setupSearchXML(string $query, ?string $type,  ?string $refinement, ?int $node = null): string
    {
        $xml  = $this->credentials('Search');
        (!is_null($type)) ? $xml->addAttribute('itemType', $type) : null;
        (!is_null($refinement)) ? $xml->addAttribute('searchType', $refinement) : null;
        $search = $xml->addChild('Search');
        $query = $search->addChild('Query', $query);
        $query->addAttribute('by', 'keyword');
        if (!is_null($node)) {
            $browse = $search->addChild('BrowseNode');
            $browse->addAttribute('id', $node);
        }
        return $xml->asXML();
    }

    function setupFeedXML(string $source, string $sourceType, ?int $feedId,  ?string $type, int $pageSize,  int $offset): string
    {
        $xml  = $this->credentials('Feed', $feedId);
        (!is_null($type)) ? $xml->addAttribute('itemType', $type) : null;
        $feed = $xml->addChild('Feed');
        $search = $feed->addChild('Source', $source);
        $search->addAttribute('type', $sourceType);
        $feed->addChild('PageSize', $pageSize);
        $feed->addChild('Offset', $offset);
        return $xml->asXML();
    }

    function SetupOrderXML(iterable $data, iterable $order): string
    {
        $dataKeys = ['orderId', 'total', 'customerName', 'address1', 'city', 'state', 'zip', 'customerId'];
        if (!$this->checkArray($data, $dataKeys)) {
            throw new \Exception('The data array is missing keys');
        }
        $orderKeys = ['qty', 'sku', 'price', 'uom'];
        foreach ($order as $o) {
            if (!$this->checkArray($o, $orderKeys)) {
                throw new \Exception('The items array is missing keys');
            }
        }

        $xml = new \SimpleXMLElement('<cXML/>', LIBXML_NOERROR);
        $xml->addAttribute('payloadID', $data['orderId'] ?? '');

        //Setup Header information
        $head       = $xml->addChild('Header');
        $to         = $head->addChild('From');
        $credDomain = $to->addChild('Credential');
        $credDomain->addAttribute('domain', 'NetworkID');
        $identFrom = $credDomain->addChild('Identity', '10001');

        //Setup From section
        $from     = $head->addChild('To');
        $mcdomain = $from->addChild('Credential');
        $mcdomain->addAttribute('domain', 'DUNS');
        $identTo = $mcdomain->addChild('Identity', '023904428');

        //Setup Credentials
        $sender     = $head->addChild('Sender');
        $credDomain = $sender->addChild('Credential');
        $credDomain->addAttribute('domain', 'NetworkID');
        $creds     = $credDomain->addChild('Identity', $this->identity);
        $creds     = $credDomain->addChild('SharedSecret', $this->secret);
        $useragent = $sender->addChild('UserAgent', $data['userAgent'] ?? 'Mckesson PHP Library');

        //Setup Request
        $req = $xml->addChild('Request');
        $req->addAttribute('deploymentMode', $this->mode);
        $ordreq = $req->addChild('OrderRequest');
        $oHead  = $ordreq->addChild('OrderRequestHeader');

        //Order Number needs to be unique so no duplicate orders
        $oHead->addAttribute('orderID', $data['orderId'] ?? '');
        $oHead->addAttribute('orderDate', date('m.d.Y'));
        $oHead->addAttribute('type', 'new');

        //Setup Total
        $total = $oHead->addChild('Total');
        $money = $total->addChild('Money', $data['total'] ?? '');
        $money->addAttribute('currency', 'USD');

        //Setup ShipTo section
        $shto = $oHead->addChild('ShipTo');
        $sadd = $shto->addChild('Address');
        $sadd->addAttribute('isoCountryCode', 'US');
        $sadd->addAttribute('addressID', '');
        $sadd->addChild('Name', ucwords($data['customerName'] ?? ''));
        $post = $sadd->addChild('PostalAddress');
        $post->addAttribute('name', '');
        $post->addChild('DeliverTo', $data['customerName'] ?? '');
        $post->addChild('Street', ucwords($data['address1'] ?? ''));
        $post->addChild('Street', ucwords($data['address2'] ?? ''));
        $post->addChild('City', ucwords($data['city'] ?? ''));
        $post->addChild('State', $data['state'] ?? '');
        $post->addChild('PostalCode', $data['zip'] ?? '');
        $country = $post->addChild('Country', "United States");
        $country->addAttribute('isoCountryCode', 'US');
        $sadd->addChild('Email', $data['email'] ?? '');

        //Phone
        $phone = $sadd->addChild('Phone');
        $tel   = $phone->addChild('TelephoneNumber');
        $tel->addChild('CountryCode', "1");
        $tel->addChild('AreaOrCityCode');
        $tel->addChild('Number', preg_replace('/\D+/', '', $data['phone'] ?? ''));

        //Billto
        $blto = $oHead->addChild('BillTo');
        $badd = $blto->addChild('Address');
        $badd->addAttribute('addressID', $this->account);
        $bn = $badd->addChild('Name', '');

        //Patient
        $pt = $oHead->addChild('Contact');
        $pt->addAttribute('role', 'patient');
        $pt->addAttribute('addressID', $data['customerId'] ?? '');
        $pt->addChild('Name', ucwords($data['customerName'] ?? ''));

        //Item Loop
        foreach ($order as $i) {
            //itemout
            $it = $ordreq->addChild('ItemOut');
            $it->addAttribute('quantity', $i['qty'] ?? '');

            //itemID
            $itID = $it->addChild('ItemID');
            $itID->addChild('SupplierPartID', $i['sku'] ?? '');

            //ItemDetail
            $itDt = $it->addChild('ItemDetail');
            $dtPr = $itDt->addChild('UnitPrice');
            $dtPr->addChild('Money', $i['price'] ?? '');
            $dtPr->addAttribute('currency', 'USD');

            //Unit of Measure (Required)
            $dtPr = $itDt->addChild('UnitOfMeasure', $i['uom'] ?? '');
        }

        return $xml->asXML();
    }

    public function credentials(string $type): object
    {
        $xml  = new \SimpleXMLElement('<Item' . $type . 'Request/>');
        $creds = $xml->addChild('Credentials');
        $creds->addChild('Identity', $this->identity);
        $creds->addChild('SharedSecret', $this->secret);
        $creds->addChild('Account', $this->account);
        $creds->addChild('ShipTo', $this->account);
        return $xml;
    }

    function lookup(string $item, ?string $type = null): object
    {
        $xml = $this->setupItemXML($item, $type);
        $url = curl_init('https://mms.mckesson.com/services/xml/' . $this->b2bkey . '/ItemLookup');
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_HEADER, false);
        $result = curl_exec($url);
        return $this->toObject($result);
    }

    function search(string $item, ?string $type = null, ?string $refinement = null, ?int $node = null): object
    {
        $xml = $this->setupSearchXML($item, $type,  $refinement, $node);
        $url = curl_init('https://mms.mckesson.com/services/xml/' . $this->b2bkey . '/ItemSearch');
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_HEADER, false);
        $result = curl_exec($url);
        return $this->toObject($result);
    }

    function feed(string $source, string $sourceType = 'list', ?int $feedId = null, ?string $type = null, int $pageSize = 25, int $offset = 0): object
    {
        $xml = $this->setupFeedXML($source, $sourceType, $feedId, $type, $pageSize,  $offset);
        $url = curl_init('https://mms.mckesson.com/services/xml/' . $this->b2bkey . '/ItemFeed');
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_HEADER, false);
        $result = curl_exec($url);
        return $this->toObject($result);
    }

    function order(array $data, array $items): object
    {
        $xml = $this->SetupOrderXML($data, $items);
        $url = curl_init('https://mms.mckesson.com/services/b2b/' . $this->b2bkey . '/cxml');
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_HEADER, false);
        $result = curl_exec($url);
        return $this->toObject($result);
    }

    function checkArray($array, $keys, $only = false): bool
    {
        if ($only && count($array) !== count($keys)) {
            return false;
        }
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
        }
        return true;
    }

    function toObject($data): object
    {
        $data = simplexml_load_string($data);
        json_decode(json_encode($data), true);
        return $data;
    }
}
