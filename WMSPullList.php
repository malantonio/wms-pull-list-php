<?php
class WMSPullList {

    const PULL_LIST_URL = "https://circ.sd00.worldcat.org/pulllist/";

    /**
     *  static wrapper to request pull-list and return only the items
     *
     *  @param  int              branch ID
     *  @param  OCLC\Auth\WSKey  wskey making request
     *  @return array            collection of stdClass objects
     */

    public static function getList($branchID, \OCLC\Auth\WSKey $wskey) {
        return (new self($branchID, $wskey))->getEntries();
    }

    /**
     *  instantiate new PullList
     *
     *  @param  int              branch ID
     *  @param  OCLC\Auth\WSKey  wskey making request
     *  @param  array            additional options:
     *                           "startIndex"   (default is 1)
     *                           "limit" (default is 0, all items)
     *  
     */

    public function __construct($branchID, \OCLC\Auth\WSKey $wskey, $opts = array()) {
        $this->branchID = $branchID;
        $this->wskey = $wskey;

        $this->startIndex = isset($opts['startIndex']) ? $opts['startIndex'] : 1;
        $this->limit = isset($opts['limit']) ? $opts['limit'] : 0;
    }

    /**
     *  makes the request to OCLC and returns only the items in the response
     *
     *  @return array   array of stdClass objects of items to be pull
     */

    public function getEntries() {
        $resp = $this->getFullResponse();
        return $resp->entry;
    }


    /**
     *  sends a curl request to OCLC to retrieve the pull-list + returns 
     *  the full response
     *
     *  @return stdClass
     *  @throws \Exception
     */

    public function getFullResponse() {
        $url = self::PULL_LIST_URL . $this->branchID
             . "?startIndex={$this->startIndex}"
             . "&itemsPerPage={$this->limit}"
             ;

        $authHeader = $this->wskey->getHMACSignature('GET', $url);
        $headers = array(
            "Authorization: {$authHeader}",
            "Accept: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $resp = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $code = $info['http_code'];

        if ( $code !== 200 ) {
            if ( $code === 401 ) {
                throw new \Exception("This request requires WsKey authentication.");
            }

            else {
                throw new \Exception("An error occurred requesting the Pull-List");
            }
        }

        return json_decode($resp);
    }

    /**
     *  set the number of items to be returned per request (or 'page')
     *
     *  @param  int             number of items
     *  @return WMS\PullList    returns self for chaining
     */

    public function limit($val) {
        if ( !is_int($val) ) { 
            trigger_error("limit must be an int", E_USER_ERROR); 
        }

        $this->limit = $val;
        return $this;
    }

    /**
     *  set the index to begin listing items at
     *
     *  @param  int             index
     *  @return WMS\PullList    returns self for chaining
     */

    public function startIndex($val) {
        if ( !is_int($val) ) {
            trigger_error("startIndex must be an int", E_USER_ERROR);
        }
        $this->startIndex = $val;
        return $this;
    }
}