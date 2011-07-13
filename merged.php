<?
/**
 * A number of classes and utilities for manipulating users, tasks,
 * URIs, and other goodies.  This file can be easily added to any
 * other PHP scripts you're working on by adding the line
 *     require_once('/path/to/your/classes.php');
 *
 * @author Andrew Hays
 * @copyright LGPLv3
 */
 
 session_start();
/**
 * Class for manipulating an account.
 * This class mostly contains getters and setters for manipulating
 * an account.
 *
 * @author Andrew Hays
 */
class Account {
    private $mName;
    private $mPassword;
    private $mUri;
    private $mUserNamePattern = '/\/a\/(?P<name>[\w% ]+)/';
    
    function __construct($name, $password, $uri) {
        $this->mName = $name;
        $this->mPassword = $password;
        $this->mUri = $uri;
    }
    
    public function getName() {
        return $this->mName;
    }
    
    public function getUri() {
        return $this->mUri;
    }
    
    public function getPassword() {
        return $this->mPassword;
    }
    
    public function getUserName() {
        preg_match($this->mUserNamePattern, $this->mUri, $matches);
        if (array_key_exists("name", $matches)) {
            return urldecode($matches["name"]);
        } else {
            return null;
        }
    }
    
    public static function uriFromUserNumber($number) {
        $urlnumber = urlencode($number);
        return "http://restapp.dyndns.org:9998/tdl/$urlnumber";
    }
    
    public static function uriFromUserName($name) {
        $urlname = str_replace('+','%20', urlencode($name));
        return "http://restapp.dyndns.org:9998/a/$urlname";
    }
    
    public static function getUserByUserName($name) {
        foreach(Util::getAccounts() as $account) {
            // TODO when a database is set up, this would be better done as
            // a query instead of filtering through a whole list.
            if ($account->getUserName() == $name) return $account;
        }
        return null;
    }
}

/**
 * Class for manipulating a task.
 * This class mostly contains getters and setters for manipulating
 * tasks.
 *
 * @author Andrew Hays
 */
class Task {
    private $mName;
    private $mUri;
    private $mType;
    private $mDetail;
    private $mStatus;
    private $mPriority;
    private $mActivationTime;
    private $mExpirationTime;
    private $mEsimtatedCompletionTime;
    private $mAdditionTime;
    private $mModificationTime;
    private $mProgress;
    private $mProcessProgress;
    private $mTags;
    private $mTaskNumberPattern = '/\/td\/(?P<number>\d+)/';
    
    function __construct($name=null, $uri=null) {
        $this->mName = $name;
        $this->mUri = $uri;
        $this->mTags = array();
    }
    
    public function setName($name) {
        $this->mName = $name;
    }
    
    public function setType($type) {
        $this->mType = $type;
    }
    
    public function setDetail($detail) {
        $this->mDetail = $detail;
    }
    
    public function setStatus($status) {
        $this->mStatus = $status;
    }
    
    public function setPriority($priority) {
        $this->mPriority = $priority;
    }
    
    public function setEstimatedCompletionTime($ect) {
        $this->mEstimatedCompletionTime = $ect;
    }
    
    public function setActivationTime($activationTime) {
        $this->mActivationTime = $activationTime;
    }
    
    public function setExpirationTime($expirationTime) {
        $this->mExpirationTime = $expirationTime;
    }
    
    public function setAdditionTime($additionTime) {
        $this->mAdditionTime = $additionTime;
    }
    
    public function setModificationTime($modificationTime) {
        $this->mModificationTime = $modificationTime;
    }
    
    public function setProgress($progress) {
        $this->mProgress = $progress;
    }
    
    public function setProcessProgress($processProgress) {
        $this->mProcessProgress = $processProgress;
    }
    
    public function setTags($tags) {
        $this->mTags = $tags;
    }    
    
    public function getName() {
        return $this->mName;
    }
    
    public function getType() {
        return $this->mType;
    }
    
    public function getDetail() {
        return $this->mDetail;
    }
    
    public function getStatus() {
        return $this->mStatus;
    }
    
    public function getPriority() {
        return $this->mPriority;
    }
    
    public function getEstimatedCompletionTime() {
        return $this->mEstimatedCompletionTime;
    }
    
