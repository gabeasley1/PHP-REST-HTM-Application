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
 * @author Grant Beasley
 * @copyright LGPLv3
 * @version 1.3
 */

/**
 * This is used for all HTTP Requests.
 */
require_once 'HTTP/Request2.php';

/**
 * An enumeration of status codes whenever adding an Account to the database.
 */
class AccountEntry {
    const SUCCESS = 0;
    const ACCOUNT_EXISTS = 1;
    const CREATION_FAILED = 2;
    const ACCOUNT_NOT_FOUND = 3;
    const ACCOUNT_NOT_LINKED = 4;
    const NOT_AUTHORIZED = 5;
    const ACCOUNT_ALREADY_LINKED = 6;
}


/**
 * Class for manipulating a user object.
 * The user object deals with the PHP server's accounts, and not the remote
 * server's accounts.  This class mostly contains getters and setters for
 * manipulating a user object.  The password object is not stored, since it
 * is never used for anything but an authentication method.
 *
 * @author Andrew Hays
 */
class User {
    /**
     * The id attribute for an account object in the database (if it exists)
     * @var int
     */
    private $mId;

    /**
     * The email attribute for an account object.
     * @var string
     */
    private $mEmail;

    /**
     * Constructor for the <code>User</code> class.
     * 
     * @param string $email The email address for the user.
     * @param string $id The id for the user, if any (default null).
     */
    function __construct($email, $id=null) {
        $this->mId = $id;
        $this->mEmail = $email;
    }
    
    /**
     * Getter for the email attribute.
     * @return string The email address for the user.
     */
    public function getEmail() {
        return $this->mEmail;
    }

    /**
     * Getter for the id attribute.
     * @return int The database id for the user, if any.
     */
    public function getId() {
        return $this->mId;
    }

    /**
     * Setter for the email attribute.
     * @param string $email The email address to set for the User.
     */
    public function setEmail($email) {
        $this->mEmail = $email;
    }

    /**
     * Setter for the id attribute.
     * @param int $id The id to set for the User.
     */
    public function setId($id) {
        $this->mId = $id;
    }

    /**
     * Static function for retrieving a User based on an email address.
     * @static
     * @param string $email The email address to use for the retrieval.
     * @return User The user with the email address given.
     */
    public static function getUserByEmail($email) {
        $util = new Util();
        return $util->getUserByEmail($email);
    }

    /**
     * Static function for retrieving a User by the id in the database.
     * @static
     * @param int $id The id to use for the retrieval.
     * @return User The user with the id given.
     */
    public static function getUserById($id) {
        $util = new Util();
        return $util->getUserById($id);
    }

    /**
     * Static function for authenticating a User with a username and password
     * @static
     * @param string $email The email to use for the authentication.
     * @param string $password The password to use for the authentication.
     * @return array|bool True if the account authenticates, an array containing
     *          'success' and 'reason' keys otherwise.  Note that this method
     *          could also allow 'success' to be true if there's other 
     *          informatoin to pass back to the user.
     */
    public static function authenticateUser($email, $password) {
        $util = new Util();
        return $util->authenticateUser($email, $password);
    }

    /**
     * Static function for registering a new user with a username and password
     * @static
     * @param string $email The email to use for the registration
     * @param string $password The password to use for the registration
     * @param string $opt_password_verify Optional verification for the password
     *          to pass along.  Checks to make sure $password and 
     *          $opt_password_verify are the same thing.
     * @return array|bool True if the account registers properly, an array
     *          containing 'success' and 'reason' keys otherwise.  Note that
     *          this method could also allow 'success' to be true if there
     *          is other information to pass back.
     */
    public static function registerUser($email, $password, 
            $opt_password_verify=null) {
        $util = new Util();
        return $util->registerUser($email, $password, $opt_password_verify);
    }
}

/**
 * Class for manipulating an account.
 * The account class deals with the REST API's accounts, and not the local
 * server's accounts. This class mostly contains getters and setters for 
 * manipulating an account.
 *
 * @author Andrew Hays
 * @author Grant Beasley
 */
