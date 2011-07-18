<?
/**
 * A number of classes and utilities for manipulating users, tasks,
 * URIs, and other goodies.  This file can be easily added to any
 * other PHP scripts you're working on by adding the line
 * <code>
 *     require_once('/path/to/your/classes.php');
 * </code>
 *
 * @author Andrew Hays
 * @copyright LGPLv3
 * @version 1.0
 * @package PhpHtmRestApplicationClasses
 * @todo {Andrew Hays|Grant Beasley} This package will likely need another class
 *              that is associated with the new style users that we will be
 *              storing in our database.  We will need methods for manipulating
 *              those and other methods (that will likely go in the
 *              {@link Util} class) to link the User objects with the Account
 *              objects.
 */

require_once 'HTTP/Request2.php';
session_start();
/**
 * Class for manipulating an account.
 * This class mostly contains getters and setters for manipulating
 * an account.
 *
 * @author Andrew Hays
 * @package PhpHtmRestApplicationClasses
 * @subpackage Accounts
 */
class Account {
    /**
     * The name attribute for an account object.
     * @var string 
     */
    private $mName;
    /**
     * The password attribute for an account object.
     * @var string
     */
    private $mPassword;
    /**
     * The Uri attribute for an account object.
     * @var string
     */
    private $mUri;
    /**
     * The pattern to match the end of a username, to make sure that it looks
     * correct.
     * @todo {Andrew Hays} I'm not entirely sure that this is still required
     * @var string
     * @static
     */
    private static $sUserNamePattern = '/\/a\/(?P<name>[\w% ]+)/';

    /**
     * Constructor for the <code>Account</code> class.  Sets up a new account
     * (from the database or hardcoded).
     *
     * @param string $name The name for the account, this should NOT be encoded
     *          for urls, that will happen naturally when required.
     * @param string $password The password for the account.
     * @param string $uri The Uri that should be associated with the account
     *          for logging in.
     */
    function __construct($name, $password, $uri) {
        $this->mName = $name;
        $this->mPassword = $password;
        $this->mUri = $uri;
    }

    /**
     * Getter for the name attribute.
     * @return string The name attribute.
     */
    public function getName() {
        return $this->mName;
    }

    /**
     * Getter for the Uri attribute
     * @return string The uri attribute.
     */
    public function getUri() {
        return $this->mUri;
    }

    /**
     * Getter for the password attribute.
     * @return string The password attribute
     */
    public function getPassword() {
        return $this->mPassword;
    }

    /**
     * Getter for the username attribute.  Currently this looks at uri for the
     * object in question and determines the username.
     * @todo {Andrew Hays} Is this required?  Will {@link Account::getName()} 
     *          work instead?
     * @return string The username associated with the account.
     */
    public function getUserName() {
        preg_match(self::$sUserNamePattern, $this->mUri, $matches);
        if (array_key_exists("name", $matches)) {
            return urldecode($matches["name"]);
        } else {
            return null;
        }
    }

    /**
     * Gets the uri for the user given a number.
     * @static
     * @deprecated
     * @todo {Andrew Hays} Can we phase this out completely?
     * @param string|int $number The number associated with the account.
     * @return string The alternate uri associated with the user number in
     *          question.
     */
    public static function uriFromUserNumber($number) {
        $urlnumber = urlencode($number);
        return "http://restapp.dyndns.org:9998/tdl/$urlnumber";
    }

    /**
     * Gets the uri for a user given a username
     * @static
     * @deprecated
     * @todo {Grant Beasley} All calls to this function should instead use some
     *          form of {@link Account::getUserByUserName()} instead.
     * @param string $name The name associated with the account.
     * @return string The Uri associated with the account in question.
     */
    public static function uriFromUserName($name) {
        $urlname = str_replace('+','%20', urlencode($name));
        return "http://restapp.dyndns.org:9998/a/$urlname";
    }

    /**
     * Gets the account for a user given a username.
     * @static
     * @todo {Grant Beasley} Any calls to this function should use the new style
     *          queries from the database.  It will return Accounts associated
     *          with Daniel's API based on a username given, by comparing it
     *          with the database.
     * @param string $name The name associated with the account.
     * @return Account The account for Daniel's database associated with the 
     *          given username.
     */
    public static function getUserByUserName($name) {
        return Util::getUserByUserName($name);
    }

    /**
     * Adds a new account to the database.
     * @static
     * @todo {Grant Beasley} This should use the new style queries, where the
     *          $name, $password, and $uri variables are associated with the
     *          API from Daniel.  You will likely need to add a few more
     *          parameters (associated with our accounts that we use), so that
     *          we can link users and accounts.
     * @param string $name The name for the account
     * @param string $password The password for the account
     * @param string $uri The uri for the account.
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
    private static $sTaskNumberPattern = '/\/td\/(?P<number>\d+)/';

    /**
     * Constructor for a Task object.  Both parameters are optional
     * @param $name Optional name parameter.  Can be set later.
     * @param $uri Optional uri parameter.  Can be set later.
     */
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

