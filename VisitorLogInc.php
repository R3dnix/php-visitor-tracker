<?php
namespace VeryIdiot;

class BrowserDetection
{

    /**#@+
     * Constant for the name of the Web browser.
     */
    const BROWSER_ANDROID = 'Android';
    const BROWSER_BINGBOT = 'Bingbot';
    const BROWSER_BLACKBERRY = 'BlackBerry';
    const BROWSER_CHROME = 'Chrome';
    const BROWSER_EDGE = 'Edge';
    const BROWSER_FIREBIRD = 'Firebird';
    const BROWSER_FIREFOX = 'Firefox';
    const BROWSER_GOOGLEBOT = 'Googlebot';
    const BROWSER_ICAB = 'iCab';
    const BROWSER_ICECAT = 'GNU IceCat';
    const BROWSER_ICEWEASEL = 'GNU IceWeasel';
    const BROWSER_IE = 'Internet Explorer';
    const BROWSER_IE_MOBILE = 'Internet Explorer Mobile';
    const BROWSER_KONQUEROR = 'Konqueror';
    const BROWSER_LYNX = 'Lynx';
    const BROWSER_MOZILLA = 'Mozilla';
    const BROWSER_MSNBOT = 'MSNBot';
    const BROWSER_MSNTV = 'MSN TV';
    const BROWSER_NETSCAPE = 'Netscape';
    const BROWSER_NOKIA = 'Nokia Browser';
    const BROWSER_OPERA = 'Opera';
    const BROWSER_OPERA_MINI = 'Opera Mini';
    const BROWSER_OPERA_MOBILE = 'Opera Mobile';
    const BROWSER_PHOENIX = 'Phoenix';
    const BROWSER_SAFARI = 'Safari';
    const BROWSER_SAMSUNG = 'Samsung Internet';
    const BROWSER_SLURP = 'Yahoo! Slurp';
    const BROWSER_TABLET_OS = 'BlackBerry Tablet OS';
    const BROWSER_UC = 'UC Browser';
    const BROWSER_UNKNOWN = 'unknown';
    const BROWSER_W3CVALIDATOR = 'W3C Validator';
    const BROWSER_YAHOO_MM = 'Yahoo! Multimedia';
    /**#@-*/

    /**#@+
     * Constant for the name of the platform on which the Web browser runs.
     */
    const PLATFORM_ANDROID = 'Android';
    const PLATFORM_BLACKBERRY = 'BlackBerry';
    const PLATFORM_FREEBSD = 'FreeBSD';
    const PLATFORM_IOS = 'iOS';
    const PLATFORM_LINUX = 'Linux';
    const PLATFORM_MACINTOSH = 'Macintosh';
    const PLATFORM_NETBSD = 'NetBSD';
    const PLATFORM_NOKIA = 'Nokia';
    const PLATFORM_OPENBSD = 'OpenBSD';
    const PLATFORM_OPENSOLARIS = 'OpenSolaris';
    const PLATFORM_SYMBIAN = 'Symbian';
    const PLATFORM_UNKNOWN = 'unknown';
    const PLATFORM_VERSION_UNKNOWN = 'unknown';
    const PLATFORM_WINDOWS = 'Windows';
    const PLATFORM_WINDOWS_CE = 'Windows CE';
    const PLATFORM_WINDOWS_PHONE = 'Windows Phone';
    /**#@-*/

    /**
     * Version unknown constant.
     */
    const VERSION_UNKNOWN = 'unknown';


    /**
     * @var string
     * @access private
     */
    private $_agent = '';

    /**
     * @var string
     * @access private
     */
    private $_browserName = '';

    /**
     * @var string
     * @access private
     */
    private $_compatibilityViewName = '';

    /**
     * @var string
     * @access private
     */
    private $_compatibilityViewVer = '';

    /**
     * @var array
     * @access private
     */
    private $_customBrowserDetection = array();

    /**
     * @var array
     * @access private
     */
    private $_customPlatformDetection = array();

    /**
     * @var boolean
     * @access private
     */
    private $_is64bit = false;

    /**
     * @var boolean
     * @access private
     */
    private $_isMobile = false;

    /**
     * @var boolean
     * @access private
     */
    private $_isRobot = false;

    /**
     * @var string
     * @access private
     */
    private $_platform = '';

    /**
     * @var string
     * @access private
     */
    private $_platformVersion = '';

    /**
     * @var string
     * @access private
     */
    private $_version = '';


    //--- MAGIC METHODS ------------------------------------------------------------------------------------------------


    /**
     * BrowserDetection class constructor.
     * @param string $useragent (optional) The user agent to work with. Leave empty for the current user agent
     * (contained in $_SERVER['HTTP_USER_AGENT']).
     */
    public function __construct($useragent = '')
    {
        $this->setUserAgent($useragent);
    }

    /**
     * Determine how the class will react when it is treated like a string.
     * @return string Returns an HTML formatted string with a summary of the browser informations.
     */
    public function __toString()
    {
        $result = '';

        $values = array();
        $values[] = array('label' => 'User agent', 'value' => $this->getUserAgent());
        $values[] = array('label' => 'Browser name', 'value' => $this->getName());
        $values[] = array('label' => 'Browser version', 'value' => $this->getVersion());
        $values[] = array('label' => 'Platform family', 'value' => $this->getPlatform());
        $values[] = array('label' => 'Platform version', 'value' => $this->getPlatformVersion(true));
        $values[] = array('label' => 'Platform version name', 'value' => $this->getPlatformVersion());
        $values[] = array('label' => 'Platform is 64-bit', 'value' => $this->is64bitPlatform() ? 'true' : 'false');
        $values[] = array('label' => 'Is mobile', 'value' => $this->isMobile() ? 'true' : 'false');
        $values[] = array('label' => 'Is robot', 'value' => $this->isRobot() ? 'true' : 'false');
        $values[] = array('label' => 'IE is in compatibility view', 'value' => $this->isInIECompatibilityView() ? 'true' : 'false');
        $values[] = array('label' => 'Emulated IE version', 'value' => $this->isInIECompatibilityView() ? $this->getIECompatibilityView() : 'Not applicable');
        $values[] = array('label' => 'Is Chrome Frame', 'value' => $this->isChromeFrame() ? 'true' : 'false');

        foreach ($values as $currVal) {
            $result .= '<strong>' . htmlspecialchars($currVal['label'], ENT_NOQUOTES) . ':</strong> ' . $currVal['value'] . '<br />' . PHP_EOL;
        }

        return $result;
    }


    //--- PUBLIC MEMBERS -----------------------------------------------------------------------------------------------


    /**
     * Dynamically add support for a new Web browser.
     * @param string $browserName The Web browser name (used for display).
     * @param mixed $uaNameToLookFor (optional) The string (or array of strings) representing the browser name to find
     * in the user agent. If omitted, $browserName will be used.
     * @param boolean $isMobile (optional) Determines if the browser is from a mobile device.
     * @param boolean $isRobot (optional) Determines if the browser is a robot or not.
     * @param string $separator (optional) The separator string used to split the browser name and the version number in
     * the user agent.
     * @param boolean $uaNameFindWords (optional) Determines if the browser name to find should match a word instead of
     * a part of a word. For example "Bar" would not be found in "FooBar" when true but would be found in "Foo Bar".
     * When set to false, the browser name can be found anywhere in the user agent string.
     * @see removeCustomBrowserDetection()
     * @return boolean Returns true if the custom rule has been added, false otherwise.
     */
    public function addCustomBrowserDetection($browserName, $uaNameToLookFor = '', $isMobile = false, $isRobot = false, $separator = '/', $uaNameFindWords = true)
    {
        if ($browserName == '') {
            return false;
        }
        if (array_key_exists($browserName, $this->_customBrowserDetection)) {
            unset($this->_customBrowserDetection[$browserName]);
        }
        if ($uaNameToLookFor == '') {
            $uaNameToLookFor = $browserName;
        }
        $this->_customBrowserDetection[$browserName] = array('uaNameToLookFor' => $uaNameToLookFor, 'isMobile' => $isMobile == true, 'isRobot' => $isRobot == true,
                                                             'separator' => $separator, 'uaNameFindWords' => $uaNameFindWords == true);
        return true;
    }

    /**
     * Dynamically add support for a new platform.
     * @param string $platformName The platform name (used for display).
     * @param mixed $platformNameToLookFor (optional) The string (or array of strings) representing the platform name to
     * find in the user agent. If omitted, $platformName will be used.
     * @param boolean $isMobile (optional) Determines if the platform is from a mobile device.
     * @see removeCustomPlatformDetection()
     * @return boolean Returns true if the custom rule has been added, false otherwise.
     */
    public function addCustomPlatformDetection($platformName, $platformNameToLookFor = '', $isMobile = false)
    {
        if ($platformName == '') {
            return false;
        }
        if (array_key_exists($platformName, $this->_customPlatformDetection)) {
            unset($this->_customPlatformDetection[$platformName]);
        }
        if ($platformNameToLookFor == '') {
            $platformNameToLookFor = $platformName;
        }
        $this->_customPlatformDetection[$platformName] = array('platformNameToLookFor' => $platformNameToLookFor, 'isMobile' => $isMobile == true);
        return true;
    }

    /**
     * Compare two version number strings.
     * @param string $sourceVer The source version number.
     * @param string $compareVer The version number to compare with the source version number.
     * @return int Returns -1 if $sourceVer < $compareVer, 0 if $sourceVer == $compareVer or 1 if $sourceVer >
     * $compareVer.
     */
    public function compareVersions($sourceVer, $compareVer)
    {
        $sourceVer = explode('.', $sourceVer);
        foreach ($sourceVer as $k => $v) {
            $sourceVer[$k] = $this->parseInt($v);
        }

        $compareVer = explode('.', $compareVer);
        foreach ($compareVer as $k => $v) {
            $compareVer[$k] = $this->parseInt($v);
        }

        if (count($sourceVer) != count($compareVer)) {
            if (count($sourceVer) > count($compareVer)) {
                for ($i = count($compareVer); $i < count($sourceVer); $i++) {
                    $compareVer[$i] = 0;
                }
            } else {
                for ($i = count($sourceVer); $i < count($compareVer); $i++) {
                    $sourceVer[$i] = 0;
                }
            }
        }

        foreach ($sourceVer as $i => $srcVerPart) {
            if ($srcVerPart > $compareVer[$i]) {
                return 1;
            } else {
                if ($srcVerPart < $compareVer[$i]) {
                    return -1;
                }
            }
        }

        return 0;
    }

    /**
     * Get the name of the browser. All of the return values are class constants. You can compare them like this:
     * $myBrowserInstance->getName() == BrowserDetection::BROWSER_FIREFOX.
     * @return string Returns the name of the browser.
     */
    public function getName()
    {
        return $this->_browserName;
    }

    /**
     * Get the name and version of the browser emulated in the compatibility view mode (if any). Since Internet
     * Explorer 8, IE can be put in compatibility mode to make websites that were created for older browsers, especially
     * IE 6 and 7, look better in IE 8+ which renders web pages closer to the standards and thus differently from those
     * older versions of IE.
     * @param boolean $asArray (optional) Determines if the return value must be an array (true) or a string (false).
     * @return mixed If a string was requested, the function returns the name and version of the browser emulated in
     * the compatibility view mode or an empty string if the browser is not in compatibility view mode. If an array was
     * requested, an array with the keys 'browser' and 'version' is returned.
     */
    public function getIECompatibilityView($asArray = false)
    {
        if ($asArray) {
            return array('browser' => $this->_compatibilityViewName, 'version' => $this->_compatibilityViewVer);
        } else {
            return trim($this->_compatibilityViewName . ' ' . $this->_compatibilityViewVer);
        }
    }

    /**
     * Return the BrowserDetection class version.
     * @return string Returns the version as a sting with the #.#.# format.
     */
    public function getLibVersion()
    {
        return '2.9.1';
    }

    /**
     * Get the name of the platform family on which the browser is run on (such as Windows, Apple, etc.). All of
     * the return values are class constants. You can compare them like this:
     * $myBrowserInstance->getPlatform() == BrowserDetection::PLATFORM_ANDROID.
     * @return string Returns the name of the platform or BrowserDetection::PLATFORM_UNKNOWN if unknown.
     */
    public function getPlatform()
    {
        return $this->_platform;
    }

    /**
     * Get the platform version on which the browser is run on. It can be returned as a string number like 'NT 6.3' or
     * as a name like 'Windows 8.1'. When returning version string numbers for Windows NT OS families the number is
     * prefixed by 'NT ' to differentiate from older Windows 3.x & 9x release. At the moment only the Windows and
     * Android operating systems are supported.
     * @param boolean $returnVersionNumbers (optional) Determines if the return value must be versions numbers as a
     * string (true) or the version name (false).
     * @param boolean $returnServerFlavor (optional) Since some Windows NT versions have the same values, this flag
     * determines if the Server flavor is returned or not. For instance Windows 8.1 and Windows Server 2012 R2 both use
     * version 6.3. This parameter is only useful when testing for Windows.
     * @return string Returns the version name/version numbers of the platform or the constant PLATFORM_VERSION_UNKNOWN
     * if unknown.
     */
    public function getPlatformVersion($returnVersionNumbers = false, $returnServerFlavor = false)
    {
        if ($this->_platformVersion == self::PLATFORM_VERSION_UNKNOWN || $this->_platformVersion == '') {
            return self::PLATFORM_VERSION_UNKNOWN;
        }

        if ($returnVersionNumbers) {
            return $this->_platformVersion;
        } else {
            switch ($this->getPlatform()) {
                case self::PLATFORM_WINDOWS:
                    if (substr($this->_platformVersion, 0, 3) == 'NT ') {
                        return $this->windowsNTVerToStr(substr($this->_platformVersion, 3), $returnServerFlavor);
                    } else {
                        return $this->windowsVerToStr($this->_platformVersion);
                    }
                    break;

                case self::PLATFORM_MACINTOSH:
                    return $this->macVerToStr($this->_platformVersion);
                    break;

                case self::PLATFORM_ANDROID:
                    return $this->androidVerToStr($this->_platformVersion);
                    break;

                case self::PLATFORM_IOS:
                    return $this->iOSVerToStr($this->_platformVersion);
                    break;

                default: return self::PLATFORM_VERSION_UNKNOWN;
            }
        }
    }

    /**
     * Get the user agent value used by the class to determine the browser details.
     * @return string The user agent string.
     */
    public function getUserAgent()
    {
        return $this->_agent;
    }

    /**
     * Get the version of the browser.
     * @return string Returns the version of the browser or BrowserDetection::VERSION_UNKNOWN if unknown.
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Determine if the browser is executed from a 64-bit platform. Keep in mind that not all platforms/browsers report
     * this and the result may not always be accurate.
     * @return boolean Returns true if the browser is executed from a 64-bit platform.
     */
    public function is64bitPlatform()
    {
        return $this->_is64bit;
    }

    /**
     * Determine if the browser runs Google Chrome Frame (it's a plug-in designed for Internet Explorer 6+ based on the
     * open-source Chromium project - it's like a Chrome browser within IE).
     * @return boolean Returns true if the browser is using Google Chrome Frame, false otherwise.
     */
    public function isChromeFrame()
    {
        return $this->containString($this->_agent, 'chromeframe');
    }

    /**
     * Determine if the browser is in compatibility view or not. Since Internet Explorer 8, IE can be put in
     * compatibility mode to make websites that were created for older browsers, especially IE 6 and 7, look better in
     * IE 8+ which renders web pages closer to the standards and thus differently from those older versions of IE.
     * @return boolean Returns true if the browser is in compatibility view, false otherwise.
     */
    public function isInIECompatibilityView()
    {
        return ($this->_compatibilityViewName != '') || ($this->_compatibilityViewVer != '');
    }

    /**
     * Determine if the browser is from a mobile device or not.
     * @return boolean Returns true if the browser is from a mobile device, false otherwise.
     */
    public function isMobile()
    {
        return $this->_isMobile;
    }

    /**
     * Determine if the browser is a robot (Googlebot, Bingbot, Yahoo! Slurp...) or not.
     * @return boolean Returns true if the browser is a robot, false otherwise.
     */
    public function isRobot()
    {
        return $this->_isRobot;
    }

    /**
     * Remove support for a previously added Web browser.
     * @param string $browserName The Web browser name as used when added.
     * @see addCustomBrowserDetection()
     * @return boolean Returns true if the custom rule has been found and removed, false otherwise.
     */
    public function removeCustomBrowserDetection($browserName)
    {
        if (array_key_exists($browserName, $this->_customBrowserDetection)) {
            unset($this->_customBrowserDetection[$browserName]);
            return true;
        }

        return false;
    }

    /**
     * Remove support for a previously added platform.
     * @param string $platformName The platform name as used when added.
     * @see addCustomPlatformDetection()
     * @return boolean Returns true if the custom rule has been found and removed, false otherwise.
     */
    public function removeCustomPlatformDetection($platformName)
    {
        if (array_key_exists($platformName, $this->_customPlatformDetection)) {
            unset($this->_customPlatformDetection[$platformName]);
            return true;
        }

        return false;
    }

    /**
     * Set the user agent to use with the class.
     * @param string $agentString (optional) The value of the user agent. If an empty string is sent (default),
     * $_SERVER['HTTP_USER_AGENT'] will be used.
     */
    public function setUserAgent($agentString = '')
    {
        if (!is_string($agentString) || trim($agentString) == '') {
            if (array_key_exists('HTTP_USER_AGENT', $_SERVER) && is_string($_SERVER['HTTP_USER_AGENT'])) {
                $agentString = $_SERVER['HTTP_USER_AGENT'];
            } else {
                $agentString = '';
            }
        }

        $this->reset();
        $this->_agent = $agentString;
        $this->detect();
    }


    //--- PROTECTED MEMBERS --------------------------------------------------------------------------------------------


    /**
     * Convert the Android version numbers to the operating system name. For instance '1.6' returns 'Donut'.
     * @access protected
     * @param string $androidVer The Android version numbers as a string.
     * @return string The operating system name or the constant PLATFORM_VERSION_UNKNOWN if nothing match the version
     * numbers.
     */
    protected function androidVerToStr($androidVer)
    {
        //https://en.wikipedia.org/wiki/Android_version_history

        if ($this->compareVersions($androidVer, '9') >= 0 && $this->compareVersions($androidVer, '10') < 0) {
            return 'Pie';
        } else if ($this->compareVersions($androidVer, '8') >= 0 && $this->compareVersions($androidVer, '9') < 0) {
            return 'Oreo';
        } else if ($this->compareVersions($androidVer, '7') >= 0 && $this->compareVersions($androidVer, '8') < 0) {
            return 'Nougat';
        } else if ($this->compareVersions($androidVer, '6') >= 0 && $this->compareVersions($androidVer, '7') < 0) {
            return 'Marshmallow';
        } else if ($this->compareVersions($androidVer, '5') >= 0 && $this->compareVersions($androidVer, '5.2') < 0) {
            return 'Lollipop';
        } else if ($this->compareVersions($androidVer, '4.4') >= 0 && $this->compareVersions($androidVer, '4.5') < 0) {
            return 'KitKat';
        } else if ($this->compareVersions($androidVer, '4.1') >= 0 && $this->compareVersions($androidVer, '4.4') < 0) {
            return 'Jelly Bean';
        } else if ($this->compareVersions($androidVer, '4') >= 0 && $this->compareVersions($androidVer, '4.1') < 0) {
            return 'Ice Cream Sandwich';
        } else if ($this->compareVersions($androidVer, '3') >= 0 && $this->compareVersions($androidVer, '3.3') < 0) {
            return 'Honeycomb';
        } else if ($this->compareVersions($androidVer, '2.3') >= 0 && $this->compareVersions($androidVer, '2.4') < 0) {
            return 'Gingerbread';
        } else if ($this->compareVersions($androidVer, '2.2') >= 0 && $this->compareVersions($androidVer, '2.3') < 0) {
            return 'Froyo';
        } else if ($this->compareVersions($androidVer, '2') >= 0 && $this->compareVersions($androidVer, '2.2') < 0) {
            return 'Eclair';
        } else if ($this->compareVersions($androidVer, '1.6') == 0) {
            return 'Donut';
        } else if ($this->compareVersions($androidVer, '1.5') == 0) {
            return 'Cupcake';
        } else {
            return self::PLATFORM_VERSION_UNKNOWN; //Unknown/unnamed Android version
        }
    }

    /**
     * Determine if the browser is the Android browser (based on the WebKit layout engine and coupled with Chrome's
     * JavaScript engine) or not.
     * @access protected
     * @return boolean Returns true if the browser is the Android browser, false otherwise.
     */
    protected function checkBrowserAndroid()
    {
        //Android don't use the standard "Android/1.0", it uses "Android 1.0;" instead
        return $this->checkSimpleBrowserUA('Android', $this->_agent, self::BROWSER_ANDROID, true);
    }

    /**
     * Determine if the browser is the Bingbot crawler or not.
     * @access protected
     * @link http://www.bing.com/webmaster/help/which-crawlers-does-bing-use-8c184ec0
     * @return boolean Returns true if the browser is Bingbot, false otherwise.
     */
    protected function checkBrowserBingbot()
    {
        return $this->checkSimpleBrowserUA('bingbot', $this->_agent, self::BROWSER_BINGBOT, false, true);
    }

