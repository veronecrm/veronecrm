# core.crm
---
#####  2016-01-03
- Add - (veronecrm) Service "registration".
- Add - (veronecrm) Class CRM\Registration - allows check if application is registered.
- Add - (veronecrm) 2 new App settings: registration.status, registration.lastcheck.
#####  2016-01-02
- Add - (veronecrm) Unit tests - System\Http\Request.
- Add - (veronecrm) Unit tests directory (phpunit).
- Fix - (veronecrm) Returned value of System\Http\Request::getFullUrl() method.
- Fix - (veronecrm) Set locale, while Session object isn't provided in Request.
- Fix - (veronecrm) Load Composer autoload.php file.
- Add - (veronecrm) Shortcut method Base::log() to Service's "history.user.log" log() method.
- Add - (veronecrm) CRM\History\User\Log::log() method - allow save log as clear text, without give Entity object.
- Add - (veronecrm) Remove Settings keys and values during uninstallation Module.
- Add - (veronecrm) SettingsProvider::unregisterKey() method.
- Add - (veronecrm) Add APP.BrowserNotification plugin to APP - for managing Browser Notification.
#####  2016-01-01
- Add - (veronecrm) Add APP.PageTitle plugin to APP - for managing page title and creating intervals.
- Add - (veronecrm) Add APP.FileInput plugin to APP.
#####  2015-12-28
- Fix - (veronecrm) BS Tooltip container.
- Add - (veronecrm) APP.RecordHistoryLog - Link to page with full changes of given Entity object and ID.
- Add - (veronecrm) Posibility to create relation with multiple changes in different Entities and/or Modules in one time.
- Fix - (veronecrm) Problem with register new services, as always-new.
- Add - (veronecrm) Function applyTextareaAutoGrow(selector) in JS to create auto-grow textarea live.
#####  2015-12-27
- Add - (veronecrm) Add event listener 'autogrow.update' for 'textarea.auto-grow' selector, that can be used for refresh height of element.
- Fix - (veronecrm) Filepath to jGrowl in APP.js.
- Add - (veronecrm) Composer dependency "yzalis/identicon": "*".
#####  2015-12-18
- Add - (veronecrm) Add flot jQuery library that create Charts.
- Add - (veronecrm) Composer dependency "zendframework/zend-crypt": "2.5.*".