    public function getActivationTime() {
        return $this->mActivationTime;
    }
    
    public function getExpirationTime() {
        return $this->mExpirationTime;
    }
    
    public function getAdditionTime() {
        return $this->mAdditionTime;
    }
    
    public function getModificationTime() {
        return $this->mModificationTime;
    }
    
    public function getProgress() {
        return $this->mProgress;
    }
    
    public function getProcessProgress() {
        return $this->mProcessProgress;
    }
    
    public function getTags() {
        return $this->mTags;
    }
    
    public function getTaskNumber() {
        preg_match($this->mTaskNumberPattern, $this->mUri, $matches);
        if (array_key_exists("number", $matches)) {
            return $matches["number"];
        } else {
            return null;
        }
    }
    
    public function addTag($tag) {
        $this->mTags[] = $tag;
    }
    
    public function addTags($tags) {
        $this->mTags = array_merge($this->mTags, $tags);
    }
    
    public function getUri() {
        return $this->mUri;
    }
    
    public function toHtml() {
        // TODO this is not a good way to do it.  I don't know of a 
        // nice way to display this yet, though.
        $out = "<table>";
        if ($this->mUri != null) $out .= "<tr><td>URI</td><td>{$this->mUri}</td></tr>";
        if ($this->mName != null) $out .= "<tr><td>Name</td><td>{$this->mName}</td></tr>";
        if ($this->mType != null) $out .= "<tr><td>Type</td><td>{$this->mType}</td></tr>";
        if ($this->mDetail != null) $out .= "<tr><td>Detail</td><td>{$this->mDetail}</td></tr>";
        if ($this->mStatus != null) $out .= "<tr><td>Status</td><td>{$this->mStatus}</td></tr>";
        if ($this->mPriority != null) $out .= "<tr><td>Priority</td><td>{$this->mPriority}</td></tr>";
        if ($this->mActivationTime != null) $out .= "<tr><td>Activation Time</td><td>{$this->mActivationTime}</td></tr>";
        if ($this->mExpirationTime != null) $out .= "<tr><td>Expiration Time</td><td>{$this->mExpirationTime}</td></tr>";
        if ($this->mEstimatedCompletionTime != null) $out .= "<tr><td>Estimated Completion Time</td><td>{$this->mEstimatedCompletionTime}</td></tr>";
        if ($this->mAdditionTime != null) $out .= "<tr><td>Addition Time</td><td>{$this->mAdditionTime}</td></tr>";
        if ($this->mModificationTime != null) $out .= "<tr><td>Modification Time</td><td>{$this->mModificationTime}</td></tr>";
        if ($this->mProgress != null) $out .= "<tr><td>Progress</td><td>{$this->mProgress}</td></tr>";
        if ($this->mProcessProgress != null) $out .= "<tr><td>Process Progress</td><td>{$this->mProcessProgress}</td></tr>";
        if (count($this->mTags) > 0) $out .= "<tr><td>Tags</td><td>".implode(",", $this->mTags)."</td></tr>";
        $out .= "</table>";
        if ($out == "<table></table>") return "<b>Nothing to output.</b>";
        else return $out;
    }
    
    public static function uriFromTaskNumber($number) {
        $urlnumber = urlencode($number);
        return "http://restapp.dyndns.org:9998/td/$urlnumber";
    }
}

/**
 * Utility module used mostly for handling HttpRequests.
 * Everything in here should be pretty straightforward.
 */
class Util {
    static function retrieveMessage($uri, $user=null, $password=null) {
      
        $etag = null;
        if (array_key_exists($uri, $_SESSION))
        {
            $etag = $_SESSION[$uri]->getHeader("etag");
        }
        $request = new HttpRequest($uri, HttpRequest::METH_GET);
        $request->setHeaders(array("Accept"=>"application/xml,text/xml;q=0.8"));
        echo "<!-- etag is $etag -->";
        if ($etag != null) {
            $request->setHeaders(array("If-None-Match" => $etag));
        }
        if ($user != null && $password != null) {
            $request->setOptions(array("httpauth"=>"$user:$password"));
        }
        
        $response = $request->send();
        
        if ($response->getResponseCode() == 304)
        {
            echo "<!-- $uri has not been changed. -->\n";
            return $_SESSION[$uri];
        } 
        else 
        {
            $etag = $response->getHeader("etag");
            echo "<!-- etag is now $etag -->\n";
            $_SESSION[$uri] = $response;
            echo "<!-- $uri is new. -->\n";
            return $_SESSION[$uri];
        }
    }
    