    /**
     * Determine if the browser is the BlackBerry browser or not.
     * @access protected
     * @link http://supportforums.blackberry.com/t5/Web-and-WebWorks-Development/How-to-detect-the-BlackBerry-Browser/ta-p/559862
     * @return boolean Returns true if the browser is the BlackBerry browser, false otherwise.
     */
    protected function checkBrowserBlackBerry()
    {
        $found = false;

        //Tablet OS check
        if ($this->checkSimpleBrowserUA('RIM Tablet OS', $this->_agent, self::BROWSER_TABLET_OS, true)) {
            return true;
        }

        //Version 6, 7 & 10 check (versions 8 & 9 does not exists)
        if ($this->checkBrowserUAWithVersion(array('BlackBerry', 'BB10'), $this->_agent, self::BROWSER_BLACKBERRY, true)) {
            if ($this->getVersion() == self::VERSION_UNKNOWN) {
                $found = true;
            } else {
                return true;
            }
        }

        //Version 4.2 to 5.0 check
        if ($this->checkSimpleBrowserUA('BlackBerry', $this->_agent, self::BROWSER_BLACKBERRY, true, false, '/', false)) {
            if ($this->getVersion() == self::VERSION_UNKNOWN) {
                $found = true;
            } else {
                return true;
            }
        }

        return $found;
    }

    /**
     * Determine if the browser is Chrome or not.
     * @access protected
     * @link http://www.google.com/chrome/
     * @return boolean Returns true if the browser is Chrome, false otherwise.
     */
    protected function checkBrowserChrome()
    {
        return $this->checkSimpleBrowserUA(array('Chrome', 'CriOS'), $this->_agent, self::BROWSER_CHROME);
    }