    /**
     * The status for the current task.
     * @return Task status
     */
    public function getStatus() {
        return $this->mStatus;
    }

    /**
     * Returns the priority for the Task
     * @return string The Task's priority.
     */
    public function getPriority() {
        return $this->mPriority;
    }
    
    /**
     * Returns the Estimated completion time for the Task.
     * @return DateTime Estimated completion time.
     */
    public function getEstimatedCompletionTime() {
        return $this->mEstimatedCompletionTime;
    }

    /**
     * Returns the activation time for the Task.
     * @return DateTime Activation time.
     */
    public function getActivationTime() {
        return $this->mActivationTime;
    }
    
    /**
     * Returns the expiration time for the Task.
     * @return DateTime Expiration time.
     */
    public function getExpirationTime() {
        return $this->mExpirationTime;
    }
    
    /**
     * Returns the addition time for the Task.
     * @return DateTime Addition time.
     */
    public function getAdditionTime() {
        return $this->mAdditionTime;
    }

    /**
     * Returns the modification time for the Task.
     * @return DateTime Modification time.
     */
    public function getModificationTime() {
        return $this->mModificationTime;
    }

    /**
     * Returns the current progress for the Task.
     * @return int Current progress.
     */
    public function getProgress() {
        return $this->mProgress;
    }

    /**
     * Returns the current process progress for the Task.
     * @return int The current process progress.
     */
    public function getProcessProgress() {
        return $this->mProcessProgress;
    }

    /**
     * Return the list of tags associated with the Task.
     * @return array The tags for the Task.
    public function getTags() {
        return $this->mTags;
    }

    /**
     * Function to get the task number associated with the account.  I don't
     * think there's any way around not using this function, yet.
     * @return int The task number, or null if none was found.
     */
    public function getTaskNumber() {
        preg_match(self::$sTaskNumberPattern, $this->mUri, $matches);
        if (array_key_exists("number", $matches)) {
            return $matches["number"];
        } else {
            return null;
        }
    }

    /**
     * Adds a single tag to the set of tags the task already has.
     * @param string $tag The tag to add to the account.
     */
    public function addTag($tag) {
        $this->mTags[] = $tag;
    }

    /**
     * Adds a series of tags to the set of tags that the task already has.
     * @param array $tags The tags to add.
     */
    public function addTags($tags) {
        $this->mTags = array_merge($this->mTags, $tags);
    }

    /**
     * Getter for the uri attribute of a task.
     * @return string The uri attribute of the task.
     */
    public function getUri() {
        return $this->mUri;
    }