    static function getXmlResponse($body, $namespace="ns") {
        $xml = new SimpleXMLElement($body);
        $xml->registerXPathNamespace($namespace, 
                        "http://danieloscarschulte.de/cs/ns/2011/tm");
        return $xml;      
    }

    static function getAccounts() {
        // TODO this needs to be fixed eventually, since it's just hardcoded.
        // When this stuff gets put in a database, pull the accounts from
        // a database instead of seeing it hardcoded.
        return array(
            new Account("Bilbo Beutlin", "obliB", 
                "http://restapp.dyndns.org:9998/a/Bilbo%20Beutlin"),
            new Account("Frodo Beutlin", "odorF", 
                "http://restapp.dyndns.org:9998/a/Frodo%20Beutlin"),
            new Account("Samweis Sam Gamdschie", "maS", 
                "http://restapp.dyndns.org:9998/a/Samweis%20Sam%20Gamdschie")
        );
    }

    static function getTasksForAccount(Account $account, $fetch_description = true) {
        try {
            $response = self::retrieveMessage($account->getUri(), 
                                $account->getName(), $account->getPassword());
            $tasks = array();
            // TODO handle other response codes besides 200
            //TODO handle 304
            if ($response->getResponseCode() == 200) {
                $xml = self::getXmlResponse($response->getBody());
                $taskNodes = $xml->xpath('//ns:link[@rel="http://danieloscarschulte.de/cs'.
                                                '/tm/taskDescription"]');
                foreach ($taskNodes as $taskNode) {
                    if ($fetch_description) {
                        $task = self::retrieveTaskDescription($account, "".$taskNode['href']);
                        if ($task != null) $tasks[] = $task;
                    } else {
                        $tasks[] = new Task("".$taskNode, "".$taskNode['href']);
                    }
                }
            }
            return $tasks;
        } catch(HttpInvalidParamException $ex) {
            die("<b>Could not connect to the network.</b>");
        }
    }
    
    static function retrieveTaskDescription(Account $account, $taskUri) {
        try {
            $response = self::retrieveMessage($taskUri,
                                $account->getName(), $account->getPassword());

            $task = null;
            // TODO handle other response codes besides 200
            if ($response->getResponseCode() == 200) {
                // TODO handle different value types.  I'm just going to treat
                // everything as a string for right now.
                $xml = self::getXmlResponse($response->getBody());
                $task = new Task(null, $taskUri);
                $task->setName(self::firstValue($xml,'//ns:taskName'));
                $task->setType(self::firstValue($xml,'//ns:taskType'));
                $task->setDetail(self::firstValue($xml,'//ns:taskDetail'));
                $task->setStatus(self::firstValue($xml,'//ns:taskStatus'));
                $task->setPriority(self::firstValue($xml,'//ns:taskPriority'));
                $task->setEstimatedCompletionTime(self::firstValue($xml,'//ns:estimatedDuration'));
                $task->setActivationTime(self::firstValue($xml,'//ns:taskActivationTime'));
                $task->setExpirationTime(self::firstValue($xml,'//ns:taskExpirationTime'));
                $task->setAdditionTime(self::firstValue($xml,'//ns:taskAdditionTime'));
                $task->setModificationTime(self::firstValue($xml,'//ns:taskModificationTime'));
                $task->setProgress(self::firstValue($xml,'//ns:taskProgress'));
                $task->setProcessProgress(self::firstValue($xml,'//ns:processProgress'));
                foreach($xml->xpath('//ns:taskTag') as $k=>$v) {
                    $task->addTag("".$v);
                }
            }
            
            return $task;
        } catch(HttpInvalidParamException $ex) {
            return null;
        }
    }
    
    private static function firstValue($xml, $xpath) {
        $result = $xml->xpath($xpath);
        if (count($result) > 0) {
            return "".$result[0];
        } else {
            return null;
        }
    }
}
?>