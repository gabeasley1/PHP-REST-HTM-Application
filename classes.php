<?
require_once 'HTTP/Request2.php';
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
        return Util::getUserByUserName($name);
    }

    public static function addAccount($name, $password, $uri) {
        Util::addAccount($name, $password, $uri);
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
    private $mEtag;
    private $mTaskNumberPattern = '/\/td\/(?P<number>\d+)/';
    
    function __construct($name=null, $uri=null) {
        $this->mName = $name;
        $this->mUri = $uri;
        $this->mTags = array();
    }

    public function setEtag($etag) {
        $this->mEtag = $etag;
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
        $this->mActivationTime = self::parseDate($activationTime);
    }
    
    public function setExpirationTime($expirationTime) {
        $this->mExpirationTime = self::parseDate($expirationTime);
    }
    
    public function setAdditionTime($additionTime) {
        $this->mAdditionTime = self::parseDate($additionTime);
    }
    
    public function setModificationTime($modificationTime) {
        $this->mModificationTime = self::parseDate($modificationTime);
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

    public function getEtag() {
        return $this->mEtag;
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
        $startDate = "Unknown"; $startAt = ""; $startTime = "";
        $expirationDate = "Unknown"; $expirationAt = ""; $expirationTime = "";
        if ($this->mActivationTime != null) {
            $startDate = $this->mActivationTime->format('D, M j, Y');
            $startAt = 'at';
            $startTime = $this->mActivationTime->format('G:i');
        }
        if ($this->mExpirationTime != null) {
            $expirationDate = $this->mExpirationDate->format('D, M j, Y');
            $expirationAt = 'at';
            $expirationTime = $this->mExpirationTime->format('G:i');
        }

        $tagsHtml = '';
        foreach ($this->mTags as $tag) {
            $tagsHtml .= "<span class='tag'>$tag</span>\n";
        }

        return <<<EOF
        <div class='task-display'>
            <div class='title-section'>
                <div class='title-wrapper'>
                <div class='input-table-row'>
                    <div class='title-col'>
                        <span id='task-title'>{$this->mName}</span>
                    <img id='task-priority' alt='{$this->mPriority}'
                         title='{$this->mPriority}'
                         src='./priority.php?priority={$this->mPriority}&len=10'
                    />
                    </div>
                    <div class='title-col-right'>
                    </div>
                </div>
                <div class='input-row'>
                    <div>
                        <label id='task-status-label'>
                            Status
                        </label>
                    </div>
                    <div>
                        {$this->mStatus}
                    </div>
                </div>
                </div> 
            </div>
            <div class='dates-section'>
                <div class='date-table'>
                    <div class='start-date'>
                    <div class='date-table'>
                        <div class='date-row'>
                            <div class='date-item'>
                                <label id='task-start-date-label'>
                                    Start Date
                                </label>
                            </div>
                            <div class='date-item'>
                                <label id='task-start-time-label'>
                                    Time
                                </label>
                            </div>
                        </div>
                        <div class='date-row'>
                            <div class='date-input'>
                                <span id='start-date'>$startDate</span>
                                <span class='divider'>$startAt</span>
                            </div>
                            <div class='time-input'>
                                <span id='start-time'>$startTime</span>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class='end-date'>
                    <div class='date-table'>
                        <div class='date-row'>
                            <div class='date-item'>
                                <label id='task-expiration-date-label'>
                                    End Date
                                </label>
                            </div>
                            <div class='date-item'>
                                <label id='task-expiration-time-label'>
                                    Time
                                </label>
                            </div>
                        </div>
                        <div class='date-row'>
                            <div class='date-input'>
                                <span id='expiration-date'>
                                    $expirationDate
                                </span>
                                <span class='divider'>$expirationAt</span>
                            </div>
                            <div class='time-input'>
                                <span id='expiration-time'>
                                    $expirationTime
                                </span>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <div class='progress-section'>
                <div class='progress-wrapper'>
                    <div class='input-row'>
                        <label id='task-progress-label'>Progress: </label>
                            <span id='task-progress'>{$this->mProgress}</span>
                        <label>%</label>
                    </div>
                    <div id='task-progress-bar'></div>
                </div>
            </div>
            <div class='details-section'>
                <div class='details-wrapper'>
                    <div class='input-row'>
                        <label id='task-details-label'>
                            Details
                        </label>
                    </div>
                    <div class='input-row'>
                        <div id='task-details'>{$this->mDetail}</div>
                    </div>
                </div>
            </div>
            <div class='tags-section'>
                <div class='tags-wrapper'>
                    <div class='input-row'>
                        <label for='task-tags-edit' id='task-tags-label'>
                            Tags
                        </label>
                    </div>
                    <div class='input-row'>
                        $tagsHtml
                    </div>
                </div>
            </div>
        </div>
EOF;
    }
    
    public static function uriFromTaskNumber($number) {
        $urlnumber = urlencode($number);
        return "http://restapp.dyndns.org:9998/td/$urlnumber";
    }

    private static function parseDate($datestr) {
        $formats = array('Y#m#d\TH#m#s#uP', 'Y#m#d\TH#m#sP', 'Y#m#d', 'Y#n#d', 
            'Y#m#d H:m:s', 'Y#m#d G#m#s', 'Y#m#d H#m', 'Y#m#d G#m', 'Y#m#j', 
            'Y#n#j', 'Y#m#j H#m#s', 'Y#m#j G#m#s', 'Y#m#j H#m', 'Y#m#j G#m', 
            'm#d#Y', 'n#d#Y', 'm#d#Y H#m#s', 'm#d#Y G#m#s', 'm#d#Y H#m', 
            'm#d#Y G#m', 'm#j#Y', 'n#j#Y', 'm#j#Y H#m#s', 'm#j#Y G#m#s', 
            'm#j#Y H#m', 'm#j#Y G#m', 'Y#m#d\TH#m#s', 'Y#m#d\TG#m#s', 
            'Y#m#d\TH#m', 'Y#m#d\TG#m', 'Y#m#j\TH#m#s', 'Y#m#j\TG#m#s', 
            'Y#m#j\TH#m', 'Y#m#j\TG#m', 'm#d#Y\TH#m#s', 'm#d#Y\TG#m#s', 
            'm#d#Y\TH#m', 'm#d#Y\TG#m', 'm#j#Y\TH#m#s', 'm#j#Y\TG#m#s', 
            'm#j#Y\TH#m', 'm#j#Y\TG#m');
        $date = null;
        foreach ($formats as $format) {
            try {
                $date = DateTime::createFromFormat($format, $datestr);
                $errs = DateTime::getLastErrors();
                if ($date) {
                    return $date;
                }
            } catch (Exception $e) {
                // Do nothing.  It was an invalid format.
            }   
        }
        return null;
    }

    public static function toEditHtml($user, $task=null, $copy=false) {
        $name = '';
        $priority = 'NONE';
        $status = '';
        $expirationDate = date('m/d/Y');
        $expirationTime = date('G:i');
        $startDate = date('m/d/Y');
        $startTime = date('G:i');
        $additionDate = date('m/d/Y');
        $additionTime = date('G:i');
        $progress = 0;
        $tags = '';
        $type = 'N/A';
        $uri = $user->getUri();
        $details = '';
        $method = 'POST';
        $etag = '';
        $tasknumber = '';
        if ($task != null) {
            $name = $task->getName();
            $priority = $task->getPriority();
            $status = $task->getStatus();
            if ($task->getExpirationTime() != null) {
                $expirationDate = $task->getExpirationTime()->format('m/d/Y');
                $expirationTime = $task->getExpirationTime()->format('G:i');
            }
            if ($task->getActivationTime() != null) {
                $startDate = $task->getActivationTime()->format('m/d/Y');
                $startTime = $task->getActivationTime()->format('G:i');
            }
            if ($task->getAdditionTime() != null) {
                $additionDate = $task->getAdditionTime()->format('m/d/Y');
                $additionTime = $task->getAdditionTime()->format('G:i');
            }
            $progress = $task->getProgress();
            $tags = implode(', ', $task->getTags());
            $details = $task->getDetail();
            $type = $task->getType(); 
            if (!$copy) $method = 'PUT';
            if (!$copy) $uri = $task->getUri();
            if (!$copy) $etag = $task->getEtag();
            if (!$copy) $tasknumber = $task->getTaskNumber();
        }
        $modificationDate = date('m/d/Y');
        $modificationTime = date('G:i');
        $userName = $user->getName();
        $priorityOptionsHtml="<select id='task-priority-edit' name='priority'>";
        $priValues = array('NONE', 'LOWEST', 'VERYLOW', 'LOW', 'MEDIUM', 'HIGH',
            'VERYHIGH', 'HIGHEST');
        $priVisual = array('None', 'Lowest', 'Very Low', 'Low', 'Medium',
            'High', 'Very High', 'Higest');
        for ($i = 0; $i < count($priValues); $i++) {
            $priVal = $priValues[$i];
            $priVis = $priVisual[$i];
            $selected = $priVal == $priority ? "selected='selected'" : "";
            $priorityOptionsHtml .= "<option value='$priVal' $selected>$priVis".
                "</option>";
        }
        $priorityOptionsHtml .= "</select>";
        return <<<EOF
        <div class='task-edit'>
            <form id='task-edit-form' method='POST' action='newentry.php'>
                <div class='title-section'>
                <div class='title-wrapper'>
                    <div class='input-table-row'>
                        <div class='title-col'>
                            <label for='task-title-edit' id='task-title-label'>
                                Title
                            </label>
                        </div>
                        <div class='title-col'>
                            <label for='task-priority-edit' 
                                   id='task-priority-label'>
                                Priority
                            </label>
                        </div>
                    </div>
                    <div class='input-table-row'>
                        <div class='title-col'>
                            <input type='text' id='task-title-edit' name='name' 
                                   value='$name' size='70'/>
                        </div>
                        <div class='title-col-right'>
                            $priorityOptionsHtml
                        </div>
                    </div>
                    <div class='input-row'>
                        <div>
                            <label for='task-status-edit'
                                   id='task-status-label'>
                                Status
                            </label>
                        </div>
                        <div>
                            <input type='text' id='task-status-edit' 
                                   name='status' value='$status' />
                        </div>
                    </div>
                </div> 
                </div>
                <div class='dates-section'>
                <div class='date-table'>
                    <div class='start-date'>
                    <div class='date-table'>
                        <div class='date-row'>
                            <div class='date-item'>
                                <label for='task-start-date-edit'
                                       id='task-start-date-label'>
                                    Start Date
                                </label>
                            </div>
                            <div class='date-item'>
                                <label for='task-start-time-edit'
                                       id='task-start-time-label'>
                                    Time
                                </label>
                            </div>
                        </div>
                        <div class='date-row'>
                            <div class='date-input'>
                                <div class='input-wrapper'>
                                    <input type='text' id='task-start-date-edit'
                                           name='start-date' value='$startDate' 
                                           maxlength='10' />
                                    <label class='date-icon'
                                           for='task-start-date-edit'
                                           id='task-start-date-icon'></label>
                                </div>
                                <span class='divider'>at</span>
                            </div>
                            <div class='time-input'>
                                <div class='input-wrapper'>
                                    <input type='text' id='task-start-time-edit'
                                           name='start-time' value='$startTime'
                                           />
                                    <label class='time-icon'
                                           for='task-start-time-edit'
                                           id='task-start-time-icon'></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class='end-date'>
                    <div class='date-table'>
                        <div class='date-row'>
                            <div class='date-item'>
                                <label for='task-expiration-date-edit'
                                       id='task-expiration-date-label'>
                                    End Date
                                </label>
                            </div>
                            <div class='date-item'>
                                <label for='task-expiration-time-edit'
                                       id='task-expiration-time-label'>
                                    Time
                                </label>
                            </div>
                        </div>
                        <div class='date-row'>
                            <div class='date-input'>
                                <div class='input-wrapper'>
                                    <input type='text' 
                                           id='task-expiration-date-edit'
                                           name='expiration-date' maxlength='10'
                                           value='$expirationDate' />
                                    <label class='date-icon'
                                           for='task-expiration-date-edit'
                                           id='task-end-date-icon'></label>
                                </div>
                                <span class='divider'>at</span>
                            </div>
                            <div class='time-input'>
                                <div class='input-wrapper'>
                                    <input type='text' 
                                           id='task-expiration-time-edit'
                                           name='expiration-time' 
                                           value='$expirationTime' />
                                    <label class='time-icon'
                                           for='task-expiration-time-edit'
                                           id='task-end-time-icon'></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <div class='progress-section'>
                <div class='progress-wrapper'>
                    <div class='input-row'>
                        <label for='task-progress-edit'
                               id='task-progress-label'>Progress: </label>
                        <input type='text' id='task-progress-edit' 
                               name='progress' value='$progress' maxlength='3'/>
                        <label for='task-progress-edit'>%</label>
                    </div>
                    <div id='task-progress-edit-slider'></div>
                </div>
                </div>
                <div class='details-section'>
                    <div class='details-wrapper'>
                        <div class='input-row'>
                            <label for='task-details-edit' 
                                   id='task-details-label'>
                                Details
                            </label>
                        </div>
                        <div class='input-row'>
                            <textarea id='task-details-edit' name='details'
                                rows='6'>$details</textarea>
                            <div id='task-details-toolbar'></div>
                            <div id='task-details-rich-editor'></div>
                        </div>
                    </div>
                </div>
                <div class='tags-section'>
                <div class='tags-wrapper'>
                    <div class='input-row'>
                        <label for='task-tags-edit' id='task-tags-label'>
                            Tags
                        </label>
                    </div>
                    <div class='input-row'>
                        <textarea id='task-tags-edit' name='tags[]' rows='1'
                                >$tags</textarea>
                    </div>
                </div>
                </div>
                <input type='hidden' name='addition-date'
                       value='$additionDate' />
                <input type='hidden' name='addition-time'
                       value='$additionTime' />
                <input type='hidden' name='modification-date'
                       value='$modificationDate' />
                <input type='hidden' name='modification-time'
                       value='$modificationTime' />
                <input type='hidden' name='user' value='$userName' />
                <input type='hidden' name='uri' value='$uri' />
                <input type='hidden' name='method' value='$method' />
                <input type='hidden' name='type' value='$type' />
                <input type='hidden' name='etag' value='$etag' />
                <input type='hidden' name='tasknumber' value='$tasknumber' />

                <div class='submit-section'>
                    <div class='submit-wrapper'>
                        <input type='submit' value='Save' />
                    </div>
                </div>
            </form>
        </div>
EOF;
    }

    static function deleteTask($user, $task) {
        $url = $task->getUri();
        $auth = "{$user->getName()}:{$user->getPassword()}";
        $request = new HTTP_Request2($url, HTTP_Request2::METHOD_DELETE);

        $response = $request->send();
        $codeBase = (int) ($response->getStatus() / 100);

        if ($codeBase == 2) {
            return true;
        } else if ($response->getStatus() == 401) {
            return "Bad authorization.  Your login credentials may be stale.";
        } else {
            return "Something went wrong, the task may not have been deleted.";
        }
    }
}

/**
 * Utility module used mostly for handling HttpRequests.
 * Everything in here should be pretty straightforward.
 */
class Util {
    private static $mysqli = null;

    public static function init() {
        self::$mysqli = new mysqli("localhost", "root", "", "login accounts");
    }

    public static function getUserByUserName($name) {
        $result = self::$mysqli->query("SELECT Username, Password, URI ".
                                       "FROM users ".
                                       "WHERE Username='$name'");
        if ($result) {
            $row = $result->fetch_assoc();
            return new Account($row["Username"], $row["Password"], $row["URI"]);
        }
        return null;
    }

    static function escape($string) {
        return str_replace('+','%20', urlencode($string));
    }

    static function retrieveMessage($uri, $user=null, $password=null) {
      
        $etag = null;
        if (array_key_exists($uri, $_SESSION))
        {
            $etag = $_SESSION[$uri]["etag"];

        }
        $request = new HTTP_Request2($uri, HTTP_Request2::METHOD_GET);
        $request->setHeader("Accept", "application/xml,text/xml;q=0.8");
        if ($etag != null) {
            $request->setHeader("If-None-Match", $etag);
        }
        if ($user != null && $password != null) {
            $request->setAuth($user, $password);
        }
        
        $response = $request->send();
        
        if ($response->getStatus() == 304)
        {
            return $_SESSION[$uri];
        } 
        else 
        {
            $etag = $response->getHeader("etag");
            $_SESSION[$uri] = array("etag"=>$etag, "body"=>$response->getBody(),
                                    "status"=>$response->getStatus());
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
        $users = array();
        $result = self::$mysqli->query("SELECT Username, Password, URI ".
                                       "FROM users; ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = new Account($row["Username"], $row["Password"], 
                    $row["URI"]);
            }
            return $users;
        }
        return null;
    }

    static function getTasksForAccount(Account $account, $fetch_description = true) {
        try {
            $response = self::retrieveMessage($account->getUri(), 
                                $account->getName(), $account->getPassword());
            $tasks = array();
            
            if ($response["status"] == 200) {
                $xml = self::getXmlResponse($response["body"]);
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
            //die("<b>Could not connect to the network.</b>");
            return array();
        }
    }
    
    static function retrieveTaskDescription(Account $account, $taskUri) {
        try {
            $response = self::retrieveMessage($taskUri,
                                $account->getName(), $account->getPassword());

            $task = null;
            // TODO handle other response codes besides 200
            if ($response["status"] == 200) {
                // TODO handle different value types.  I'm just going to treat
                // everything as a string for right now.
                $xml = self::getXmlResponse($response["body"]);
                $task = new Task(null, $taskUri);
                $task->setEtag(htmlentities($response["etag"]));
                $task->setName(self::firstValue($xml,'//ns:taskName'));
                $task->setType(self::firstValue($xml,'//ns:taskType'));
                $task->setDetail(html_entity_decode(html_entity_decode(self::firstValue(
                    $xml,'//ns:taskDetail'))));
                $task->setStatus(self::firstValue($xml,'//ns:taskStatus'));
                $task->setPriority(self::firstValue($xml,'//ns:taskPriority'));
                $task->setEstimatedCompletionTime(self::firstValue($xml,'//ns:estimatedDuration'));
                $task->setActivationTime(self::firstValue($xml,'//ns:taskActivationTime'));
                $task->setExpirationTime(self::firstValue($xml, '//ns:taskExpriationTime'));
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

    private static function GenerateXmlDoc($params) {
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        $xml .= "<tm xmlns=\"http://danieloscarschulte.de/cs/ns/2011/tm\">\n";
        $xml .= "\t<taskDescription>\n";
        foreach ($params as $name=>$value) {
            if ($name == "link:self") {
                $xml .= "\t\t<link href=\"$value\" rel=\"self\" />\n";
            } else if ($name == "taskDetail:") {
                $value = htmlentities(htmlentities($value));
                $xml .= "\t\t<taskDetail type='text/plain'>$value</taskDetail>\n";
            } else {
                $xml .= "\t\t<$name>$value</$name>\n";
            }
        }
        $xml .= "\t</taskDescription>\n</tm>";
        return $xml;
    }

    private static function TaskEntry($user, $name=null, $priority=null, $status=null, $eta=null, 
                      $ActivationTime=null, $ExpirationTime=null, $Addition=null, 
                      $Modification=null, $progress=null, $processProgress=null,
                      $tags=array(), $type=null, $details=null, $uri=null, $method="POST", $etag=null)
    {
        $params = array("taskPriority"=>"NONE");
        if ($name != null) $params["taskName"] = $name;
        if ($priority != null) $params["taskPriority"] = $priority;
        if ($status != null) $params["taskStatus"] = $status;
        if ($eta != null) $params["estimatedDuration"] = $eta;
        if ($ActivationTime != null) $params["taskActivationTime"]= $ActivationTime;
        if ($ExpirationTime != null) $params["taskExpirationTime"]= $ExpirationTime;
        if ($Addition != null) $params["taskAdditionTime"] = $Addition;
        if ($Modification != null) $params["taskModificationTime"] = $Modification;
        if ($progress != null) $params["taskProgress"] = $progress;
        if ($processProgress != null) $params["processProgress"] = $processProgress;
        if ($type != null) $params["taskType"] = $type;
        if ($uri != null && $method == "PUT") $params["link:self"] = $uri;
        if ($details != null) $params["taskDetail:"] = $details;
        if ($tags != null && count($tags) > 0) {
            if (count($tags) == 1) $tags = explode(",", $tags[0]);
            $tags = array_map("trim", $tags);
            $tagsHtml = implode("</taskTag>\n\t\t<taskTag>", $tags);
            $params["taskTag"] = $tagsHtml;
        }
        $http_method = $method == "POST" ? HTTP_Request2::METHOD_POST :
            HTTP_Request2::METHOD_PUT;
        if ($uri == null && $method == "POST") {
            $uri = $user->getUri();
        }
        $request = new HTTP_Request2($uri, $http_method);
        $userpass = "{$user->getName()}:{$user->getPassword()}";
        $request->setAuth($user->getName(), $user->getPassword());
        $body = self::GenerateXmlDoc($params);
        $request->setBody($body);
        $request->setHeader("Content-Type", "application/xml;charset=UTF-8");
        $request->setHeader("If-Match", html_entity_decode($etag));
        $request->setHeader("Accept-Encoding", "UTF-8");
        $request->setHeader("Connection", "Keep-Alive");
        return $request->send();
    }

    static function AddEntry($user, $name=null, $priority=null, $status=null, $eta=null, 
                      $ActivationTime=null, $ExpirationTime=null, $Addition=null, 
                      $Modification=null, $progress=null, $processProgress=null,
                      $tags=array(), $type=null, $details=null) {
          return self::TaskEntry($user, $name, $priority, $status, $eta, 
              $ActivationTime, $ExpirationTime, $Addition,
              $Modification, $progress, $processProgress,
              $tags, $type, $details, $user->getUri(), "POST", null);
    }

    static function EditEntry($user, $name=null, $priority=null, $status=null, $eta=null, 
                       $ActivationTime=null, $ExpirationTime=null, 
                       $Addition=null, $Modification=null, $Progress=null, 
                       $ProcessProgress=null, $Tags=null, $Type=null, 
                       $details=null, $Uri=null, $etag=null)
    {
          return self::TaskEntry($user, $name, $priority, $status, $eta, 
              $ActivationTime, $ExpirationTime, $Addition,
              $Modification, $Progress, $ProcessProgress,
              $Tags, $Type, $details, $Uri, "PUT", $etag);
    }

    public static function addAccount($name, $password, $uri) {
        if ($result = self::$mysqli->query("SELECT COUNT(*) as total ".
                                           "FROM users ".
                                           "WHERE Username='$name';")) {
            $row = $result->fetch_assoc();
            if ($row["total"] > 0) {
                return null;
            }
        }
        $result = self::retrieveMessage($uri, $name, $password);
        var_dump($result);
        if ((int) ($result["status"]/100) == 2) {
            $query = "INSERT INTO users(Username, Password,    URI) ".
                     "           VALUES('$name',  '$password', '$uri');";
            $result = self::$mysqli->query($query);
            if ($result === TRUE) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
} Util::init();
?>