    /**
     * Determine if the browser is among the custom browser rules or not. Rules are checked in the order they were
     * added.
     * @access protected
     * @return boolean Returns true if we found the browser we were looking for in the custom rules, false otherwise.
     */
    protected function checkBrowserCustom()
    {
        foreach ($this->_customBrowserDetection as $browserName => $customBrowser) {
            $uaNameToLookFor = $customBrowser['uaNameToLookFor'];
            $isMobile = $customBrowser['isMobile'];
            $isRobot = $customBrowser['isRobot'];
            $separator = $customBrowser['separator'];
            $uaNameFindWords = $customBrowser['uaNameFindWords'];
            if ($this->checkSimpleBrowserUA($uaNameToLookFor, $this->_agent, $browserName, $isMobile, $isRobot, $separator, $uaNameFindWords)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the browser is Edge or not.
     * @access protected
     * @return boolean Returns true if the browser is Edge, false otherwise.
     */
    protected function checkBrowserEdge()
    {
        return $this->checkSimpleBrowserUA('Edge', $this->_agent, self::BROWSER_EDGE);
    }

    /**
     * Determine if the browser is Firebird or not. Firebird was the name of Firefox from version 0.6 to 0.7.1.
     * @access protected
     * @return boolean Returns true if the browser is Firebird, false otherwise.
     */
    protected function checkBrowserFirebird()
    {
        return $this->checkSimpleBrowserUA('Firebird', $this->_agent, self::BROWSER_FIREBIRD);
    }

    /**
     * Determine if the browser is Firefox or not.
     * @access protected
     * @link http://www.mozilla.org/en-US/firefox/new/
     * @return boolean Returns true if the browser is Firefox, false otherwise.
     */
    protected function checkBrowserFirefox()
    {
        //Safari heavily matches with Firefox, ensure that Safari is filtered out...
        if (preg_match('/.*Firefox[ (\/]*([a-z0-9.-]*)/i', $this->_agent, $matches) &&
                !$this->containString($this->_agent, 'Safari')) {
            $this->setBrowser(self::BROWSER_FIREFOX);
            $this->setVersion($matches[1]);
            $this->setMobile(false);
            $this->setRobot(false);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is the Googlebot crawler or not.
     * @access protected
     * @return boolean Returns true if the browser is Googlebot, false otherwise.
     */
    protected function checkBrowserGooglebot()
    {
        if ($this->checkSimpleBrowserUA('Googlebot', $this->_agent, self::BROWSER_GOOGLEBOT, false, true)) {

            if ($this->containString($this->_agent, 'googlebot-mobile')) {
                $this->setMobile(true);
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iCab or not.
     * @access protected
     * @link http://www.icab.de/
     * @return boolean Returns true if the browser is iCab, false otherwise.
     */
    protected function checkBrowserIcab()
    {
        //Some (early) iCab versions don't use the standard "iCab/1.0", they uses "iCab 1.0;" instead
        return $this->checkSimpleBrowserUA('iCab', $this->_agent, self::BROWSER_ICAB);
    }

    /**
     * Determine if the browser is GNU IceCat (formerly known as GNU IceWeasel) or not.
     * @access protected
     * @link http://www.gnu.org/software/gnuzilla/
     * @return boolean Returns true if the browser is GNU IceCat, false otherwise.
     */
    protected function checkBrowserIceCat()
    {
        return $this->checkSimpleBrowserUA('IceCat', $this->_agent, self::BROWSER_ICECAT);
    }

    /**
     * Determine if the browser is GNU IceWeasel (now know as GNU IceCat) or not.
     * @access protected
     * @see checkBrowserIceCat()
     * @return boolean Returns true if the browser is GNU IceWeasel, false otherwise.
     */
    protected function checkBrowserIceWeasel()
    {
        return $this->checkSimpleBrowserUA('Iceweasel', $this->_agent, self::BROWSER_ICEWEASEL);
    }

    /**
     * Determine if the browser is Internet Explorer or not.
     * @access protected
     * @link http://www.microsoft.com/ie/
     * @link http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
     * @return boolean Returns true if the browser is Internet Explorer, false otherwise.
     */
    protected function checkBrowserInternetExplorer()
    {
        //Test for Internet Explorer Mobile (formerly Pocket Internet Explorer)
        if ($this->checkSimpleBrowserUA(array('IEMobile', 'MSPIE'), $this->_agent, self::BROWSER_IE_MOBILE, true)) {
            return true;
        }

        //Several browsers uses IE compatibility UAs filter these browsers out (but after testing for IE Mobile)
        if ($this->containString($this->_agent, 'Opera') || $this->containString($this->_agent, array('BlackBerry', 'Nokia'), true, false)) {
            return false;
        }

        //Test for Internet Explorer 1
        if ($this->checkSimpleBrowserUA('Microsoft Internet Explorer', $this->_agent, self::BROWSER_IE)) {
            if ($this->getVersion() == self::VERSION_UNKNOWN) {
                if (preg_match('/308|425|426|474|0b1/i', $this->_agent)) {
                    $this->setVersion('1.5');
                } else {
                    $this->setVersion('1.0');
                }
            }
            return true;
        }

        //Test for Internet Explorer 2+
        if ($this->containString($this->_agent, array('MSIE', 'Trident'))) {
            $version = '';

            if ($this->containString($this->_agent, 'Trident')) {
                //Test for Internet Explorer 11+ (check the rv: string)
                if ($this->containString($this->_agent, 'rv:', true, false)) {
                    if ($this->checkSimpleBrowserUA('Trident', $this->_agent, self::BROWSER_IE, false, false, 'rv:')) {
                        return true;
                    }
                } else {
                    //Test for Internet Explorer 8, 9 & 10 (check the Trident string)
                    if (preg_match('/Trident\/([\d]+)/i', $this->_agent, $foundVersion)) {
                        //Trident started with version 4.0 on IE 8
                        $verFromTrident = $this->parseInt($foundVersion[1]) + 4;
                        if ($verFromTrident >= 8) {
                            $version = $verFromTrident . '.0';
                        }
                    }
                }

                //If we have the IE version from Trident, we can check for the compatibility view mode
                if ($version != '') {
                    $emulatedVer = '';
                    preg_match_all('/MSIE\s*([^\s;$]+)/i', $this->_agent, $foundVersions);
                    foreach ($foundVersions[1] as $currVer) {
                        //Keep the lowest MSIE version for the emulated version (in compatibility view mode)
                        if ($emulatedVer == '' || $this->compareVersions($emulatedVer, $currVer) == 1) {
                            $emulatedVer = $currVer;
                        }
                    }
                    //Set the compatibility view mode if $version != $emulatedVer
                    if ($this->compareVersions($version, $emulatedVer) != 0) {
                        $this->_compatibilityViewName = self::BROWSER_IE;
                        $this->_compatibilityViewVer = $this->cleanVersion($emulatedVer);
                    }
                }
            }

            //Test for Internet Explorer 2-7 versions if needed
            if ($version == '') {
                preg_match_all('/MSIE\s+([^\s;$]+)/i', $this->_agent, $foundVersions);
                foreach ($foundVersions[1] as $currVer) {
                    //Keep the highest MSIE version
                    if ($version == '' || $this->compareVersions($version, $currVer) == -1) {
                        $version = $currVer;
                    }
                }
            }

            $this->setBrowser(self::BROWSER_IE);
            $this->setVersion($version);
            $this->setMobile(false);
            $this->setRobot(false);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Konqueror or not.
     * @access protected
     * @link http://www.konqueror.org/
     * @return boolean Returns true if the browser is Konqueror, false otherwise.
     */
    protected function checkBrowserKonqueror()
    {
        return $this->checkSimpleBrowserUA('Konqueror', $this->_agent, self::BROWSER_KONQUEROR);
    }

    /**
     * Determine if the browser is Lynx or not. It is the oldest web browser currently in general use and development.
     * It is a text-based only Web browser.
     * @access protected
     * @link http://en.wikipedia.org/wiki/Lynx
     * @return boolean Returns true if the browser is Lynx, false otherwise.
     */
    protected function checkBrowserLynx()
    {
        return $this->checkSimpleBrowserUA('Lynx', $this->_agent, self::BROWSER_LYNX);
    }

    /**
     * Determine if the browser is Mozilla or not.
     * @access protected
     * @return boolean Returns true if the browser is Mozilla, false otherwise.
     */
    protected function checkBrowserMozilla()
    {
        return $this->checkSimpleBrowserUA('Mozilla', $this->_agent, self::BROWSER_MOZILLA, false, false, 'rv:');
    }

    /**
     * Determine if the browser is the MSNBot crawler or not. In October 2010 it was replaced by the Bingbot robot.
     * @access protected
     * @see checkBrowserBingbot()
     * @return boolean Returns true if the browser is MSNBot, false otherwise.
     */
    protected function checkBrowserMsnBot()
    {
        return $this->checkSimpleBrowserUA('msnbot', $this->_agent, self::BROWSER_MSNBOT, false, true);
    }

    /**
     * Determine if the browser is MSN TV (formerly WebTV) or not.
     * @access protected
     * @link http://en.wikipedia.org/wiki/MSN_TV
     * @return boolean Returns true if the browser is WebTv, false otherwise.
     */
    protected function checkBrowserMsnTv()
    {
        return $this->checkSimpleBrowserUA('webtv', $this->_agent, self::BROWSER_MSNTV);
    }

    /**
     * Determine if the browser is Netscape or not. Official support for this browser ended on March 1st, 2008.
     * @access protected
     * @link http://en.wikipedia.org/wiki/Netscape
     * @return boolean Returns true if the browser is Netscape, false otherwise.
     */
    protected function checkBrowserNetscape()
    {
        //BlackBerry & Nokia UAs can conflict with Netscape UAs
        if ($this->containString($this->_agent, array('BlackBerry', 'Nokia'), true, false)) {
            return false;
        }

        //Netscape v6 to v9 check
        if ($this->checkSimpleBrowserUA(array('Netscape', 'Navigator', 'Netscape6'), $this->_agent, self::BROWSER_NETSCAPE)) {
            return true;
        }

        //Netscape v1-4 (v5 don't exists)
        $found = false;
        if ($this->containString($this->_agent, 'Mozilla') && !$this->containString($this->_agent, 'rv:', true, false)) {
            $version = '';
            $verParts = explode('/', stristr($this->_agent, 'Mozilla'));
            if (count($verParts) > 1) {
                $verParts = explode(' ', $verParts[1]);
                $verParts = explode('.', $verParts[0]);

                $majorVer = $this->parseInt($verParts[0]);
                if ($majorVer > 0 && $majorVer < 5) {
                    $version = implode('.', $verParts);
                    $found = true;

                    if (strtolower(substr($version, -4)) == '-sgi') {
                        $version = substr($version, 0, -4);
                    } else {
                        if (strtolower(substr($version, -4)) == 'gold') {
                            $version = substr($version, 0, -4) . ' Gold'; //Doubles spaces (if any) will be normalized by setVersion()
                        }
                    }
                }
            }
        }

        if ($found) {
            $this->setBrowser(self::BROWSER_NETSCAPE);
            $this->setVersion($version);
            $this->setMobile(false);
            $this->setRobot(false);
        }

        return $found;
    }

    /**
     * Determine if the browser is a Nokia browser or not.
     * @access protected
     * @link http://www.developer.nokia.com/Community/Wiki/User-Agent_headers_for_Nokia_devices
     * @return boolean Returns true if the browser is a Nokia browser, false otherwise.
     */
    protected function checkBrowserNokia()
    {
        if ($this->containString($this->_agent, array('Nokia5800', 'Nokia5530', 'Nokia5230'), true, false)) {
            $this->setBrowser(self::BROWSER_NOKIA);
            $this->setVersion('7.0');
            $this->setMobile(true);
            $this->setRobot(false);

            return true;
        }

        if ($this->checkSimpleBrowserUA(array('NokiaBrowser', 'BrowserNG', 'Series60', 'S60', 'S40OviBrowser'), $this->_agent, self::BROWSER_NOKIA, true)) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Opera or not.
     * @access protected
     * @link http://www.opera.com/
     * @link http://www.opera.com/mini/
     * @link http://www.opera.com/mobile/
     * @link http://my.opera.com/community/openweb/idopera/
     * @return boolean Returns true if the browser is Opera, false otherwise.
     */
    protected function checkBrowserOpera()
    {
        if ($this->checkBrowserUAWithVersion('Opera Mobi', $this->_agent, self::BROWSER_OPERA_MOBILE, true)) {
            return true;
        }

        if ($this->checkSimpleBrowserUA('Opera Mini', $this->_agent, self::BROWSER_OPERA_MINI, true)) {
            return true;
        }

        $version = '';
        $found = $this->checkBrowserUAWithVersion('Opera', $this->_agent, self::BROWSER_OPERA);
        if ($found && $this->getVersion() != self::VERSION_UNKNOWN) {
            $version = $this->getVersion();
        }

        if (!$found || $version == '') {
            if ($this->checkSimpleBrowserUA('Opera', $this->_agent, self::BROWSER_OPERA)) {
                return true;
            }
        }

        if (!$found && $this->checkSimpleBrowserUA('Chrome', $this->_agent, self::BROWSER_CHROME) ) {
            if ($this->checkSimpleBrowserUA('OPR/', $this->_agent, self::BROWSER_OPERA)) {
                return true;
            }
        }

        return $found;
    }

    /**
     * Determine if the browser is Phoenix or not. Phoenix was the name of Firefox from version 0.1 to 0.5.
     * @access protected
     * @return boolean Returns true if the browser is Phoenix, false otherwise.
     */
    protected function checkBrowserPhoenix()
    {
        return $this->checkSimpleBrowserUA('Phoenix', $this->_agent, self::BROWSER_PHOENIX);
    }

    /**
     * Determine what is the browser used by the user.
     * @access protected
     * @return boolean Returns true if the browser has been identified, false otherwise.
     */
    protected function checkBrowsers()
    {
        //Changing the check order can break the class detection results!
        return
               /* Major browsers and browsers that need to be detected in a special order */
               $this->checkBrowserCustom() ||           /* Customs rules are always checked first */
               $this->checkBrowserMsnTv() ||            /* MSN TV is based on IE so we must check for MSN TV before IE */
               $this->checkBrowserInternetExplorer() ||
               $this->checkBrowserOpera() ||            /* Opera must be checked before Firefox, Netscape and Chrome to avoid conflicts */
               $this->checkBrowserEdge() ||             /* Edge must be checked before Firefox, Safari and Chrome to avoid conflicts */
               $this->checkBrowserSamsung() ||          /* Samsung Internet browser must be checked before Chrome and Safari to avoid conflicts */
               $this->checkBrowserUC() ||               /* UC Browser must be checked before Chrome and Safari to avoid conflicts */
               $this->checkBrowserChrome() ||           /* Chrome must be checked before Netscaoe and Mozilla to avoid conflicts */
               $this->checkBrowserIcab() ||             /* Check iCab before Netscape since iCab have Mozilla UAs */
               $this->checkBrowserNetscape() ||         /* Must be checked before Firefox since Netscape 8-9 are based on Firefox */
               $this->checkBrowserIceCat() ||           /* Check IceCat and IceWeasel before Firefox since they are GNU builds of Firefox */
               $this->checkBrowserIceWeasel() ||
               $this->checkBrowserFirefox() ||
               /* Current browsers that don't need to be detected in any special order */
               $this->checkBrowserKonqueror() ||
               $this->checkBrowserLynx() ||
               /* Mobile */
               $this->checkBrowserAndroid() ||
               $this->checkBrowserBlackBerry() ||
               $this->checkBrowserNokia() ||
               /* Bots */
               $this->checkBrowserGooglebot() ||
               $this->checkBrowserBingbot() ||
               $this->checkBrowserMsnBot() ||
               $this->checkBrowserSlurp() ||
               $this->checkBrowserYahooMultimedia() ||
               $this->checkBrowserW3CValidator() ||
               /* WebKit base check (after most other checks) */
               $this->checkBrowserSafari() ||
               /* Deprecated browsers that don't need to be detected in any special order */
               $this->checkBrowserFirebird() ||
               $this->checkBrowserPhoenix() ||
               /* Mozilla is such an open standard that it must be checked last */
               $this->checkBrowserMozilla();
    }

    /**
     * Determine if the browser is Safari or not.
     * @access protected
     * @link http://www.apple.com/safari/
     * @link http://web.archive.org/web/20080514173941/http://developer.apple.com/internet/safari/uamatrix.html
     * @link http://en.wikipedia.org/wiki/Safari_version_history#Release_history
     * @return boolean Returns true if the browser is Safari, false otherwise.
     */
    protected function checkBrowserSafari()
    {
        $version = '';

        //Check for current versions of Safari
        $found = $this->checkBrowserUAWithVersion(array('Safari', 'AppleWebKit'), $this->_agent, self::BROWSER_SAFARI);
        if ($found && $this->getVersion() != self::VERSION_UNKNOWN) {
            $version = $this->getVersion();
        }

        //Safari 1-2 didn't had a "Version" string in the UA, only a WebKit build and/or Safari build, extract version from these...
        if (!$found || $version == '') {
            if (preg_match('/.*Safari[ (\/]*([a-z0-9.-]*)/i', $this->_agent, $matches)) {
                $version = $this->safariBuildToSafariVer($matches[1]);
                $found = true;
            }
        }
        if (!$found || $version == '') {
            if (preg_match('/.*AppleWebKit[ (\/]*([a-z0-9.-]*)/i', $this->_agent, $matches)) {
                $version = $this->webKitBuildToSafariVer($matches[1]);
                $found = true;
            }
        }

        if ($found) {
            $this->setBrowser(self::BROWSER_SAFARI);
            $this->setVersion($version);
            $this->setMobile(false);
            $this->setRobot(false);
        }

        return $found;
    }

    /**
     * Determine if the browser is the Samsung Internet browser or not.
     * @access protected
     * @return boolean Returns true if the browser is the the Samsung Internet browser, false otherwise.
     */
    protected function checkBrowserSamsung()
    {
        return $this->checkSimpleBrowserUA('SamsungBrowser', $this->_agent, self::BROWSER_SAMSUNG, true);
    }

    /**
     * Determine if the browser is the Yahoo! Slurp crawler or not.
     * @access protected
     * @return boolean Returns true if the browser is Yahoo! Slurp, false otherwise.
     */
    protected function checkBrowserSlurp()
    {
        return $this->checkSimpleBrowserUA('Yahoo! Slurp', $this->_agent, self::BROWSER_SLURP, false, true);
    }

    /**
     * Test the user agent for a specific browser that use a "Version" string (like Safari and Opera). The user agent
     * should look like: "Version/1.0 Browser name/123.456" or "Browser name/123.456 Version/1.0".
     * @access protected
     * @param mixed $uaNameToLookFor The string (or array of strings) representing the browser name to find in the user
     * agent.
     * @param string $userAgent The user agent string to work with.
     * @param string $browserName The literal browser name. Always use a class constant!
     * @param boolean $isMobile (optional) Determines if the browser is from a mobile device.
     * @param boolean $isRobot (optional) Determines if the browser is a robot or not.
     * @param boolean $findWords (optional) Determines if the needle should match a word to be found. For example "Bar"
     * would not be found in "FooBar" when true but would be found in "Foo Bar". When set to false, the needle can be
     * found anywhere in the haystack.
     * @return boolean Returns true if we found the browser we were looking for, false otherwise.
     */
    protected function checkBrowserUAWithVersion($uaNameToLookFor, $userAgent, $browserName, $isMobile = false, $isRobot = false, $findWords = true)
    {
        if (!is_array($uaNameToLookFor)) {
            $uaNameToLookFor = array($uaNameToLookFor);
        }

        foreach ($uaNameToLookFor as $currUANameToLookFor) {
            if ($this->containString($userAgent, $currUANameToLookFor, true, $findWords)) {
                $version = '';
                $verParts = explode('/', stristr($this->_agent, 'Version'));
                if (count($verParts) > 1) {
                    $verParts = explode(' ', $verParts[1]);
                    $version = $verParts[0];
                }

                $this->setBrowser($browserName);
                $this->setVersion($version);

                $this->setMobile($isMobile);
                $this->setRobot($isRobot);

                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the browser is UC Browser or not.
     * @access protected
     * @return boolean Returns true if the browser is UC Browser, false otherwise.
     */
    protected function checkBrowserUC()
    {
        return $this->checkSimpleBrowserUA('UCBrowser', $this->_agent, self::BROWSER_UC, true, false);
    }

    /**
     * Determine if the browser is the W3C Validator or not.
     * @access protected
     * @link http://validator.w3.org/
     * @return boolean Returns true if the browser is the W3C Validator, false otherwise.
     */
    protected function checkBrowserW3CValidator()
    {
        //Since the W3C validates pages with different robots we will prefix our versions with the part validated on the page...

        //W3C Link Checker (prefixed with "Link-")
        if ($this->checkSimpleBrowserUA('W3C-checklink', $this->_agent, self::BROWSER_W3CVALIDATOR, false, true)) {
            if ($this->getVersion() != self::VERSION_UNKNOWN) {
                $this->setVersion('Link-' . $this->getVersion());
            }
            return true;
        }

        //W3C CSS Validation Service (prefixed with "CSS-")
        if ($this->checkSimpleBrowserUA('Jigsaw', $this->_agent, self::BROWSER_W3CVALIDATOR, false, true)) {
            if ($this->getVersion() != self::VERSION_UNKNOWN) {
                $this->setVersion('CSS-' . $this->getVersion());
            }
            return true;
        }

        //W3C mobileOK Checker (prefixed with "mobileOK-")
        if ($this->checkSimpleBrowserUA('W3C-mobileOK', $this->_agent, self::BROWSER_W3CVALIDATOR, false, true)) {
            if ($this->getVersion() != self::VERSION_UNKNOWN) {
                $this->setVersion('mobileOK-' . $this->getVersion());
            }
            return true;
        }

        //W3C Markup Validation Service (no prefix)
        return $this->checkSimpleBrowserUA('W3C_Validator', $this->_agent, self::BROWSER_W3CVALIDATOR, false, true);
    }

    /**
     * Determine if the browser is the Yahoo! multimedia crawler or not.
     * @access protected
     * @return boolean Returns true if the browser is the Yahoo! multimedia crawler, false otherwise.
     */
    protected function checkBrowserYahooMultimedia()
    {
        return $this->checkSimpleBrowserUA('Yahoo-MMCrawler', $this->_agent, self::BROWSER_YAHOO_MM, false, true);
    }

    /**
     * Determine the user's platform.
     * @access protected
     */
    protected function checkPlatform()
    {
        if (!$this->checkPlatformCustom()) { /* Customs rules are always checked first */
            /* Mobile platforms */
            if ($this->containString($this->_agent, array('Windows Phone', 'IEMobile'))) { /* Check Windows Phone (formerly Windows Mobile) before Windows */
                $this->setPlatform(self::PLATFORM_WINDOWS_PHONE);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, 'Windows CE')) { /* Check Windows CE before Windows */
                $this->setPlatform(self::PLATFORM_WINDOWS_CE);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, array('CPU OS', 'CPU iPhone OS', 'iPhone', 'iPad', 'iPod'))) { /* Check iOS (iPad/iPod/iPhone) before Macintosh */
                $this->setPlatform(self::PLATFORM_IOS);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, 'Android')) {
                $this->setPlatform(self::PLATFORM_ANDROID);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, 'BlackBerry', true, false) || $this->containString($this->_agent, array('BB10', 'RIM Tablet OS'))) {
                $this->setPlatform(self::PLATFORM_BLACKBERRY);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, 'Nokia', true, false)) {
                $this->setPlatform(self::PLATFORM_NOKIA);
                $this->setMobile(true);

            /* Desktop platforms */
            } else if ($this->containString($this->_agent, 'Windows')) {
                $this->setPlatform(self::PLATFORM_WINDOWS);
            } else if ($this->containString($this->_agent, 'Macintosh')) {
                $this->setPlatform(self::PLATFORM_MACINTOSH);
            } else if ($this->containString($this->_agent, 'Linux')) {
                $this->setPlatform(self::PLATFORM_LINUX);
            } else if ($this->containString($this->_agent, 'FreeBSD')) {
                $this->setPlatform(self::PLATFORM_FREEBSD);
            } else if ($this->containString($this->_agent, 'OpenBSD')) {
                $this->setPlatform(self::PLATFORM_OPENBSD);
            } else if ($this->containString($this->_agent, 'NetBSD')) {
                $this->setPlatform(self::PLATFORM_NETBSD);

            /* Discontinued */
            } else if ($this->containString($this->_agent, array('Symbian', 'SymbianOS'))) {
                $this->setPlatform(self::PLATFORM_SYMBIAN);
                $this->setMobile(true);
            } else if ($this->containString($this->_agent, 'OpenSolaris')) {
                $this->setPlatform(self::PLATFORM_OPENSOLARIS);

            /* Generic */
            } else if ($this->containString($this->_agent, 'Win', true, false)) {
                $this->setPlatform(self::PLATFORM_WINDOWS);
            } else if ($this->containString($this->_agent, 'Mac', true, false)) {
                $this->setPlatform(self::PLATFORM_MACINTOSH);
            }
        }

        //Check if it's a 64-bit platform
        if ($this->containString($this->_agent, array('WOW64', 'Win64', 'AMD64', 'x86_64', 'x86-64', 'ia64', 'IRIX64',
                'ppc64', 'sparc64', 'x64;', 'x64_64'))) {
            $this->set64bit(true);
        }

        $this->checkPlatformVersion();
    }

    /**
     * Determine if the platform is among the custom platform rules or not. Rules are checked in the order they were
     * added.
     * @access protected
     * @return boolean Returns true if we found the platform we were looking for in the custom rules, false otherwise.
     */
    protected function checkPlatformCustom()
    {
        foreach ($this->_customPlatformDetection as $platformName => $customPlatform) {
            $platformNameToLookFor = $customPlatform['platformNameToLookFor'];
            $isMobile = $customPlatform['isMobile'];
            if ($this->containString($this->_agent, $platformNameToLookFor)) {
                $this->setPlatform($platformName);
                if ($isMobile) {
                    $this->setMobile(true);
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the user's platform version.
     * @access protected
     */
    protected function checkPlatformVersion()
    {
        $result = '';
        switch ($this->getPlatform()) {
            case self::PLATFORM_WINDOWS:
                if (preg_match('/Windows NT\s*(\d+(?:\.\d+)*)/i', $this->_agent, $foundVersion)) {
                    $result = 'NT ' . $foundVersion[1];
                } else {
                    //https://support.microsoft.com/en-us/kb/158238

                    if ($this->containString($this->_agent, array('Windows XP', 'WinXP', 'Win XP'))) {
                        $result = '5.1';
                    } else if ($this->containString($this->_agent, 'Windows 2000', 'Win 2000', 'Win2000')) {
                        $result = '5.0';
                    } else if ($this->containString($this->_agent, array('Win 9x 4.90', 'Windows ME', 'WinME', 'Win ME'))) {
                        $result = '4.90.3000'; //Windows Me version range from 4.90.3000 to 4.90.3000A
                    } else if ($this->containString($this->_agent, array('Windows 98', 'Win98', 'Win 98'))) {
                        $result = '4.10'; //Windows 98 version range from 4.10.1998 to 4.10.2222B
                    } else if ($this->containString($this->_agent, array('Windows 95', 'Win95', 'Win 95'))) {
                        $result = '4.00'; //Windows 95 version range from 4.00.950 to 4.03.1214
                    } else if (($foundAt = stripos($this->_agent, 'Windows 3')) !== false) {
                        $result = '3';
                        if (preg_match('/\d+(?:\.\d+)*/', substr($this->_agent, $foundAt + strlen('Windows 3')), $foundVersion)) {
                            $result .= '.' . $foundVersion[0];
                        }
                    } else if ($this->containString($this->_agent, 'Win16')) {
                        $result = '3.1';
                    }
                }
                break;

            case self::PLATFORM_MACINTOSH:
                if (preg_match('/Mac OS X\s*(\d+(?:_\d+)+)/i', $this->_agent, $foundVersion)) {
                    $result = str_replace('_', '.', $this->cleanVersion($foundVersion[1]));
                } else if ($this->containString($this->_agent, 'Mac OS X')) {
                    $result = '10';
                }
                break;

            case self::PLATFORM_ANDROID:
                if (preg_match('/Android\s+([^\s;$]+)/i', $this->_agent, $foundVersion)) {
                    $result = $this->cleanVersion($foundVersion[1]);
                }
                break;

            case self::PLATFORM_IOS:
                if (preg_match('/(?:CPU OS|iPhone OS|iOS)[\s_]*([\d_]+)/i', $this->_agent, $foundVersion)) {
                    $result = str_replace('_', '.', $this->cleanVersion($foundVersion[1]));
                }
                break;
        }

        if (trim($result) == '') {
            $result = self::PLATFORM_VERSION_UNKNOWN;
        }
        $this->setPlatformVersion($result);
    }

    /**
     * Test the user agent for a specific browser where the browser name is immediately followed by the version number.
     * The user agent should look like: "Browser name/1.0" or "Browser 1.0;".
     * @access protected
     * @param mixed $uaNameToLookFor The string (or array of strings) representing the browser name to find in the user
     * agent.
     * @param string $userAgent The user agent string to work with.
     * @param string $browserName The literal browser name. Always use a class constant!
     * @param boolean $isMobile (optional) Determines if the browser is from a mobile device.
     * @param boolean $isRobot (optional) Determines if the browser is a robot or not.
     * @param string $separator (optional) The separator string used to split the browser name and the version number in
     * the user agent.
     * @param boolean $uaNameFindWords (optional) Determines if the browser name to find should match a word instead of
     * a part of a word. For example "Bar" would not be found in "FooBar" when true but would be found in "Foo Bar".
     * When set to false, the browser name can be found anywhere in the user agent string.
     * @return boolean Returns true if we found the browser we were looking for, false otherwise.
     */
    protected function checkSimpleBrowserUA($uaNameToLookFor, $userAgent, $browserName, $isMobile = false, $isRobot = false, $separator = '/', $uaNameFindWords = true)
    {
        if (!is_array($uaNameToLookFor)) {
            $uaNameToLookFor = array($uaNameToLookFor);
        }

        foreach ($uaNameToLookFor as $currUANameToLookFor) {

            if ($this->containString($userAgent, $currUANameToLookFor, true, $uaNameFindWords)) {
                //Many browsers don't use the standard "Browser/1.0" format, they uses "Browser 1.0;" instead
                if (stripos($userAgent, $currUANameToLookFor . $separator) === false) {
                    $userAgent = str_ireplace($currUANameToLookFor . ' ', $currUANameToLookFor . $separator, $this->_agent);
                }

                $version = '';
                $verParts = explode($separator, stristr($userAgent, $currUANameToLookFor));
                if (count($verParts) > 1) {
                    $verParts = explode(' ', $verParts[1]);
                    $version = $verParts[0];
                }

                $this->setBrowser($browserName);
                $this->setVersion($version);

                $this->setMobile($isMobile);
                $this->setRobot($isRobot);

                return true;
            }
        }

        return false;
    }

    /**
     * Find if one or more substring is contained in a string.
     * @access protected
     * @param string $haystack The string to search in.
     * @param mixed $needle The string to search for. Can be a string or an array of strings if multiples values are to
     * be searched.
     * @param boolean $insensitive (optional) Determines if we do a case-sensitive search (false) or a case-insensitive
     * one (true).
     * @param boolean $findWords (optional) Determines if the needle should match a word to be found. For example "Bar"
     * would not be found in "FooBar" when true but would be found in "Foo Bar". When set to false, the needle can be
     * found anywhere in the haystack.
     * @return boolean Returns true if the needle (or one of the needles) has been found in the haystack, false
     * otherwise.
     */
    protected function containString($haystack, $needle, $insensitive = true, $findWords = true)
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }

        foreach ($needle as $currNeedle) {
            if ($findWords) {
                 $found = $this->wordPos($haystack, $currNeedle, $insensitive) !== false;
            } else {
                if ($insensitive) {
                    $found = stripos($haystack, $currNeedle) !== false;
                } else {
                    $found = strpos($haystack, $currNeedle) !== false;
                }
            }

            if ($found) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect the user environment from the details in the user agent string.
     * @access protected
     */
    protected function detect()
    {
        $this->checkBrowsers();
        $this->checkPlatform(); //Check the platform after the browser since some platforms can change the mobile value
    }

    /**
     * Clean a version string from unwanted characters.
     * @access protected
     * @param string $version The version string to clean.
     * @return string Returns the cleaned version number string.
     */
    protected function cleanVersion($version)
    {
        //Clear anything that is in parentheses (and the parentheses themselves) - will clear started but unclosed ones too
        $cleanVer = preg_replace('/\([^)]+\)?/', '', $version);
        //Replace with a space any character which is NOT an alphanumeric, dot (.), hyphen (-), underscore (_) or space
        $cleanVer = preg_replace('/[^0-9.a-zA-Z_ -]/', ' ', $cleanVer);

        //Remove trailing and leading spaces
        $cleanVer = trim($cleanVer);

        //Remove trailing dot (.), hyphen (-), underscore (_)
        while (in_array(substr($cleanVer, -1), array('.', '-', '_'))) {
            $cleanVer = substr($cleanVer, 0, -1);
        }
        //Remove leading dot (.), hyphen (-), underscore (_) and character v
        while (in_array(substr($cleanVer, 0, 1), array('.', '-', '_', 'v', 'V'))) {
            $cleanVer = substr($cleanVer, 1);
        }

        //Remove double spaces if any
        while (strpos($cleanVer, '  ') !== false) {
            $cleanVer = str_replace('  ', ' ', $cleanVer);
        }

        return trim($cleanVer);
    }

    /**
     * Convert the iOS version numbers to the operating system name. For instance '2.0' returns 'iPhone OS 2.0'.
     * @access protected
     * @param string $iOSVer The iOS version numbers as a string.
     * @return string The operating system name.
     */
    protected function iOSVerToStr($iOSVer)
    {
        if ($this->compareVersions($iOSVer, '3.0') <= 0) {
            return 'iPhone OS ' . $iOSVer;
        } else {
            return 'iOS ' . $iOSVer;
        }
    }

    /**
     * Convert the macOS version numbers to the operating system name. For instance '10.7' returns 'Mac OS X Lion'.
     * @access protected
     * @param string $macVer The macOS version numbers as a string.
     * @return string The operating system name or the constant PLATFORM_VERSION_UNKNOWN if nothing match the version
     * numbers.
     */
    protected function macVerToStr($macVer)
    {
        //https://en.wikipedia.org/wiki/OS_X#Release_history

        if ($this->_platformVersion === '10') {
            return 'Mac OS X'; //Unspecified Mac OS X version
        } else if ($this->compareVersions($macVer, '10.14') >= 0 && $this->compareVersions($macVer, '10.15') < 0) {
            return 'macOS Mojave';
        } else if ($this->compareVersions($macVer, '10.13') >= 0 && $this->compareVersions($macVer, '10.14') < 0) {
            return 'macOS High Sierra';
        } else if ($this->compareVersions($macVer, '10.12') >= 0 && $this->compareVersions($macVer, '10.13') < 0) {
            return 'macOS Sierra';
        } else if ($this->compareVersions($macVer, '10.11') >= 0 && $this->compareVersions($macVer, '10.12') < 0) {
            return 'OS X El Capitan';
        } else if ($this->compareVersions($macVer, '10.10') >= 0 && $this->compareVersions($macVer, '10.11') < 0) {
            return 'OS X Yosemite';
        } else if ($this->compareVersions($macVer, '10.9') >= 0 && $this->compareVersions($macVer, '10.10') < 0) {
            return 'OS X Mavericks';
        } else if ($this->compareVersions($macVer, '10.8') >= 0 && $this->compareVersions($macVer, '10.9') < 0) {
            return 'OS X Mountain Lion';
        } else if ($this->compareVersions($macVer, '10.7') >= 0 && $this->compareVersions($macVer, '10.8') < 0) {
            return 'Mac OS X Lion';
        } else if ($this->compareVersions($macVer, '10.6') >= 0 && $this->compareVersions($macVer, '10.7') < 0) {
            return 'Mac OS X Snow Leopard';
        } else if ($this->compareVersions($macVer, '10.5') >= 0 && $this->compareVersions($macVer, '10.6') < 0) {
            return 'Mac OS X Leopard';
        } else if ($this->compareVersions($macVer, '10.4') >= 0 && $this->compareVersions($macVer, '10.5') < 0) {
            return 'Mac OS X Tiger';
        } else if ($this->compareVersions($macVer, '10.3') >= 0 && $this->compareVersions($macVer, '10.4') < 0) {
            return 'Mac OS X Panther';
        } else if ($this->compareVersions($macVer, '10.2') >= 0 && $this->compareVersions($macVer, '10.3') < 0) {
            return 'Mac OS X Jaguar';
        } else if ($this->compareVersions($macVer, '10.1') >= 0 && $this->compareVersions($macVer, '10.2') < 0) {
            return 'Mac OS X Puma';
        } else if ($this->compareVersions($macVer, '10.0') >= 0 && $this->compareVersions($macVer, '10.1') < 0) {
            return 'Mac OS X Cheetah';
        } else {
            return self::PLATFORM_VERSION_UNKNOWN; //Unknown/unnamed Mac OS version
        }
    }

    /**
     * Get the integer value of a string variable.
     * @access protected
     * @param string $intStr The scalar value being converted to an integer.
     * @return int The integer value of $intStr on success, or 0 on failure.
     */
    protected function parseInt($intStr)
    {
        return intval($intStr, 10);
    }

    /**
     * Reset all the properties of the class.
     * @access protected
     */
    protected function reset()
    {
        $this->_agent = '';
        $this->_browserName = self::BROWSER_UNKNOWN;
        $this->_compatibilityViewName = '';
        $this->_compatibilityViewVer = '';
        $this->_is64bit = false;
        $this->_isMobile = false;
        $this->_isRobot = false;
        $this->_platform = self::PLATFORM_UNKNOWN;
        $this->_platformVersion = self::PLATFORM_VERSION_UNKNOWN;
        $this->_version = self::VERSION_UNKNOWN;
    }

    /**
     * Convert a Safari build number to a Safari version number.
     * @access protected
     * @param string $version A string representing the version number.
     * @link http://web.archive.org/web/20080514173941/http://developer.apple.com/internet/safari/uamatrix.html
     * @return string Returns the Safari version string. If the version can't be determined, an empty string is
     * returned.
     */
    protected function safariBuildToSafariVer($version)
    {
        $verParts = explode('.', $version);

        //We need a 3 parts version (version 2 will becomes 2.0.0)
        while (count($verParts) < 3) {
            $verParts[] = 0;
        }
        foreach ($verParts as $i => $currPart) {
            $verParts[$i] = $this->parseInt($currPart);
        }

        switch ($verParts[0]) {
            case 419: $result = '2.0.4';
                break;
            case 417: $result = '2.0.3';
                break;
            case 416: $result = '2.0.2';
                break;

            case 412:
                if ($verParts[1] >= 5) {
                    $result = '2.0.1';
                } else {
                    $result = '2.0';
                }
                break;

            case 312:
                if ($verParts[1] >= 5) {
                    $result = '1.3.2';
                } else {
                    if ($verParts[1] >= 3) {
                        $result = '1.3.1';
                    } else {
                        $result = '1.3';
                    }
                }
                break;

            case 125:
                if ($verParts[1] >= 11) {
                    $result = '1.2.4';
                } else {
                    if ($verParts[1] >= 9) {
                        $result = '1.2.3';
                    } else {
                        if ($verParts[1] >= 7) {
                            $result = '1.2.2';
                        } else {
                            $result = '1.2';
                        }
                    }
                }
                break;

            case 100:
                if ($verParts[1] >= 1) {
                    $result = '1.1.1';
                } else {
                    $result = '1.1';
                }
                break;

            case 85:
                if ($verParts[1] >= 8) {
                    $result = '1.0.3';
                } else {
                    if ($verParts[1] >= 7) {
                        $result = '1.0.2';
                    } else {
                        $result = '1.0';
                    }
                }
                break;

            case 73: $result = '0.9';
                break;
            case 51: $result = '0.8.1';
                break;
            case 48: $result = '0.8';
                break;

            default: $result = '';
        }

        return $result;
    }

    /**
     * Set if the browser is executed from a 64-bit platform.
     * @access protected
     * @param boolean $is64bit Value that tells if the browser is executed from a 64-bit platform.
     */
    protected function set64bit($is64bit)
    {
        $this->_is64bit = $is64bit == true;
    }

    /**
     * Set the name of the browser.
     * @access protected
     * @param string $browserName The name of the browser.
     */
    protected function setBrowser($browserName)
    {
        $this->_browserName = $browserName;
    }

    /**
     * Set the browser to be from a mobile device or not.
     * @access protected
     * @param boolean $isMobile (optional) Value that tells if the browser is on a mobile device or not.
     */
    protected function setMobile($isMobile = true)
    {
        $this->_isMobile = $isMobile == true;
    }

    /**
     * Set the platform on which the browser is on.
     * @access protected
     * @param string $platform The name of the platform.
     */
    protected function setPlatform($platform)
    {
        $this->_platform = $platform;
    }

    /**
     * Set the platform version on which the browser is on.
     * @access protected
     * @param string $platformVer The version numbers of the platform.
     */
    protected function setPlatformVersion($platformVer)
    {
        $this->_platformVersion = $platformVer;
    }

    /**
     * Set the browser to be a robot (crawler) or not.
     * @access protected
     * @param boolean $isRobot (optional) Value that tells if the browser is a robot or not.
     */
    protected function setRobot($isRobot = true)
    {
        $this->_isRobot = $isRobot == true;
    }

    /**
     * Set the version of the browser.
     * @access protected
     * @param string $version The version of the browser.
     */
    protected function setVersion($version)
    {
        $cleanVer = $this->cleanVersion($version);

        if ($cleanVer == '') {
            $this->_version = self::VERSION_UNKNOWN;
        } else {
            $this->_version = $cleanVer;
        }
    }

    /**
     * Convert a WebKit build number to a Safari version number.
     * @access protected
     * @param string $version A string representing the version number.
     * @link http://web.archive.org/web/20080514173941/http://developer.apple.com/internet/safari/uamatrix.html
     * @return string Returns the Safari version string. If the version can't be determined, an empty string is
     * returned.
     */
    protected function webKitBuildToSafariVer($version)
    {
        $verParts = explode('.', $version);

        //We need a 3 parts version (version 2 will becomes 2.0.0)
        while (count($verParts) < 3) {
            $verParts[] = 0;
        }
        foreach ($verParts as $i => $currPart) {
            $verParts[$i] = $this->parseInt($currPart);
        }

        switch ($verParts[0]) {
            case 419: $result = '2.0.4';
                break;

            case 418:
                if ($verParts[1] >= 8) {
                    $result = '2.0.4';
                } else {
                    $result = '2.0.3';
                }
                break;

            case 417: $result = '2.0.3';
                break;

            case 416: $result = '2.0.2';
                break;

            case 412:
                if ($verParts[1] >= 7) {
                    $result = '2.0.1';
                } else {
                    $result = '2.0';
                }
                break;

            case 312:
                if ($verParts[1] >= 8) {
                    $result = '1.3.2';
                } else {
                    if ($verParts[1] >= 5) {
                        $result = '1.3.1';
                    } else {
                        $result = '1.3';
                    }
                }
                break;

            case 125:
                if ($this->compareVersions('5.4', $verParts[1] . '.' . $verParts[2]) == -1) {
                    $result = '1.2.4'; //125.5.5+
                } else {
                    if ($verParts[1] >= 4) {
                        $result = '1.2.3';
                    } else {
                        if ($verParts[1] >= 2) {
                            $result = '1.2.2';
                        } else {
                            $result = '1.2';
                        }
                    }
                }
                break;

            //WebKit 100 can be either Safari 1.1 (Safari build 100) or 1.1.1 (Safari build 100.1)
            //for this reason, check the Safari build before the WebKit build.
            case 100: $result = '1.1.1';
                break;

            case 85:
                if ($verParts[1] >= 8) {
                    $result = '1.0.3';
                } else {
                    if ($verParts[1] >= 7) {
                        //WebKit 85.7 can be either Safari 1.0 (Safari build 85.5) or 1.0.2 (Safari build 85.7)
                        //for this reason, check the Safari build before the WebKit build.
                        $result = '1.0.2';
                    } else {
                        $result = '1.0';
                    }
                }
                break;

            case 73: $result = '0.9';
                break;
            case 51: $result = '0.8.1';
                break;
            case 48: $result = '0.8';
                break;

            default: $result = '';
        }

        return $result;
    }

    /**
     * Convert the Windows NT family version numbers to the operating system name. For instance '5.1' returns
     * 'Windows XP'.
     * @access protected
     * @param string $winVer The Windows NT family version numbers as a string.
     * @param boolean $returnServerFlavor (optional) Since some Windows NT versions have the same values, this flag
     * determines if the Server flavor is returned or not. For instance Windows 8.1 and Windows Server 2012 R2 both use
     * version 6.3.
     * @return string The operating system name or the constant PLATFORM_VERSION_UNKNOWN if nothing match the version
     * numbers.
     */
    protected function windowsNTVerToStr($winVer, $returnServerFlavor = false)
    {
        //https://en.wikipedia.org/wiki/List_of_Microsoft_Windows_versions

        $cleanWinVer = explode('.', $winVer);
        while (count($cleanWinVer) > 2) {
            array_pop($cleanWinVer);
        }
        $cleanWinVer = implode('.', $cleanWinVer);

        if ($this->compareVersions($cleanWinVer, '11') >= 0) {
            //Future versions of Windows
            return self::PLATFORM_WINDOWS . ' ' . $winVer;
        } else if ($this->compareVersions($cleanWinVer, '10') >= 0) {
            //Current version of Windows
            return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2016') : (self::PLATFORM_WINDOWS . ' 10');
        } else if ($this->compareVersions($cleanWinVer, '7') < 0) {
            if ($this->compareVersions($cleanWinVer, '6.3') == 0) {
                return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2012 R2') : (self::PLATFORM_WINDOWS . ' 8.1');
            } else if ($this->compareVersions($cleanWinVer, '6.2') == 0) {
                return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2012') : (self::PLATFORM_WINDOWS . ' 8');
            } else if ($this->compareVersions($cleanWinVer, '6.1') == 0) {
                return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2008 R2') : (self::PLATFORM_WINDOWS . ' 7');
            } else if ($this->compareVersions($cleanWinVer, '6') == 0) {
                return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2008') : (self::PLATFORM_WINDOWS . ' Vista');
            } else if ($this->compareVersions($cleanWinVer, '5.2') == 0) {
                return $returnServerFlavor ? (self::PLATFORM_WINDOWS . ' Server 2003 / ' . self::PLATFORM_WINDOWS . ' Server 2003 R2') : (self::PLATFORM_WINDOWS . ' XP x64 Edition');
            } else if ($this->compareVersions($cleanWinVer, '5.1') == 0) {
                return self::PLATFORM_WINDOWS . ' XP';
            } else if ($this->compareVersions($cleanWinVer, '5') == 0) {
                return self::PLATFORM_WINDOWS . ' 2000';
            } else if ($this->compareVersions($cleanWinVer, '5') < 0 && $this->compareVersions($cleanWinVer, '3') >= 0) {
                return self::PLATFORM_WINDOWS . ' NT ' . $winVer;
            }
        }

        return self::PLATFORM_VERSION_UNKNOWN; //Invalid Windows NT version
    }

    /**
     * Convert the Windows 3.x & 9x family version numbers to the operating system name. For instance '4.10.1998'
     * returns 'Windows 98'.
     * @access protected
     * @param string $winVer The Windows 3.x or 9x family version numbers as a string.
     * @return string The operating system name or the constant PLATFORM_VERSION_UNKNOWN if nothing match the version
     * numbers.
     */
    protected function windowsVerToStr($winVer)
    {
        //https://support.microsoft.com/en-us/kb/158238

        if ($this->compareVersions($winVer, '4.90') >= 0 && $this->compareVersions($winVer, '4.91') < 0) {
            return self::PLATFORM_WINDOWS . ' Me'; //Normally range from 4.90.3000 to 4.90.3000A
        } else if ($this->compareVersions($winVer, '4.10') >= 0 && $this->compareVersions($winVer, '4.11') < 0) {
            return self::PLATFORM_WINDOWS . ' 98'; //Normally range from 4.10.1998 to 4.10.2222B
        } else if ($this->compareVersions($winVer, '4') >= 0 && $this->compareVersions($winVer, '4.04') < 0) {
            return self::PLATFORM_WINDOWS . ' 95'; //Normally range from 4.00.950 to 4.03.1214
        } else if ($this->compareVersions($winVer, '3.1') == 0 || $this->compareVersions($winVer, '3.11') == 0) {
            return self::PLATFORM_WINDOWS . ' ' . $winVer;
        } else if ($this->compareVersions($winVer, '3.10') == 0) {
            return self::PLATFORM_WINDOWS . ' 3.1';
        } else {
            return self::PLATFORM_VERSION_UNKNOWN; //Invalid Windows version
        }
    }

    /**
     * Find the position of the first occurrence of a word in a string.
     * @access protected
     * @param string $haystack The string to search in.
     * @param string $needle The string to search for.
     * @param boolean $insensitive (optional) Determines if we do a case-sensitive search (false) or a case-insensitive
     * one (true).
     * @param int $offset If specified, search will start this number of characters counted from the beginning of the
     * string. If the offset is negative, the search will start this number of characters counted from the end of the
     * string.
     * @param string $foundString String buffer that will contain the exact matching needle found. Set to NULL when
     * return value of the function is false.
     * @return mixed Returns the position of the needle (int) if found, false otherwise. Warning this function may
     * return Boolean false, but may also return a non-Boolean value which evaluates to false.
     */
    protected function wordPos($haystack, $needle, $insensitive = true, $offset = 0, &$foundString = NULL)
    {
        if ($offset != 0) {
            $haystack = substr($haystack, $offset);
        }

        $parts = explode(' ', $needle);
        foreach ($parts as $i => $currPart) {
            $parts[$i] = preg_quote($currPart, '/');
        }

        $regex = '/(?<=\A|[\s\/\\.,;:_()-])' . implode('[\s\/\\.,;:_()-]', $parts) . '(?=[\s\/\\.,;:_()-]|$)/';
        if ($insensitive) {
             $regex .= 'i';
        }

        if (preg_match($regex, $haystack, $matches, PREG_OFFSET_CAPTURE)) {
            $foundString = $matches[0][0];
            return (int)$matches[0][1];
        }

        return false;
    }
}

class MobileDetect
{
    /**
     * Mobile detection type.
     *
     * @deprecated since version 2.6.9
     */
    const DETECTION_TYPE_MOBILE     = 'mobile';

    /**
     * Extended detection type.
     *
     * @deprecated since version 2.6.9
     */
    const DETECTION_TYPE_EXTENDED   = 'extended';

    /**
     * A frequently used regular expression to extract version #s.
     *
     * @deprecated since version 2.6.9
     */
    const VER                       = '([\w._\+]+)';

    /**
     * Top-level device.
     */
    const MOBILE_GRADE_A            = 'A';

    /**
     * Mid-level device.
     */
    const MOBILE_GRADE_B            = 'B';

    /**
     * Low-level device.
     */
    const MOBILE_GRADE_C            = 'C';

    /**
     * Stores the version number of the current release.
     */
    const VERSION                   = '2.8.32';

    /**
     * A type for the version() method indicating a string return value.
     */
    const VERSION_TYPE_STRING       = 'text';

    /**
     * A type for the version() method indicating a float return value.
     */
    const VERSION_TYPE_FLOAT        = 'float';

    /**
     * A cache for resolved matches
     * @var array
     */
    protected $cache = array();

    /**
     * The User-Agent HTTP header is stored in here.
     * @var string
     */
    protected $userAgent = null;

    /**
     * HTTP headers in the PHP-flavor. So HTTP_USER_AGENT and SERVER_SOFTWARE.
     * @var array
     */
    protected $httpHeaders = array();

    /**
     * CloudFront headers. E.g. CloudFront-Is-Desktop-Viewer, CloudFront-Is-Mobile-Viewer & CloudFront-Is-Tablet-Viewer.
     * @var array
     */
    protected $cloudfrontHeaders = array();

    /**
     * The matching Regex.
     * This is good for debug.
     * @var string
     */
    protected $matchingRegex = null;

    /**
     * The matches extracted from the regex expression.
     * This is good for debug.
     *
     * @var string
     */
    protected $matchesArray = null;

    /**
     * The detection type, using self::DETECTION_TYPE_MOBILE or self::DETECTION_TYPE_EXTENDED.
     *
     * @deprecated since version 2.6.9
     *
     * @var string
     */
    protected $detectionType = self::DETECTION_TYPE_MOBILE;

    /**
     * HTTP headers that trigger the 'isMobile' detection
     * to be true.
     *
     * @var array
     */
    protected static $mobileHeaders = array(

            'HTTP_ACCEPT'                  => array('matches' => array(
                                                                        // Opera Mini; @reference: http://dev.opera.com/articles/view/opera-binary-markup-language/
                                                                        'application/x-obml2d',
                                                                        // BlackBerry devices.
                                                                        'application/vnd.rim.html',
                                                                        'text/vnd.wap.wml',
                                                                        'application/vnd.wap.xhtml+xml'
                                            )),
            'HTTP_X_WAP_PROFILE'           => null,
            'HTTP_X_WAP_CLIENTID'          => null,
            'HTTP_WAP_CONNECTION'          => null,
            'HTTP_PROFILE'                 => null,
            // Reported by Opera on Nokia devices (eg. C3).
            'HTTP_X_OPERAMINI_PHONE_UA'    => null,
            'HTTP_X_NOKIA_GATEWAY_ID'      => null,
            'HTTP_X_ORANGE_ID'             => null,
            'HTTP_X_VODAFONE_3GPDPCONTEXT' => null,
            'HTTP_X_HUAWEI_USERID'         => null,
            // Reported by Windows Smartphones.
            'HTTP_UA_OS'                   => null,
            // Reported by Verizon, Vodafone proxy system.
            'HTTP_X_MOBILE_GATEWAY'        => null,
            // Seen this on HTC Sensation. SensationXE_Beats_Z715e.
            'HTTP_X_ATT_DEVICEID'          => null,
            // Seen this on a HTC.
            'HTTP_UA_CPU'                  => array('matches' => array('ARM')),
    );

    /**
     * List of mobile devices (phones).
     *
     * @var array
     */
    protected static $phoneDevices = array(
        'iPhone'        => '\biPhone\b|\biPod\b', // |\biTunes
        'BlackBerry'    => 'BlackBerry|\bBB10\b|rim[0-9]+',
        'HTC'           => 'HTC|HTC.*(Sensation|Evo|Vision|Explorer|6800|8100|8900|A7272|S510e|C110e|Legend|Desire|T8282)|APX515CKT|Qtek9090|APA9292KT|HD_mini|Sensation.*Z710e|PG86100|Z715e|Desire.*(A8181|HD)|ADR6200|ADR6400L|ADR6425|001HT|Inspire 4G|Android.*\bEVO\b|T-Mobile G1|Z520m|Android [0-9.]+; Pixel',
        'Nexus'         => 'Nexus One|Nexus S|Galaxy.*Nexus|Android.*Nexus.*Mobile|Nexus 4|Nexus 5|Nexus 6',
        // @todo: Is 'Dell Streak' a tablet or a phone? ;)
        'Dell'          => 'Dell[;]? (Streak|Aero|Venue|Venue Pro|Flash|Smoke|Mini 3iX)|XCD28|XCD35|\b001DL\b|\b101DL\b|\bGS01\b',
        'Motorola'      => 'Motorola|DROIDX|DROID BIONIC|\bDroid\b.*Build|Android.*Xoom|HRI39|MOT-|A1260|A1680|A555|A853|A855|A953|A955|A956|Motorola.*ELECTRIFY|Motorola.*i1|i867|i940|MB200|MB300|MB501|MB502|MB508|MB511|MB520|MB525|MB526|MB611|MB612|MB632|MB810|MB855|MB860|MB861|MB865|MB870|ME501|ME502|ME511|ME525|ME600|ME632|ME722|ME811|ME860|ME863|ME865|MT620|MT710|MT716|MT720|MT810|MT870|MT917|Motorola.*TITANIUM|WX435|WX445|XT300|XT301|XT311|XT316|XT317|XT319|XT320|XT390|XT502|XT530|XT531|XT532|XT535|XT603|XT610|XT611|XT615|XT681|XT701|XT702|XT711|XT720|XT800|XT806|XT860|XT862|XT875|XT882|XT883|XT894|XT901|XT907|XT909|XT910|XT912|XT928|XT926|XT915|XT919|XT925|XT1021|\bMoto E\b|XT1068|XT1092|XT1052',
        'Samsung'       => '\bSamsung\b|SM-G950F|SM-G955F|SM-G9250|GT-19300|SGH-I337|BGT-S5230|GT-B2100|GT-B2700|GT-B2710|GT-B3210|GT-B3310|GT-B3410|GT-B3730|GT-B3740|GT-B5510|GT-B5512|GT-B5722|GT-B6520|GT-B7300|GT-B7320|GT-B7330|GT-B7350|GT-B7510|GT-B7722|GT-B7800|GT-C3010|GT-C3011|GT-C3060|GT-C3200|GT-C3212|GT-C3212I|GT-C3262|GT-C3222|GT-C3300|GT-C3300K|GT-C3303|GT-C3303K|GT-C3310|GT-C3322|GT-C3330|GT-C3350|GT-C3500|GT-C3510|GT-C3530|GT-C3630|GT-C3780|GT-C5010|GT-C5212|GT-C6620|GT-C6625|GT-C6712|GT-E1050|GT-E1070|GT-E1075|GT-E1080|GT-E1081|GT-E1085|GT-E1087|GT-E1100|GT-E1107|GT-E1110|GT-E1120|GT-E1125|GT-E1130|GT-E1160|GT-E1170|GT-E1175|GT-E1180|GT-E1182|GT-E1200|GT-E1210|GT-E1225|GT-E1230|GT-E1390|GT-E2100|GT-E2120|GT-E2121|GT-E2152|GT-E2220|GT-E2222|GT-E2230|GT-E2232|GT-E2250|GT-E2370|GT-E2550|GT-E2652|GT-E3210|GT-E3213|GT-I5500|GT-I5503|GT-I5700|GT-I5800|GT-I5801|GT-I6410|GT-I6420|GT-I7110|GT-I7410|GT-I7500|GT-I8000|GT-I8150|GT-I8160|GT-I8190|GT-I8320|GT-I8330|GT-I8350|GT-I8530|GT-I8700|GT-I8703|GT-I8910|GT-I9000|GT-I9001|GT-I9003|GT-I9010|GT-I9020|GT-I9023|GT-I9070|GT-I9082|GT-I9100|GT-I9103|GT-I9220|GT-I9250|GT-I9300|GT-I9305|GT-I9500|GT-I9505|GT-M3510|GT-M5650|GT-M7500|GT-M7600|GT-M7603|GT-M8800|GT-M8910|GT-N7000|GT-S3110|GT-S3310|GT-S3350|GT-S3353|GT-S3370|GT-S3650|GT-S3653|GT-S3770|GT-S3850|GT-S5210|GT-S5220|GT-S5229|GT-S5230|GT-S5233|GT-S5250|GT-S5253|GT-S5260|GT-S5263|GT-S5270|GT-S5300|GT-S5330|GT-S5350|GT-S5360|GT-S5363|GT-S5369|GT-S5380|GT-S5380D|GT-S5560|GT-S5570|GT-S5600|GT-S5603|GT-S5610|GT-S5620|GT-S5660|GT-S5670|GT-S5690|GT-S5750|GT-S5780|GT-S5830|GT-S5839|GT-S6102|GT-S6500|GT-S7070|GT-S7200|GT-S7220|GT-S7230|GT-S7233|GT-S7250|GT-S7500|GT-S7530|GT-S7550|GT-S7562|GT-S7710|GT-S8000|GT-S8003|GT-S8500|GT-S8530|GT-S8600|SCH-A310|SCH-A530|SCH-A570|SCH-A610|SCH-A630|SCH-A650|SCH-A790|SCH-A795|SCH-A850|SCH-A870|SCH-A890|SCH-A930|SCH-A950|SCH-A970|SCH-A990|SCH-I100|SCH-I110|SCH-I400|SCH-I405|SCH-I500|SCH-I510|SCH-I515|SCH-I600|SCH-I730|SCH-I760|SCH-I770|SCH-I830|SCH-I910|SCH-I920|SCH-I959|SCH-LC11|SCH-N150|SCH-N300|SCH-R100|SCH-R300|SCH-R351|SCH-R400|SCH-R410|SCH-T300|SCH-U310|SCH-U320|SCH-U350|SCH-U360|SCH-U365|SCH-U370|SCH-U380|SCH-U410|SCH-U430|SCH-U450|SCH-U460|SCH-U470|SCH-U490|SCH-U540|SCH-U550|SCH-U620|SCH-U640|SCH-U650|SCH-U660|SCH-U700|SCH-U740|SCH-U750|SCH-U810|SCH-U820|SCH-U900|SCH-U940|SCH-U960|SCS-26UC|SGH-A107|SGH-A117|SGH-A127|SGH-A137|SGH-A157|SGH-A167|SGH-A177|SGH-A187|SGH-A197|SGH-A227|SGH-A237|SGH-A257|SGH-A437|SGH-A517|SGH-A597|SGH-A637|SGH-A657|SGH-A667|SGH-A687|SGH-A697|SGH-A707|SGH-A717|SGH-A727|SGH-A737|SGH-A747|SGH-A767|SGH-A777|SGH-A797|SGH-A817|SGH-A827|SGH-A837|SGH-A847|SGH-A867|SGH-A877|SGH-A887|SGH-A897|SGH-A927|SGH-B100|SGH-B130|SGH-B200|SGH-B220|SGH-C100|SGH-C110|SGH-C120|SGH-C130|SGH-C140|SGH-C160|SGH-C170|SGH-C180|SGH-C200|SGH-C207|SGH-C210|SGH-C225|SGH-C230|SGH-C417|SGH-C450|SGH-D307|SGH-D347|SGH-D357|SGH-D407|SGH-D415|SGH-D780|SGH-D807|SGH-D980|SGH-E105|SGH-E200|SGH-E315|SGH-E316|SGH-E317|SGH-E335|SGH-E590|SGH-E635|SGH-E715|SGH-E890|SGH-F300|SGH-F480|SGH-I200|SGH-I300|SGH-I320|SGH-I550|SGH-I577|SGH-I600|SGH-I607|SGH-I617|SGH-I627|SGH-I637|SGH-I677|SGH-I700|SGH-I717|SGH-I727|SGH-i747M|SGH-I777|SGH-I780|SGH-I827|SGH-I847|SGH-I857|SGH-I896|SGH-I897|SGH-I900|SGH-I907|SGH-I917|SGH-I927|SGH-I937|SGH-I997|SGH-J150|SGH-J200|SGH-L170|SGH-L700|SGH-M110|SGH-M150|SGH-M200|SGH-N105|SGH-N500|SGH-N600|SGH-N620|SGH-N625|SGH-N700|SGH-N710|SGH-P107|SGH-P207|SGH-P300|SGH-P310|SGH-P520|SGH-P735|SGH-P777|SGH-Q105|SGH-R210|SGH-R220|SGH-R225|SGH-S105|SGH-S307|SGH-T109|SGH-T119|SGH-T139|SGH-T209|SGH-T219|SGH-T229|SGH-T239|SGH-T249|SGH-T259|SGH-T309|SGH-T319|SGH-T329|SGH-T339|SGH-T349|SGH-T359|SGH-T369|SGH-T379|SGH-T409|SGH-T429|SGH-T439|SGH-T459|SGH-T469|SGH-T479|SGH-T499|SGH-T509|SGH-T519|SGH-T539|SGH-T559|SGH-T589|SGH-T609|SGH-T619|SGH-T629|SGH-T639|SGH-T659|SGH-T669|SGH-T679|SGH-T709|SGH-T719|SGH-T729|SGH-T739|SGH-T746|SGH-T749|SGH-T759|SGH-T769|SGH-T809|SGH-T819|SGH-T839|SGH-T919|SGH-T929|SGH-T939|SGH-T959|SGH-T989|SGH-U100|SGH-U200|SGH-U800|SGH-V205|SGH-V206|SGH-X100|SGH-X105|SGH-X120|SGH-X140|SGH-X426|SGH-X427|SGH-X475|SGH-X495|SGH-X497|SGH-X507|SGH-X600|SGH-X610|SGH-X620|SGH-X630|SGH-X700|SGH-X820|SGH-X890|SGH-Z130|SGH-Z150|SGH-Z170|SGH-ZX10|SGH-ZX20|SHW-M110|SPH-A120|SPH-A400|SPH-A420|SPH-A460|SPH-A500|SPH-A560|SPH-A600|SPH-A620|SPH-A660|SPH-A700|SPH-A740|SPH-A760|SPH-A790|SPH-A800|SPH-A820|SPH-A840|SPH-A880|SPH-A900|SPH-A940|SPH-A960|SPH-D600|SPH-D700|SPH-D710|SPH-D720|SPH-I300|SPH-I325|SPH-I330|SPH-I350|SPH-I500|SPH-I600|SPH-I700|SPH-L700|SPH-M100|SPH-M220|SPH-M240|SPH-M300|SPH-M305|SPH-M320|SPH-M330|SPH-M350|SPH-M360|SPH-M370|SPH-M380|SPH-M510|SPH-M540|SPH-M550|SPH-M560|SPH-M570|SPH-M580|SPH-M610|SPH-M620|SPH-M630|SPH-M800|SPH-M810|SPH-M850|SPH-M900|SPH-M910|SPH-M920|SPH-M930|SPH-N100|SPH-N200|SPH-N240|SPH-N300|SPH-N400|SPH-Z400|SWC-E100|SCH-i909|GT-N7100|GT-N7105|SCH-I535|SM-N900A|SGH-I317|SGH-T999L|GT-S5360B|GT-I8262|GT-S6802|GT-S6312|GT-S6310|GT-S5312|GT-S5310|GT-I9105|GT-I8510|GT-S6790N|SM-G7105|SM-N9005|GT-S5301|GT-I9295|GT-I9195|SM-C101|GT-S7392|GT-S7560|GT-B7610|GT-I5510|GT-S7582|GT-S7530E|GT-I8750|SM-G9006V|SM-G9008V|SM-G9009D|SM-G900A|SM-G900D|SM-G900F|SM-G900H|SM-G900I|SM-G900J|SM-G900K|SM-G900L|SM-G900M|SM-G900P|SM-G900R4|SM-G900S|SM-G900T|SM-G900V|SM-G900W8|SHV-E160K|SCH-P709|SCH-P729|SM-T2558|GT-I9205|SM-G9350|SM-J120F|SM-G920F|SM-G920V|SM-G930F|SM-N910C|SM-A310F|GT-I9190|SM-J500FN|SM-G903F|SM-J330F',
        'LG'            => '\bLG\b;|LG[- ]?(C800|C900|E400|E610|E900|E-900|F160|F180K|F180L|F180S|730|855|L160|LS740|LS840|LS970|LU6200|MS690|MS695|MS770|MS840|MS870|MS910|P500|P700|P705|VM696|AS680|AS695|AX840|C729|E970|GS505|272|C395|E739BK|E960|L55C|L75C|LS696|LS860|P769BK|P350|P500|P509|P870|UN272|US730|VS840|VS950|LN272|LN510|LS670|LS855|LW690|MN270|MN510|P509|P769|P930|UN200|UN270|UN510|UN610|US670|US740|US760|UX265|UX840|VN271|VN530|VS660|VS700|VS740|VS750|VS910|VS920|VS930|VX9200|VX11000|AX840A|LW770|P506|P925|P999|E612|D955|D802|MS323|M257)',
        'Sony'          => 'SonyST|SonyLT|SonyEricsson|SonyEricssonLT15iv|LT18i|E10i|LT28h|LT26w|SonyEricssonMT27i|C5303|C6902|C6903|C6906|C6943|D2533',
        'Asus'          => 'Asus.*Galaxy|PadFone.*Mobile',
        'NokiaLumia'    => 'Lumia [0-9]{3,4}',
        // http://www.micromaxinfo.com/mobiles/smartphones
        // Added because the codes might conflict with Acer Tablets.
        'Micromax'      => 'Micromax.*\b(A210|A92|A88|A72|A111|A110Q|A115|A116|A110|A90S|A26|A51|A35|A54|A25|A27|A89|A68|A65|A57|A90)\b',
        // @todo Complete the regex.
        'Palm'          => 'PalmSource|Palm', // avantgo|blazer|elaine|hiptop|plucker|xiino ;
        'Vertu'         => 'Vertu|Vertu.*Ltd|Vertu.*Ascent|Vertu.*Ayxta|Vertu.*Constellation(F|Quest)?|Vertu.*Monika|Vertu.*Signature', // Just for fun ;)
        // http://www.pantech.co.kr/en/prod/prodList.do?gbrand=VEGA (PANTECH)
        // Most of the VEGA devices are legacy. PANTECH seem to be newer devices based on Android.
        'Pantech'       => 'PANTECH|IM-A850S|IM-A840S|IM-A830L|IM-A830K|IM-A830S|IM-A820L|IM-A810K|IM-A810S|IM-A800S|IM-T100K|IM-A725L|IM-A780L|IM-A775C|IM-A770K|IM-A760S|IM-A750K|IM-A740S|IM-A730S|IM-A720L|IM-A710K|IM-A690L|IM-A690S|IM-A650S|IM-A630K|IM-A600S|VEGA PTL21|PT003|P8010|ADR910L|P6030|P6020|P9070|P4100|P9060|P5000|CDM8992|TXT8045|ADR8995|IS11PT|P2030|P6010|P8000|PT002|IS06|CDM8999|P9050|PT001|TXT8040|P2020|P9020|P2000|P7040|P7000|C790',
        // http://www.fly-phone.com/devices/smartphones/ ; Included only smartphones.
        'Fly'           => 'IQ230|IQ444|IQ450|IQ440|IQ442|IQ441|IQ245|IQ256|IQ236|IQ255|IQ235|IQ245|IQ275|IQ240|IQ285|IQ280|IQ270|IQ260|IQ250',
        // http://fr.wikomobile.com
        'Wiko'          => 'KITE 4G|HIGHWAY|GETAWAY|STAIRWAY|DARKSIDE|DARKFULL|DARKNIGHT|DARKMOON|SLIDE|WAX 4G|RAINBOW|BLOOM|SUNSET|GOA(?!nna)|LENNY|BARRY|IGGY|OZZY|CINK FIVE|CINK PEAX|CINK PEAX 2|CINK SLIM|CINK SLIM 2|CINK +|CINK KING|CINK PEAX|CINK SLIM|SUBLIM',
        'iMobile'        => 'i-mobile (IQ|i-STYLE|idea|ZAA|Hitz)',
        // Added simvalley mobile just for fun. They have some interesting devices.
        // http://www.simvalley.fr/telephonie---gps-_22_telephonie-mobile_telephones_.html
        'SimValley'     => '\b(SP-80|XT-930|SX-340|XT-930|SX-310|SP-360|SP60|SPT-800|SP-120|SPT-800|SP-140|SPX-5|SPX-8|SP-100|SPX-8|SPX-12)\b',
         // Wolfgang - a brand that is sold by Aldi supermarkets.
         // http://www.wolfgangmobile.com/
        'Wolfgang'      => 'AT-B24D|AT-AS50HD|AT-AS40W|AT-AS55HD|AT-AS45q2|AT-B26D|AT-AS50Q',
        'Alcatel'       => 'Alcatel',
        'Nintendo'      => 'Nintendo (3DS|Switch)',
        // http://en.wikipedia.org/wiki/Amoi
        'Amoi'          => 'Amoi',
        // http://en.wikipedia.org/wiki/INQ
        'INQ'           => 'INQ',
        // @Tapatalk is a mobile app; http://support.tapatalk.com/threads/smf-2-0-2-os-and-browser-detection-plugin-and-tapatalk.15565/#post-79039
        'GenericPhone'  => 'Tapatalk|PDA;|SAGEM|\bmmp\b|pocket|\bpsp\b|symbian|Smartphone|smartfon|treo|up.browser|up.link|vodafone|\bwap\b|nokia|Series40|Series60|S60|SonyEricsson|N900|MAUI.*WAP.*Browser',
    );

    /**
     * List of tablet devices.
     *
     * @var array
     */
    protected static $tabletDevices = array(
        // @todo: check for mobile friendly emails topic.
        'iPad'              => 'iPad|iPad.*Mobile',
        // Removed |^.*Android.*Nexus(?!(?:Mobile).)*$
        // @see #442
        // @todo Merge NexusTablet into GoogleTablet.
        'NexusTablet'       => 'Android.*Nexus[\s]+(7|9|10)',
        // https://en.wikipedia.org/wiki/Pixel_C
        'GoogleTablet'           => 'Android.*Pixel C',
        'SamsungTablet'     => 'SAMSUNG.*Tablet|Galaxy.*Tab|SC-01C|GT-P1000|GT-P1003|GT-P1010|GT-P3105|GT-P6210|GT-P6800|GT-P6810|GT-P7100|GT-P7300|GT-P7310|GT-P7500|GT-P7510|SCH-I800|SCH-I815|SCH-I905|SGH-I957|SGH-I987|SGH-T849|SGH-T859|SGH-T869|SPH-P100|GT-P3100|GT-P3108|GT-P3110|GT-P5100|GT-P5110|GT-P6200|GT-P7320|GT-P7511|GT-N8000|GT-P8510|SGH-I497|SPH-P500|SGH-T779|SCH-I705|SCH-I915|GT-N8013|GT-P3113|GT-P5113|GT-P8110|GT-N8010|GT-N8005|GT-N8020|GT-P1013|GT-P6201|GT-P7501|GT-N5100|GT-N5105|GT-N5110|SHV-E140K|SHV-E140L|SHV-E140S|SHV-E150S|SHV-E230K|SHV-E230L|SHV-E230S|SHW-M180K|SHW-M180L|SHW-M180S|SHW-M180W|SHW-M300W|SHW-M305W|SHW-M380K|SHW-M380S|SHW-M380W|SHW-M430W|SHW-M480K|SHW-M480S|SHW-M480W|SHW-M485W|SHW-M486W|SHW-M500W|GT-I9228|SCH-P739|SCH-I925|GT-I9200|GT-P5200|GT-P5210|GT-P5210X|SM-T311|SM-T310|SM-T310X|SM-T210|SM-T210R|SM-T211|SM-P600|SM-P601|SM-P605|SM-P900|SM-P901|SM-T217|SM-T217A|SM-T217S|SM-P6000|SM-T3100|SGH-I467|XE500|SM-T110|GT-P5220|GT-I9200X|GT-N5110X|GT-N5120|SM-P905|SM-T111|SM-T2105|SM-T315|SM-T320|SM-T320X|SM-T321|SM-T520|SM-T525|SM-T530NU|SM-T230NU|SM-T330NU|SM-T900|XE500T1C|SM-P605V|SM-P905V|SM-T337V|SM-T537V|SM-T707V|SM-T807V|SM-P600X|SM-P900X|SM-T210X|SM-T230|SM-T230X|SM-T325|GT-P7503|SM-T531|SM-T330|SM-T530|SM-T705|SM-T705C|SM-T535|SM-T331|SM-T800|SM-T700|SM-T537|SM-T807|SM-P907A|SM-T337A|SM-T537A|SM-T707A|SM-T807A|SM-T237|SM-T807P|SM-P607T|SM-T217T|SM-T337T|SM-T807T|SM-T116NQ|SM-T116BU|SM-P550|SM-T350|SM-T550|SM-T9000|SM-P9000|SM-T705Y|SM-T805|GT-P3113|SM-T710|SM-T810|SM-T815|SM-T360|SM-T533|SM-T113|SM-T335|SM-T715|SM-T560|SM-T670|SM-T677|SM-T377|SM-T567|SM-T357T|SM-T555|SM-T561|SM-T713|SM-T719|SM-T813|SM-T819|SM-T580|SM-T355Y?|SM-T280|SM-T817A|SM-T820|SM-W700|SM-P580|SM-T587|SM-P350|SM-P555M|SM-P355M|SM-T113NU|SM-T815Y|SM-T585|SM-T285|SM-T825|SM-W708', // SCH-P709|SCH-P729|SM-T2558|GT-I9205 - Samsung Mega - treat them like a regular phone.
        // http://docs.aws.amazon.com/silk/latest/developerguide/user-agent.html
        'Kindle'            => 'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE|KFSAWA|KFSAWI|KFASWI|KFARWI|KFFOWI|KFGIWI|KFMEWI)\b|Android.*Silk/[0-9.]+ like Chrome/[0-9.]+ (?!Mobile)',
        // Only the Surface tablets with Windows RT are considered mobile.
        // http://msdn.microsoft.com/en-us/library/ie/hh920767(v=vs.85).aspx
        'SurfaceTablet'     => 'Windows NT [0-9.]+; ARM;.*(Tablet|ARMBJS)',
        // http://shopping1.hp.com/is-bin/INTERSHOP.enfinity/WFS/WW-USSMBPublicStore-Site/en_US/-/USD/ViewStandardCatalog-Browse?CatalogCategoryID=JfIQ7EN5lqMAAAEyDcJUDwMT
        'HPTablet'          => 'HP Slate (7|8|10)|HP ElitePad 900|hp-tablet|EliteBook.*Touch|HP 8|Slate 21|HP SlateBook 10',
        // Watch out for PadFone, see #132.
        // http://www.asus.com/de/Tablets_Mobile/Memo_Pad_Products/
        'AsusTablet'        => '^.*PadFone((?!Mobile).)*$|Transformer|TF101|TF101G|TF300T|TF300TG|TF300TL|TF700T|TF700KL|TF701T|TF810C|ME171|ME301T|ME302C|ME371MG|ME370T|ME372MG|ME172V|ME173X|ME400C|Slider SL101|\bK00F\b|\bK00C\b|\bK00E\b|\bK00L\b|TX201LA|ME176C|ME102A|\bM80TA\b|ME372CL|ME560CG|ME372CG|ME302KL| K010 | K011 | K017 | K01E |ME572C|ME103K|ME170C|ME171C|\bME70C\b|ME581C|ME581CL|ME8510C|ME181C|P01Y|PO1MA|P01Z|\bP027\b|\bP024\b|\bP00C\b',
        'BlackBerryTablet'  => 'PlayBook|RIM Tablet',
        'HTCtablet'         => 'HTC_Flyer_P512|HTC Flyer|HTC Jetstream|HTC-P715a|HTC EVO View 4G|PG41200|PG09410',
        'MotorolaTablet'    => 'xoom|sholest|MZ615|MZ605|MZ505|MZ601|MZ602|MZ603|MZ604|MZ606|MZ607|MZ608|MZ609|MZ615|MZ616|MZ617',
        'NookTablet'        => 'Android.*Nook|NookColor|nook browser|BNRV200|BNRV200A|BNTV250|BNTV250A|BNTV400|BNTV600|LogicPD Zoom2',
        // http://www.acer.ro/ac/ro/RO/content/drivers
        // http://www.packardbell.co.uk/pb/en/GB/content/download (Packard Bell is part of Acer)
        // http://us.acer.com/ac/en/US/content/group/tablets
        // http://www.acer.de/ac/de/DE/content/models/tablets/
        // Can conflict with Micromax and Motorola phones codes.
        'AcerTablet'        => 'Android.*; \b(A100|A101|A110|A200|A210|A211|A500|A501|A510|A511|A700|A701|W500|W500P|W501|W501P|W510|W511|W700|G100|G100W|B1-A71|B1-710|B1-711|A1-810|A1-811|A1-830)\b|W3-810|\bA3-A10\b|\bA3-A11\b|\bA3-A20\b|\bA3-A30',
        // http://eu.computers.toshiba-europe.com/innovation/family/Tablets/1098744/banner_id/tablet_footerlink/
        // http://us.toshiba.com/tablets/tablet-finder
        // http://www.toshiba.co.jp/regza/tablet/
        'ToshibaTablet'     => 'Android.*(AT100|AT105|AT200|AT205|AT270|AT275|AT300|AT305|AT1S5|AT500|AT570|AT700|AT830)|TOSHIBA.*FOLIO',
        // http://www.nttdocomo.co.jp/english/service/developer/smart_phone/technical_info/spec/index.html
        // http://www.lg.com/us/tablets
        'LGTablet'          => '\bL-06C|LG-V909|LG-V900|LG-V700|LG-V510|LG-V500|LG-V410|LG-V400|LG-VK810\b',
        'FujitsuTablet'     => 'Android.*\b(F-01D|F-02F|F-05E|F-10D|M532|Q572)\b',
        // Prestigio Tablets http://www.prestigio.com/support
        'PrestigioTablet'   => 'PMP3170B|PMP3270B|PMP3470B|PMP7170B|PMP3370B|PMP3570C|PMP5870C|PMP3670B|PMP5570C|PMP5770D|PMP3970B|PMP3870C|PMP5580C|PMP5880D|PMP5780D|PMP5588C|PMP7280C|PMP7280C3G|PMP7280|PMP7880D|PMP5597D|PMP5597|PMP7100D|PER3464|PER3274|PER3574|PER3884|PER5274|PER5474|PMP5097CPRO|PMP5097|PMP7380D|PMP5297C|PMP5297C_QUAD|PMP812E|PMP812E3G|PMP812F|PMP810E|PMP880TD|PMT3017|PMT3037|PMT3047|PMT3057|PMT7008|PMT5887|PMT5001|PMT5002',
        // http://support.lenovo.com/en_GB/downloads/default.page?#
        'LenovoTablet'      => 'Lenovo TAB|Idea(Tab|Pad)( A1|A10| K1|)|ThinkPad([ ]+)?Tablet|YT3-850M|YT3-X90L|YT3-X90F|YT3-X90X|Lenovo.*(S2109|S2110|S5000|S6000|K3011|A3000|A3500|A1000|A2107|A2109|A1107|A5500|A7600|B6000|B8000|B8080)(-|)(FL|F|HV|H|)|TB-X103F|TB-X304F|TB-X304L|TB-8703F|Tab2A7-10F',
        // http://www.dell.com/support/home/us/en/04/Products/tab_mob/tablets
        'DellTablet'        => 'Venue 11|Venue 8|Venue 7|Dell Streak 10|Dell Streak 7',
        // http://www.yarvik.com/en/matrix/tablets/
        'YarvikTablet'      => 'Android.*\b(TAB210|TAB211|TAB224|TAB250|TAB260|TAB264|TAB310|TAB360|TAB364|TAB410|TAB411|TAB420|TAB424|TAB450|TAB460|TAB461|TAB464|TAB465|TAB467|TAB468|TAB07-100|TAB07-101|TAB07-150|TAB07-151|TAB07-152|TAB07-200|TAB07-201-3G|TAB07-210|TAB07-211|TAB07-212|TAB07-214|TAB07-220|TAB07-400|TAB07-485|TAB08-150|TAB08-200|TAB08-201-3G|TAB08-201-30|TAB09-100|TAB09-211|TAB09-410|TAB10-150|TAB10-201|TAB10-211|TAB10-400|TAB10-410|TAB13-201|TAB274EUK|TAB275EUK|TAB374EUK|TAB462EUK|TAB474EUK|TAB9-200)\b',
        'MedionTablet'      => 'Android.*\bOYO\b|LIFE.*(P9212|P9514|P9516|S9512)|LIFETAB',
        'ArnovaTablet'      => '97G4|AN10G2|AN7bG3|AN7fG3|AN8G3|AN8cG3|AN7G3|AN9G3|AN7dG3|AN7dG3ST|AN7dG3ChildPad|AN10bG3|AN10bG3DT|AN9G2',
        // http://www.intenso.de/kategorie_en.php?kategorie=33
        // @todo: http://www.nbhkdz.com/read/b8e64202f92a2df129126bff.html - investigate
        'IntensoTablet'     => 'INM8002KP|INM1010FP|INM805ND|Intenso Tab|TAB1004',
        // IRU.ru Tablets http://www.iru.ru/catalog/soho/planetable/
        'IRUTablet'         => 'M702pro',
        'MegafonTablet'     => 'MegaFon V9|\bZTE V9\b|Android.*\bMT7A\b',
        // http://www.e-boda.ro/tablete-pc.html
        'EbodaTablet'       => 'E-Boda (Supreme|Impresspeed|Izzycomm|Essential)',
        // http://www.allview.ro/produse/droseries/lista-tablete-pc/
        'AllViewTablet'           => 'Allview.*(Viva|Alldro|City|Speed|All TV|Frenzy|Quasar|Shine|TX1|AX1|AX2)',
        // http://wiki.archosfans.com/index.php?title=Main_Page
        // @note Rewrite the regex format after we add more UAs.
        'ArchosTablet'      => '\b(101G9|80G9|A101IT)\b|Qilive 97R|Archos5|\bARCHOS (70|79|80|90|97|101|FAMILYPAD|)(b|c|)(G10| Cobalt| TITANIUM(HD|)| Xenon| Neon|XSK| 2| XS 2| PLATINUM| CARBON|GAMEPAD)\b',
        // http://www.ainol.com/plugin.php?identifier=ainol&module=product
        'AinolTablet'       => 'NOVO7|NOVO8|NOVO10|Novo7Aurora|Novo7Basic|NOVO7PALADIN|novo9-Spark',
        'NokiaLumiaTablet'  => 'Lumia 2520',
        // @todo: inspect http://esupport.sony.com/US/p/select-system.pl?DIRECTOR=DRIVER
        // Readers http://www.atsuhiro-me.net/ebook/sony-reader/sony-reader-web-browser
        // http://www.sony.jp/support/tablet/
        'SonyTablet'        => 'Sony.*Tablet|Xperia Tablet|Sony Tablet S|SO-03E|SGPT12|SGPT13|SGPT114|SGPT121|SGPT122|SGPT123|SGPT111|SGPT112|SGPT113|SGPT131|SGPT132|SGPT133|SGPT211|SGPT212|SGPT213|SGP311|SGP312|SGP321|EBRD1101|EBRD1102|EBRD1201|SGP351|SGP341|SGP511|SGP512|SGP521|SGP541|SGP551|SGP621|SGP612|SOT31',
        // http://www.support.philips.com/support/catalog/worldproducts.jsp?userLanguage=en&userCountry=cn&categoryid=3G_LTE_TABLET_SU_CN_CARE&title=3G%20tablets%20/%20LTE%20range&_dyncharset=UTF-8
        'PhilipsTablet'     => '\b(PI2010|PI3000|PI3100|PI3105|PI3110|PI3205|PI3210|PI3900|PI4010|PI7000|PI7100)\b',
        // db + http://www.cube-tablet.com/buy-products.html
        'CubeTablet'        => 'Android.*(K8GT|U9GT|U10GT|U16GT|U17GT|U18GT|U19GT|U20GT|U23GT|U30GT)|CUBE U8GT',
        // http://www.cobyusa.com/?p=pcat&pcat_id=3001
        'CobyTablet'        => 'MID1042|MID1045|MID1125|MID1126|MID7012|MID7014|MID7015|MID7034|MID7035|MID7036|MID7042|MID7048|MID7127|MID8042|MID8048|MID8127|MID9042|MID9740|MID9742|MID7022|MID7010',
        // http://www.match.net.cn/products.asp
        'MIDTablet'         => 'M9701|M9000|M9100|M806|M1052|M806|T703|MID701|MID713|MID710|MID727|MID760|MID830|MID728|MID933|MID125|MID810|MID732|MID120|MID930|MID800|MID731|MID900|MID100|MID820|MID735|MID980|MID130|MID833|MID737|MID960|MID135|MID860|MID736|MID140|MID930|MID835|MID733|MID4X10',
        // http://www.msi.com/support
        // @todo Research the Windows Tablets.
        'MSITablet' => 'MSI \b(Primo 73K|Primo 73L|Primo 81L|Primo 77|Primo 93|Primo 75|Primo 76|Primo 73|Primo 81|Primo 91|Primo 90|Enjoy 71|Enjoy 7|Enjoy 10)\b',
        // @todo http://www.kyoceramobile.com/support/drivers/
    //    'KyoceraTablet' => null,
        // @todo http://intexuae.com/index.php/category/mobile-devices/tablets-products/
    //    'IntextTablet' => null,
        // http://pdadb.net/index.php?m=pdalist&list=SMiT (NoName Chinese Tablets)
        // http://www.imp3.net/14/show.php?itemid=20454
        'SMiTTablet'        => 'Android.*(\bMID\b|MID-560|MTV-T1200|MTV-PND531|MTV-P1101|MTV-PND530)',
        // http://www.rock-chips.com/index.php?do=prod&pid=2
        'RockChipTablet'    => 'Android.*(RK2818|RK2808A|RK2918|RK3066)|RK2738|RK2808A',
        // http://www.fly-phone.com/devices/tablets/ ; http://www.fly-phone.com/service/
        'FlyTablet'         => 'IQ310|Fly Vision',
        // http://www.bqreaders.com/gb/tablets-prices-sale.html
        'bqTablet'          => 'Android.*(bq)?.*(Elcano|Curie|Edison|Maxwell|Kepler|Pascal|Tesla|Hypatia|Platon|Newton|Livingstone|Cervantes|Avant|Aquaris ([E|M]10|M8))|Maxwell.*Lite|Maxwell.*Plus',
        // http://www.huaweidevice.com/worldwide/productFamily.do?method=index&directoryId=5011&treeId=3290
        // http://www.huaweidevice.com/worldwide/downloadCenter.do?method=index&directoryId=3372&treeId=0&tb=1&type=software (including legacy tablets)
        'HuaweiTablet'      => 'MediaPad|MediaPad 7 Youth|IDEOS S7|S7-201c|S7-202u|S7-101|S7-103|S7-104|S7-105|S7-106|S7-201|S7-Slim|M2-A01L|BAH-L09|BAH-W09',
        // Nec or Medias Tab
        'NecTablet'         => '\bN-06D|\bN-08D',
        // Pantech Tablets: http://www.pantechusa.com/phones/
        'PantechTablet'     => 'Pantech.*P4100',
        // Broncho Tablets: http://www.broncho.cn/ (hard to find)
        'BronchoTablet'     => 'Broncho.*(N701|N708|N802|a710)',
        // http://versusuk.com/support.html
        'VersusTablet'      => 'TOUCHPAD.*[78910]|\bTOUCHTAB\b',
        // http://www.zync.in/index.php/our-products/tablet-phablets
        'ZyncTablet'        => 'z1000|Z99 2G|z99|z930|z999|z990|z909|Z919|z900',
        // http://www.positivoinformatica.com.br/www/pessoal/tablet-ypy/
        'PositivoTablet'    => 'TB07STA|TB10STA|TB07FTA|TB10FTA',
        // https://www.nabitablet.com/
        'NabiTablet'        => 'Android.*\bNabi',
        'KoboTablet'        => 'Kobo Touch|\bK080\b|\bVox\b Build|\bArc\b Build',
        // French Danew Tablets http://www.danew.com/produits-tablette.php
        'DanewTablet'       => 'DSlide.*\b(700|701R|702|703R|704|802|970|971|972|973|974|1010|1012)\b',
        // Texet Tablets and Readers http://www.texet.ru/tablet/
        'TexetTablet'       => 'NaviPad|TB-772A|TM-7045|TM-7055|TM-9750|TM-7016|TM-7024|TM-7026|TM-7041|TM-7043|TM-7047|TM-8041|TM-9741|TM-9747|TM-9748|TM-9751|TM-7022|TM-7021|TM-7020|TM-7011|TM-7010|TM-7023|TM-7025|TM-7037W|TM-7038W|TM-7027W|TM-9720|TM-9725|TM-9737W|TM-1020|TM-9738W|TM-9740|TM-9743W|TB-807A|TB-771A|TB-727A|TB-725A|TB-719A|TB-823A|TB-805A|TB-723A|TB-715A|TB-707A|TB-705A|TB-709A|TB-711A|TB-890HD|TB-880HD|TB-790HD|TB-780HD|TB-770HD|TB-721HD|TB-710HD|TB-434HD|TB-860HD|TB-840HD|TB-760HD|TB-750HD|TB-740HD|TB-730HD|TB-722HD|TB-720HD|TB-700HD|TB-500HD|TB-470HD|TB-431HD|TB-430HD|TB-506|TB-504|TB-446|TB-436|TB-416|TB-146SE|TB-126SE',
        // Avoid detecting 'PLAYSTATION 3' as mobile.
        'PlaystationTablet' => 'Playstation.*(Portable|Vita)',
        // http://www.trekstor.de/surftabs.html
        'TrekstorTablet'    => 'ST10416-1|VT10416-1|ST70408-1|ST702xx-1|ST702xx-2|ST80208|ST97216|ST70104-2|VT10416-2|ST10216-2A|SurfTab',
        // http://www.pyleaudio.com/Products.aspx?%2fproducts%2fPersonal-Electronics%2fTablets
        'PyleAudioTablet'   => '\b(PTBL10CEU|PTBL10C|PTBL72BC|PTBL72BCEU|PTBL7CEU|PTBL7C|PTBL92BC|PTBL92BCEU|PTBL9CEU|PTBL9CUK|PTBL9C)\b',
        // http://www.advandigital.com/index.php?link=content-product&jns=JP001
        // because of the short codenames we have to include whitespaces to reduce the possible conflicts.
        'AdvanTablet'       => 'Android.* \b(E3A|T3X|T5C|T5B|T3E|T3C|T3B|T1J|T1F|T2A|T1H|T1i|E1C|T1-E|T5-A|T4|E1-B|T2Ci|T1-B|T1-D|O1-A|E1-A|T1-A|T3A|T4i)\b ',
        // http://www.danytech.com/category/tablet-pc
        'DanyTechTablet' => 'Genius Tab G3|Genius Tab S2|Genius Tab Q3|Genius Tab G4|Genius Tab Q4|Genius Tab G-II|Genius TAB GII|Genius TAB GIII|Genius Tab S1',
        // http://www.galapad.net/product.html
        'GalapadTablet'     => 'Android.*\bG1\b',
        // http://www.micromaxinfo.com/tablet/funbook
        'MicromaxTablet'    => 'Funbook|Micromax.*\b(P250|P560|P360|P362|P600|P300|P350|P500|P275)\b',
        // http://www.karbonnmobiles.com/products_tablet.php
        'KarbonnTablet'     => 'Android.*\b(A39|A37|A34|ST8|ST10|ST7|Smart Tab3|Smart Tab2)\b',
        // http://www.myallfine.com/Products.asp
        'AllFineTablet'     => 'Fine7 Genius|Fine7 Shine|Fine7 Air|Fine8 Style|Fine9 More|Fine10 Joy|Fine11 Wide',
        // http://www.proscanvideo.com/products-search.asp?itemClass=TABLET&itemnmbr=
        'PROSCANTablet'     => '\b(PEM63|PLT1023G|PLT1041|PLT1044|PLT1044G|PLT1091|PLT4311|PLT4311PL|PLT4315|PLT7030|PLT7033|PLT7033D|PLT7035|PLT7035D|PLT7044K|PLT7045K|PLT7045KB|PLT7071KG|PLT7072|PLT7223G|PLT7225G|PLT7777G|PLT7810K|PLT7849G|PLT7851G|PLT7852G|PLT8015|PLT8031|PLT8034|PLT8036|PLT8080K|PLT8082|PLT8088|PLT8223G|PLT8234G|PLT8235G|PLT8816K|PLT9011|PLT9045K|PLT9233G|PLT9735|PLT9760G|PLT9770G)\b',
        // http://www.yonesnav.com/products/products.php
        'YONESTablet' => 'BQ1078|BC1003|BC1077|RK9702|BC9730|BC9001|IT9001|BC7008|BC7010|BC708|BC728|BC7012|BC7030|BC7027|BC7026',
        // http://www.cjshowroom.com/eproducts.aspx?classcode=004001001
        // China manufacturer makes tablets for different small brands (eg. http://www.zeepad.net/index.html)
        'ChangJiaTablet'    => 'TPC7102|TPC7103|TPC7105|TPC7106|TPC7107|TPC7201|TPC7203|TPC7205|TPC7210|TPC7708|TPC7709|TPC7712|TPC7110|TPC8101|TPC8103|TPC8105|TPC8106|TPC8203|TPC8205|TPC8503|TPC9106|TPC9701|TPC97101|TPC97103|TPC97105|TPC97106|TPC97111|TPC97113|TPC97203|TPC97603|TPC97809|TPC97205|TPC10101|TPC10103|TPC10106|TPC10111|TPC10203|TPC10205|TPC10503',
        // http://www.gloryunion.cn/products.asp
        // http://www.allwinnertech.com/en/apply/mobile.html
        // http://www.ptcl.com.pk/pd_content.php?pd_id=284 (EVOTAB)
        // @todo: Softwiner tablets?
        // aka. Cute or Cool tablets. Not sure yet, must research to avoid collisions.
        'GUTablet'          => 'TX-A1301|TX-M9002|Q702|kf026', // A12R|D75A|D77|D79|R83|A95|A106C|R15|A75|A76|D71|D72|R71|R73|R77|D82|R85|D92|A97|D92|R91|A10F|A77F|W71F|A78F|W78F|W81F|A97F|W91F|W97F|R16G|C72|C73E|K72|K73|R96G
        // http://www.pointofview-online.com/showroom.php?shop_mode=product_listing&category_id=118
        'PointOfViewTablet' => 'TAB-P506|TAB-navi-7-3G-M|TAB-P517|TAB-P-527|TAB-P701|TAB-P703|TAB-P721|TAB-P731N|TAB-P741|TAB-P825|TAB-P905|TAB-P925|TAB-PR945|TAB-PL1015|TAB-P1025|TAB-PI1045|TAB-P1325|TAB-PROTAB[0-9]+|TAB-PROTAB25|TAB-PROTAB26|TAB-PROTAB27|TAB-PROTAB26XL|TAB-PROTAB2-IPS9|TAB-PROTAB30-IPS9|TAB-PROTAB25XXL|TAB-PROTAB26-IPS10|TAB-PROTAB30-IPS10',
        // http://www.overmax.pl/pl/katalog-produktow,p8/tablety,c14/
        // @todo: add more tests.
        'OvermaxTablet'     => 'OV-(SteelCore|NewBase|Basecore|Baseone|Exellen|Quattor|EduTab|Solution|ACTION|BasicTab|TeddyTab|MagicTab|Stream|TB-08|TB-09)|Qualcore 1027',
        // http://hclmetablet.com/India/index.php
        'HCLTablet'         => 'HCL.*Tablet|Connect-3G-2.0|Connect-2G-2.0|ME Tablet U1|ME Tablet U2|ME Tablet G1|ME Tablet X1|ME Tablet Y2|ME Tablet Sync',
        // http://www.edigital.hu/Tablet_es_e-book_olvaso/Tablet-c18385.html
        'DPSTablet'         => 'DPS Dream 9|DPS Dual 7',
        // http://www.visture.com/index.asp
        'VistureTablet'     => 'V97 HD|i75 3G|Visture V4( HD)?|Visture V5( HD)?|Visture V10',
        // http://www.mijncresta.nl/tablet
        'CrestaTablet'     => 'CTP(-)?810|CTP(-)?818|CTP(-)?828|CTP(-)?838|CTP(-)?888|CTP(-)?978|CTP(-)?980|CTP(-)?987|CTP(-)?988|CTP(-)?989',
        // MediaTek - http://www.mediatek.com/_en/01_products/02_proSys.php?cata_sn=1&cata1_sn=1&cata2_sn=309
        'MediatekTablet' => '\bMT8125|MT8389|MT8135|MT8377\b',
        // Concorde tab
        'ConcordeTablet' => 'Concorde([ ]+)?Tab|ConCorde ReadMan',
        // GoClever Tablets - http://www.goclever.com/uk/products,c1/tablet,c5/
        'GoCleverTablet' => 'GOCLEVER TAB|A7GOCLEVER|M1042|M7841|M742|R1042BK|R1041|TAB A975|TAB A7842|TAB A741|TAB A741L|TAB M723G|TAB M721|TAB A1021|TAB I921|TAB R721|TAB I720|TAB T76|TAB R70|TAB R76.2|TAB R106|TAB R83.2|TAB M813G|TAB I721|GCTA722|TAB I70|TAB I71|TAB S73|TAB R73|TAB R74|TAB R93|TAB R75|TAB R76.1|TAB A73|TAB A93|TAB A93.2|TAB T72|TAB R83|TAB R974|TAB R973|TAB A101|TAB A103|TAB A104|TAB A104.2|R105BK|M713G|A972BK|TAB A971|TAB R974.2|TAB R104|TAB R83.3|TAB A1042',
        // Modecom Tablets - http://www.modecom.eu/tablets/portal/
        'ModecomTablet' => 'FreeTAB 9000|FreeTAB 7.4|FreeTAB 7004|FreeTAB 7800|FreeTAB 2096|FreeTAB 7.5|FreeTAB 1014|FreeTAB 1001 |FreeTAB 8001|FreeTAB 9706|FreeTAB 9702|FreeTAB 7003|FreeTAB 7002|FreeTAB 1002|FreeTAB 7801|FreeTAB 1331|FreeTAB 1004|FreeTAB 8002|FreeTAB 8014|FreeTAB 9704|FreeTAB 1003',
        // Vonino Tablets - http://www.vonino.eu/tablets
        'VoninoTablet'  => '\b(Argus[ _]?S|Diamond[ _]?79HD|Emerald[ _]?78E|Luna[ _]?70C|Onyx[ _]?S|Onyx[ _]?Z|Orin[ _]?HD|Orin[ _]?S|Otis[ _]?S|SpeedStar[ _]?S|Magnet[ _]?M9|Primus[ _]?94[ _]?3G|Primus[ _]?94HD|Primus[ _]?QS|Android.*\bQ8\b|Sirius[ _]?EVO[ _]?QS|Sirius[ _]?QS|Spirit[ _]?S)\b',
        // ECS Tablets - http://www.ecs.com.tw/ECSWebSite/Product/Product_Tablet_List.aspx?CategoryID=14&MenuID=107&childid=M_107&LanID=0
        'ECSTablet'     => 'V07OT2|TM105A|S10OT1|TR10CS1',
        // Storex Tablets - http://storex.fr/espace_client/support.html
        // @note: no need to add all the tablet codes since they are guided by the first regex.
        'StorexTablet'  => 'eZee[_\']?(Tab|Go)[0-9]+|TabLC7|Looney Tunes Tab',
        // Generic Vodafone tablets.
        'VodafoneTablet' => 'SmartTab([ ]+)?[0-9]+|SmartTabII10|SmartTabII7|VF-1497',
        // French tablets - Essentiel B http://www.boulanger.fr/tablette_tactile_e-book/tablette_tactile_essentiel_b/cl_68908.htm?multiChoiceToDelete=brand&mc_brand=essentielb
        // Aka: http://www.essentielb.fr/
        'EssentielBTablet' => 'Smart[ \']?TAB[ ]+?[0-9]+|Family[ \']?TAB2',
        // Ross & Moor - http://ross-moor.ru/
        'RossMoorTablet' => 'RM-790|RM-997|RMD-878G|RMD-974R|RMT-705A|RMT-701|RME-601|RMT-501|RMT-711',
        // i-mobile http://product.i-mobilephone.com/Mobile_Device
        'iMobileTablet'        => 'i-mobile i-note',
        // http://www.tolino.de/de/vergleichen/
        'TolinoTablet'  => 'tolino tab [0-9.]+|tolino shine',
        // AudioSonic - a Kmart brand
        // http://www.kmart.com.au/webapp/wcs/stores/servlet/Search?langId=-1&storeId=10701&catalogId=10001&categoryId=193001&pageSize=72&currentPage=1&searchCategory=193001%2b4294965664&sortBy=p_MaxPrice%7c1
        'AudioSonicTablet' => '\bC-22Q|T7-QC|T-17B|T-17P\b',
        // AMPE Tablets - http://www.ampe.com.my/product-category/tablets/
        // @todo: add them gradually to avoid conflicts.
        'AMPETablet' => 'Android.* A78 ',
        // Skk Mobile - http://skkmobile.com.ph/product_tablets.php
        'SkkTablet' => 'Android.* (SKYPAD|PHOENIX|CYCLOPS)',
        // Tecno Mobile (only tablet) - http://www.tecno-mobile.com/index.php/product?filterby=smart&list_order=all&page=1
        'TecnoTablet' => 'TECNO P9|TECNO DP8D',
        // JXD (consoles & tablets) - http://jxd.hk/products.asp?selectclassid=009008&clsid=3
        'JXDTablet' => 'Android.* \b(F3000|A3300|JXD5000|JXD3000|JXD2000|JXD300B|JXD300|S5800|S7800|S602b|S5110b|S7300|S5300|S602|S603|S5100|S5110|S601|S7100a|P3000F|P3000s|P101|P200s|P1000m|P200m|P9100|P1000s|S6600b|S908|P1000|P300|S18|S6600|S9100)\b',
        // i-Joy tablets - http://www.i-joy.es/en/cat/products/tablets/
        'iJoyTablet' => 'Tablet (Spirit 7|Essentia|Galatea|Fusion|Onix 7|Landa|Titan|Scooby|Deox|Stella|Themis|Argon|Unique 7|Sygnus|Hexen|Finity 7|Cream|Cream X2|Jade|Neon 7|Neron 7|Kandy|Scape|Saphyr 7|Rebel|Biox|Rebel|Rebel 8GB|Myst|Draco 7|Myst|Tab7-004|Myst|Tadeo Jones|Tablet Boing|Arrow|Draco Dual Cam|Aurix|Mint|Amity|Revolution|Finity 9|Neon 9|T9w|Amity 4GB Dual Cam|Stone 4GB|Stone 8GB|Andromeda|Silken|X2|Andromeda II|Halley|Flame|Saphyr 9,7|Touch 8|Planet|Triton|Unique 10|Hexen 10|Memphis 4GB|Memphis 8GB|Onix 10)',
        // http://www.intracon.eu/tablet
        'FX2Tablet' => 'FX2 PAD7|FX2 PAD10',
        // http://www.xoro.de/produkte/
        // @note: Might be the same brand with 'Simply tablets'
        'XoroTablet'        => 'KidsPAD 701|PAD[ ]?712|PAD[ ]?714|PAD[ ]?716|PAD[ ]?717|PAD[ ]?718|PAD[ ]?720|PAD[ ]?721|PAD[ ]?722|PAD[ ]?790|PAD[ ]?792|PAD[ ]?900|PAD[ ]?9715D|PAD[ ]?9716DR|PAD[ ]?9718DR|PAD[ ]?9719QR|PAD[ ]?9720QR|TelePAD1030|Telepad1032|TelePAD730|TelePAD731|TelePAD732|TelePAD735Q|TelePAD830|TelePAD9730|TelePAD795|MegaPAD 1331|MegaPAD 1851|MegaPAD 2151',
        // http://www1.viewsonic.com/products/computing/tablets/
        'ViewsonicTablet'   => 'ViewPad 10pi|ViewPad 10e|ViewPad 10s|ViewPad E72|ViewPad7|ViewPad E100|ViewPad 7e|ViewSonic VB733|VB100a',
        // https://www.verizonwireless.com/tablets/verizon/
        'VerizonTablet' => 'QTAQZ3|QTAIR7|QTAQTZ3|QTASUN1|QTASUN2|QTAXIA1',
        // http://www.odys.de/web/internet-tablet_en.html
        'OdysTablet'        => 'LOOX|XENO10|ODYS[ -](Space|EVO|Xpress|NOON)|\bXELIO\b|Xelio10Pro|XELIO7PHONETAB|XELIO10EXTREME|XELIOPT2|NEO_QUAD10',
        // http://www.captiva-power.de/products.html#tablets-en
        'CaptivaTablet'     => 'CAPTIVA PAD',
        // IconBIT - http://www.iconbit.com/products/tablets/
        'IconbitTablet' => 'NetTAB|NT-3702|NT-3702S|NT-3702S|NT-3603P|NT-3603P|NT-0704S|NT-0704S|NT-3805C|NT-3805C|NT-0806C|NT-0806C|NT-0909T|NT-0909T|NT-0907S|NT-0907S|NT-0902S|NT-0902S',
        // http://www.teclast.com/topic.php?channelID=70&topicID=140&pid=63
        'TeclastTablet' => 'T98 4G|\bP80\b|\bX90HD\b|X98 Air|X98 Air 3G|\bX89\b|P80 3G|\bX80h\b|P98 Air|\bX89HD\b|P98 3G|\bP90HD\b|P89 3G|X98 3G|\bP70h\b|P79HD 3G|G18d 3G|\bP79HD\b|\bP89s\b|\bA88\b|\bP10HD\b|\bP19HD\b|G18 3G|\bP78HD\b|\bA78\b|\bP75\b|G17s 3G|G17h 3G|\bP85t\b|\bP90\b|\bP11\b|\bP98t\b|\bP98HD\b|\bG18d\b|\bP85s\b|\bP11HD\b|\bP88s\b|\bA80HD\b|\bA80se\b|\bA10h\b|\bP89\b|\bP78s\b|\bG18\b|\bP85\b|\bA70h\b|\bA70\b|\bG17\b|\bP18\b|\bA80s\b|\bA11s\b|\bP88HD\b|\bA80h\b|\bP76s\b|\bP76h\b|\bP98\b|\bA10HD\b|\bP78\b|\bP88\b|\bA11\b|\bA10t\b|\bP76a\b|\bP76t\b|\bP76e\b|\bP85HD\b|\bP85a\b|\bP86\b|\bP75HD\b|\bP76v\b|\bA12\b|\bP75a\b|\bA15\b|\bP76Ti\b|\bP81HD\b|\bA10\b|\bT760VE\b|\bT720HD\b|\bP76\b|\bP73\b|\bP71\b|\bP72\b|\bT720SE\b|\bC520Ti\b|\bT760\b|\bT720VE\b|T720-3GE|T720-WiFi',
        // Onda - http://www.onda-tablet.com/buy-android-onda.html?dir=desc&limit=all&order=price
        'OndaTablet' => '\b(V975i|Vi30|VX530|V701|Vi60|V701s|Vi50|V801s|V719|Vx610w|VX610W|V819i|Vi10|VX580W|Vi10|V711s|V813|V811|V820w|V820|Vi20|V711|VI30W|V712|V891w|V972|V819w|V820w|Vi60|V820w|V711|V813s|V801|V819|V975s|V801|V819|V819|V818|V811|V712|V975m|V101w|V961w|V812|V818|V971|V971s|V919|V989|V116w|V102w|V973|Vi40)\b[\s]+',
        'JaytechTablet'     => 'TPC-PA762',
        'BlaupunktTablet'   => 'Endeavour 800NG|Endeavour 1010',
        // http://www.digma.ru/support/download/
        // @todo: Ebooks also (if requested)
        'DigmaTablet' => '\b(iDx10|iDx9|iDx8|iDx7|iDxD7|iDxD8|iDsQ8|iDsQ7|iDsQ8|iDsD10|iDnD7|3TS804H|iDsQ11|iDj7|iDs10)\b',
        // http://www.evolioshop.com/ro/tablete-pc.html
        // http://www.evolio.ro/support/downloads_static.html?cat=2
        // @todo: Research some more
        'EvolioTablet' => 'ARIA_Mini_wifi|Aria[ _]Mini|Evolio X10|Evolio X7|Evolio X8|\bEvotab\b|\bNeura\b',
        // @todo http://www.lavamobiles.com/tablets-data-cards
        'LavaTablet' => 'QPAD E704|\bIvoryS\b|E-TAB IVORY|\bE-TAB\b',
        // http://www.breezetablet.com/
        'AocTablet' => 'MW0811|MW0812|MW0922|MTK8382|MW1031|MW0831|MW0821|MW0931|MW0712',
        // http://www.mpmaneurope.com/en/products/internet-tablets-14/android-tablets-14/
        'MpmanTablet' => 'MP11 OCTA|MP10 OCTA|MPQC1114|MPQC1004|MPQC994|MPQC974|MPQC973|MPQC804|MPQC784|MPQC780|\bMPG7\b|MPDCG75|MPDCG71|MPDC1006|MP101DC|MPDC9000|MPDC905|MPDC706HD|MPDC706|MPDC705|MPDC110|MPDC100|MPDC99|MPDC97|MPDC88|MPDC8|MPDC77|MP709|MID701|MID711|MID170|MPDC703|MPQC1010',
        // https://www.celkonmobiles.com/?_a=categoryphones&sid=2
        'CelkonTablet' => 'CT695|CT888|CT[\s]?910|CT7 Tab|CT9 Tab|CT3 Tab|CT2 Tab|CT1 Tab|C820|C720|\bCT-1\b',
        // http://www.wolderelectronics.com/productos/manuales-y-guias-rapidas/categoria-2-miTab
        'WolderTablet' => 'miTab \b(DIAMOND|SPACE|BROOKLYN|NEO|FLY|MANHATTAN|FUNK|EVOLUTION|SKY|GOCAR|IRON|GENIUS|POP|MINT|EPSILON|BROADWAY|JUMP|HOP|LEGEND|NEW AGE|LINE|ADVANCE|FEEL|FOLLOW|LIKE|LINK|LIVE|THINK|FREEDOM|CHICAGO|CLEVELAND|BALTIMORE-GH|IOWA|BOSTON|SEATTLE|PHOENIX|DALLAS|IN 101|MasterChef)\b',
        'MediacomTablet' => 'M-MPI10C3G|M-SP10EG|M-SP10EGP|M-SP10HXAH|M-SP7HXAH|M-SP10HXBH|M-SP8HXAH|M-SP8MXA',
        // http://www.mi.com/en
        'MiTablet' => '\bMI PAD\b|\bHM NOTE 1W\b',
        // http://www.nbru.cn/index.html
        'NibiruTablet' => 'Nibiru M1|Nibiru Jupiter One',
        // http://navroad.com/products/produkty/tablety/
        // http://navroad.com/products/produkty/tablety/
        'NexoTablet' => 'NEXO NOVA|NEXO 10|NEXO AVIO|NEXO FREE|NEXO GO|NEXO EVO|NEXO 3G|NEXO SMART|NEXO KIDDO|NEXO MOBI',
        // http://leader-online.com/new_site/product-category/tablets/
        // http://www.leader-online.net.au/List/Tablet
        'LeaderTablet' => 'TBLT10Q|TBLT10I|TBL-10WDKB|TBL-10WDKBO2013|TBL-W230V2|TBL-W450|TBL-W500|SV572|TBLT7I|TBA-AC7-8G|TBLT79|TBL-8W16|TBL-10W32|TBL-10WKB|TBL-W100',
        // http://www.datawind.com/ubislate/
        'UbislateTablet' => 'UbiSlate[\s]?7C',
        // http://www.pocketbook-int.com/ru/support
        'PocketBookTablet' => 'Pocketbook',
        // http://www.kocaso.com/product_tablet.html
        'KocasoTablet' => '\b(TB-1207)\b',
        // http://global.hisense.com/product/asia/tablet/Sero7/201412/t20141215_91832.htm
        'HisenseTablet' => '\b(F5281|E2371)\b',
        // http://www.tesco.com/direct/hudl/
        'Hudl'              => 'Hudl HT7S3|Hudl 2',
        // http://www.telstra.com.au/home-phone/thub-2/
        'TelstraTablet'     => 'T-Hub2',
        'GenericTablet'     => 'Android.*\b97D\b|Tablet(?!.*PC)|BNTV250A|MID-WCDMA|LogicPD Zoom2|\bA7EB\b|CatNova8|A1_07|CT704|CT1002|\bM721\b|rk30sdk|\bEVOTAB\b|M758A|ET904|ALUMIUM10|Smartfren Tab|Endeavour 1010|Tablet-PC-4|Tagi Tab|\bM6pro\b|CT1020W|arc 10HD|\bTP750\b|\bQTAQZ3\b|WVT101|TM1088|KT107'
    );

    /**
     * List of mobile Operating Systems.
     *
     * @var array
     */
    protected static $operatingSystems = array(
        'AndroidOS'         => 'Android',
        'BlackBerryOS'      => 'blackberry|\bBB10\b|rim tablet os',
        'PalmOS'            => 'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
        'SymbianOS'         => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
        // @reference: http://en.wikipedia.org/wiki/Windows_Mobile
        'WindowsMobileOS'   => 'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
        // @reference: http://en.wikipedia.org/wiki/Windows_Phone
        // http://wifeng.cn/?r=blog&a=view&id=106
        // http://nicksnettravels.builttoroam.com/post/2011/01/10/Bogus-Windows-Phone-7-User-Agent-String.aspx
        // http://msdn.microsoft.com/library/ms537503.aspx
        // https://msdn.microsoft.com/en-us/library/hh869301(v=vs.85).aspx
        'WindowsPhoneOS'   => 'Windows Phone 10.0|Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7|Windows NT 6.[23]; ARM;',
        'iOS'               => '\biPhone.*Mobile|\biPod|\biPad|AppleCoreMedia',
        // http://en.wikipedia.org/wiki/MeeGo
        // @todo: research MeeGo in UAs
        'MeeGoOS'           => 'MeeGo',
        // http://en.wikipedia.org/wiki/Maemo
        // @todo: research Maemo in UAs
        'MaemoOS'           => 'Maemo',
        'JavaOS'            => 'J2ME/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
        'webOS'             => 'webOS|hpwOS',
        'badaOS'            => '\bBada\b',
        'BREWOS'            => 'BREW',
    );

    /**
     * List of mobile User Agents.
     *
     * IMPORTANT: This is a list of only mobile browsers.
     * Mobile Detect 2.x supports only mobile browsers,
     * it was never designed to detect all browsers.
     * The change will come in 2017 in the 3.x release for PHP7.
     *
     * @var array
     */
    protected static $browsers = array(
        //'Vivaldi'         => 'Vivaldi',
        // @reference: https://developers.google.com/chrome/mobile/docs/user-agent
        'Chrome'          => '\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
        'Dolfin'          => '\bDolfin\b',
        'Opera'           => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
        'Skyfire'         => 'Skyfire',
        'Edge'             => 'Mobile Safari/[.0-9]* Edge',
        'IE'              => 'IEMobile|MSIEMobile', // |Trident/[.0-9]+
        'Firefox'         => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile|FxiOS',
        'Bolt'            => 'bolt',
        'TeaShark'        => 'teashark',
        'Blazer'          => 'Blazer',
        // @reference: http://developer.apple.com/library/safari/#documentation/AppleApplications/Reference/SafariWebContent/OptimizingforSafarioniPhone/OptimizingforSafarioniPhone.html#//apple_ref/doc/uid/TP40006517-SW3
        'Safari'          => 'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
        // http://en.wikipedia.org/wiki/Midori_(web_browser)
        //'Midori'          => 'midori',
        //'Tizen'           => 'Tizen',
        'UCBrowser'       => 'UC.*Browser|UCWEB',
        'baiduboxapp'     => 'baiduboxapp',
        'baidubrowser'    => 'baidubrowser',
        // https://github.com/serbanghita/Mobile-Detect/issues/7
        'DiigoBrowser'    => 'DiigoBrowser',
        // http://www.puffinbrowser.com/index.php
        'Puffin'            => 'Puffin',
        // http://mercury-browser.com/index.html
        'Mercury'          => '\bMercury\b',
        // http://en.wikipedia.org/wiki/Obigo_Browser
        'ObigoBrowser' => 'Obigo',
        // http://en.wikipedia.org/wiki/NetFront
        'NetFront' => 'NF-Browser',
        // @reference: http://en.wikipedia.org/wiki/Minimo
        // http://en.wikipedia.org/wiki/Vision_Mobile_Browser
        'GenericBrowser'  => 'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
        // @reference: https://en.wikipedia.org/wiki/Pale_Moon_(web_browser)
        'PaleMoon'        => 'Android.*PaleMoon|Mobile.*PaleMoon',
    );

    /**
     * Utilities.
     *
     * @var array
     */
    protected static $utilities = array(
        // Experimental. When a mobile device wants to switch to 'Desktop Mode'.
        // http://scottcate.com/technology/windows-phone-8-ie10-desktop-or-mobile/
        // https://github.com/serbanghita/Mobile-Detect/issues/57#issuecomment-15024011
        // https://developers.facebook.com/docs/sharing/best-practices
        'Bot'         => 'Googlebot|facebookexternalhit|AdsBot-Google|Google Keyword Suggestion|Facebot|YandexBot|YandexMobileBot|bingbot|ia_archiver|AhrefsBot|Ezooms|GSLFbot|WBSearchBot|Twitterbot|TweetmemeBot|Twikle|PaperLiBot|Wotbox|UnwindFetchor|Exabot|MJ12bot|YandexImages|TurnitinBot|Pingdom',
        'MobileBot'   => 'Googlebot-Mobile|AdsBot-Google-Mobile|YahooSeeker/M1A1-R2D2',
        'DesktopMode' => 'WPDesktop',
        'TV'          => 'SonyDTV|HbbTV', // experimental
        'WebKit'      => '(webkit)[ /]([\w.]+)',
        // @todo: Include JXD consoles.
        'Console'     => '\b(Nintendo|Nintendo WiiU|Nintendo 3DS|Nintendo Switch|PLAYSTATION|Xbox)\b',
        'Watch'       => 'SM-V700',
    );

    /**
     * All possible HTTP headers that represent the
     * User-Agent string.
     *
     * @var array
     */
    protected static $uaHttpHeaders = array(
        // The default User-Agent string.
        'HTTP_USER_AGENT',
        // Header can occur on devices using Opera Mini.
        'HTTP_X_OPERAMINI_PHONE_UA',
        // Vodafone specific header: http://www.seoprinciple.com/mobile-web-community-still-angry-at-vodafone/24/
        'HTTP_X_DEVICE_USER_AGENT',
        'HTTP_X_ORIGINAL_USER_AGENT',
        'HTTP_X_SKYFIRE_PHONE',
        'HTTP_X_BOLT_PHONE_UA',
        'HTTP_DEVICE_STOCK_UA',
        'HTTP_X_UCBROWSER_DEVICE_UA'
    );

    /**
     * The individual segments that could exist in a User-Agent string. VER refers to the regular
     * expression defined in the constant self::VER.
     *
     * @var array
     */
    protected static $properties = array(

        // Build
        'Mobile'        => 'Mobile/[VER]',
        'Build'         => 'Build/[VER]',
        'Version'       => 'Version/[VER]',
        'VendorID'      => 'VendorID/[VER]',

        // Devices
        'iPad'          => 'iPad.*CPU[a-z ]+[VER]',
        'iPhone'        => 'iPhone.*CPU[a-z ]+[VER]',
        'iPod'          => 'iPod.*CPU[a-z ]+[VER]',
        //'BlackBerry'    => array('BlackBerry[VER]', 'BlackBerry [VER];'),
        'Kindle'        => 'Kindle/[VER]',

        // Browser
        'Chrome'        => array('Chrome/[VER]', 'CriOS/[VER]', 'CrMo/[VER]'),
        'Coast'         => array('Coast/[VER]'),
        'Dolfin'        => 'Dolfin/[VER]',
        // @reference: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/User-Agent/Firefox
        'Firefox'       => array('Firefox/[VER]', 'FxiOS/[VER]'), 
        'Fennec'        => 'Fennec/[VER]',
        // http://msdn.microsoft.com/en-us/library/ms537503(v=vs.85).aspx
        // https://msdn.microsoft.com/en-us/library/ie/hh869301(v=vs.85).aspx
        'Edge' => 'Edge/[VER]',
        'IE'      => array('IEMobile/[VER];', 'IEMobile [VER]', 'MSIE [VER];', 'Trident/[0-9.]+;.*rv:[VER]'),
        // http://en.wikipedia.org/wiki/NetFront
        'NetFront'      => 'NetFront/[VER]',
        'NokiaBrowser'  => 'NokiaBrowser/[VER]',
        'Opera'         => array( ' OPR/[VER]', 'Opera Mini/[VER]', 'Version/[VER]' ),
        'Opera Mini'    => 'Opera Mini/[VER]',
        'Opera Mobi'    => 'Version/[VER]',
        'UCBrowser'    => array( 'UCWEB[VER]', 'UC.*Browser/[VER]' ),
        'MQQBrowser'    => 'MQQBrowser/[VER]',
        'MicroMessenger' => 'MicroMessenger/[VER]',
        'baiduboxapp'   => 'baiduboxapp/[VER]',
        'baidubrowser'  => 'baidubrowser/[VER]',
        'SamsungBrowser' => 'SamsungBrowser/[VER]',
        'Iron'          => 'Iron/[VER]',
        // @note: Safari 7534.48.3 is actually Version 5.1.
        // @note: On BlackBerry the Version is overwriten by the OS.
        'Safari'        => array( 'Version/[VER]', 'Safari/[VER]' ),
        'Skyfire'       => 'Skyfire/[VER]',
        'Tizen'         => 'Tizen/[VER]',
        'Webkit'        => 'webkit[ /][VER]',
        'PaleMoon'         => 'PaleMoon/[VER]',

        // Engine
        'Gecko'         => 'Gecko/[VER]',
        'Trident'       => 'Trident/[VER]',
        'Presto'        => 'Presto/[VER]',
        'Goanna'           => 'Goanna/[VER]',

        // OS
        'iOS'              => ' \bi?OS\b [VER][ ;]{1}',
        'Android'          => 'Android [VER]',
        'BlackBerry'       => array('BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'),
        'BREW'             => 'BREW [VER]',
        'Java'             => 'Java/[VER]',
        // @reference: http://windowsteamblog.com/windows_phone/b/wpdev/archive/2011/08/29/introducing-the-ie9-on-windows-phone-mango-user-agent-string.aspx
        // @reference: http://en.wikipedia.org/wiki/Windows_NT#Releases
        'Windows Phone OS' => array( 'Windows Phone OS [VER]', 'Windows Phone [VER]'),
        'Windows Phone'    => 'Windows Phone [VER]',
        'Windows CE'       => 'Windows CE/[VER]',
        // http://social.msdn.microsoft.com/Forums/en-US/windowsdeveloperpreviewgeneral/thread/6be392da-4d2f-41b4-8354-8dcee20c85cd
        'Windows NT'       => 'Windows NT [VER]',
        'Symbian'          => array('SymbianOS/[VER]', 'Symbian/[VER]'),
        'webOS'            => array('webOS/[VER]', 'hpwOS/[VER];'),
    );

    /**
     * Construct an instance of this class.
     *
     * @param array  $headers   Specify the headers as injection. Should be PHP _SERVER flavored.
     *                          If left empty, will use the global _SERVER['HTTP_*'] vars instead.
     * @param string $userAgent Inject the User-Agent header. If null, will use HTTP_USER_AGENT
     *                          from the $headers array instead.
     */
    public function __construct(
        array $headers = null,
        $userAgent = null
    ) {
        $this->setHttpHeaders($headers);
        $this->setUserAgent($userAgent);
    }

    /**
     * Get the current script version.
     * This is useful for the demo.php file,
     * so people can check on what version they are testing
     * for mobile devices.
     *
     * @return string The version number in semantic version format.
     */
    public static function getScriptVersion()
    {
        return self::VERSION;
    }

    /**
     * Set the HTTP Headers. Must be PHP-flavored. This method will reset existing headers.
     *
     * @param array $httpHeaders The headers to set. If null, then using PHP's _SERVER to extract
     *                           the headers. The default null is left for backwards compatibility.
     */
    public function setHttpHeaders($httpHeaders = null)
    {
        // use global _SERVER if $httpHeaders aren't defined
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        // clear existing headers
        $this->httpHeaders = array();

        // Only save HTTP headers. In PHP land, that means only _SERVER vars that
        // start with HTTP_.
        foreach ($httpHeaders as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $this->httpHeaders[$key] = $value;
            }
        }

        // In case we're dealing with CloudFront, we need to know.
        $this->setCfHeaders($httpHeaders);
    }

    /**
     * Retrieves the HTTP headers.
     *
     * @return array
     */
    public function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    /**
     * Retrieves a particular header. If it doesn't exist, no exception/error is caused.
     * Simply null is returned.
     *
     * @param string $header The name of the header to retrieve. Can be HTTP compliant such as
     *                       "User-Agent" or "X-Device-User-Agent" or can be php-esque with the
     *                       all-caps, HTTP_ prefixed, underscore seperated awesomeness.
     *
     * @return string|null The value of the header.
     */
    public function getHttpHeader($header)
    {
        // are we using PHP-flavored headers?
        if (strpos($header, '_') === false) {
            $header = str_replace('-', '_', $header);
            $header = strtoupper($header);
        }

        // test the alternate, too
        $altHeader = 'HTTP_' . $header;

        //Test both the regular and the HTTP_ prefix
        if (isset($this->httpHeaders[$header])) {
            return $this->httpHeaders[$header];
        } elseif (isset($this->httpHeaders[$altHeader])) {
            return $this->httpHeaders[$altHeader];
        }

        return null;
    }

    public function getMobileHeaders()
    {
        return self::$mobileHeaders;
    }

    /**
     * Get all possible HTTP headers that
     * can contain the User-Agent string.
     *
     * @return array List of HTTP headers.
     */
    public function getUaHttpHeaders()
    {
        return self::$uaHttpHeaders;
    }


    /**
     * Set CloudFront headers
     * http://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/header-caching.html#header-caching-web-device
     *
     * @param array $cfHeaders List of HTTP headers
     *
     * @return  boolean If there were CloudFront headers to be set
     */
    public function setCfHeaders($cfHeaders = null) {
        // use global _SERVER if $cfHeaders aren't defined
        if (!is_array($cfHeaders) || !count($cfHeaders)) {
            $cfHeaders = $_SERVER;
        }

        // clear existing headers
        $this->cloudfrontHeaders = array();

        // Only save CLOUDFRONT headers. In PHP land, that means only _SERVER vars that
        // start with cloudfront-.
        $response = false;
        foreach ($cfHeaders as $key => $value) {
            if (substr(strtolower($key), 0, 16) === 'http_cloudfront_') {
                $this->cloudfrontHeaders[strtoupper($key)] = $value;
                $response = true;
            }
        }

        return $response;
    }

    /**
     * Retrieves the cloudfront headers.
     *
     * @return array
     */
    public function getCfHeaders()
    {
        return $this->cloudfrontHeaders;
    }

    /**
     * @param string $userAgent
     * @return string
     */
    private function prepareUserAgent($userAgent) {
        $userAgent = trim($userAgent);
        $userAgent = substr($userAgent, 0, 500);
        return $userAgent;
    }

    /**
     * Set the User-Agent to be used.
     *
     * @param string $userAgent The user agent string to set.
     *
     * @return string|null
     */
    public function setUserAgent($userAgent = null)
    {
        // Invalidate cache due to #375
        $this->cache = array();

        if (false === empty($userAgent)) {
            return $this->userAgent = $this->prepareUserAgent($userAgent);
        } else {
            $this->userAgent = null;
            foreach ($this->getUaHttpHeaders() as $altHeader) {
                if (false === empty($this->httpHeaders[$altHeader])) { // @todo: should use getHttpHeader(), but it would be slow. (Serban)
                    $this->userAgent .= $this->httpHeaders[$altHeader] . " ";
                }
            }

            if (!empty($this->userAgent)) {
                return $this->userAgent = $this->prepareUserAgent($this->userAgent);
            }
        }

        if (count($this->getCfHeaders()) > 0) {
            return $this->userAgent = 'Amazon CloudFront';
        }
        return $this->userAgent = null;
    }

    /**
     * Retrieve the User-Agent.
     *
     * @return string|null The user agent if it's set.
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set the detection type. Must be one of self::DETECTION_TYPE_MOBILE or
     * self::DETECTION_TYPE_EXTENDED. Otherwise, nothing is set.
     *
     * @deprecated since version 2.6.9
     *
     * @param string $type The type. Must be a self::DETECTION_TYPE_* constant. The default
     *                     parameter is null which will default to self::DETECTION_TYPE_MOBILE.
     */
    public function setDetectionType($type = null)
    {
        if ($type === null) {
            $type = self::DETECTION_TYPE_MOBILE;
        }

        if ($type !== self::DETECTION_TYPE_MOBILE && $type !== self::DETECTION_TYPE_EXTENDED) {
            return;
        }

        $this->detectionType = $type;
    }

    public function getMatchingRegex()
    {
        return $this->matchingRegex;
    }

    public function getMatchesArray()
    {
        return $this->matchesArray;
    }

    /**
     * Retrieve the list of known phone devices.
     *
     * @return array List of phone devices.
     */
    public static function getPhoneDevices()
    {
        return self::$phoneDevices;
    }

    /**
     * Retrieve the list of known tablet devices.
     *
     * @return array List of tablet devices.
     */
    public static function getTabletDevices()
    {
        return self::$tabletDevices;
    }

    /**
     * Alias for getBrowsers() method.
     *
     * @return array List of user agents.
     */
    public static function getUserAgents()
    {
        return self::getBrowsers();
    }

    /**
     * Retrieve the list of known browsers. Specifically, the user agents.
     *
     * @return array List of browsers / user agents.
     */
    public static function getBrowsers()
    {
        return self::$browsers;
    }

    /**
     * Retrieve the list of known utilities.
     *
     * @return array List of utilities.
     */
    public static function getUtilities()
    {
        return self::$utilities;
    }

    /**
     * Method gets the mobile detection rules. This method is used for the magic methods $detect->is*().
     *
     * @deprecated since version 2.6.9
     *
     * @return array All the rules (but not extended).
     */
    public static function getMobileDetectionRules()
    {
        static $rules;

        if (!$rules) {
            $rules = array_merge(
                self::$phoneDevices,
                self::$tabletDevices,
                self::$operatingSystems,
                self::$browsers
            );
        }

        return $rules;

    }

    /**
     * Method gets the mobile detection rules + utilities.
     * The reason this is separate is because utilities rules
     * don't necessary imply mobile. This method is used inside
     * the new $detect->is('stuff') method.
     *
     * @deprecated since version 2.6.9
     *
     * @return array All the rules + extended.
     */
    public function getMobileDetectionRulesExtended()
    {
        static $rules;

        if (!$rules) {
            // Merge all rules together.
            $rules = array_merge(
                self::$phoneDevices,
                self::$tabletDevices,
                self::$operatingSystems,
                self::$browsers,
                self::$utilities
            );
        }

        return $rules;
    }

    /**
     * Retrieve the current set of rules.
     *
     * @deprecated since version 2.6.9
     *
     * @return array
     */
    public function getRules()
    {
        if ($this->detectionType == self::DETECTION_TYPE_EXTENDED) {
            return self::getMobileDetectionRulesExtended();
        } else {
            return self::getMobileDetectionRules();
        }
    }

    /**
     * Retrieve the list of mobile operating systems.
     *
     * @return array The list of mobile operating systems.
     */
    public static function getOperatingSystems()
    {
        return self::$operatingSystems;
    }

    /**
     * Check the HTTP headers for signs of mobile.
     * This is the fastest mobile check possible; it's used
     * inside isMobile() method.
     *
     * @return bool
     */
    public function checkHttpHeadersForMobile()
    {

        foreach ($this->getMobileHeaders() as $mobileHeader => $matchType) {
            if (isset($this->httpHeaders[$mobileHeader])) {
                if (is_array($matchType['matches'])) {
                    foreach ($matchType['matches'] as $_match) {
                        if (strpos($this->httpHeaders[$mobileHeader], $_match) !== false) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Magic overloading method.
     *
     * @method boolean is[...]()
     * @param  string                 $name
     * @param  array                  $arguments
     * @return mixed
     * @throws BadMethodCallException when the method doesn't exist and doesn't start with 'is'
     */
    public function __call($name, $arguments)
    {
        // make sure the name starts with 'is', otherwise
        if (substr($name, 0, 2) !== 'is') {
            throw new BadMethodCallException("No such method exists: $name");
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        $key = substr($name, 2);

        return $this->matchUAAgainstKey($key);
    }

    /**
     * Find a detection rule that matches the current User-agent.
     *
     * @param  null    $userAgent deprecated
     * @return boolean
     */
    protected function matchDetectionRulesAgainstUA($userAgent = null)
    {
        // Begin general search.
        foreach ($this->getRules() as $_regex) {
            if (empty($_regex)) {
                continue;
            }

            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search for a certain key in the rules array.
     * If the key is found then try to match the corresponding
     * regex against the User-Agent.
     *
     * @param string $key
     *
     * @return boolean
     */
    protected function matchUAAgainstKey($key)
    {
        // Make the keys lowercase so we can match: isIphone(), isiPhone(), isiphone(), etc.
        $key = strtolower($key);
        if (false === isset($this->cache[$key])) {

            // change the keys to lower case
            $_rules = array_change_key_case($this->getRules());

            if (false === empty($_rules[$key])) {
                $this->cache[$key] = $this->match($_rules[$key]);
            }

            if (false === isset($this->cache[$key])) {
                $this->cache[$key] = false;
            }
        }

        return $this->cache[$key];
    }

    /**
     * Check if the device is mobile.
     * Returns true if any type of mobile device detected, including special ones
     * @param  null $userAgent   deprecated
     * @param  null $httpHeaders deprecated
     * @return bool
     */
    public function isMobile($userAgent = null, $httpHeaders = null)
    {

        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }

        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        // Check specifically for cloudfront headers if the useragent === 'Amazon CloudFront'
        if ($this->getUserAgent() === 'Amazon CloudFront') {
            $cfHeaders = $this->getCfHeaders();
            if(array_key_exists('HTTP_CLOUDFRONT_IS_MOBILE_VIEWER', $cfHeaders) && $cfHeaders['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] === 'true') {
                return true;
            }
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        if ($this->checkHttpHeadersForMobile()) {
            return true;
        } else {
            return $this->matchDetectionRulesAgainstUA();
        }

    }

    /**
     * Check if the device is a tablet.
     * Return true if any type of tablet device is detected.
     *
     * @param  string $userAgent   deprecated
     * @param  array  $httpHeaders deprecated
     * @return bool
     */
    public function isTablet($userAgent = null, $httpHeaders = null)
    {
        // Check specifically for cloudfront headers if the useragent === 'Amazon CloudFront'
        if ($this->getUserAgent() === 'Amazon CloudFront') {
            $cfHeaders = $this->getCfHeaders();
            if(array_key_exists('HTTP_CLOUDFRONT_IS_TABLET_VIEWER', $cfHeaders) && $cfHeaders['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] === 'true') {
                return true;
            }
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        foreach (self::$tabletDevices as $_regex) {
            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method checks for a certain property in the
     * userAgent.
     * @todo: The httpHeaders part is not yet used.
     *
     * @param  string        $key
     * @param  string        $userAgent   deprecated
     * @param  string        $httpHeaders deprecated
     * @return bool|int|null
     */
    public function is($key, $userAgent = null, $httpHeaders = null)
    {
        // Set the UA and HTTP headers only if needed (eg. batch mode).
        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }

        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        $this->setDetectionType(self::DETECTION_TYPE_EXTENDED);

        return $this->matchUAAgainstKey($key);
    }

    /**
     * Some detection rules are relative (not standard),
     * because of the diversity of devices, vendors and
     * their conventions in representing the User-Agent or
     * the HTTP headers.
     *
     * This method will be used to check custom regexes against
     * the User-Agent string.
     *
     * @param $regex
     * @param  string $userAgent
     * @return bool
     *
     * @todo: search in the HTTP headers too.
     */
    public function match($regex, $userAgent = null)
    {
        $match = (bool) preg_match(sprintf('#%s#is', $regex), (false === empty($userAgent) ? $userAgent : $this->userAgent), $matches);
        // If positive match is found, store the results for debug.
        if ($match) {
            $this->matchingRegex = $regex;
            $this->matchesArray = $matches;
        }

        return $match;
    }

    /**
     * Get the properties array.
     *
     * @return array
     */
    public static function getProperties()
    {
        return self::$properties;
    }

    /**
     * Prepare the version number.
     *
     * @todo Remove the error supression from str_replace() call.
     *
     * @param string $ver The string version, like "2.6.21.2152";
     *
     * @return float
     */
    public function prepareVersionNo($ver)
    {
        $ver = str_replace(array('_', ' ', '/'), '.', $ver);
        $arrVer = explode('.', $ver, 2);

        if (isset($arrVer[1])) {
            $arrVer[1] = @str_replace('.', '', $arrVer[1]); // @todo: treat strings versions.
        }

        return (float) implode('.', $arrVer);
    }

    /**
     * Check the version of the given property in the User-Agent.
     * Will return a float number. (eg. 2_0 will return 2.0, 4.3.1 will return 4.31)
     *
     * @param string $propertyName The name of the property. See self::getProperties() array
     *                             keys for all possible properties.
     * @param string $type         Either self::VERSION_TYPE_STRING to get a string value or
     *                             self::VERSION_TYPE_FLOAT indicating a float value. This parameter
     *                             is optional and defaults to self::VERSION_TYPE_STRING. Passing an
     *                             invalid parameter will default to the this type as well.
     *
     * @return string|float The version of the property we are trying to extract.
     */
    public function version($propertyName, $type = self::VERSION_TYPE_STRING)
    {
        if (empty($propertyName)) {
            return false;
        }

        // set the $type to the default if we don't recognize the type
        if ($type !== self::VERSION_TYPE_STRING && $type !== self::VERSION_TYPE_FLOAT) {
            $type = self::VERSION_TYPE_STRING;
        }

        $properties = self::getProperties();

        // Check if the property exists in the properties array.
        if (true === isset($properties[$propertyName])) {

            // Prepare the pattern to be matched.
            // Make sure we always deal with an array (string is converted).
            $properties[$propertyName] = (array) $properties[$propertyName];

            foreach ($properties[$propertyName] as $propertyMatchString) {

                $propertyPattern = str_replace('[VER]', self::VER, $propertyMatchString);

                // Identify and extract the version.
                preg_match(sprintf('#%s#is', $propertyPattern), $this->userAgent, $match);

                if (false === empty($match[1])) {
                    $version = ($type == self::VERSION_TYPE_FLOAT ? $this->prepareVersionNo($match[1]) : $match[1]);

                    return $version;
                }

            }

        }

        return false;
    }

    /**
     * Retrieve the mobile grading, using self::MOBILE_GRADE_* constants.
     *
     * @return string One of the self::MOBILE_GRADE_* constants.
     */
    public function mobileGrade()
    {
        $isMobile = $this->isMobile();

        if (
            // Apple iOS 4-7.0 – Tested on the original iPad (4.3 / 5.0), iPad 2 (4.3 / 5.1 / 6.1), iPad 3 (5.1 / 6.0), iPad Mini (6.1), iPad Retina (7.0), iPhone 3GS (4.3), iPhone 4 (4.3 / 5.1), iPhone 4S (5.1 / 6.0), iPhone 5 (6.0), and iPhone 5S (7.0)
            $this->is('iOS') && $this->version('iPad', self::VERSION_TYPE_FLOAT) >= 4.3 ||
            $this->is('iOS') && $this->version('iPhone', self::VERSION_TYPE_FLOAT) >= 4.3 ||
            $this->is('iOS') && $this->version('iPod', self::VERSION_TYPE_FLOAT) >= 4.3 ||

            // Android 2.1-2.3 - Tested on the HTC Incredible (2.2), original Droid (2.2), HTC Aria (2.1), Google Nexus S (2.3). Functional on 1.5 & 1.6 but performance may be sluggish, tested on Google G1 (1.5)
            // Android 3.1 (Honeycomb)  - Tested on the Samsung Galaxy Tab 10.1 and Motorola XOOM
            // Android 4.0 (ICS)  - Tested on a Galaxy Nexus. Note: transition performance can be poor on upgraded devices
            // Android 4.1 (Jelly Bean)  - Tested on a Galaxy Nexus and Galaxy 7
            ( $this->version('Android', self::VERSION_TYPE_FLOAT)>2.1 && $this->is('Webkit') ) ||

            // Windows Phone 7.5-8 - Tested on the HTC Surround (7.5), HTC Trophy (7.5), LG-E900 (7.5), Nokia 800 (7.8), HTC Mazaa (7.8), Nokia Lumia 520 (8), Nokia Lumia 920 (8), HTC 8x (8)
            $this->version('Windows Phone OS', self::VERSION_TYPE_FLOAT) >= 7.5 ||

            // Tested on the Torch 9800 (6) and Style 9670 (6), BlackBerry® Torch 9810 (7), BlackBerry Z10 (10)
            $this->is('BlackBerry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT) >= 6.0 ||
            // Blackberry Playbook (1.0-2.0) - Tested on PlayBook
            $this->match('Playbook.*Tablet') ||

            // Palm WebOS (1.4-3.0) - Tested on the Palm Pixi (1.4), Pre (1.4), Pre 2 (2.0), HP TouchPad (3.0)
            ( $this->version('webOS', self::VERSION_TYPE_FLOAT) >= 1.4 && $this->match('Palm|Pre|Pixi') ) ||
            // Palm WebOS 3.0  - Tested on HP TouchPad
            $this->match('hp.*TouchPad') ||

            // Firefox Mobile 18 - Tested on Android 2.3 and 4.1 devices
            ( $this->is('Firefox') && $this->version('Firefox', self::VERSION_TYPE_FLOAT) >= 18 ) ||

            // Chrome for Android - Tested on Android 4.0, 4.1 device
            ( $this->is('Chrome') && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT) >= 4.0 ) ||

            // Skyfire 4.1 - Tested on Android 2.3 device
            ( $this->is('Skyfire') && $this->version('Skyfire', self::VERSION_TYPE_FLOAT) >= 4.1 && $this->is('AndroidOS') && $this->version('Android', self::VERSION_TYPE_FLOAT) >= 2.3 ) ||

            // Opera Mobile 11.5-12: Tested on Android 2.3
            ( $this->is('Opera') && $this->version('Opera Mobi', self::VERSION_TYPE_FLOAT) >= 11.5 && $this->is('AndroidOS') ) ||

            // Meego 1.2 - Tested on Nokia 950 and N9
            $this->is('MeeGoOS') ||

            // Tizen (pre-release) - Tested on early hardware
            $this->is('Tizen') ||

            // Samsung Bada 2.0 - Tested on a Samsung Wave 3, Dolphin browser
            // @todo: more tests here!
            $this->is('Dolfin') && $this->version('Bada', self::VERSION_TYPE_FLOAT) >= 2.0 ||

            // UC Browser - Tested on Android 2.3 device
            ( ($this->is('UC Browser') || $this->is('Dolfin')) && $this->version('Android', self::VERSION_TYPE_FLOAT) >= 2.3 ) ||

            // Kindle 3 and Fire  - Tested on the built-in WebKit browser for each
            ( $this->match('Kindle Fire') ||
            $this->is('Kindle') && $this->version('Kindle', self::VERSION_TYPE_FLOAT) >= 3.0 ) ||

            // Nook Color 1.4.1 - Tested on original Nook Color, not Nook Tablet
            $this->is('AndroidOS') && $this->is('NookTablet') ||

            // Chrome Desktop 16-24 - Tested on OS X 10.7 and Windows 7
            $this->version('Chrome', self::VERSION_TYPE_FLOAT) >= 16 && !$isMobile ||

            // Safari Desktop 5-6 - Tested on OS X 10.7 and Windows 7
            $this->version('Safari', self::VERSION_TYPE_FLOAT) >= 5.0 && !$isMobile ||

            // Firefox Desktop 10-18 - Tested on OS X 10.7 and Windows 7
            $this->version('Firefox', self::VERSION_TYPE_FLOAT) >= 10.0 && !$isMobile ||

            // Internet Explorer 7-9 - Tested on Windows XP, Vista and 7
            $this->version('IE', self::VERSION_TYPE_FLOAT) >= 7.0 && !$isMobile ||

            // Opera Desktop 10-12 - Tested on OS X 10.7 and Windows 7
            $this->version('Opera', self::VERSION_TYPE_FLOAT) >= 10 && !$isMobile
        ){
            return self::MOBILE_GRADE_A;
        }

        if (
            $this->is('iOS') && $this->version('iPad', self::VERSION_TYPE_FLOAT)<4.3 ||
            $this->is('iOS') && $this->version('iPhone', self::VERSION_TYPE_FLOAT)<4.3 ||
            $this->is('iOS') && $this->version('iPod', self::VERSION_TYPE_FLOAT)<4.3 ||

            // Blackberry 5.0: Tested on the Storm 2 9550, Bold 9770
            $this->is('Blackberry') && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT) >= 5 && $this->version('BlackBerry', self::VERSION_TYPE_FLOAT)<6 ||

            //Opera Mini (5.0-6.5) - Tested on iOS 3.2/4.3 and Android 2.3
            ($this->version('Opera Mini', self::VERSION_TYPE_FLOAT) >= 5.0 && $this->version('Opera Mini', self::VERSION_TYPE_FLOAT) <= 7.0 &&
            ($this->version('Android', self::VERSION_TYPE_FLOAT) >= 2.3 || $this->is('iOS')) ) ||

            // Nokia Symbian^3 - Tested on Nokia N8 (Symbian^3), C7 (Symbian^3), also works on N97 (Symbian^1)
            $this->match('NokiaN8|NokiaC7|N97.*Series60|Symbian/3') ||

            // @todo: report this (tested on Nokia N71)
            $this->version('Opera Mobi', self::VERSION_TYPE_FLOAT) >= 11 && $this->is('SymbianOS')
        ){
            return self::MOBILE_GRADE_B;
        }

        if (
            // Blackberry 4.x - Tested on the Curve 8330
            $this->version('BlackBerry', self::VERSION_TYPE_FLOAT) <= 5.0 ||
            // Windows Mobile - Tested on the HTC Leo (WinMo 5.2)
            $this->match('MSIEMobile|Windows CE.*Mobile') || $this->version('Windows Mobile', self::VERSION_TYPE_FLOAT) <= 5.2 ||

            // Tested on original iPhone (3.1), iPhone 3 (3.2)
            $this->is('iOS') && $this->version('iPad', self::VERSION_TYPE_FLOAT) <= 3.2 ||
            $this->is('iOS') && $this->version('iPhone', self::VERSION_TYPE_FLOAT) <= 3.2 ||
            $this->is('iOS') && $this->version('iPod', self::VERSION_TYPE_FLOAT) <= 3.2 ||

            // Internet Explorer 7 and older - Tested on Windows XP
            $this->version('IE', self::VERSION_TYPE_FLOAT) <= 7.0 && !$isMobile
        ){
            return self::MOBILE_GRADE_C;
        }

        // All older smartphone platforms and featurephones - Any device that doesn't support media queries
        // will receive the basic, C grade experience.
        return self::MOBILE_GRADE_C;
    }
}