    /**
     * Displays a task in Html.  No styling is given, so all styling should
     * be done through external CSS.
     * 
     * @return string The Html for displaying the Task.
     */
    public function toHtml() {
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
                         src='/priority.php?priority={$this->mPriority}&len=10'
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

    /**
     * Returns a task, given a task number.  We still use this, I believe,
     * because we don't typically store any information about tasks in our
     * database.  So this method is required for now.
     *
     * @param string|int $number The number associated with the task.
     * @param string The uri associated with the task.
     */
    public static function uriFromTaskNumber($number) {
        $urlnumber = urlencode($number);
        return "http://restapp.dyndns.org:9998/td/$urlnumber";
    }

    /**
     * Attempts to parse the date given a date string using one of many
     * date string styles.
     * @todo {Andrew Hays} I would love to see some way to clean this up,
     *          but right now I don't know what that would be.
     * @param string The string to parse.
     * @return DateTime the DateTime associated with the account, or null if 
     *          no date could be parsed.
     */
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

    /**
     * Static method for returning an Html string that allows for manipulation
     * through an Html form.  Note that this code isn't styled at all, so any
     * styling will have to be done externally through CSS.
     * 
     * @param Account $user The account associated with the task.
     * @param Task $task Optional task to add.  If no task is given, it's
     *          assumed that you're creating a new account.
     * @param boolean $copy Optional parameter to specify whether or not to
     *          just create a copy of the provided task.
     * @return string The Html required to create the form.
     */
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


    /** 
     * Static method to delete a task from the database, given an 
     * {@link Account} and a {@link Task}.
     *
     * @param Account $user The user associated with the task
     * @param Task $task The task to be deleted.
     * @return boolean|string If the deletion was successful, thie method
     *          returns <code>true</code>.  Otherwise this method returns
     *          some kind of error string.
     */
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
 * All methods in this class should remain static and can be called with
 * <code>
 *      Util::methodName()
 * </code>
 * @todo {Grant Beasley|Andrew Hays} Methods should be added to link the 
 *      {@link User} class (when created) with {@link Account} objects.
 * @author Andrew Hays
 */
class Util {
    /**
     * Static variable for the MySql class.
     * @var Mysqli
     */
    private static $mysqli = null;

    /**
     * Static initializer for this class.  Has to be explicitly called since
     * PHP doesn't support constructors for static classes.
     * @todo {Grant Beasley} This needs to be updated with the new database
     *          credentials.
     */
    public static function init() {
        self::$mysqli = new mysqli("localhost", "root", "", "login accounts");
    }

    /**
     * Searches the database for an Account with a username.
     * @todo {Grant Beasley} This should use the new query tables.
     * @todo {Andrew Hays} Rename to getAccountByUserName and fix all references
     */
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

    /**
     * Utility function for escaping a string for Daniel's server.  Encodes
     * the string using urlencode, and then replaces all '+' with '%20' so that
     * Daniel's server can handle it.
     *
     * @param string $string The string to encode.
     * @return string The encoded string.
     */
    static function escape($string) {
        return str_replace('+','%20', urlencode($string));
    }

    /**
     * Utility function for retrieving a message from the server.
     * @param string $uri The uri to query for the message
     * @param string $user The user for authentication purposes, if required.
     * @param string $password The password for authentication purposes, if
     *          required.
     * @return array Some details associated with the account.
     */
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

    /**
     * Converts an Xml string to an Xml dom.
     * @param string $body The xml string to convert
     * @param string $namespace the alias namespace to use with searching for
     *          elements in the Xml.
     * @return SimpleXMLElement The xml dom for the body.
     */
    static function getXmlResponse($body, $namespace="ns") {
        $xml = new SimpleXMLElement($body);
        $xml->registerXPathNamespace($namespace, 
                        "http://danieloscarschulte.de/cs/ns/2011/tm");
        return $xml;      
    }

    /**
     * Returns all accounts in the database
     * @todo {Grant Beasley} Adjust this query to use new tables.  You'll likely
     *          need to accept a new parameter (like a {@link User} object) so
     *          that you will only get accounts associated with that User.
     * @return array An array of {@link Account} objects.
     */
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

    /**
     * Returns a list of tasks for a given account.
     * @todo {Andrew Hays} Write similar method that accepts an array of
     *          accounts to display a list of all tasks for the listed accounts
     * @param Account $account The account to look up tasks for.
     * @param boolean $fetch_description Whether or not to continue to do 
     *          requests to fetch descriptions for the tasks as well, or if
     *          names and links will be fine.
     * @return array An array of {@link Task} objects
     */
    static function getTasksForAccount(Account $account, 
                                       $fetch_description = true) {
        try {
            $response = self::retrieveMessage($account->getUri(), 
                                $account->getName(), $account->getPassword());
            $tasks = array();
            
            if ($response["status"] == 200) {
                $xml = self::getXmlResponse($response["body"]);
                $taskNodes = $xml->xpath('//ns:link[@rel="'.
                                         'http://danieloscarschulte.de/cs'.
                                         '/tm/taskDescription"]');
                foreach ($taskNodes as $taskNode) {
                    if ($fetch_description) {
                        $task = self::retrieveTaskDescription($account, 
                            "".$taskNode['href']);
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

    /**
     * Retrieves a task description for a given account and task uri
     * @param Account $account The account to use for authentication purposes.
     * @param string $taskUri The Uri to look in for the account.
     * @return Task A task with descriptions loaded.
     */
    static function retrieveTaskDescription(Account $account, $taskUri) {
        try {
            $response = self::retrieveMessage($taskUri,
                                $account->getName(), $account->getPassword());

            $task = null;
            // TODO {Andrew Hays|Grant Beasley} handle other response codes 
            // besides 200
            if ($response["status"] == 200) {
                $xml = self::getXmlResponse($response["body"]);
                $task = new Task(null, $taskUri);
                $task->setEtag(htmlentities($response["etag"]));
                $task->setName(self::firstValue($xml,'//ns:taskName'));
                $task->setType(self::firstValue($xml,'//ns:taskType'));
                $task->setDetail(html_entity_decode(html_entity_decode(
                    self::firstValue($xml,'//ns:taskDetail'))));
                $task->setStatus(self::firstValue($xml,'//ns:taskStatus'));
                $task->setPriority(self::firstValue($xml,'//ns:taskPriority'));
                $task->setEstimatedCompletionTime(self::firstValue(
                    $xml,'//ns:estimatedDuration'));
                $task->setActivationTime(self::firstValue(
                    $xml,'//ns:taskActivationTime'));
                $task->setExpirationTime(self::firstValue(
                    $xml, '//ns:taskExpriationTime'));
                $task->setAdditionTime(self::firstValue(
                    $xml,'//ns:taskAdditionTime'));
                $task->setModificationTime(self::firstValue(
                    $xml,'//ns:taskModificationTime'));
                $task->setProgress(self::firstValue(
                    $xml,'//ns:taskProgress'));
                $task->setProcessProgress(self::firstValue(
                    $xml,'//ns:processProgress'));
                foreach($xml->xpath('//ns:taskTag') as $k=>$v) {
                    $task->addTag("".$v);
                }
            }
            
            return $task;
        } catch(HttpInvalidParamException $ex) {
            return null;
        }
    }

    /**
     * Uses xpath to find the first value of a certain type in the Xml.  Not
     * the most beautiful way to handle this, but it gets the job done.
     * @param SimpleXMLElement $xml The xml to search through
     * @param string $xpath The xpath search to use.
     * @return string The value at the point.
     */
    private static function firstValue($xml, $xpath) {
        $result = $xml->xpath($xpath);
        if (count($result) > 0) {
            return "".$result[0];
        } else {
            return null;
        }
    }

    /**
     * Generates an xml document for a given list of parameters
     * @param array $params An associative array used to create the Xml document
     * @return string An xml string to submit to the server.
     */
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

    /**
     * Generic task entry method for adding or updating a task on the server.
     * Each parameter is generally easily understandable and I'm not going
     * to document them specifically.
     * @return HTTP_Request2 The request to the server for later parsing.
     */
    private static function TaskEntry($user, $name=null, $priority=null, 
            $status=null, $eta=null, $ActivationTime=null, 
            $ExpirationTime=null, $Addition=null, $Modification=null, 
            $progress=null, $processProgress=null,$tags=array(), $type=null, 
            $details=null, $uri=null, $method="POST", $etag=null) {
            $params = array("taskPriority"=>"NONE");
        if ($name != null) $params["taskName"] = $name;
        if ($priority != null) $params["taskPriority"] = $priority;
        if ($status != null) $params["taskStatus"] = $status;
        if ($eta != null) $params["estimatedDuration"] = $eta;
        if ($ActivationTime != null) 
            $params["taskActivationTime"]= $ActivationTime;
        if ($ExpirationTime != null) 
            $params["taskExpirationTime"]= $ExpirationTime;
        if ($Addition != null) $params["taskAdditionTime"] = $Addition;
        if ($Modification != null) 
            $params["taskModificationTime"] = $Modification;
        if ($progress != null) $params["taskProgress"] = $progress;
        if ($processProgress != null) 
            $params["processProgress"] = $processProgress;
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

    /**
     * Method for adding a new task entry.
     */
    static function AddEntry($user, $name=null, $priority=null, $status=null, 
            $eta=null, $ActivationTime=null, $ExpirationTime=null, 
            $Addition=null, $Modification=null, $progress=null, 
            $processProgress=null, $tags=array(), $type=null, $details=null) {
        return self::TaskEntry($user, $name, $priority, $status, $eta, 
            $ActivationTime, $ExpirationTime, $Addition,
            $Modification, $progress, $processProgress,
            $tags, $type, $details, $user->getUri(), "POST", null);
    }

    /**
     * Method for updating an existing task entry.
     */
    static function EditEntry($user, $name=null, $priority=null, $status=null, 
            $eta=null, $ActivationTime=null, $ExpirationTime=null, 
            $Addition=null, $Modification=null, $Progress=null, 
            $ProcessProgress=null, $Tags=null, $Type=null, 
            $details=null, $Uri=null, $etag=null) {
        return self::TaskEntry($user, $name, $priority, $status, $eta, 
            $ActivationTime, $ExpirationTime, $Addition,
            $Modification, $Progress, $ProcessProgress,
            $Tags, $Type, $details, $Uri, "PUT", $etag);
    }

    /**
     * Method for adding a new account to the database, if it doesn't already
     * exist and it validates against the server correctly.
     * @todo {Grant Beasley|Andrew Hays} Update function to use new queries.
     *          This may require a bit of magic to not die if the user already
     *          exists in the database, but we still want to link it to another
     *          User.  Speaking of which, this will probably require more
     *          parameters for linking it to a {@link User} object.
     * @todo {Andrew Hays} Use a better return system instead of true|false|null
     *          and update all methods that call it.
     * @param string $name The name for the Account
     * @param string $password The password for the Account.
     * @param string $uri The uri for the Account.
     * @return boolean Whether or not the method was successful.
     */
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

    /**
     * Takes a task name and converts it to a pretty url for displaying in
     * the url bar of the client's browser.
     * @param string $name The name to put in the address bar.
     * @return string A safe string to display in the address bar.
     */
    public static function urlifyTaskName($name) {
        return trim(substr(preg_replace('/[^A-Z0-9]+/i','-',$name),0,18),'-');
    }
} Util::init();
?>