class Account {
    /**
     * The id attribute for an account object in the database (if it exists)
     * @var int
     */
    private $mId;

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
     * Constructor for the <code>Account</code> class.  Sets up a new account
     * (from the database or hardcoded).
     *
     * @param string $name The name for the account, this should NOT be encoded
     *          for urls, that will happen naturally when required.
     * @param string $password The password for the account.
     * @param string $uri The Uri that should be associated with the account
     *          for logging in.
     * @param int $id The id for the account from the database, if any.
     */
    function __construct($name, $password, $uri, $id = null) {
        $this->mId = $id;
        $this->mName = $name;
        $this->mPassword = $password;
        $this->mUri = $uri;
    }

    /**
     * Getter for the id attribute.
     * @return int The id attribute
     */
    public function getId() {
        return $this->mId;
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
     * Gets the account for a user given a username.
     * @static
     * @param string $name The name associated with the account.
     * @return Account The account for Daniel's database associated with the 
     *          given username.
     */
    public static function getAccountByName($name) {
        $util = new Util();
        return $util->getAccountByName($name);
    }

    /**
     * Gets the account for a given uri.
     * @static
     * @param string $uri The uri to use for looking up the Account.
     * @return Account the account with the given uri, or null if none.
     */
    public static function getAccountByUri($uri) {
        $util = new Util();
        return $util->getAccountByUri($uri);
    }

    /**
     * Gets the account for a given id in the database.
     * @static
     * @param string $id The id to use for looking up the Account.
     * @return Account The account with the given id, or null if none.
     */
    public static function getAccountById($uri) {
        $util = new Util();
        return $util->getAccountById($id);
    }

    /**
     * Adds a new account to the database.
     * @static
     * @param User $user The User object to use to link the account.
     * @param mixed $accounts A list of accounts to add.
     */
    public static function addAccounts($user, $accounts) {
        $util = new Util();
        $util->addAccounts($accounts, $user);
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
    private $mId;
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
        $this->mId = $this->getTaskNumber();
        $this->mTags = array();
    }

    public function setEtag($etag) {
        $this->mEtag = $etag;
    }
    
    public function setName($name) {
        $this->mName = $name;
    }

    public function setUri($uri) {
        $this->mUri = $uri;
        $this->mId = $this->getTaskNumber();
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

    public function getId() {
        return $this->mId;
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
     */
    public function getTags() {
        return $this->mTags;
    }

    /**
     * Function to get the task number associated with the account.  I don't
     * think there's any way around not using this function, yet.
     * @return int The task number, or null if none was found.
     */
    private function getTaskNumber() {
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
            $expirationDate = $this->mExpirationTime->format('D, M j, Y');
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
     * @param string The string to parse.
     * @return DateTime the DateTime associated with the account, or null if 
     *          no date could be parsed.
     */
    private static function parseDate($datestr) {
        $formats = array('Y#m#d\TH#i#s#uP', 'Y#m#d\TH#i#sP', 'Y#m#d', 'Y#n#d', 
            'Y#m#d H:i:s', 'Y#m#d G#i#s', 'Y#m#d H#i', 'Y#m#d G#i', 'Y#m#j', 
            'Y#n#j', 'Y#m#j H#i#s', 'Y#m#j G#i#s', 'Y#m#j H#i', 'Y#m#j G#i', 
            'm#d#Y', 'n#d#Y', 'm#d#Y H#i#s', 'm#d#Y G#i#s', 'm#d#Y H#i', 
            'm#d#Y G#i', 'm#j#Y', 'n#j#Y', 'm#j#Y H#i#s', 'm#j#Y G#i#s', 
            'm#j#Y H#i', 'm#j#Y G#i', 'Y#m#d\TH#i#s', 'Y#m#d\TG#i#s', 
            'Y#m#d\TH#i', 'Y#m#d\TG#i', 'Y#m#j\TH#i#s', 'Y#m#j\TG#i#s', 
            'Y#m#j\TH#i', 'Y#m#j\TG#i', 'm#d#Y\TH#i#s', 'm#d#Y\TG#i#s', 
            'm#d#Y\TH#i', 'm#d#Y\TG#i', 'm#j#Y\TH#i#s', 'm#j#Y\TG#i#s', 
            'm#j#Y\TH#i', 'm#j#Y\TG#i');
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
            if (!$copy) $tasknumber = $task->getId();
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
            <form id='task-edit-form' method='POST' action='/newentry.php'>
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
                <table class='date-table'>
                    <tr><td class='start-date'>
                    <table class='date-table'>
                        <tr class='date-row'>
                            <td class='date-item'>
                                <label for='task-start-date-edit'
                                       id='task-start-date-label'>
                                    Start Date
                                </label>
                            </td>
                            <td class='date-item'>
                                <label for='task-start-time-edit'
                                       id='task-start-time-label'>
                                    Time
                                </label>
                            </td>
                        </tr>
                        <tr class='date-row'>
                            <td class='date-input'>
                                <div class='input-wrapper'>
                                    <input type='text' id='task-start-date-edit'
                                           name='start-date' value='$startDate' 
                                           maxlength='10' />
                                    <label class='date-icon'
                                           for='task-start-date-edit'
                                           id='task-start-date-icon'></label>
                                </div>
                                <span class='divider'>at</span>
                            </td>
                            <td class='time-input'>
                                <div class='input-wrapper'>
                                    <input type='text' id='task-start-time-edit'
                                           name='start-time' value='$startTime'
                                           />
                                    <label class='time-icon'
                                           for='task-start-time-edit'
                                           id='task-start-time-icon'></label>
                                </div>
                            </td>
                        </tr>
                        <tr class='date-row'>
                            <td colspan='2'>
                                <div id='start-date-widget'></div>
                            </td>
                        </tr>
                    </table></td><td class='end-date'>
                    <table class='date-table'>
                        <tr class='date-row'>
                            <td class='date-item'>
                                <label for='task-expiration-date-edit'
                                       id='task-expiration-date-label'>
                                    End Date
                                </label>
                            </td>
                            <td class='date-item'>
                                <label for='task-expiration-time-edit'
                                       id='task-expiration-time-label'>
                                    Time
                                </label>
                            </td>
                        </tr>
                        <tr class='date-row'>
                            <td class='date-input'>
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
                            </td>
                            <td class='time-input'>
                                <div class='input-wrapper'>
                                    <input type='text' 
                                           id='task-expiration-time-edit'
                                           name='expiration-time' 
                                           value='$expirationTime' />
                                    <label class='time-icon'
                                           for='task-expiration-time-edit'
                                           id='task-end-time-icon'></label>
                                </div>
                            </td>
                        </tr>
                        <tr class='date-row'>
                            <td colspan='2'>
                                <div id='end-date-widget'></div>
                            </td>
                        </tr>
                    </table></td>
                    </tr>
                </table>
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
 * @author Andrew Hays
 * @author Grant Beasley
 */
class Util {
    /**
     * Static variable for the MySql class.
     * @var Mysqli
     */
    private $mysqli = null;

    /**
     * Static variable used to store the user currently logged in, if any.
     * This value is pulled from $_SESSION['user'].  If the value doesn't
     * exist, a null value is placed there instead, along with the value for
     * $mysqli
     * @var User
     */
    private $mUser = null;

    /**
     * Static initializer for this class.  Has to be explicitly called since
     * PHP doesn't support constructors for static classes.
     */
    public function __construct() {
        if (!isset($_SESSION)) session_start();
        if (isset($_SESSION['user']) and is_a($_SESSION['user'], 'User')) {
            $this->mUser = $_SESSION['user'];
        } else {
            $this->mUser = null;
        }
        $this->mysqli = new mysqli("localhost", "root", "", "htm_database");
    }

    /**
     * Handles methods where a user needs to be logged in before the query
     * can be completed.
     */
    public function handleLogin() {
        if ($this->mUser == null) {
            $_SESSION['flash'] = "You must be logged in to use that feature.";
            header("Location: /login/");
        }
    }

    /**
     * Returns the currently logged in user, if any.
     * @return User The user that is currently logged in.  Or null if none.
     */
    public function getLoggedInUser() {
        return $this->mUser;
    }

    /**
     * Utility function for escaping a string for Daniel's server.  Encodes
     * the string using urlencode, and then replaces all '+' with '%20' so that
     * Daniel's server can handle it.
     *
     * @param string $string The string to encode.
     * @return string The encoded string.
     */
    public static function escape($string) {
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
    public function retrieveMessage(Account $account, $uri = null) {
        if ($uri == null) $uri = $account->getUri();
        $user = $account->getName();
        $password = $account->getPassword();
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
    public function getXmlResponse($body, $namespace="ns") {
        $xml = new SimpleXMLElement($body);
        $xml->registerXPathNamespace($namespace, 
                        "http://danieloscarschulte.de/cs/ns/2011/tm");
        return $xml;      
    }

    /**
     * Returns all accounts in the database
     * @param mixed $all Whether or not to return all accounts.  
     *          If it's an array, only for the selected <code>Users</code>, if
     *          its a User, only do it for the given user.  If false, only use
     *          the logged in account.
     * @param bool $associative Whether or not to return them as an associative
     *          array of arrays. (i.e. array("Bob"=>array(Task, Task, Task)))
     * @return array An array of {@link Account} objects.
     */
    public function getAccounts($all = false, $associative=false) {
        $users = array();
        if (is_bool($all)) {
            if ($all) {
                $query = "SELECT id, email FROM users";
                if ($result = $this->$mysql->query($query)) {
                    while ($row = $result->fetch_assoc()) {
                        $users[] = new User($row['email'], $row['id']);
                    }
                }
            } else {
                $this->handleLogin();
                $users[] = $this->getLoggedInUser();
            }
        } else if (is_array($all)) {
            foreach($all as $user) {
                if (is_string($user)) {
                    $query = "SELECT id, email FROM users ".
                             "WHERE email='$user'";
                    if ($result = $this->mysql->query($query)) {
                        $row = $result->fetch_assoc();
                        $users[] = new User($row['email'], $row['id']);
                    }
                }  else if (is_a($user, 'User')) {
                    $users[] = $user;
                }
            }
        } else if (is_a($all, 'User')) {
            $users[] = $all;
        } else if (is_string($all)) {
            $query = "SELECT id, email FROM users ".
                     "WHERE email='$user'";
            if ($result = $this->mysql->query($query)) {
                $row = $result->fetch_assoc();
                $users[] = new User($row['email'], $row['id']);
            }
        }
           
        $results = array();
        foreach ($users as $user) {
            $query = "SELECT a.id, a.link, a.username, a.passphrase ".
                "FROM user_accounts ua JOIN accounts a ON ".
                "(ua.accountID = a.id) WHERE ua.userID='{$user->getId()}'";
            if ($result = $this->mysqli->query($query)) {
                $pointer = null;
                if ($associative) {
                    $results[$user->getName()] = array();
                    $pointer = &$results[$user->getName()];
                } else {
                    $pointer = &$results;
                }

                while ($row = $result->fetch_assoc()) {
                    $pointer[] = new Account($row['username'],
                        $row['passphrase'], $row['link'], $row['id']);
                }
            }
        }
        return $results;
    }

    /**
     * Returns a list of tasks for a given account.
     * @param Account|array $accounts The account or accounts to look up 
     *          tasks for.
     * @param boolean $fetch_description Whether or not to continue to do 
     *          requests to fetch descriptions for the tasks as well, or if
     *          names and links will be fine.
     * @return array An array of {@link Task} objects.  Note that if you
     */
    public function getTasksForAccounts($accounts, $fetch_description = true,
                                        $associative = true) {
        if (is_array($accounts)) {
            $results = array();
            foreach ($accounts as $key=>$account) {
                if ($account == null) continue;
                $list = getTasksForAccounts($account, $fetch_description, 
                    $associative);                
                if ($associative) {
                    $results[$key] = $list;
                } else {
                    foreach ($list as $l) {
                        $results[] = $l;
                    }
                }
            }
            return $results;
        } else {
            try {
                if ($accounts == null) return array();
                $response = $this->retrieveMessage($accounts);
                $tasks = array();
                
                if ($response["status"] == 200) {
                    $xml = $this->getXmlResponse($response["body"]);
                    $taskNodes = $xml->xpath('//ns:link[@rel="'.
                                             'http://danieloscarschulte.de/cs'.
                                             '/tm/taskDescription"]');
                    foreach ($taskNodes as $taskNode) {
                        if ($fetch_description) {
                            $task = $this->retrieveTaskDescription($accounts, 
                                "".$taskNode['href']);
                            if ($task != null) $tasks[] = $task;
                        } else {
                            $tasks[] = new Task("".$taskNode, 
                                "".$taskNode['href']);
                        }
                    }
                }
                return $tasks;
            } catch(HttpInvalidParamException $ex) {
                //die("<b>Could not connect to the network.</b>");
                return array();
            }
        }
    }

    /**
     * Quick function to get an associative array of all tasks for a specific
     * user.  See {@link Util::getAccounts()} and 
     *          {@link Util::getTasksForAccounts()} as this method just calls
     *          both of those methods and returns the results.
     */
    public function getTasksForUsers($users, $fetch_descriptions) {
        return getTasksForAccounts(getAccounts($users, true), 
            $fetch_descriptions, true);
    }

    private function getXByY($table, $key, $value) {
        $query = "SELECT * FROM $table WHERE $key='$value'";
        if ($result = $this->mysqli->query($query)) {
            return $row = $result->fetch_assoc();
        }
        return null;
    }

    /**
     * Retrieves a user by a given email address.
     */
    public function getUserByEmail($email) {
        if ($row = $this->getXByY('users', 'email', $email)) {
            return new User($row['email'], $row['id']);
        }
    }

    /**
     * Retrieves a user by a given id.
     */
    public function getUserById($id) {
        if ($row = $this->getXByY('users', 'id', $id)) {
            return new User($row['email'], $row['id']);
        }
    }

    /**
     * Retrieve an account by a given username.
     */
    public function getAccountByName($name) {
        if ($row = $this->getXByY('accounts', 'username', $name)) {
            return new Account($row['username'], $row['passphrase'],
                $row['link'], $row['id']);
        }
    }

    /**
     * Retrieve an account by a given Uri.
     */
    public function getAccountByUri($uri) {
        if ($row = $this->getXByY('accounts', 'link', $uri)) {
            return new Account($row['username'], $row['passphrase'],
                $row['link'], $row['id']);
        }
    }

    /**
     * Retrieve an account by a given id.
     */
    public function getAccountById($id) {
        if ($row = $this->getXByY('accounts', 'id', $id)) {
            return new Account($row['username'], $row['passphrase'],
                $row['link'], $row['id']);
        }
    }

    /**
     * Authenticates a user by checking to see if the username and password
     * match the database's.
     */
    public function authenticateUser($email, $password) {
        $password = sha1($password);
        $query = "SELECT COUNT(*) as count FROM users ".
            "WHERE email='$email' AND passphrase='$password'";
        if ($result = $this->mysqli->query($query)) {
            $row = $result->fetch_assoc();
            return $row['count'] != 0;
        }
        return false;
    }

    /**
     * Register a new user, if the user doesn't already exist in the database.
     */
    public function registerUser($email, $password, $opt_password_again=null) {
        if ($password != $opt_password_again and $opt_password_again != null) {
            return false;
        }
        $query = "SELECT COUNT(*) as count FROM users WHERE email='$email';";
        if ($result = $this->mysqli->query($query)) {
            $row = $result->fetch_assoc();
            if ($row['count'] != 0) return false;
        }
        $password = sha1($password);
        $query = "INSERT INTO users(email, passphrase) ".
            "VALUES('$email', '$password');";
        return $this->mysqli->query($query);
    }

    /**
     * Retrieves a task description for a given account and task uri
     * @param Account $account The account to use for authentication purposes.
     * @param string $taskUri The Uri to look in for the account.
     * @return Task A task with descriptions loaded.
     */
    public function retrieveTaskDescription(Account $account, $taskUri) {
        try {
            $response = $this->retrieveMessage($account, $taskUri);
            $task = null;
            // TODO {Andrew Hays|Grant Beasley} handle other response codes 
            // besides 200
            if ($response["status"] == 200) {
                $xml = $this->getXmlResponse($response["body"]);
                $task = new Task(null, $taskUri);
                $task->setEtag(htmlentities($response["etag"]));
                $task->setName($this->firstValue($xml,'//ns:taskName'));
                $task->setType($this->firstValue($xml,'//ns:taskType'));
                $task->setDetail(html_entity_decode(html_entity_decode(
                    $this->firstValue($xml,'//ns:taskDetail'))));
                $task->setStatus($this->firstValue($xml,'//ns:taskStatus'));
                $task->setPriority($this->firstValue($xml,'//ns:taskPriority'));
                $task->setEstimatedCompletionTime($this->firstValue(
                    $xml,'//ns:estimatedDuration'));
                $task->setActivationTime($this->firstValue(
                    $xml,'//ns:taskActivationTime'));
                $task->setExpirationTime($this->firstValue(
                    $xml, '//ns:taskExpirationTime'));
                $task->setAdditionTime($this->firstValue(
                    $xml,'//ns:taskAdditionTime'));
                $task->setModificationTime($this->firstValue(
                    $xml,'//ns:taskModificationTime'));
                $task->setProgress($this->firstValue(
                    $xml,'//ns:taskProgress'));
                $task->setProcessProgress($this->firstValue(
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
    private function firstValue($xml, $xpath) {
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
    private function GenerateXmlDoc($params) {
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
    private function TaskEntry($user, $name=null, $priority=null, 
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
            $tags = array_unique(array_map("trim", $tags));
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
        $body = $this->GenerateXmlDoc($params);
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
    public function AddEntry($user, $name=null, $priority=null, $status=null, 
            $eta=null, $ActivationTime=null, $ExpirationTime=null, 
            $Addition=null, $Modification=null, $progress=null, 
            $processProgress=null, $tags=array(), $type=null, $details=null) {
        return $this->TaskEntry($user, $name, $priority, $status, $eta, 
            $ActivationTime, $ExpirationTime, $Addition,
            $Modification, $progress, $processProgress,
            $tags, $type, $details, $user->getUri(), "POST", null);
    }

    /**
     * Method for updating an existing task entry.
     */
    public function EditEntry($user, $name=null, $priority=null, $status=null, 
            $eta=null, $ActivationTime=null, $ExpirationTime=null, 
            $Addition=null, $Modification=null, $Progress=null, 
            $ProcessProgress=null, $Tags=null, $Type=null, 
            $details=null, $Uri=null, $etag=null) {
        return $this->TaskEntry($user, $name, $priority, $status, $eta, 
            $ActivationTime, $ExpirationTime, $Addition,
            $Modification, $Progress, $ProcessProgress,
            $Tags, $Type, $details, $Uri, "PUT", $etag);
    }

    /**
     * Method for adding a new account to the database, if it doesn't already
     * exist and it validates against the server correctly.
     * @param mixed $accounts The Account to add, or an array of them.
     * @param User $user The user to add for, or null to select the logged in
     *          User.
     * @return mixed An integer representing the code if only one account was
     *          added, or an array of codes with the key being the 
     *          account name and the value being the result code.
     */
    public function addAccounts($accounts, User $user = null) {
        if (is_array($accounts)) {
            $results = array();
            foreach($accounts as $account) {
                $results[$account->getName()] = $this->addAccounts($account,
                    $user);
            }
            return $results;
        }
        if (!$user) {
            $this->handleLogin();
            $user = $this->getLoggedInUser();
        }
        $query = "SELECT id FROM accounts ".
            "WHERE link='{$accounts->getUri()}' AND ".
            "username='{$accounts->getName()}'";
        $id = null;
        $result_code = null;
        if ($result = $this->mysqli->query($query)) {
            if ($result->num_rows != 0) {
                $id = $result->fetch_assoc();
                $id = $id["id"];
                $result_code = AccountEntry::ACCOUNT_EXISTS;
            }
        }
        $result = $this->retrieveMessage($accounts);

        if ((int) ($result["status"]/100) == 2 and 
                $result_code != AccountEntry::ACCOUNT_EXISTS) {
            $query = "INSERT INTO accounts(username, passphrase, link) ".
                "VALUES('{$accounts->getName()}', ".
                "'{$accounts->getPassword()}', '{$accounts->getUri()}');";
            $result = $this->mysqli->query($query);
            if ($result === TRUE) {
                $id = $this->mysqli->insert_id;
                $result_code = AccountEntry::SUCCESS;
            } else {
                $result_code = AccountEntry::CREATION_FAILED;
            }
        } else if ($result["status"] == 401) {
            $result_code = AccountEntry::NOT_AUTHORIZED;
        } else if ((int)($result["status"]/100)!=2){
            $result_code = AccountEntry::ACCOUNT_NOT_FOUND;
        }
        
        if (($result_code == AccountEntry::SUCCESS || 
                $result_code == AccountEntry::ACCOUNT_EXISTS) && $id != null) {
            $query = "SELECT COUNT(*) as count FROM user_accounts WHERE ".
                "userID='{$user->getId()}' AND accountID='$id'";
            if ($result = $this->mysqli->query($query)) {
                $row = $result->fetch_assoc();
                if (((int) $row['count']) != 0) {
                    return AccountEntry::ACCOUNT_ALREADY_LINKED;
                }
            }
            $query = "INSERT INTO user_accounts(userID, accountID) ".
                "VALUES('{$user->getId()}', '$id')";
            $result = $this->mysqli->query($query);
            if ($result === TRUE) {
                return $result_code;
            } else {
                return AccountEntry::ACCOUNT_NOT_LINKED;
            }
        }
    
        return $result_code;
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

    /**
     * Returns a URI from a username.  Note that the user must first exist
     * in the database in order to return its URI.
     * @param string $username The name to look up.
     * @return string The uri that the username corresponds to, or null if it
     *          doesn't exist.
     */
    public function getUriFromUsername($username) {
        $query = "SELECT a.link FROM accounts a WHERE a.username='$name'";
        if ($result = $this->mysql->query($query)) {
            $row = $result->fetch_assoc();
            return $row['link'];
        } else {
            return null;
        }
    }

    /**
     * Returns a Task from a Task Number.
     * @param int $taskNumber The number to use.
     * @param Account
     * @return Task The task with the given number.
     */
    public function getTaskById($taskNumber, Account $account) {
        return $this->retrieveTaskDescription($account, 
            Task::uriFromTaskNumber($taskNumber));
    }
}

/**
 * Class for manipulating wizard pages.  Mostly useless.
 * @author Andrew hays
 */
class Wizard {
    public static function getPage($page) {
        $util = new Util();
        switch($page) {
        case 1:
    ?>
    <div class='page-1'>
    <h1>Welcome!</h1>
    <p>In this tutorial, we will walk you through step-by-step how to work with this client for the Human Task Management application.  By the end of it, you should be set up well enough to have an account set up and be able to view all of the tasks for that account.</p>
    <p>Please click the "next" button in order to keep going.</p>
    <a href='/wizard/page/2' rel='next' class='next-link'>Next</a>
    </div>
    <?
            break;

        case 2:
    ?>
    <div class='page-2'>
    <h1>Accounts</h1>
    <p>In order to do anything with this application, you will need to have at least one account set up on the REST server and attach it to your account on this server.  Use the following form to set this up now.</p>
    <div id='flash'>
    <? if (isset($_SESSION['flash'])) echo $_SESSION['flash']; ?>
    </div>
    <form action='new_account_wiz.php' method='POST'>
        <table>
            <tr>
                <td class='label'>
                    <label for='username' id='username-label'>Username</label>
                </td>
                <td>
    <?
    $name = '';
    if (isset($_SESSION['username'])) {
        $name = $_SESSION['username'];
    }
    ?>
                    <input type='text' name='username' id='username'
                           value='<?=$name?>' />
                </td>
            </tr>
            <tr>
                <td class='label'>
                    <label for='uri' id='uri-label'>Link</label>
                </td>
                <td>
    <?
    $uri = '';
    if (isset($_SESSION['uri'])) {
        $name = $_SESSION['uri'];
    }
    ?>
                    <input type='text' name='uri' id='uri'
                           value='<?=$uri?>' />
                </td>
            </tr>
            <tr>
                <td class='label'>
                    <label for='password' id='password-label'>Password</label>
                </td>
                <td>
                    <input type='password' name='password' id='password' />
                </td>
            </tr>
        </table>
    <p>Whenever the form is complete, just hit the next button and the application will sign the account in for you.</p>
    <a href='/wizard/page/1' rel='prev' class='prev-link'>Prev</a>
    <input type='submit' href='/wizard/page/3' rel='next' class='next-link'
           value='Next' />
    </form>
    </div>
    <?
            break;
        case 3:
    ?>
    <div class='page-3'>
    <h1>Great Job!</h1>
    <?
            $name = $util->getAccounts();
            if ($name == null or count($name) == 0) {
                $name = 'Johnny';
            } else {
                $name = $name[0]->getName();
            }
    ?>
    <p>Now every time you log in to the application, you will see <?=$name?>'s tasks, along with anyone else's tasks that you add to your account on this server.  Have a group account with shared tasks?  No problem!  Just have everyone add the same account and any tasks that you add, all of your partners will be able to see as well.  You can also see any tasks that they add for the account.</p>
    <p>Click on the "finish" button to go to your homescreen now and start managing all of our tasks!</p>
    <a href='/wizard/page/2' rel='prev' class='prev-link'>Prev</a>
    <a href='/' rel='last' class='next-link'>Finish</a>
    </div>
    <?
            break;
        };
    }

    public static function hasPrev($page) {
        return $page > 1;
    }

    public static function hasNext($page) {
        return $page < 3;
    }
}
?>
